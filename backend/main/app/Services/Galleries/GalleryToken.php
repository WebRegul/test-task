<?php

namespace App\Services\Galleries;

use App\Helpers\Network;
use App\Models\Gallery as GalleryModel;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ItemNotFoundException;
use App\Exceptions\ValidationException;

/**
 * Class GalleryToken
 * @package App\Services\Galleries
 */
class GalleryToken
{
    /**
     * array
     */
    public const ACCESS_TOKEN_TYPES = [
        'show',
        'download'
    ];

    /**
     * @var string
     */
    private $galleryId;

    /**
     * @var GalleryModel
     */
    private $gallery;

    /**
     * @var string
     */
    private $tokenType;

    /**
     * GalleryToken constructor.
     * @param string $galleryId
     * @param string $tokenType
     */
    public function __construct(string $galleryId, string $tokenType)
    {
        $gallery = GalleryModel::query()->find($galleryId);
        if (empty($gallery->id)) {
            throw new ItemNotFoundException(sprintf('галерея %s не существует', $galleryId));
        }

        if (!in_array($tokenType, self::ACCESS_TOKEN_TYPES)) {
            throw new ValidationException(sprintf('неизвестный тип токена %s', $tokenType));
        }

        $this->gallery = $gallery;
        $this->tokenType = $tokenType;
    }

    /**
     * @param string|null $accessToken
     * @return bool
     */
    public function checkToken(string $accessToken = null): bool
    {
        $token = $this->getCachedToken();

        if (empty($token) || $token->get('value') != $accessToken) {
            return false;
        }

        $galleryPassword = $this->makeTokenString();

        return password_verify($galleryPassword, $accessToken);
    }

    /**
     * @return Collection
     */
    private function getCachedToken(): Collection
    {
        $result = collect();
        $tokenKey = $this->makeTokenKey();

        if (Cache::has($tokenKey)) {
            $result->put('key', $tokenKey);
            $result->put('value', Cache::get($tokenKey));
        }

        return $result;
    }

    /**
     *
     */
    public function forgetCachedToken(): void
    {
        $tokenKey = $this->makeTokenKey();
        if (Cache::has($tokenKey)) {
            Cache::forget($tokenKey);
        }
    }

    /**
     * @param Collection $token
     * @return Collection
     * @throws \Exception
     */
    private function cacheToken(Collection $token): Collection
    {
        if (empty($token->get('key')) || empty($token->get('value'))) {
            throw new ValidationException('нет ключа и значения токена');
        }

        if (!Cache::has($token->get('key'))) {
            Cache::put(
                $token->get('key'),
                $token->get('value'),
                config('gallery.passwords.ttl') * 60
            );
        }

        return $token;
    }

    /**
     * @return string
     */
    private function makeTokenValue(): string
    {
        return password_hash($this->makeTokenString(), PASSWORD_BCRYPT);
    }

    /**
     * @return string
     */
    private function makeTokenKey(): string
    {
        return md5($this->gallery->id . $this->tokenType . Network::getIp());
    }

    /**
     * @return Collection
     */
    public function getToken(): Collection
    {
        $token = $this->getCachedToken();

        if (empty($token->get('value'))) {
            $token = collect();
            $token->put('key', $this->makeTokenKey());
            $token->put('value', $this->makeTokenValue());

            $this->cacheToken($token);
        }

        return $token;
    }

    /**
     * @return string
     */
    private function makeTokenString(): string
    {
        return $this->gallery->id
            . $this->getGalleryPassword()
            . config('gallery.passwords.salt');
    }

    /**
     * @return string
     */
    public function getGalleryPassword(): string
    {
        return ($this->tokenType == self::ACCESS_TOKEN_TYPES[0])
            ? $this->gallery->password
            : $this->gallery->download_password;
    }
}

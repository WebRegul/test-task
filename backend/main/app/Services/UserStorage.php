<?php

namespace App\Services;

use App\Models\Gallery as GalleryModel;
use Illuminate\Support\Facades\Storage;
use App\Models\UserStorage as UserStorageModel;
use Illuminate\Support\ItemNotFoundException;

/**
 * Class UserStorage
 * @package App\Services
 */
class UserStorage
{
    /**
     * @var array
     */
    public const FILE_SIZES = [
        'B' => 0,
        'K' => 1,
        'M' => 2,
        'G' => 3,
        'T' => 4,
        'P' => 5,
    ];

    /**
     * @var string
     */
    private $id;

    /**
     * @var \Illuminate\Contracts\Filesystem\Filesystem
     */
    private $disk;

    /**
     * @param string $userId
     */
    public function __construct(string $userId)
    {
        $this->id = $userId;
        $this->disk = Storage::disk('users');
    }

    /**
     * @return mixed
     */
    public function create()
    {
        $storage = UserStorageModel::query()
            ->where('user_id', $this->id)
            ->firstOrNew();
        $isNewStorage = empty($storage->id);

        $storage->user_id = $this->id;
        $storage->save();
        $storage->refresh();

        if ($isNewStorage || !$this->disk->exists($storage->id)) {
            $this->disk->makeDirectory($storage->id);
        }

        return $storage->id;
    }

    /**
     * @return string
     */
    private function getUserStorage()
    {
        $userStorage = UserStorageModel::query()
            ->where('user_id', $this->id)
            ->first()
            ->pluck('id');

        return $userStorage->id;
    }

    /**
     * @param string $returnType
     * @return float
     */
    public function getStorageSize(string $returnType = 'M'): float
    {
        $storageSize = 0;
        $galleries = GalleryModel::query()
            ->where('user_id', $this->id)
            ->get()
            ->pluck('id');
        foreach ($galleries as $gallery) {
            $storageFiles = $this->disk->files($gallery);
            foreach ($storageFiles as $file) {
                $storageSize += $this->disk->size($file);
            }
        }

        if ($storageSize > 0) {
            if (!array_key_exists($returnType, static::FILE_SIZES)) {
                throw new ItemNotFoundException(sprintf('тип объема %s не существует', $returnType));
            }

            for ($i = 0; $i < static::FILE_SIZES[$returnType]; $i++) {
                $storageSize /= 1024;
            }

            return round($storageSize, 2);
        }

        return $storageSize;
    }
}

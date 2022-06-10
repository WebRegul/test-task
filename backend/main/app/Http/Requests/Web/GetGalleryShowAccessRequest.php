<?php

namespace App\Http\Requests\Web;

use Anik\Form\FormRequest;
use App\Models\Gallery as GalleryModel;
use App\Services\Profile;
use Ramsey\Uuid\Rfc4122\Validator as UuidValidator;

/**
 * Class GetGalleryShowAccessRequest
 * @package App\Http\Requests\Web
 */
class GetGalleryShowAccessRequest extends FormRequest
{
    /**
     * GetGalleryInfoByNameRequest constructor.
     * @param array $query
     * @param array $request
     * @param array $attributes
     * @param array $cookies
     * @param array $files
     * @param array $server
     * @param null $content
     */
    public function __construct(array $query = [], array $request = [], array $attributes = [], array $cookies = [], array $files = [], array $server = [], $content = null)
    {
        request()->request->remove('profile_id');
        request()->request->remove('profile_name');
        request()->request->remove('gallery_id');
        request()->request->remove('gallery_name');

        $profileId = request()->route('pid');
        if ((new UuidValidator())->validate($profileId)) {
            request()->merge(['profile_id' => $profileId]);
        } else {
            request()->merge(['profile_name' => $profileId]);
        }

        $galleryId = request()->route('gid');
        if ((new UuidValidator())->validate($galleryId)) {
            request()->merge(['gallery_id' => $galleryId]);
        } else {
            request()->merge(['gallery_name' => $galleryId]);
        }

        parent::__construct($query, $request, $attributes, $cookies, $files, $server, $content);
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'profile_id' => [
                'bail',
                'required_if:profile_name,null',
                'string',
                'exists:profiles,id',
            ],
            'profile_name' => [
                'bail',
                'required_if:profile_id,null',
                'normal_slug',
                'exists:profiles,name',
            ],
            'gallery_id' => [
                'bail',
                'required_if:gallery_name,null',
                'string',
                'exists:galleries,id',
            ],
            'gallery_name' => [
                'bail',
                'required_if:gallery_id,null',
                'normal_slug',
                function ($attribute, $value, $fail) {
                    $request = $this->request;
                    $profile = new Profile(
                        $request->get('profile_id'),
                        $request->get('profile_name')
                    );
                    $profile = $profile->get();

                    $gallery = GalleryModel::query()
                        ->where('user_id', $profile->user_id)
                        ->where('name', $value)
                        ->select('id')
                        ->first();

                    if (empty($gallery->id)) {
                        $fail('галереи с указанным адресом :input не существует');
                    }
                },
            ],
            'password' => [
                'required',
                'string'
            ],
        ];
    }

    /**
     * @return array
     */
    public function messages(): array
    {
        return [
            'profile_id.required_if' => 'не указан id профиля',
            'profile_id.string' => 'id профиля не строка',
            'profile_id.exists' => 'профиля с указанным id :input не существует',
            'profile_name.required_if' => 'не введен адрес профиля',
            'profile_name.normal_slug' => 'адрес профиля может содержать только латиницу, цифры, знаки тире и подчеркивания',
            'profile_name.exists' => 'профиля с указанным адресом :input не существует',
            'gallery_id.required_if' => 'не указан id галереи',
            'gallery_id.string' => 'id галереи не строка',
            'gallery_id.exists' => 'галереи с указанным id :input не существует',
            'gallery_name.required_if' => 'не введен адрес галереи',
            'gallery_name.normal_slug' => 'адрес галереи может содержать только латиницу, цифры, знаки тире и подчеркивания',
            'gallery_name.exists' => 'галереи с указанным адресом :input не существует',
            'password.required' => 'для получения доступа к защищенной галерее заполнение пароля обязательно',
            'password.string' => 'пароль должен быть строкой',
        ];
    }
}

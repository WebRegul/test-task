<?php

namespace App\Services;

use App\Exceptions\ForbiddenException;
use App\Exceptions\ValidationException;
use App\Helpers\Helper;
use App\Helpers\Permissions;
use App\Models\UserContact as UserContactModel;
use App\Models\UserContactType as UserContactTypeModel;
use App\Registries\Member as MemberRegistry;
use Egulias\EmailValidator\EmailValidator;
use Egulias\EmailValidator\Validation\RFCValidation;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\ItemNotFoundException;
use Illuminate\Support\Str;

/**
 * Class UserContact
 * @package App\Services
 */
class UserContact
{
    /**
     * @var MemberRegistry
     */
    private $member;

    public function __construct(MemberRegistry $member)
    {
        $this->member = $member;
    }

    /**
     * @param string $typeName
     * @param string $value
     * @return Collection
     */
    public function create(string $typeName, string $value): Collection
    {
        $type = UserContactTypeModel::query()
            ->where('name', $typeName)
            ->first();
        if (empty($type)) {
            throw new ItemNotFoundException(sprintf('тип контакта %s не существует', $typeName), 404);
        }

        $userContact = UserContactModel::where('user_id', $this->member->get('id'))
            ->where('user_contacts_type_id', $type->id)
            ->first();

        if (!empty($userContact)) {
            throw new ItemNotFoundException('Ваш контакт уже привязан к аккаунту', 409);
        }
        try {
            $this->createContactTypeValidate($typeName, $value);
        } catch (ValidationException $e) {
            throw  $e;
        }
        $value = $this->clearValue($typeName, $value);

        $userContact = new UserContactModel();
        $userContact->user_id = $this->member->get('id');
        $userContact->user_contacts_type_id = $type->id;
        $userContact->value = $value;

        $userContact->save();
        $userContact->refresh();
        $userContact = UserContactModel::where('id', $userContact->id)
            ->with('type')
            ->first();
        return collect($userContact);
    }

    public function createContactTypeValidate(string $type, string $value)
    {
        switch ($type) {
            case 'phone':
                if (!preg_match('/^7\d{10}$/', $value)) {
                    throw new  ValidationException('телефон должен состоять из 11 цифр и начинаться с 7');
                }
                break;
            case 'email':
                $validator = new EmailValidator();
                if (!$validator->isValid($value, new RFCValidation())) {
                    throw new  ValidationException('указан некорректный адрес электронной почты');
                }
                break;
            case 'instagram':
                if (!preg_match("/^[A-Za-z0-9-_.@:\/?=]+$/", $value)) {
                    throw new  ValidationException('указан некорректный адрес социальной сети');
                }
                break;
            case 'facebook':
                if (!preg_match("/^[A-Za-z0-9-_.@:\/?=]+$/", $value)) {
                    throw new  ValidationException('указан некорректный адрес социальной сети');
                }
                break;
            case 'vkontakte':
                if (!preg_match("/^[A-Za-z0-9-_.@:\/?=]+$/", $value)) {
                    throw new  ValidationException('указан некорректный адрес социальной сети');
                }
                break;
            default:
        }
    }

    /**
     * @param string $typeName
     * @param string $value
     * @return string
     */
    protected function clearValue(string $typeName, string $value): string
    {
        $value = Helper::clearString($value);

        if (in_array($typeName, ['vkontakte', 'instagram', 'facebook'])) {
            $value = strtok($value, '?');
            if ($value) {
                while (in_array($value[strlen($value) - 1], ['/', '\\'])) {
                    $value = substr($value, 0, -1);
                }
            }
            if (strrpos($value, "/")) {
                $value = substr($value, strrpos($value, "/") + 1);
            }
        }
        return $value;
    }

    /**
     * @param string $id
     * @param string $value
     * @return Collection
     * @throws ForbiddenException
     */
    public function update(string $id, string $value): Collection
    {
        $userContact = UserContactModel::query()
            ->where('id', $id)
            ->with('type')
            ->first();

        if (!Permissions::isOwner($userContact)) {
            throw new ForbiddenException('контакт принадлежит другому пользователю');
        }

        $userContact->value = $value;

        $userContact->save();
        $userContact->refresh();

        return collect($userContact);
    }

    /**
     * @param string $id
     * @throws ForbiddenException
     */
    public function delete(string $id): void
    {
        $userContact = UserContactModel::query()
            ->find($id);

        if (!Permissions::isOwner($userContact)) {
            throw new ForbiddenException('контакт принадлежит другому пользователю');
        }

        $userContact->delete();
    }

    /**
     * @return Collection
     */
    public function getContacts(): Collection
    {
        $userContacts = UserContactModel::query()
            ->where('user_id', $this->member->get('id'))
            ->with('type')
            ->get();

        return collect($userContacts);
    }

    /**
     * @return Collection
     */
    public function getContactsTypes(): Collection
    {
        $userContactsTypes = UserContactTypeModel::query()
            ->orderBy('weight')
            ->get();

        return collect($userContactsTypes);
    }
}

<?php

namespace App\Http\Controllers\V1\Cabinet;

use App\Http\Controllers\V1\Controller;
use Illuminate\Auth\Access\AuthorizationException;
use App\Registries\Member;

abstract class BaseController extends Controller
{
    protected $userId;

    /**
     * @throws AuthorizationException
     */
    public function __construct(Member $member)
    {
        $this->userId = $member->get('id');
        if (!$this->userId) {
            //throw new AuthorizationException('Метод доступен только авторизованным пользователям');
        }
    }
}

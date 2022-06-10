<?php

namespace App\Http\Middleware;

use App\Services\User;
use Closure;
use App\Registries\Member as MemberRegistry;

/**
 * посредник  для получения данных о пользователе из сервиса и записи их в singleton реестр
 * Class Member
 * @package App\Http\Middleware
 */
class Member
{
    /**
     * @var User
     */
    protected User $member;

    /**
     * @var MemberRegistry
     */
    protected MemberRegistry $registry;

    public function __construct(User $member, MemberRegistry $registry)
    {
        $this->member = $member;
        $this->registry = $registry;
    }

    public function handle($request, Closure $next)
    {
        $this->member->setMember($this->registry);

        return $next($request);
    }
}

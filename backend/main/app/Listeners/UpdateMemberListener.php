<?php

namespace App\Listeners;

use App\Events\UpdateMemberEvent;
use App\Registries\Member as MemberRegistry;
use App\Services\User;

class UpdateMemberListener
{
    /**
     * @param UpdateMemberEvent $event
     * @throws \Exception
     */
    public function handle(UpdateMemberEvent $event)
    {
        $user = new User($event->id);
        $user->resetMemberData();
        $user->setMember(app(MemberRegistry::class));
    }
}

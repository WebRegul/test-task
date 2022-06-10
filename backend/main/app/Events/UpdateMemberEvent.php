<?php

namespace App\Events;

use Illuminate\Support\Facades\Log;

class UpdateMemberEvent extends Event
{
    #use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var string
     */
    public string $id;

    /**
     * Create a new event instance.
     *
     * @param $id
     * @return void
     */
    public function __construct($id)
    {
        $this->id = $id;
        Log::info('event update member success');
    }
}

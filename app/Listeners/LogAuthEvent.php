<?php

namespace App\Listeners;

use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;
use ReflectionClass;

class LogAuthEvent
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        $type = Str::of((new ReflectionClass($event))->getShortName())->kebab();

        if ($event->user && $event->user instanceof User) {
            activity()
                ->causedBy($event->user)
                ->performedOn($event->user)
                ->event($type)
                ->withProperties([
                    'ip_address' => Request::ip(), // User's IP address
                    'user_agent' => Request::header('User-Agent'), // Browser/Device information
                    'session_id' => session()->getId(), // Session ID
                ])
                ->log($type);
        }
    }
}

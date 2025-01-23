<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class LogRegisteredUser
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
    public function handle(Registered $event): void
    {
        $user = $event->user;

        $filePath = 'registered_users.json';

        $existingData = Storage::exists($filePath)
            ? json_decode(Storage::get($filePath), true)
            : [];

        $newUserData = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'password' => Hash::make($user->password),
            'registered_at' => now()->toDateTimeString(),
        ];

        $existingData[] = $newUserData;

        Storage::put($filePath, json_encode($existingData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
}

<?php

namespace App\Services;

use App\Models\User;
use App\Models\notification;
use Carbon\Carbon;

class NotificationService
{
    /**
     * Send a notification to all users with a specific role.
     *
     * @param string $role
     * @param string $message
     */
    public function sendToRole(string $role, string $message)
    {
        $users = User::where('role', $role)->where('actif', true)->get();

        foreach ($users as $user) {
            Notification::create([
                'user_id' => $user->id,
                'message' => $message,
                'date_envoi' => Carbon::now()->toDateString(),
                'lu' => false,
            ]);
        }
    }

    public function sendToAdmins(string $message)
    {
        $this->sendToRole('admin', $message);
    }

    public function sendToReceptionists(string $message)
    {
        $this->sendToRole('receptionniste', $message);
    }

    public function sendToTechnicians(string $message)
    {
        $this->sendToRole('technicien', $message);
    }

    public function sendToUser(User $user, string $message)
    {
        if ($user->actif) {
            Notification::create([
                'user_id' => $user->id,
                'message' => $message,
                'date_envoi' => Carbon::now()->toDateString(),
                'lu' => false,
            ]);
        }
    }
}

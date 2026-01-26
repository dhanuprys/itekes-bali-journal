<?php

namespace App\Services;

use App\Enums\PermissionRole;
use App\Models\Notification;
use App\Models\User;
use App\Mail\GeneralNotificationMail;
use Illuminate\Support\Facades\Mail;

use App\DTO\NotificationPayload;

class NotificationService
{
    /**
     * Send a notification to a specific user.
     */
    public function send(int|User $user, string $content, array|NotificationPayload|null $extra = null, bool $sendEmail = false): Notification
    {
        $userModel = $user instanceof User ? $user : User::find($user);
        $userId = $userModel?->id ?? (is_int($user) ? $user : $user->id);

        $extraArray = $extra instanceof NotificationPayload ? $extra->toArray() : $extra;

        $notification = Notification::create([
            'user_id' => $userId,
            'content' => $content,
            'extra' => $extraArray,
        ]);

        if ($sendEmail && $userModel && $userModel->email) {
            try {
                $subject = $extraArray['title'] ?? 'Notifikasi Baru';
                $url = $extraArray['url'] ?? null;
                Mail::to($userModel)->queue(new GeneralNotificationMail($subject, $content, $url));
            } catch (\Exception $e) {
                // Log error or ignore to prevent blocking
                \Illuminate\Support\Facades\Log::error('Failed to send notification email: ' . $e->getMessage());
            }
        }

        return $notification;
    }

    /**
     * Send a notification to a specific user.
     */
    public function sendToPermission(PermissionRole $permission, string $content, array|NotificationPayload|null $extra = null, bool $sendEmail = false): void
    {
        // Find users with this permission (via roles)
        // Note: This assumes using Spatie Permission or similar acting on roles/permissions
        // Since we are using a simple PermissionRole enum, we might need to query users by role.
        // Let's assume User model has standard relations. 
        // If using standard many-to-many roles:

        $users = User::permission($permission->value)->get();

        foreach ($users as $user) {
            $this->send($user, $content, $extra, $sendEmail);
        }
    }

    /**
     * Send to all users with specific role(s).
     */
    public function sendToRole(string|array $roles, string $content, array|NotificationPayload|null $extra = null, bool $sendEmail = false): void
    {
        $users = User::role($roles)->get();

        foreach ($users as $user) {
            $this->send($user, $content, $extra, $sendEmail);
        }
    }
}

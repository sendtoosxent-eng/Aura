<?php

namespace App\Jobs;

use App\Models\UserNotification;
use App\Services\ApnsPushService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendUserPushNotification implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    /** @var list<int> */
    public array $backoff = [10, 60, 300];

    public function __construct(public int $notificationId) {}

    /**
     * Execute the job.
     */
    public function handle(ApnsPushService $push): void
    {
        $notification = UserNotification::query()->with('user')->find($this->notificationId);
        if (! $notification?->user) {
            return;
        }

        $push->send($notification->user, $notification->title, $notification->body, [
            'notification_id' => $notification->id,
            'category' => $notification->category,
        ]);
    }
}

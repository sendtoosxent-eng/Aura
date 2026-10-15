<?php

namespace App\Services;

use App\Models\ChatMessage;
use App\Models\MessageThread;
use App\Models\User;
use App\Models\UserNotification;

class MobileDataService
{
    /** @return array{notifications: mixed, message_threads: mixed} */
    public function snapshot(User $user): array
    {
        $notifications = $user->userNotifications()
            ->latest()
            ->limit(100)
            ->get()
            ->map(fn (UserNotification $notification): array => $this->notification($notification))
            ->values();

        $threads = MessageThread::query()
            ->with([
                'patient:id,name',
                'providerUser:id,name,registration_status',
                'healthcareProvider:id,name,is_verified',
                'doctor:id,name',
                'messages' => fn ($builder) => $builder->oldest('sent_at'),
            ])
            ->where(fn ($builder) => $builder
                ->where('patient_user_id', $user->id)
                ->orWhere('provider_user_id', $user->id))
            ->latest('last_activity_at')
            ->get()
            ->map(fn (MessageThread $thread): array => $this->thread($thread, $user))
            ->values();

        return [
            'notifications' => $notifications,
            'message_threads' => $threads,
        ];
    }

    public function canAccessThread(User $user, MessageThread $thread): bool
    {
        return $thread->patient_user_id === $user->id || $thread->provider_user_id === $user->id;
    }

    /** @return array<string, mixed> */
    public function notification(UserNotification $notification): array
    {
        return [
            'id' => $notification->id,
            'title' => $notification->title,
            'message' => $notification->body,
            'category' => $notification->category,
            'icon' => $notification->icon,
            'read' => $notification->read_at !== null,
            'created_at' => $notification->created_at?->toIso8601String(),
        ];
    }

    /** @return array<string, mixed> */
    public function message(ChatMessage $message): array
    {
        return [
            'id' => $message->id,
            'author' => $message->sender_type,
            'text' => $message->body,
            'sent_at' => $message->sent_at->toIso8601String(),
        ];
    }

    /** @return array<string, mixed> */
    public function thread(MessageThread $thread, User $viewer): array
    {
        $participant = $thread->is_automated
            ? ($thread->title ?: 'AfyaLink Care Assistant')
            : ($viewer->id === $thread->patient_user_id
                ? ($thread->providerUser?->name ?? $thread->healthcareProvider?->name ?? $thread->doctor?->name ?? 'Healthcare provider')
                : ($thread->patient?->name ?? 'Patient'));

        $unread = $thread->messages->filter(fn (ChatMessage $message): bool => $message->read_at === null && $message->sender_user_id !== $viewer->id
        )->count();

        return [
            'id' => $thread->id,
            'participant_name' => $participant,
            'subtitle' => $thread->subtitle ?? ($thread->is_automated ? 'Automated health reminders' : 'AfyaLink care'),
            'is_online' => false,
            'is_automated' => $thread->is_automated,
            'verified' => $thread->is_automated
                || $thread->providerUser?->registration_status === 'verified'
                || (bool) $thread->healthcareProvider?->is_verified,
            'last_active_at' => $thread->last_activity_at?->toIso8601String(),
            'unread_count' => $unread,
            'messages' => $thread->messages
                ->map(fn (ChatMessage $message): array => $this->message($message))
                ->values(),
        ];
    }
}

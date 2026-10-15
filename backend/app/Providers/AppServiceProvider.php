<?php

namespace App\Providers;

use App\Models\UserNotification;
use App\Observers\UserNotificationObserver;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        UserNotification::observe(UserNotificationObserver::class);

        VerifyEmail::toMailUsing(function (object $notifiable, string $url): MailMessage {
            return (new MailMessage)
                ->subject('Verify your AfyaLink email address')
                ->greeting('Welcome to AfyaLink, '.$notifiable->name.'!')
                ->line('Verify your email address before completing your healthcare registration.')
                ->action('Verify email address', $url)
                ->line('This verification link expires in 60 minutes. If you did not start this registration, you can ignore this email.');
        });
    }
}

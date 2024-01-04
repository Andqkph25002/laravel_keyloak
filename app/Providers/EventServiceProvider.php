<?php

namespace App\Providers;


use App\Events\DeleteUserEvent;
use App\Events\RegisterUserEvent;
use App\Events\UpdateUserEvent;
use App\Listeners\CreateUserKeycloakListener;
use App\Listeners\DeleteKeycloakListener;
use App\Listeners\UpdateKeycloakListener;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;


class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        RegisterUserEvent::class => [
            CreateUserKeycloakListener::class,
        ],

        DeleteUserEvent::class => [
            DeleteKeycloakListener::class,
        ],
        UpdateUserEvent::class => [
            UpdateKeycloakListener::class,
        ]
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}

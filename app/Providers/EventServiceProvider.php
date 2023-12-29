<?php

namespace App\Providers;

use App\Events\DeleteEvent;
use App\Events\PostCardProcessed;
use App\Events\RegisterEvent;
use App\Events\UpdateEvent;
use App\Listeners\CreateUserKeycloakListener;
use App\Listeners\DeleteKeycloakListener;
use App\Listeners\DeleteListener;
use App\Listeners\PostCardNotification;
use App\Listeners\RegisterListener;
use App\Listeners\UpdateKeycloakListener;
use App\Listeners\UpdateListener;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

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

        RegisterEvent::class => [
            CreateUserKeycloakListener::class,
        ],

        DeleteEvent::class => [
            DeleteKeycloakListener::class,
        ],
        UpdateEvent::class => [
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

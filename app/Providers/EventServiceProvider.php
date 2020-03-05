<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        \App\Events\UserRegistered::class => [
            \App\Listeners\SendWelcomeEmail::class,
        ],
        \App\Events\StudentInfoUpdateRequested::class => [
            \App\Listeners\UpdateStudentInfo::class,
        ],
        // Added event for TCT Registration
        \App\Events\TCTStudentInfoUpdateRequested::class => [
            \App\Listeners\UpdateTCTStudentInfo::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }
}

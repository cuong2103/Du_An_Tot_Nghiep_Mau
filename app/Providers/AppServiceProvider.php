<?php

namespace App\Providers;

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
        // 1. Register GenericModelObserver for important models
        $modelsToObserve = [
            \App\Models\User::class,
            \App\Models\Appointment::class,
            \App\Models\DoctorProfile::class,
            \App\Models\SystemSetting::class,
            \App\Models\Post::class,
        ];

        foreach ($modelsToObserve as $model) {
            $model::observe(\App\Observers\GenericModelObserver::class);
        }

        // 2. Listen to Laravel Auth Events for logging
        \Illuminate\Support\Facades\Event::listen(function (\Illuminate\Auth\Events\Login $event) {
            app(\App\Services\SystemLogService::class)->log(
                action: 'USER_LOGIN',
                module: 'auth',
                refType: 'users',
                refId: $event->user->id,
                userId: $event->user->id
            );
        });

        \Illuminate\Support\Facades\Event::listen(function (\Illuminate\Auth\Events\Logout $event) {
            if ($event->user) {
                app(\App\Services\SystemLogService::class)->log(
                    action: 'USER_LOGOUT',
                    module: 'auth',
                    refType: 'users',
                    refId: $event->user->id,
                    userId: $event->user->id
                );
            }
        });

        \Illuminate\Support\Facades\Event::listen(function (\Illuminate\Auth\Events\Failed $event) {
            app(\App\Services\SystemLogService::class)->log(
                action: 'LOGIN_FAILED',
                module: 'auth',
                oldData: ['email' => $event->credentials['email'] ?? null]
            );
        });
    }
}

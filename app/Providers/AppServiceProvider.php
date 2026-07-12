<?php

namespace App\Providers;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Laravel\Fortify\Events\RecoveryCodesGenerated;
use Laravel\Fortify\Events\TwoFactorAuthenticationConfirmed;
use Laravel\Fortify\Events\TwoFactorAuthenticationDisabled;

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
        $this->configureDefaults();
        $this->configureGates();

        Event::listen(function (TwoFactorAuthenticationConfirmed $event) {
            Inertia::flash('toast', [
                'type' => 'success',
                'message' => __('Two-factor authentication enabled.'),
            ]);
        });

        Event::listen(function (TwoFactorAuthenticationDisabled $event) {
            Inertia::flash('toast', [
                'type' => 'success',
                'message' => __('Two-factor authentication disabled.'),
            ]);
        });

        Event::listen(function (RecoveryCodesGenerated $event) {
            Inertia::flash('toast', [
                'type' => 'success',
                'message' => __('Recovery codes regenerated.'),
            ]);
        });
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(
            fn(): ?Password => app()->isProduction()
                ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
                : null,
        );
    }

    protected function configureGates(): void
    {
        // anggota
        Gate::define('create-anggota', function ($user) {
            return in_array($user->role, ['academy']);
        });

        Gate::define('update-anggota', function ($user) {
            return in_array($user->role, ['academy']);
        });

        Gate::define('delete-anggota', function ($user) {
            return in_array($user->role, ['academy']);
        });

        // layanan
        Gate::define('create-layanan', function ($user) {
            return in_array($user->role, ['academy']);
        });

        Gate::define('update-layanan', function ($user) {
            return in_array($user->role, ['academy']);
        });

        Gate::define('delete-layanan', function ($user) {
            return in_array($user->role, ['academy']);
        });
    }
}

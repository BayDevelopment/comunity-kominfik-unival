<?php

namespace App\Providers;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

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

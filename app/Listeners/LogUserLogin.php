<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;

class LogUserLogin
{
    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        /** @var \App\Models\User $user */
        $user = $event->user;

        $agent = new \Jenssegers\Agent\Agent();
        $ip = request()->ip();
        $position = \Stevebauman\Location\Facades\Location::get($ip);

        activity('login')
            ->causedBy($user)
            ->withProperties([
                'ip'        => $ip,
                'browser'   => $agent->browser(),
                'platform'  => $agent->platform(),
                'device'    => $agent->isDesktop() ? 'Desktop' : ($agent->isTablet() ? 'Tablet' : 'Mobile'),
                'location'  => $position ? "{$position->cityName}, {$position->countryName}" : 'Unknown',
            ])
            ->log('User logged in');
    }
}

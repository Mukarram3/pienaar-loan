<?php

namespace App\Providers;

use App\Models\Role;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class BladeDirectivesServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Blade::if('can', function ($codes) {
            // Support single string or array input
            $codes = is_array($codes) ? $codes : [$codes];

            foreach ($codes as $code) {
                if (Role::hasPermission($code)) {
                    return true;
                }
            }
            return false;
        });
    }
}

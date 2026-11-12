<?php

namespace App\Providers;

use App\Enums\Auth\Permissions;
use App\Models\User\User;
use Illuminate\Foundation\DevCommands;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use SocialiteProviders\Manager\SocialiteWasCalled;
use SocialiteProviders\Steam\SteamExtendSocialite;

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
        // Production schema was created with 125 char strings (originally for spatie/laravel-permission
        // index limits). Keep fresh databases identical until the columns are widened by a migration.
        Schema::defaultStringLength(125);

        // Registers the "steam" Socialite driver (previously in EventServiceProvider)
        Event::listen(SocialiteWasCalled::class, [SteamExtendSocialite::class, 'handle']);

        // Log Viewer 3.x requires this gate in production (require_auth_in_production). It also protects
        // the log viewer API routes, which only have the web guard and no permission middleware.
        Gate::define('viewLogViewer', fn (User $user) => $user->can(Permissions::VIEW_LOG->value));

        // Octane registers "octane:start --watch" as the "artisan dev" server, which needs pcntl.
        // Fall back to the built-in server where it is missing (native Windows).
        if (! extension_loaded('pcntl')) {
            DevCommands::artisan('serve', 'server');
        }
    }
}

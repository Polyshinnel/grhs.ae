<?php

namespace App\Providers;

use App\Models\ContactSettings;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\DevCommands;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View as ViewFacade;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\View;

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
        RateLimiter::for('contact-inquiries', fn (Request $request): Limit => Limit::perMinute(5)->by($request->ip() ?? 'unknown'));

        ViewFacade::composer('components.site.footer', function (View $view): void {
            $view->with('contactSettings', ContactSettings::query()->find(1));
        });

        if ($this->app->runningInConsole()) {
            DevCommands::register(
                'php -d upload_max_filesize=512M -d post_max_size=520M artisan serve',
                'server',
            );
        }
    }
}

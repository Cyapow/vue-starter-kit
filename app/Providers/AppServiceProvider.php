<?php

declare(strict_types=1);

namespace App\Providers;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Sleep;
use Illuminate\Validation\Rules\Password;

final class AppServiceProvider extends ServiceProvider
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
        Date::use(CarbonImmutable::class);
        Model::automaticallyEagerLoadRelationships();
        Model::shouldBeStrict();
        Password::defaults(fn (): ?Password => app()->isProduction() ? Password::min(12)
            ->max(255)
            ->letters()
            ->mixedCase()
            ->numbers()
            ->symbols()
            ->uncompromised() : null);
        URL::forceHttps();
        Vite::useAggressivePrefetching();

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        if (app()->runningUnitTests()) {
            Http::preventStrayRequests();
            Sleep::fake();
        }
    }
}

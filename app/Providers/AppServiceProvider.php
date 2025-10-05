<?php

namespace App\Providers;

use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use App\ActivityLog\ActivityLogger;
use App\Listeners\LogAuthEvent;
use App\Services\ActivityLog\CustomCauserResolver;
use Filament\Facades\Filament;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Spatie\Activitylog\CauserResolver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
        $this->app->singleton(
            LoginResponse::class,
            \App\Http\Responses\LoginResponse::class
        );
        $this->app->bind(CauserResolver::class, CustomCauserResolver::class); // registra os logs para o tipo de usuario autenticado
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        // User::observe(UserObserver::class);

        Event::listen(Login::class, [LogAuthEvent::class, 'handle']);
        Event::listen(Logout::class, [LogAuthEvent::class, 'handle']);
        Event::listen(Registered::class, [LogAuthEvent::class, 'handle']);
        Event::listen(Failed::class, [LogAuthEvent::class, 'handle']);

        Fieldset::configureUsing(fn(Fieldset $fieldset) => $fieldset
            ->columnSpanFull());

        Grid::configureUsing(fn(Grid $grid) => $grid
            ->columnSpanFull());

        Section::configureUsing(fn(Section $section) => $section
            ->columnSpanFull());
    }
}

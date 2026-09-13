<?php

declare(strict_types = 1);

namespace Octopy\Tests;

use BladeUI\Heroicons\BladeHeroiconsServiceProvider;
use BladeUI\Icons\BladeIconsServiceProvider;
use Filament\Actions\ActionsServiceProvider;
use Filament\FilamentServiceProvider;
use Filament\Forms\FormsServiceProvider;
use Filament\Notifications\NotificationsServiceProvider;
use Filament\Schemas\SchemasServiceProvider;
use Filament\Support\SupportServiceProvider;
use Filament\Tables\TablesServiceProvider;
use Filament\Widgets\WidgetsServiceProvider;
use Livewire\LivewireServiceProvider;
use Livewire\Mechanisms\DataStore;
use Octopy\Filament\Tabify\Providers\TabifyServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function setUp() : void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app) : array
    {
        $providers = [
            BladeIconsServiceProvider::class,
            BladeHeroiconsServiceProvider::class,
            LivewireServiceProvider::class,
            SupportServiceProvider::class,
        ];

        if (class_exists(SchemasServiceProvider::class)) {
            $providers[] = SchemasServiceProvider::class;
        }

        if (class_exists(ActionsServiceProvider::class)) {
            $providers[] = ActionsServiceProvider::class;
        }

        if (class_exists(FormsServiceProvider::class)) {
            $providers[] = FormsServiceProvider::class;
        }

        if (class_exists(TablesServiceProvider::class)) {
            $providers[] = TablesServiceProvider::class;
        }

        if (class_exists(NotificationsServiceProvider::class)) {
            $providers[] = NotificationsServiceProvider::class;
        }

        if (class_exists(WidgetsServiceProvider::class)) {
            $providers[] = WidgetsServiceProvider::class;
        }

        if (class_exists(FilamentServiceProvider::class)) {
            $providers[] = FilamentServiceProvider::class;
        }

        $providers[] = TabifyServiceProvider::class;

        return $providers;
    }

    protected function defineEnvironment($app) : void
    {
        if (class_exists(DataStore::class)) {
            $app->singleton(DataStore::class);
        }

        $app['config']->set('app.key', 'base64:m+p/3j5e5r6q7t8u9v0w1x2y3z4a5b6c7d8e9f0g1h2=');
        $app['config']->set('view.paths', [
            __DIR__ . '/../resources/views',
            __DIR__ . '/Fixtures/views',
        ]);
        $app['view']->addNamespace('fixtures', __DIR__ . '/Fixtures/views');
    }
}

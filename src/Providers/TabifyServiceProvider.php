<?php

declare(strict_types = 1);

namespace Octopy\Filament\Tabify\Providers;

use Illuminate\Support\ServiceProvider;

class TabifyServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function register() : void
    {
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'tabify');
    }
}

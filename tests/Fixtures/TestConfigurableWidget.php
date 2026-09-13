<?php

declare(strict_types = 1);

namespace Octopy\Tests\Fixtures;

use Filament\Widgets\Widget;
use Filament\Widgets\WidgetConfiguration;

class TestConfigurableWidget extends Widget
{
    protected string $view = 'fixtures::configurable-widget';

    public string $period = 'daily';

    public static function make(array $properties = []) : WidgetConfiguration
    {
        return new WidgetConfiguration(static::class, $properties);
    }
}

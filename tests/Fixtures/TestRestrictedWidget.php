<?php

declare(strict_types = 1);

namespace Octopy\Tests\Fixtures;

use Filament\Widgets\Widget;

class TestRestrictedWidget extends Widget
{
    protected string $view = 'fixtures::restricted-widget';

    public static bool $canView = false;

    public static function canView() : bool
    {
        return static::$canView;
    }
}

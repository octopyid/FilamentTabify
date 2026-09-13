<?php

declare(strict_types = 1);

namespace Octopy\Tests\Fixtures;

use Filament\Widgets\Widget;

class TestSimpleWidget extends Widget
{
    protected string $view = 'fixtures::simple-widget';

    protected static bool $isLazy = false;
}

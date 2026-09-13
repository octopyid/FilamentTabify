<?php

declare(strict_types = 1);

namespace Octopy\Tests\Fixtures;

use Filament\Widgets\Widget;

class TestLazyWidget extends Widget
{
    protected string $view = 'fixtures::lazy-widget';

    protected static bool $isLazy = true;
}

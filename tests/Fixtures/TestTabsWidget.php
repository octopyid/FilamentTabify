<?php

declare(strict_types = 1);

namespace Octopy\Tests\Fixtures;

use Octopy\Filament\Tabify\Tab;
use Octopy\Filament\Tabify\TabsWidget;

class TestTabsWidget extends TabsWidget
{
    public bool $customKeepAlive = false;

    public function isKeepAlive() : bool
    {
        return $this->customKeepAlive || parent::isKeepAlive();
    }

    public function getTabs() : array
    {
        return [
            Tab::make('First')
                ->icon('heroicon-o-home')
                ->badge('5')
                ->badgeColor('primary')
                ->schema([
                    TestSimpleWidget::class,
                    TestLazyWidget::class,
                ]),

            Tab::make('Configured')
                ->schema([
                    TestConfigurableWidget::make(['period' => 'weekly']),
                ]),

            Tab::make('Restricted')
                ->schema([
                    TestRestrictedWidget::class,
                ]),

            Tab::make('Custom Grid')
                ->columns(3)
                ->schema([
                    TestSimpleWidget::class,
                ]),

            Tab::make('Hidden Tab')
                ->hidden(true)
                ->schema([
                    TestSimpleWidget::class,
                ]),

            Tab::make('Forced Lazy')
                ->lazy()
                ->schema([
                    TestSimpleWidget::class,
                ]),

            Tab::make('Forced Eager')
                ->eager()
                ->schema([
                    TestLazyWidget::class,
                ]),

            Tab::make('Forced Defer')
                ->defer()
                ->schema([
                    TestSimpleWidget::class,
                ]),
        ];
    }
}

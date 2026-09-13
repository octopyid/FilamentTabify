<?php

declare(strict_types = 1);

use Octopy\Filament\Tabify\Tab;
use Octopy\Tests\Fixtures\TestSimpleWidget;

it('creates tab with label and generates slug key', function () {
    $tab = Tab::make('Analytics Overview');

    expect($tab->getLabel())->toBe('Analytics Overview');
    expect($tab->getKey())->toBe('analytics-overview');
});

it('supports icon, badge, and badge color', function () {
    $tab = Tab::make('Orders')
        ->icon('heroicon-o-shopping-cart')
        ->badge('42')
        ->badgeColor('success');

    expect($tab->getIcon())->toBe('heroicon-o-shopping-cart');
    expect($tab->getBadge())->toBe('42');
    expect($tab->getBadgeColor())->toBe('success');
});

it('supports lazy, eager, and defer configuration', function () {
    $tab = Tab::make('Tab 1');
    expect($tab->isLazy())->toBeFalse();
    expect($tab->isDeferred())->toBeFalse();

    $tab->lazy();
    expect($tab->isLazy())->toBeTrue();

    $tab->eager();
    expect($tab->isLazy())->toBeFalse();

    $tab->defer();
    expect($tab->isDeferred())->toBeTrue();
});

it('supports per-tab grid columns', function () {
    $tab = Tab::make('Grid Tab')->columns(3);
    expect($tab->getColumns())->toBe(['lg' => 3]);

    $tabResponsive = Tab::make('Responsive Tab')->columns([
        'default' => 1,
        'md'      => 2,
        'xl'      => 4,
    ]);

    expect($tabResponsive->getColumns())->toBe([
        'default' => 1,
        'md'      => 2,
        'xl'      => 4,
    ]);
});

it('supports schema with widget classes', function () {
    $tab = Tab::make('Widgets')
        ->schema([
            TestSimpleWidget::class,
        ]);

    expect($tab->getDefaultChildComponents())->toBe([
        TestSimpleWidget::class,
    ]);
});

it('respects visibility', function () {
    $visibleTab = Tab::make('Visible')->visible(true);
    expect($visibleTab->isVisible())->toBeTrue();

    $hiddenTab = Tab::make('Hidden')->hidden(true);
    expect($hiddenTab->isVisible())->toBeFalse();
});

<?php

declare(strict_types = 1);

use Octopy\Tests\Fixtures\TestConfigurableWidget;
use Octopy\Tests\Fixtures\TestLazyWidget;
use Octopy\Tests\Fixtures\TestRestrictedWidget;
use Octopy\Tests\Fixtures\TestSimpleWidget;
use Octopy\Tests\Fixtures\TestTabsWidget;

it('initializes with the first visible tab as default', function () {
    $widget = new TestTabsWidget;
    $widget->mount();

    expect($widget->activeTab)->toBe('first');
    expect($widget->isActiveTab('first'))->toBeTrue();
    expect($widget->isActiveTab('configured'))->toBeFalse();
    expect($widget->visitedTabs)->toHaveKey('first');
});

it('switches active tab and tracks visited tabs', function () {
    $widget = new TestTabsWidget;
    $widget->mount();

    $widget->setActiveTab('configured');

    expect($widget->activeTab)->toBe('configured');
    expect($widget->isActiveTab('configured'))->toBeTrue();
    expect($widget->visitedTabs)->toHaveKeys(['first', 'configured']);
});

it('filters out hidden tabs from getVisibleTabs', function () {
    $widget = new TestTabsWidget;

    $visibleKeys = $widget->getVisibleTabs()->keys()->all();

    expect($visibleKeys)->toContain('first', 'configured', 'restricted', 'custom-grid', 'forced-lazy', 'forced-eager', 'forced-defer');
    expect($visibleKeys)->not->toContain('hidden-tab');
});

it('normalizes simple widget class with default properties', function () {
    $widget = new TestTabsWidget;

    $normalized = $widget->normalizeWidget(TestSimpleWidget::class);

    expect($normalized['class'])->toBe(TestSimpleWidget::class);
    expect($normalized['properties'])->toBeArray();
});

it('automatically includes lazy flag for widgets that are lazy', function () {
    $widget = new TestTabsWidget;

    $simpleNormalized = $widget->normalizeWidget(TestSimpleWidget::class);
    expect($simpleNormalized['properties'])->not->toHaveKey('lazy');

    $lazyNormalized = $widget->normalizeWidget(TestLazyWidget::class);
    expect($lazyNormalized['properties'])->toHaveKey('lazy', true);
});

it('normalizes WidgetConfiguration and passes custom properties', function () {
    $widget = new TestTabsWidget;
    $config = TestConfigurableWidget::make(['period' => 'monthly']);

    $normalized = $widget->normalizeWidget($config);

    expect($normalized['class'])->toBe(TestConfigurableWidget::class);
    expect($normalized['properties'])->toHaveKey('period', 'monthly');
});

it('respects tab-level lazy, eager, and defer overrides', function () {
    $widget = new TestTabsWidget;
    $cachedTabs = $widget->getCachedTabs();

    // Forced Lazy tab should force lazy = true on TestSimpleWidget
    $lazyTab = $cachedTabs->get('forced-lazy');
    $normalizedLazy = $widget->normalizeWidget(TestSimpleWidget::class, $lazyTab);
    expect($normalizedLazy['properties'])->toHaveKey('lazy', true);

    // Forced Eager tab should force lazy = false on TestLazyWidget
    $eagerTab = $cachedTabs->get('forced-eager');
    $normalizedEager = $widget->normalizeWidget(TestLazyWidget::class, $eagerTab);
    expect($normalizedEager['properties'])->toHaveKey('lazy', false);

    // Forced Defer tab should set defer = true on TestSimpleWidget
    $deferTab = $cachedTabs->get('forced-defer');
    $normalizedDefer = $widget->normalizeWidget(TestSimpleWidget::class, $deferTab);
    expect($normalizedDefer['properties'])->toHaveKey('defer', true);
});

it('filters out widgets that user cannot view', function () {
    $widget = new TestTabsWidget;
    $restrictedTab = $widget->getCachedTabs()->get('restricted');

    TestRestrictedWidget::$canView = false;
    $widgets = $widget->getTabWidgets($restrictedTab);
    expect($widgets)->toBeEmpty();

    TestRestrictedWidget::$canView = true;
    $widgets = $widget->getTabWidgets($restrictedTab);
    expect($widgets)->toHaveCount(1);
    expect($widgets[0]['class'])->toBe(TestRestrictedWidget::class);
});

it('forwards dashboard pageFilters to widgets', function () {
    $widget = new class extends TestTabsWidget
    {
        public array $filters = [
            'startDate' => '2026-01-01',
            'endDate'   => '2026-12-31',
        ];
    };

    $normalized = $widget->normalizeWidget(TestSimpleWidget::class);

    expect($normalized['properties'])->toHaveKey('pageFilters', [
        'startDate' => '2026-01-01',
        'endDate'   => '2026-12-31',
    ]);
});

it('overrides columns when tab defines custom columns', function () {
    $widget = new TestTabsWidget;
    $widget->mount();

    // Default tab 'first' has no custom columns, falls back to 2
    expect($widget->getColumns())->toBe(2);

    // Switch to tab with columns(3)
    $widget->setActiveTab('custom-grid');
    expect($widget->getColumns())->toBe(['lg' => 3]);
});

it('supports keep-alive mode', function () {
    $widget = new TestTabsWidget;
    expect($widget->isKeepAlive())->toBeFalse();

    $widget->keepAlive(true);
    expect($widget->isKeepAlive())->toBeTrue();
});

it('returns all unique widget classes across all tabs', function () {
    $widget = new TestTabsWidget;

    $all = $widget->getAllWidgets();

    expect($all)->toContain(
        TestSimpleWidget::class,
        TestLazyWidget::class,
        TestConfigurableWidget::class,
        TestRestrictedWidget::class,
    );
});

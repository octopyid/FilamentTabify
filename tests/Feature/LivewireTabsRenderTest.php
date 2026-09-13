<?php

declare(strict_types = 1);

use Livewire\Livewire;
use Octopy\Tests\Fixtures\TestTabsWidget;

it('renders the tabs widget with livewire', function () {
    Livewire::test(TestTabsWidget::class)
        ->assertSuccessful()
        ->assertSee('First')
        ->assertSee('Configured')
        ->assertSee('Custom Grid')
        ->assertDontSee('Hidden Tab');
});

it('switches tabs in livewire component and renders content or lazy placeholder', function () {
    $component = Livewire::test(TestTabsWidget::class)
        ->assertSet('activeTab', 'first');

    // Switch to custom-grid tab which contains TestSimpleWidget (eager by default)
    $component->call('setActiveTab', 'custom-grid')
        ->assertSet('activeTab', 'custom-grid')
        ->assertSee('Simple Widget Content');

    // Switch to configured tab which contains a lazy widget by default
    $component->call('setActiveTab', 'configured')
        ->assertSet('activeTab', 'configured')
        ->assertSee('fi-loading-section', false)
        ->assertSee('Loading...');

    // Switch to forced-eager tab which forces TestLazyWidget to render eagerly
    $component->call('setActiveTab', 'forced-eager')
        ->assertSet('activeTab', 'forced-eager')
        ->assertSee('Lazy Widget Content');

    // Switch to forced-lazy tab which forces TestSimpleWidget to render as lazy placeholder
    $component->call('setActiveTab', 'forced-lazy')
        ->assertSet('activeTab', 'forced-lazy')
        ->assertSee('fi-loading-section', false);
});

it('renders in keep alive mode properly', function () {
    $component = Livewire::test(TestTabsWidget::class);

    $component->set('customKeepAlive', true);
    $component->assertSuccessful();

    $component->call('setActiveTab', 'custom-grid');
    $component->assertSet('activeTab', 'custom-grid');
    $component->assertSee('Simple Widget Content');
});

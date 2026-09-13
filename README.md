<p align="center" class="filament-hidden">
    <a href="https://github.com/octopyid/filament-tabify/actions/workflows/run-tests.yml"><img src="https://img.shields.io/github/actions/workflow/status/octopyid/filament-tabify/run-tests.yml?branch=main&label=tests&style=for-the-badge" alt="Tests"></a>
    <img src="https://img.shields.io/packagist/v/octopyid/filament-tabify.svg?style=for-the-badge" alt="Version">
    <img src="https://img.shields.io/packagist/dt/octopyid/filament-tabify.svg?style=for-the-badge&color=F28D1A" alt="Downloads">
    <img src="https://img.shields.io/packagist/l/octopyid/filament-tabify.svg?style=for-the-badge" alt="License">
</p>

# Filament Tabify

Transform your Filament dashboard with elegant Tabbed Widgets. Group multiple widgets into a single, organized view to save space, improve clarity, and optimize performance.

Built for **Filament v4 & v5** with support for **Laravel 11, 12, and 13**.

## Installation

You can install the package via composer:

```bash
composer require octopyid/filament-tabify
```

## Usage

To create a tabbed widget, extend the `TabsWidget` class and define your tabs using the `getTabs()` method. Each tab can contain a schema of other widgets or widget configurations.

```php
use App\Filament\Widgets\CustomerChart;
use App\Filament\Widgets\OrdersTable;
use App\Filament\Widgets\StatsOverview;
use Octopy\Filament\Tabify\Tab;
use Octopy\Filament\Tabify\TabsWidget;

class DashboardTabsWidget extends TabsWidget
{
    protected int|string|array $columnSpan = 'full';

    public function getTabs() : array
    {
        return [
            Tab::make('Overview')
                ->icon('heroicon-o-home')
                ->badge('New')
                ->badgeColor('success')
                ->schema([
                    StatsOverview::class,
                    CustomerChart::class,
                ]),

            Tab::make('Orders')
                ->icon('heroicon-o-shopping-bag')
                ->schema([
                    OrdersTable::class,
                ]),
        ];
    }
}
```

### Registering the Widget

You can register the widget in your Filament Pages or Resources just like any other widget.

```php
use App\Filament\Widgets\DashboardTabsWidget;

class Dashboard extends \Filament\Pages\Dashboard
{
    public function getWidgets(): array
    {
        return [
            DashboardTabsWidget::class,
        ];
    }
}
```

Or in a Resource Page:

```php
use App\Filament\Widgets\DashboardTabsWidget;

class ListOrders extends \Filament\Resources\Pages\ListRecords
{
    protected function getHeaderWidgets(): array
    {
        return [
            DashboardTabsWidget::class,
        ];
    }
}
```

## Features

### Lazy Render Widgets

Filament Tabify fully supports asynchronous lazy loading for widgets! Widgets that have lazy loading enabled will display Filament's skeleton loading state while fetching data in the background, keeping dashboard page loads lightning fast.

#### 1. Automatic Native Widget Lazy Loading
Any widget with `protected static bool $isLazy = true;` or `isLazy()` returning `true` will automatically be lazy loaded inside tabs.

#### 2. Tab-Level Lazy Overrides
You can enforce or override lazy loading behavior per tab:

```php
Tab::make('Analytics')
    ->lazy() // Force all widgets in this tab to be lazy loaded
    ->schema([
        RevenueChart::class,
    ]);

Tab::make('Quick Stats')
    ->eager() // Force widgets in this tab to load synchronously without skeletons
    ->schema([
        QuickStats::class,
    ]);

Tab::make('Reports')
    ->defer() // Load immediately after initial page render without waiting for viewport
    ->schema([
        HeavyReportWidget::class,
    ]);
```

### Configurable Widgets (`WidgetConfiguration`)

You can pass arguments and custom parameters to widgets within tabs using Filament's widget configuration pattern:

```php
Tab::make('Transactions')
    ->schema([
        OrdersTable::make(['status' => 'pending']),
    ]);
```

### Keep-Alive Mode (Instant Tab Switching)

By default, switching tabs only mounts and renders the active tab to save server resources. If you want instant client-side tab switching without re-requesting or re-rendering previously opened tabs, enable **Keep-Alive mode**:

```php
class DashboardTabsWidget extends TabsWidget
{
    protected bool $isKeepAlive = true;
    
    // or override dynamically
    public function isKeepAlive(): bool
    {
        return true;
    }
}
```

Once a tab is visited, its rendered components remain active in the DOM with Alpine.js visibility toggling, making switching back and forth 100% instant!

### Per-Tab Grid Customization

You can define custom grid columns for specific tabs, overriding the widget-level columns:

```php
Tab::make('Charts')
    ->columns(3) // 3 columns on desktop
    ->schema([...]),

Tab::make('Tables')
    ->columns(1) // Full-width 1 column
    ->schema([...]),

Tab::make('Responsive')
    ->columns([
        'default' => 1,
        'sm' => 2,
        'lg' => 3,
        'xl' => 4,
    ])
    ->schema([...]),
```

### Dashboard Filters Integration (`pageFilters`)

If your dashboard uses `HasFiltersForm` or date-range filters, Tabify automatically forwards `$pageFilters` down to all child widgets inside the active tab.

### Authorization & Visibility

- **Widget Authorization**: Tabify automatically checks `Widget::canView()`. Widgets that the current user cannot access are automatically excluded.
- **Tab Visibility**: Tabify respects `$tab->visible(...)` and `$tab->hidden(...)`.

```php
Tab::make('Admin Only')
    ->visible(fn () => auth()->user()->isAdmin())
    ->schema([
        AdminStatsWidget::class,
    ]);
```

### Interacting with Widgets

If you need to interact with all widgets within the tabs, for example to dispatch an event to them, you can use the `getAllWidgets()` method:

```php
public function updatedYear()
{
    foreach ($this->getAllWidgets() as $widget) {
        $this->dispatch('updateYear', $this->year)->to($widget);
    }
}
```

## Testing

Run the test suite using Pest:

```bash
composer test
```

## Changelog

Please see [releases](https://github.com/octopyid/filament-tabify/releases) for more information on what has changed recently.

## Security Vulnerabilities

If you discover a security vulnerability within this package, please send an e-mail to [security@octopy.dev](mailto:security@octopy.dev). All security vulnerabilities will be promptly addressed. Please review [our security policy](SECURITY.md) for more details.

## Credits

- [Supian M](https://github.com/SupianIDz)
- [All Contributors](https://github.com/octopyid/filament-tabify/contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.

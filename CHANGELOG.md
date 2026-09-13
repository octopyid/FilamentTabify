# Changelog

All notable changes to `octopyid/filament-tabify` will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- **Asynchronous Lazy Render Widgets**:
  - Automatic lazy loading support honoring `Widget::$isLazy = true` or `Widget::isLazy()`.
  - Lazy widgets now display Filament's skeleton loading section (`fi-loading-section`) and fetch data in the background asynchronously.
  - Granular lazy loading controls on `Tab`:
    - `Tab::make()->lazy()` to force all widgets in a tab to load lazily.
    - `Tab::make()->eager()` to force widgets to render synchronously without skeletons.
    - `Tab::make()->defer()` for deferred loading immediately after initial page render.
- **Configurable Widgets (`WidgetConfiguration`)**:
  - Full support for widget configurations in tab schemas, e.g. `OrdersTable::make(['status' => 'pending'])`.
- **Keep-Alive Mode**:
  - Added `keepAlive()` method and `$isKeepAlive` property on `TabsWidget`.
  - Once a tab is visited, its rendered components remain active in the DOM with Alpine.js (`x-show`), making returning to previously visited tabs 100% instantaneous without server roundtrips.
- **Per-Tab Grid Customization**:
  - `Tab::make()->columns(...)` to override grid columns per tab.
- **Dashboard Filter Passing**:
  - Automatic forwarding of dashboard filters (`pageFilters` / `filters`) down to child widgets.
- **Authorization & Visibility**:
  - Automatically checks `Widget::canView()` before rendering widgets.
  - Automatically filters tabs based on `$tab->isVisible()`.
- **Testing Suite**:
  - Integrated automated testing with **Pest** and **Orchestra Testbench**.
  - Unit tests for `Tab` component.
  - Feature and integration tests for `TabsWidget` and Livewire rendering.
  - Added `composer test` script.
- **Compatibility**:
  - Explicit support for Laravel 11, 12, and 13.
  - Compatible with Filament v4 and v5.

## [1.1.0] - 2026-01-30

### Added
- Added `getAllWidgets()` method to `TabsWidget` to easily retrieve and interact with all widgets across tabs (e.g. dispatching Livewire events).
- Added documentation for widget interaction in README.

### Changed
- Refactored active tab URL alias to `tab-widget`.
- Reorganized internal widget methods.

## [1.0.1] - 2026-01-30

### Fixed
- Added unique key to Livewire widget rendering to ensure proper DOM element matching and state persistence.

## [1.0.0] - 2026-01-23

### Added
- Initial release of Filament Tabify.
- Support for grouping multiple Filament widgets into tabs via `TabsWidget`.
- `Tab` component with labels, custom icons, badges, and extra attributes.
- Customizable column spans and responsive grid configurations.

[Unreleased]: https://github.com/OctopyID/FilamentTabify/compare/v1.1.0...HEAD
[1.1.0]: https://github.com/OctopyID/FilamentTabify/compare/v1.0.1...v1.1.0
[1.0.1]: https://github.com/OctopyID/FilamentTabify/compare/v1.0.0...v1.0.1
[1.0.0]: https://github.com/OctopyID/FilamentTabify/releases/tag/v1.0.0

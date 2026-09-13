<?php

declare(strict_types = 1);

namespace Octopy\Filament\Tabify;

use Filament\Widgets\Widget;
use Filament\Widgets\WidgetConfiguration;
use Illuminate\Support\Collection;
use Illuminate\View\ComponentAttributeBag;
use Livewire\Attributes\Url;

abstract class TabsWidget extends Widget
{
    /**
     * @var string|null
     */
    #[Url(as: 'tab-widget', except: null)]
    public ?string $activeTab = null;

    /**
     * @var array<string, bool>
     */
    public array $visitedTabs = [];

    /**
     * @var bool
     */
    protected bool $isKeepAlive = false;

    /**
     * @var Collection<string, Tab>|null
     */
    protected ?Collection $cachedTabs = null;

    /**
     * @var string
     */
    protected string $view = 'tabify::tabs';

    /**
     * @return array<int, Tab>
     */
    public function getTabs() : array
    {
        return [];
    }

    /**
     * @return int|array<string, int>
     */
    public function getColumns() : int|array
    {
        $activeTab = $this->getActiveTab();

        if ($activeTab && method_exists($activeTab, 'hasCustomColumns') && $activeTab->hasCustomColumns()) {
            return $activeTab->getColumns() ?? 2;
        }

        return 2;
    }

    /**
     * Initialize active and visited tab state.
     */
    public function mount() : void
    {
        $defaultTab = $this->getDefaultActiveTab();

        if (blank($this->activeTab) || ! $this->getCachedTabs()->has($this->activeTab)) {
            $this->activeTab = $defaultTab !== null ? (string) $defaultTab : null;
        }

        if (filled($this->activeTab)) {
            $this->visitedTabs[$this->activeTab] = true;
        }
    }

    /**
     * @param  string $key
     * @return void
     */
    public function setActiveTab(string $key) : void
    {
        $this->activeTab = $key;
        $this->visitedTabs[$key] = true;
    }

    /**
     * @param  string $key
     * @return bool
     */
    public function isActiveTab(string $key) : bool
    {
        $active = $this->activeTab;

        if (blank($active)) {
            $active = (string) $this->getDefaultActiveTab();
        }

        return $active === $key;
    }

    /**
     * @return string|int|null
     */
    public function getDefaultActiveTab() : string|int|null
    {
        return $this->getVisibleTabs()->keys()->first();
    }

    /**
     * @return Tab|null
     */
    public function getActiveTab() : ?Tab
    {
        /** @var Collection<string, Tab> $tabs */
        $tabs = $this->getCachedTabs();

        if ($this->activeTab && $tabs->has($this->activeTab)) {
            return $tabs->get($this->activeTab);
        }

        $defaultKey = $this->getDefaultActiveTab();

        return $defaultKey !== null ? $tabs->get($defaultKey) : null;
    }

    /**
     * @return Collection<string, Tab>
     */
    public function getVisibleTabs() : Collection
    {
        return $this->getCachedTabs()->filter(function (Tab $tab) : bool {
            return $tab->isVisible();
        });
    }

    /**
     * Enable or disable keeping previously visited tabs mounted in DOM.
     *
     * @param  bool  $condition
     * @return $this
     */
    public function keepAlive(bool $condition = true) : static
    {
        $this->isKeepAlive = $condition;

        return $this;
    }

    /**
     * @return bool
     */
    public function isKeepAlive() : bool
    {
        return $this->isKeepAlive;
    }

    /**
     * Normalize a widget class string or WidgetConfiguration into an array with class name and properties.
     *
     * @param  string|WidgetConfiguration                             $widget
     * @param  Tab|null                                               $tab
     * @return array{class: string, properties: array<string, mixed>}
     */
    public function normalizeWidget(string|WidgetConfiguration $widget, ?Tab $tab = null) : array
    {
        if ($widget instanceof WidgetConfiguration) {
            $widgetClass = $widget->widget;
            $widgetProperties = [
                ...(method_exists($widgetClass, 'getDefaultProperties') ? $widgetClass::getDefaultProperties() : []),
                ...$widget->getProperties(),
            ];
        } else {
            $widgetClass = $widget;
            $widgetProperties = method_exists($widgetClass, 'getDefaultProperties')
                ? $widgetClass::getDefaultProperties()
                : [];
        }

        // Apply Tab-level lazy/defer overrides
        if ($tab !== null) {
            if ($tab->isDeferred()) {
                $widgetProperties['defer'] = true;
            } elseif ($tab->hasCustomLazy()) {
                $widgetProperties['lazy'] = $tab->isLazy();
            }
        }

        $properties = [
            ...$this->getWidgetData(),
            ...$widgetProperties,
        ];

        return [
            'class'      => $widgetClass,
            'properties' => $properties,
        ];
    }

    /**
     * Determine if a widget can be viewed by the current user.
     *
     * @param  string|WidgetConfiguration $widget
     * @return bool
     */
    public function canViewWidget(string|WidgetConfiguration $widget) : bool
    {
        $widgetClass = $widget instanceof WidgetConfiguration ? $widget->widget : $widget;

        if (method_exists($widgetClass, 'canView')) {
            return (bool) $widgetClass::canView();
        }

        return true;
    }

    /**
     * Data forwarded to each child widget (such as pageFilters from a Dashboard).
     *
     * @return array<string, mixed>
     */
    public function getWidgetData() : array
    {
        $data = [];

        if (property_exists($this, 'filters') && filled($this->filters)) {
            $data['pageFilters'] = $this->filters;
        }

        if (property_exists($this, 'pageFilters') && filled($this->pageFilters)) {
            $data['pageFilters'] = $this->pageFilters;
        }

        return $data;
    }

    /**
     * @return array<int, array{class: string, properties: array<string, mixed>}>
     */
    public function getActiveWidgets() : array
    {
        $activeTab = $this->getActiveTab();

        if (! $activeTab) {
            return [];
        }

        return $this->getTabWidgets($activeTab);
    }

    /**
     * @param  Tab                                                                $tab
     * @return array<int, array{class: string, properties: array<string, mixed>}>
     */
    public function getTabWidgets(Tab $tab) : array
    {
        $widgets = [];

        foreach ($tab->getDefaultChildComponents() as $widget) {
            if (! $this->canViewWidget($widget)) {
                continue;
            }

            $widgets[] = $this->normalizeWidget($widget, $tab);
        }

        return $widgets;
    }

    /**
     * @return array<int, string>
     */
    public function getAllWidgets() : array
    {
        $widgets = [];

        foreach ($this->getCachedTabs() as $tab) {
            foreach ($tab->getDefaultChildComponents() as $widget) {
                $widgets[] = $widget instanceof WidgetConfiguration ? $widget->widget : $widget;
            }
        }

        return array_values(array_unique($widgets));
    }

    /**
     * @return Collection<string, Tab>
     */
    public function getCachedTabs() : Collection
    {
        if ($this->cachedTabs !== null) {
            return $this->cachedTabs;
        }

        return $this->cachedTabs = collect($this->getTabs())
            ->mapWithKeys(function (Tab $tab, string|int $key) : array {
                return [
                    $tab->getKey() => $tab->hasCustomLabel() ? $tab : $tab->label($this->generateTabLabel($key)),
                ];
            });
    }

    /**
     * @param  string|int $key
     * @return string
     */
    protected function generateTabLabel(string|int $key) : string
    {
        return (string) str((string) $key)->replace(['_', '-'], ' ')->ucfirst();
    }

    /**
     * @return array<string, mixed>
     */
    protected function getViewData() : array
    {
        return [
            'attributes' => new ComponentAttributeBag,
        ];
    }
}

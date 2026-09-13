@php
    $attributes ??= new \Illuminate\View\ComponentAttributeBag();
@endphp

<x-filament-widgets::widget class="fi-wi-group">
    <x-filament::tabs>
        @foreach($this->getVisibleTabs() as $key => $tab)
            <x-filament::tabs.item
                :key="$key"
                :active="$this->isActiveTab($key)"

                :badge="$tab->getBadge()"
                :badge-color="$tab->getBadgeColor()"

                :icon="$tab->getIcon()"
                :icon-position="$tab->getIconPosition()"
                :attributes="$tab->getExtraAttributeBag()"
                wire:click="setActiveTab('{{ $key }}')"
            >
                {{ $tab->getLabel() }}
            </x-filament::tabs.item>
        @endforeach
    </x-filament::tabs>

    @if($this->isKeepAlive())
        @foreach($this->getVisibleTabs() as $key => $tab)
            @if(isset($this->visitedTabs[$key]))
                <div
                    wire:key="tab-panel-{{ $key }}"
                    x-show="$wire.activeTab === '{{ $key }}'"
                    x-cloak
                    {{
                        $attributes->grid($tab->getColumns() ?? $this->getColumns())->class(['fi-wi-widget mt-4 gap-6'])
                    }}
                >
                    @foreach($this->getTabWidgets($tab) as $index => $widgetData)
                        @livewire($widgetData['class'], $widgetData['properties'], key("{$widgetData['class']}-{$key}-{$index}"))
                    @endforeach
                </div>
            @endif
        @endforeach
    @else
        <div
            wire:key="tab-panel-{{ $this->activeTab }}"
            {{
                $attributes->grid($this->getColumns())->class(['fi-wi-widget mt-4 gap-6'])
            }}
        >
            @foreach($this->getActiveWidgets() as $index => $widgetData)
                @livewire($widgetData['class'], $widgetData['properties'], key("{$widgetData['class']}-{$this->activeTab}-{$index}"))
            @endforeach
        </div>
    @endif
</x-filament-widgets::widget>
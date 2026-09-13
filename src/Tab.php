<?php

declare(strict_types = 1);

namespace Octopy\Filament\Tabify;

use Closure;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Concerns\HasLabel;
use Filament\Support\Concerns\HasBadge;
use Filament\Support\Concerns\HasExtraAttributes;
use Filament\Support\Concerns\HasIcon;
use Filament\Support\Concerns\HasIconPosition;
use Illuminate\Contracts\Support\Htmlable;

class Tab extends Component
{
    use HasBadge, HasExtraAttributes, HasIcon, HasIconPosition, HasLabel;

    /**
     * @var bool|Closure|null
     */
    protected bool|Closure|null $isLazy = null;

    /**
     * @var bool|Closure
     */
    protected bool|Closure $isDeferred = false;

    /**
     * Tab constructor.
     */
    public function __construct(Closure|Htmlable|string|null $label)
    {
        $this->label($label);
    }

    /**
     * @param  Closure|Htmlable|string|null $label
     * @return static
     */
    public static function make(Closure|Htmlable|string|null $label) : static
    {
        $static = app(static::class, [
            'label' => $label,
        ]);

        $static->configure();

        return $static;
    }

    /**
     * @return void
     */
    protected function setUp() : void
    {
        parent::setUp();

        $this
            ->key(function () {
                return (string) str($this->getLabel())->slug();
            });
    }

    /**
     * Configure lazy loading for widgets in this tab.
     *
     * @param  bool|Closure $condition
     * @return $this
     */
    public function lazy(bool|Closure $condition = true) : static
    {
        $this->isLazy = $condition;

        return $this;
    }

    /**
     * Configure deferred loading for widgets in this tab.
     *
     * @param  bool|Closure $condition
     * @return $this
     */
    public function defer(bool|Closure $condition = true) : static
    {
        $this->isDeferred = $condition;

        return $this;
    }

    /**
     * Force widgets in this tab to load eagerly (disable lazy loading).
     *
     * @param  bool|Closure $condition
     * @return $this
     */
    public function eager(bool|Closure $condition = true) : static
    {
        $this->isLazy = fn () : bool => ! ((bool) $this->evaluate($condition));

        return $this;
    }

    /**
     * @return bool
     */
    public function isLazy() : bool
    {
        return (bool) $this->evaluate($this->isLazy);
    }

    /**
     * Check if a custom lazy setting was defined on this tab.
     *
     * @return bool
     */
    public function hasCustomLazy() : bool
    {
        return $this->isLazy !== null;
    }

    /**
     * @return bool
     */
    public function isDeferred() : bool
    {
        return (bool) $this->evaluate($this->isDeferred);
    }

    /**
     * @param  bool   $isAbsolute
     * @return string
     */
    public function getKey(bool $isAbsolute = true) : string
    {
        return $this->evaluate($this->key);
    }
}

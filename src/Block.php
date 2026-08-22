<?php

namespace DejoDev\PageBlocks;

use Closure;
use Filament\Forms\Components\Builder\Block as FilamentBlock;
use Illuminate\Contracts\Support\Htmlable;

class Block extends FilamentBlock
{
    protected string|Htmlable|Closure|null $tabGroup = null;

    public function tabGroup(string|Htmlable|Closure|null $tabGroup): static
    {
        $this->tabGroup = $tabGroup;

        return $this;
    }

    public function getTabGroup(): string|Htmlable|null
    {
        return $this->evaluate($this->tabGroup);
    }
}

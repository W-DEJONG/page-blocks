<?php

namespace DejoDev\PageBlocks;

use Filament\Forms\Components\Builder;

class PageBuilder extends Builder
{
    protected ?string $blockGroup = null {
        get {
            return $this->blockGroup;
        }
    }

    protected function setUp(): void
    {
        parent::setUp();

        $manager = resolve(PageBlocksManager::class);
        $manager->registerAllDirectories();
        $this->blocks(fn (PageBuilder $component): array => $component->getFilteredBlocks($manager));
    }

    public function getFilteredBlocks(PageBlocksManager $manager): array
    {
        return $manager->getBlockClasses($this->blockGroup)
            ->values()
            ->map(fn ($block) => $block::blockSchema())
            ->toArray();
    }

    public function blockGroup(?string $group): self
    {
        $this->blockGroup = $group;

        return $this;
    }

}

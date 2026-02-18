<?php

namespace DejoDev\PageBlocks;

use Filament\Forms\Components\Builder;
use Illuminate\Support\Arr;

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

        $this->afterStateHydrated(static function (Builder $component, ?array $rawState): void {
            $items = [];

            foreach ($rawState ?? [] as $itemData) {
                if ($uuid = $itemData['id'] ?? $component->generateUuid()) {
                    $items[$uuid] = $itemData;
                } else {
                    $items[] = $itemData;
                }
            }

            $component->rawState($items);
        });

        $this->mutateDehydratedStateUsing(static function (?array $state): array {
            $state = Arr::map($state ?? [], fn($block, $uuid) => [
                'id' => $uuid,
                'data' => $block['data'],
                'type' => $block['type'],
            ]);

            return array_values($state ?? []);
        });

        $manager = resolve(PageBlocksManager::class);
        $manager->registerAllDirectories();
        $this->blocks(fn(PageBuilder $component): array => $component->getFilteredBlocks($manager));
    }

    public function getFilteredBlocks(PageBlocksManager $manager): array
    {
        return $manager->getBlockClasses($this->blockGroup)
            ->values()
            ->map(fn($block) => $block::blockSchema())
            ->toArray();
    }

    public function blockGroup(?string $group): self
    {
        $this->blockGroup = $group;

        return $this;
    }
}

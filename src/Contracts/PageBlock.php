<?php

namespace DejoDev\PageBlocks\Contracts;

use Filament\Forms\Components\Builder\Block;

interface PageBlock
{
    public static function blockSchema(): Block;

    public static function getName(): string;

    public static function inGroup(string $group): bool;

    public function getComponent(): string;

    public function prepareData(array $data, array $context): array;

    public static function isLivewire(): bool;
}

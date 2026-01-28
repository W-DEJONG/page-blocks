<?php

namespace DejoDev\PageBlocks;

use DejoDev\PageBlocks\Contracts\PageBlock as PageBlockContract;
use Filament\Forms\Components\Builder\Block;
use Illuminate\Support\Str;

class PageBlock implements PageBlockContract
{
    public static function blockSchema(): Block
    {
        return Block::make(Str::kebab(class_basename(static::class)))
            ->schema([]);
    }

    public static function getName(): string
    {
        return static::blockSchema()->getName();
    }

    public static function inGroup(string $group): bool
    {
        return in_array($group, static::getGroups());
    }

    public static function getGroups(): array
    {
        if (property_exists(static::class, 'groups') && is_array(static::$groups)) {
            return static::$groups;
        }

        return [];
    }

    public function getComponent(): string
    {
        if (property_exists(static::class, 'component') && is_string(static::$component)) {
            return static::$component;
        }

        return 'page-blocks.'.static::getName();
    }

    public static function isLivewire(): bool
    {
        return false;
    }

    public function prepareData(array $data, array $context): array
    {
        return $data;
    }
}

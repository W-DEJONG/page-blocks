<?php

namespace DejoDev\PageBlocks\Facades;

use DejoDev\PageBlocks\PageBlocksManager;
use Illuminate\Support\Facades\Facade;

class PageBlocks extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return PageBlocksManager::class;
    }
}

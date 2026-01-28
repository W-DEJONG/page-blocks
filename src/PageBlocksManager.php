<?php

namespace DejoDev\PageBlocks;

use DejoDev\PageBlocks\Contracts\PageBlock as PageBlockContract;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use ReflectionClass;

class PageBlocksManager
{
    private bool $isInitialized = false;

    private Collection $blockClasses;

    public function __construct(protected readonly Filesystem $filesystem)
    {
        $this->blockClasses = collect();
        $this->initialize();
    }

    public function registerClass(string $class): void
    {
        throw_unless(
            is_subclass_of($class, PageBlockContract::class),
            InvalidArgumentException::class,
            $class.' must extend '.PageBlock::class
        );

        $this->blockClasses->put(resolve($class)->getName(), $class);
    }

    public function registerBlock(PageBlockContract $block): void
    {
        $this->blockClasses->put($block->getName(), $block::class);
    }

    public function getBlock(string $name): PageBlockContract
    {
        $block = $this->blockClasses->get($name);
        throw_unless($block, InvalidArgumentException::class, 'Page block '.$name.' not found');

        return resolve($block);
    }

    public function getBlocks(?string $group = null): Collection
    {
        return $this->getBlockClasses($group)->map(fn (string $class) => resolve($class));
    }

    public function getBlockClasses(?string $group = null): Collection
    {
        return $this->blockClasses
            ->filter(fn ($block) => empty($group) || $block::inGroup($group));
    }

    public function registerDirectory(string $directory, string $namespace): void
    {
        if (blank($directory) || blank($namespace)) {
            return;
        }

        collect($this->filesystem->allFiles($directory))
            ->map(fn ($file) => $namespace.'\\'.basename($file, '.php'))
            ->filter(
                fn (string $class): bool => is_subclass_of($class, PageBlockContract::class) && (! (new ReflectionClass(
                    $class
                ))->isAbstract())
            )
            ->each(function ($class) {
                $this->registerBlock(resolve($class));
            });
    }

    public function registerAllDirectories(): void
    {
        $dirs = config('page-blocks.directories_to_scan') ?? [];
        foreach ($dirs as $dir) {
            $this->registerDirectory(app()->basePath($dir['directory']), $dir['namespace']);
        }
    }

    public function initialize(): void
    {
        if ($this->isInitialized) {
            return;
        }
        $this->registerAllDirectories();
        $this->isInitialized = true;
    }

    public function isInitialized(): bool
    {
        return $this->isInitialized;
    }
}

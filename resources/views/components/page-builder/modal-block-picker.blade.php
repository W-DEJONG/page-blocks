@php
    use Filament\Support\Enums\Alignment;
    use Illuminate\Contracts\Support\Htmlable;
    use Illuminate\View\ComponentAttributeBag;
@endphp

@props([
    'action',
    'actionAlignment' => null,
    'afterItem' => null,
    'blocks',
    'columns' => null,
    'iconHeight' => '2rem',
    'iconWidth' => '2rem',
    'key',
    'trigger',
    'width' => null,
])

@once
    <style>
        .fi-fo-builder-block-picker-modal-items {
            gap: 0.5rem;
        }

        .fi-fo-builder-block-picker-modal-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 100%;
            min-width: 0;
            gap: 1rem;
            padding: 0.5rem;
            border-radius: 0.375rem;
            border: 1px solid color-mix(in oklab, var(--gray-950) 5%, transparent);
            background: transparent;
            cursor: pointer;
            font-size: 0.875rem;
            outline: none;
            transition: background-color 75ms;
        }

        .fi-fo-builder-block-picker-modal-item:hover,
        .fi-fo-builder-block-picker-modal-item:focus-visible {
            background-color: color-mix(in oklab, var(--gray-950) 5%, transparent);
        }

        .dark .fi-fo-builder-block-picker-modal-item {
            border-color: color-mix(in oklab, var(--white) 10%, transparent);
        }

        .dark .fi-fo-builder-block-picker-modal-item:hover,
        .dark .fi-fo-builder-block-picker-modal-item:focus-visible {
            background-color: color-mix(in oklab, var(--white) 5%, transparent);
        }

        .fi-fo-builder-block-picker-modal-item > span {
            width: 100%;
            text-align: center;
            overflow-wrap: anywhere;
        }

        .fi-fo-builder-block-picker-modal-item-icon {
            width: var(--pb-icon-width, 2rem);
            height: var(--pb-icon-height, 2rem);
            color: var(--gray-400);
        }

        .dark .fi-fo-builder-block-picker-modal-item-icon {
            color: var(--gray-500);
        }

        .fi-fo-builder-block-picker-modal-tabs {
            margin-top: 1rem;
            margin-bottom: 1rem;
        }

        .fi-fo-builder-block-picker-modal-tabs:first-child {
            margin-top: 0;
        }
    </style>
@endonce

@php
    $modalId = 'page-blocks-block-picker-' . $key . '-' . $action->getName() . (filled($afterItem) ? '-' . $afterItem : '');

    $ungroupedBlocks = [];
    $tabbedBlocks = [];

    foreach ($blocks as $block) {
        $tabGroup = method_exists($block, 'getTabGroup') ? $block->getTabGroup() : null;

        if (blank($tabGroup)) {
            $ungroupedBlocks[] = $block;

            continue;
        }

        $tabKey = $tabGroup instanceof Htmlable ? $tabGroup->toHtml() : (string) $tabGroup;
        $tabbedBlocks[$tabKey]['label'] ??= $tabGroup;
        $tabbedBlocks[$tabKey]['blocks'][] = $block;
    }

    $hasTabs = $tabbedBlocks !== [];
    $activeTab = array_key_first($tabbedBlocks);
@endphp

<x-filament::modal
    :heading="$action->getLabel()"
    :id="$modalId"
    teleport="body"
    :width="$width"
    :attributes="
        \Filament\Support\prepare_inherited_attributes(
            $attributes->class([
                'fi-fo-builder-block-picker',
                ($actionAlignment instanceof Alignment) ? ('fi-align-' . $actionAlignment->value) : $actionAlignment => $actionAlignment,
            ]),
        )
    "
>
    <x-slot name="trigger">
        {{ $trigger }}
    </x-slot>

    <div
        @if ($hasTabs)
            x-data="{ tab: @js($activeTab) }"
        @endif
    >
        @if (count($ungroupedBlocks))
            <x-page-blocks::page-builder.modal-block-picker-items
                :action="$action"
                :after-item="$afterItem"
                :blocks="$ungroupedBlocks"
                :columns="$columns"
                :icon-height="$iconHeight"
                :icon-width="$iconWidth"
                :key="$key"
            />
        @endif

        @if ($hasTabs)
            <x-filament::tabs class="fi-fo-builder-block-picker-modal-tabs">
                @foreach ($tabbedBlocks as $tabKey => $tab)
                    <x-filament::tabs.item
                        :alpine-active="'tab === ' . \Illuminate\Support\Js::from($tabKey)"
                        x-on:click="tab = {{ \Illuminate\Support\Js::from($tabKey) }}"
                    >
                        {{ $tab['label'] }}
                    </x-filament::tabs.item>
                @endforeach
            </x-filament::tabs>

            @foreach ($tabbedBlocks as $tabKey => $tab)
                <div x-show="tab === {{ \Illuminate\Support\Js::from($tabKey) }}" x-cloak>
                    <x-page-blocks::page-builder.modal-block-picker-items
                        :action="$action"
                        :after-item="$afterItem"
                        :blocks="$tab['blocks']"
                        :columns="$columns"
                        :icon-height="$iconHeight"
                        :icon-width="$iconWidth"
                        :key="$key"
                    />
                </div>
            @endforeach
        @endif
    </div>
</x-filament::modal>

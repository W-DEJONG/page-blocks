@php
    use Filament\Support\Enums\Alignment;
    use Filament\Support\Enums\GridDirection;
    use Filament\Support\Enums\IconSize;
    use Illuminate\View\ComponentAttributeBag;
@endphp

@props([
    'action',
    'actionAlignment' => null,
    'afterItem' => null,
    'blocks',
    'columns' => null,
    'key',
    'trigger',
    'width' => null,
])

@once
    <style>
        .fi-fo-builder-block-picker-modal-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 100%;
            gap: 1rem;
            padding: 0.5rem;
            border-radius: 0.375rem;
            border: 1px solid color-mix(in oklab, var(--gray-950) 10%, transparent);
            background: transparent;
            cursor: pointer;
            white-space: nowrap;
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

        .fi-fo-builder-block-picker-modal-item-icon {
            width: 2rem;
            height: 2rem;
            color: var(--gray-400);
        }

        .dark .fi-fo-builder-block-picker-modal-item-icon {
            color: var(--gray-500);
        }
    </style>
@endonce

@php
    $modalId = 'page-blocks-block-picker-' . $key . '-' . $action->getName() . (filled($afterItem) ? '-' . $afterItem : '');
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
        {{ (new ComponentAttributeBag)->grid($columns, GridDirection::Row) }}
    >
        @foreach ($blocks as $block)
            @php
                $blockIcon = $block->getIcon();

                $wireClickActionArguments = ['block' => $block->getName()];

                if (filled($afterItem)) {
                    $wireClickActionArguments['afterItem'] = $afterItem;
                }

                $wireClickActionArguments = \Illuminate\Support\Js::from($wireClickActionArguments);

                $wireClickAction = "mountAction('{$action->getName()}', {$wireClickActionArguments}, { schemaComponent: '{$key}' })";
            @endphp

            <button
                type="button"
                class="fi-fo-builder-block-picker-modal-item"
                x-on:click="close"
                wire:click="{{ $wireClickAction }}"
            >
                @if (filled($blockIcon))
                    {{ \Filament\Support\generate_icon_html($blockIcon, size: IconSize::TwoExtraLarge, attributes: (new ComponentAttributeBag)->class(['fi-fo-builder-block-picker-modal-item-icon'])) }}
                @endif

                <span>
                    {{ $block->getLabel() }}
                </span>
            </button>
        @endforeach
    </div>
</x-filament::modal>

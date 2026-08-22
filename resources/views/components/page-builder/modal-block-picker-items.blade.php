@php
    use Filament\Support\Enums\GridDirection;
    use Filament\Support\Enums\IconSize;
    use Illuminate\View\ComponentAttributeBag;
@endphp

@props([
    'action',
    'afterItem' => null,
    'blocks',
    'columns' => null,
    'iconHeight' => '2rem',
    'iconWidth' => '2rem',
    'key',
])

<div
    {{ (new ComponentAttributeBag)->grid($columns, GridDirection::Row)->class(['fi-fo-builder-block-picker-modal-items']) }}
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
                {{ \Filament\Support\generate_icon_html($blockIcon, size: IconSize::TwoExtraLarge, attributes: (new ComponentAttributeBag)->class(['fi-fo-builder-block-picker-modal-item-icon'])->style([
                    '--pb-icon-width: ' . $iconWidth,
                    '--pb-icon-height: ' . $iconHeight,
                ])) }}
            @endif

            <span>
                {{ $block->getLabel() }}
            </span>
        </button>
    @endforeach
</div>

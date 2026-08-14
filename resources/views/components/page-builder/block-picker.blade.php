@props([
    'action',
    'actionAlignment' => null,
    'afterItem' => null,
    'blocks',
    'columns' => null,
    'key',
    'style',
    'trigger',
    'width' => null,
])

@if ($style === \DejoDev\PageBlocks\Enums\BlockPickerStyle::Modal)
    <x-page-blocks::page-builder.modal-block-picker
        :action="$action"
        :action-alignment="$actionAlignment"
        :after-item="$afterItem"
        :blocks="$blocks"
        :columns="$columns"
        :key="$key"
        :width="$width"
        :attributes="$attributes"
    >
        <x-slot name="trigger">
            {{ $trigger }}
        </x-slot>
    </x-page-blocks::page-builder.modal-block-picker>
@else
    <x-filament-forms::builder.block-picker
        :action="$action"
        :action-alignment="$actionAlignment"
        :after-item="$afterItem"
        :blocks="$blocks"
        :columns="$columns"
        :key="$key"
        :width="$width"
        :attributes="$attributes"
    >
        <x-slot name="trigger">
            {{ $trigger }}
        </x-slot>
    </x-filament-forms::builder.block-picker>
@endif

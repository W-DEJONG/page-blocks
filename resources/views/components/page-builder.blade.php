@php
    use Filament\Actions\Action;
    use Filament\Support\Enums\Alignment;
    use Illuminate\View\ComponentAttributeBag;

    $fieldWrapperView = $getFieldWrapperView();
    $loadedItems = $getItems();
    $blockPickerBlocks = $getBlockPickerBlocks();
    $blockPickerColumns = $getBlockPickerColumns();
    $blockPickerStyle = $getBlockPickerStyle();
    $blockPickerWidth = $getBlockPickerWidth();
    $hasBlockPreviews = $hasBlockPreviews();
    $hasInteractiveBlockPreviews = $hasInteractiveBlockPreviews();

    $addAction = $getAction($getAddActionName());
    $addActionAlignment = $getAddActionAlignment();
    $addBetweenAction = $getAction($getAddBetweenActionName());
    $collapseAllAction = $getAction($getCollapseAllActionName());
    $expandAllAction = $getAction($getExpandAllActionName());
    $reorderAction = $getAction($getReorderActionName());
    $extraItemActions = $getExtraItemActions();

    $isAddable = $isAddable();
    $isCloneable = $isCloneable();
    $isCollapsible = $isCollapsible();
    $isDeletable = $isDeletable();
    $isReorderableWithButtons = $isReorderableWithButtons();
    $isReorderableWithDragAndDrop = $isReorderableWithDragAndDrop();

    $collapseAllActionIsVisible = $isCollapsible && $collapseAllAction->isVisible();
    $expandAllActionIsVisible = $isCollapsible && $expandAllAction->isVisible();

    $key = $getKey();
    $statePath = $getStatePath();

    $blockLabelHeadingTag = $getHeadingTag();
    $isBlockLabelTruncated = $isBlockLabelTruncated();
    $labelBetweenItems = $getLabelBetweenItems();

    $allRawItems = collect($getRawState())
        ->filter(fn (array $data): bool => filled($data['type'] ?? null) && $hasBlock($data['type']));
@endphp

<x-dynamic-component :component="$fieldWrapperView" :field="$field">
    <div
        {{
            $attributes
                ->merge($getExtraAttributes(), escape: false)
                ->class([
                    'fi-fo-builder',
                    'fi-collapsible' => $isCollapsible,
                ])
        }}
    >
        @if ($collapseAllActionIsVisible || $expandAllActionIsVisible)
            <div
                @class([
                    'fi-fo-builder-actions',
                    'fi-hidden' => $allRawItems->count() < 2,
                ])
            >
                @if ($collapseAllActionIsVisible)
                    <span
                        x-on:click="$dispatch('builder-collapse', '{{ $statePath }}')"
                    >
                        {{ $collapseAllAction }}
                    </span>
                @endif

                @if ($expandAllActionIsVisible)
                    <span
                        x-on:click="
                            $wire.mountAction('loadAllItems', {}, { schemaComponent: '{{ $key }}' });
                            $dispatch('builder-expand', '{{ $statePath }}');
                        "
                    >
                        {{ $expandAllAction }}
                    </span>
                @endif
            </div>
        @endif

        @if ($allRawItems->count())
            <ul
                x-sortable
                data-sortable-animation-duration="{{ $getReorderAnimationDuration() }}"
                x-on:end.stop="
                    $wire.mountAction(
                        'reorder',
                        { items: $event.target.sortable.toArray() },
                        { schemaComponent: '{{ $key }}' },
                    )
                "
                class="fi-fo-builder-items"
            >
                @php
                    $hasBlockLabels = $hasBlockLabels();
                    $hasBlockIcons = $hasBlockIcons();
                    $hasBlockNumbers = $hasBlockNumbers();
                    $hasBlockHeaders = $hasBlockHeaders();
                @endphp

                @foreach ($allRawItems as $itemKey => $rawItem)
                    @php
                        $isItemLoaded = isset($loadedItems[$itemKey]);
                        $item = $loadedItems[$itemKey] ?? null;
                    @endphp

                    @if ($isItemLoaded)
                        {{-- Loaded item: full header with all actions + rendered form --}}
                        @php
                            $cloneAction = $getAction($getCloneActionName())(['item' => $itemKey]);
                            $cloneActionIsVisible = $isCloneable && $cloneAction->isVisible();
                            $deleteAction = $getAction($getDeleteActionName())(['item' => $itemKey]);
                            $deleteActionIsVisible = $isDeletable && $deleteAction->isVisible();
                            $editAction = $getAction($getEditActionName())(['item' => $itemKey]);
                            $editActionIsVisible = $hasBlockPreviews && $editAction->isVisible();
                            $moveDownAction = $getAction($getMoveDownActionName())(['item' => $itemKey])->disabled($loop->last);
                            $moveDownActionIsVisible = $isReorderableWithButtons && $moveDownAction->isVisible();
                            $moveUpAction = $getAction($getMoveUpActionName())(['item' => $itemKey])->disabled($loop->first);
                            $moveUpActionIsVisible = $isReorderableWithButtons && $moveUpAction->isVisible();
                            $reorderActionIsVisible = $isReorderableWithDragAndDrop && $reorderAction->isVisible();
                            $visibleExtraItemActions = array_filter(
                                $extraItemActions,
                                fn (Action $action): bool => $action(['item' => $itemKey])->isVisible(),
                            );
                            $hasItemHeader = $hasBlockHeaders && ($reorderActionIsVisible || $moveUpActionIsVisible || $moveDownActionIsVisible || $hasBlockIcons || $hasBlockLabels || $editActionIsVisible || $cloneActionIsVisible || $deleteActionIsVisible || $isCollapsible || $visibleExtraItemActions);
                        @endphp

                        <li
                            wire:ignore.self
                            wire:key="{{ $item->getLivewireKey() }}.item"
                            x-data="{
                                isCollapsed: @js($isCollapsed($item)),
                                isLoaded: true,
                            }"
                            x-on:builder-expand.window="$event.detail === '{{ $statePath }}' && (isCollapsed = false)"
                            x-on:builder-collapse.window="$event.detail === '{{ $statePath }}' && (isCollapsed = true)"
                            x-on:expand="isCollapsed = false"
                            x-sortable-item="{{ $itemKey }}"
                            {{
                                $item->getParentComponent()->getExtraAttributeBag()
                                    ->class([
                                        'fi-fo-builder-item',
                                        'fi-fo-builder-item-has-header' => $hasItemHeader,
                                    ])
                            }}
                            x-bind:class="{ 'fi-collapsed': isCollapsed }"
                        >
                            @if ($hasItemHeader)
                                <div
                                    @if ($isCollapsible)
                                        x-on:click.stop="isCollapsed = !isCollapsed"
                                    @endif
                                    class="fi-fo-builder-item-header"
                                >
                                    @if ($reorderActionIsVisible || $moveUpActionIsVisible || $moveDownActionIsVisible)
                                        <ul class="fi-fo-builder-item-header-start-actions">
                                            @if ($reorderActionIsVisible)
                                                <li x-on:click.stop>
                                                    {{ $reorderAction->extraAttributes(['x-sortable-handle' => true], merge: true) }}
                                                </li>
                                            @endif

                                            @if ($moveUpActionIsVisible || $moveDownActionIsVisible)
                                                <li x-on:click.stop>{{ $moveUpAction }}</li>
                                                <li x-on:click.stop>{{ $moveDownAction }}</li>
                                            @endif
                                        </ul>
                                    @endif

                                    @php
                                        $blockIcon = $item->getParentComponent()->getIcon($item->getRawState(), $itemKey);
                                    @endphp

                                    @if ($hasBlockIcons && filled($blockIcon))
                                        {{ \Filament\Support\generate_icon_html($blockIcon, attributes: (new ComponentAttributeBag)->class(['fi-fo-builder-item-header-icon'])) }}
                                    @endif

                                    @if ($hasBlockLabels)
                                        <{{ $blockLabelHeadingTag }}
                                            @class([
                                                'fi-fo-builder-item-header-label',
                                                'fi-truncated' => $isBlockLabelTruncated,
                                            ])
                                        >
                                            {{ $item->getParentComponent()->getLabel($item->getRawState(), $itemKey, $loop->index) }}

                                            @if ($hasBlockNumbers)
                                                {{ $loop->iteration }}
                                            @endif
                                        </{{ $blockLabelHeadingTag }}>
                                    @endif

                                    @if ($editActionIsVisible || $cloneActionIsVisible || $deleteActionIsVisible || $isCollapsible || $visibleExtraItemActions)
                                        <ul class="fi-fo-builder-item-header-end-actions">
                                            @foreach ($visibleExtraItemActions as $extraItemAction)
                                                <li x-on:click.stop>
                                                    {{ $extraItemAction(['item' => $itemKey]) }}
                                                </li>
                                            @endforeach

                                            @if ($editActionIsVisible)
                                                <li x-on:click.stop>{{ $editAction }}</li>
                                            @endif

                                            @if ($cloneActionIsVisible)
                                                <li x-on:click.stop>{{ $cloneAction }}</li>
                                            @endif

                                            @if ($deleteActionIsVisible)
                                                <li x-on:click.stop>{{ $deleteAction }}</li>
                                            @endif

                                            @if ($isCollapsible)
                                                <li
                                                    class="fi-fo-builder-item-header-collapsible-actions"
                                                    x-on:click.stop="isCollapsed = !isCollapsed"
                                                >
                                                    <div class="fi-fo-builder-item-header-collapse-action">
                                                        {{ $getAction('collapse') }}
                                                    </div>
                                                    <div class="fi-fo-builder-item-header-expand-action">
                                                        {{ $getAction('expand') }}
                                                    </div>
                                                </li>
                                            @endif
                                        </ul>
                                    @endif
                                </div>
                            @endif

                            <div
                                x-show="! isCollapsed"
                                @class([
                                    'fi-fo-builder-item-content',
                                    'fi-fo-builder-item-content-has-preview' => $hasBlockPreviews && $item->getParentComponent()->hasPreview(),
                                ])
                            >
                                @if ($hasBlockPreviews && $item->getParentComponent()->hasPreview())
                                    <div
                                        @class([
                                            'fi-fo-builder-item-preview',
                                            'fi-interactive' => $hasInteractiveBlockPreviews,
                                        ])
                                    >
                                        {{ $item->getParentComponent()->renderPreview($item->getRawState()) }}
                                    </div>

                                    @if ($editActionIsVisible && (! $hasInteractiveBlockPreviews))
                                        <div
                                            class="fi-fo-builder-item-preview-edit-overlay"
                                            role="button"
                                            x-on:click.stop="{{ '$wire.mountAction(\'edit\', { item: \'' . $itemKey . '\' }, { schemaComponent: \'' . $key . '\' })' }}"
                                        ></div>
                                    @endif
                                @else
                                    {{ $item }}
                                @endif
                            </div>
                        </li>
                    @else
                        {{-- Deferred item: lightweight header only, no schema instantiation --}}
                        @php
                            $reorderActionIsVisible = $isReorderableWithDragAndDrop && $reorderAction->isVisible();
                            $hasItemHeader = $hasBlockHeaders;
                        @endphp

                        <li
                            wire:ignore.self
                            wire:key="{{ $statePath }}.{{ $itemKey }}.item"
                            x-data="{
                                isCollapsed: true,
                                isLoaded: false,
                            }"
                            x-on:builder-expand.window="
                                if ($event.detail === '{{ $statePath }}') {
                                    isCollapsed = false;
                                    if (!isLoaded) { isLoaded = true; }
                                }
                            "
                            x-on:builder-collapse.window="$event.detail === '{{ $statePath }}' && (isCollapsed = true)"
                            x-sortable-item="{{ $itemKey }}"
                            @class([
                                'fi-fo-builder-item',
                                'fi-fo-builder-item-has-header' => $hasItemHeader,
                            ])
                            x-bind:class="{ 'fi-collapsed': isCollapsed }"
                        >
                            @if ($hasItemHeader)
                                <div
                                    @if ($isCollapsible)
                                        x-on:click.stop="
                                            if (!isLoaded) {
                                                isLoaded = true;
                                                isCollapsed = false;
                                                $wire.mountAction('loadItem', { item: '{{ $itemKey }}' }, { schemaComponent: '{{ $key }}' });
                                            } else {
                                                isCollapsed = !isCollapsed;
                                            }
                                        "
                                    @endif
                                    class="fi-fo-builder-item-header"
                                >
                                    @if ($reorderActionIsVisible)
                                        <ul class="fi-fo-builder-item-header-start-actions">
                                            <li x-on:click.stop>
                                                {{ $reorderAction->extraAttributes(['x-sortable-handle' => true], merge: true) }}
                                            </li>
                                        </ul>
                                    @endif

                                    @if ($hasBlockLabels)
                                        <{{ $blockLabelHeadingTag }}
                                            @class([
                                                'fi-fo-builder-item-header-label',
                                                'fi-truncated' => $isBlockLabelTruncated,
                                            ])
                                        >
                                            {{ str($rawItem['type'])->replace(['-', '_'], ' ')->ucfirst() }}

                                            @if ($hasBlockNumbers)
                                                {{ $loop->iteration }}
                                            @endif
                                        </{{ $blockLabelHeadingTag }}>
                                    @endif

                                    <ul class="fi-fo-builder-item-header-end-actions">
                                        @if ($isDeletable)
                                            <li x-on:click.stop>
                                                {{ $getAction($getDeleteActionName())(['item' => $itemKey]) }}
                                            </li>
                                        @endif

                                        @if ($isCollapsible)
                                            <li
                                                class="fi-fo-builder-item-header-collapsible-actions"
                                                x-on:click.stop="
                                                    if (!isLoaded) {
                                                        isLoaded = true;
                                                        isCollapsed = false;
                                                        $wire.mountAction('loadItem', { item: '{{ $itemKey }}' }, { schemaComponent: '{{ $key }}' });
                                                    } else {
                                                        isCollapsed = !isCollapsed;
                                                    }
                                                "
                                            >
                                                <div class="fi-fo-builder-item-header-collapse-action">
                                                    {{ $getAction('collapse') }}
                                                </div>
                                                <div class="fi-fo-builder-item-header-expand-action">
                                                    {{ $getAction('expand') }}
                                                </div>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            @endif

                            <div
                                x-show="! isCollapsed"
                                class="fi-fo-builder-item-content"
                            >
                                <div class="flex items-center justify-center p-4">
                                    <x-filament::loading-indicator class="h-5 w-5" />
                                </div>
                            </div>
                        </li>
                    @endif

                    @if (! $loop->last)
                        @if ($isAddable && $addBetweenAction(['afterItem' => $itemKey])->isVisible())
                            <li class="fi-fo-builder-add-between-items-ctn">
                                <div class="fi-fo-builder-add-between-items">
                                    <div class="fi-fo-builder-block-picker-ctn">
                                        <x-page-blocks::page-builder.block-picker
                                            :action="$addBetweenAction"
                                            :after-item="$itemKey"
                                            :columns="$blockPickerColumns"
                                            :blocks="$blockPickerBlocks"
                                            :key="$key"
                                            :style="$blockPickerStyle"
                                            :width="$blockPickerWidth"
                                        >
                                            <x-slot name="trigger">
                                                {{ $addBetweenAction(['afterItem' => $itemKey]) }}
                                            </x-slot>
                                        </x-page-blocks::page-builder.block-picker>
                                    </div>
                                </div>
                            </li>
                        @elseif (filled($labelBetweenItems))
                            <li class="fi-fo-builder-label-between-items-ctn">
                                <div class="fi-fo-builder-label-between-items-divider-before"></div>
                                <span class="fi-fo-builder-label-between-items">
                                    {{ $labelBetweenItems }}
                                </span>
                                <div class="fi-fo-builder-label-between-items-divider-after"></div>
                            </li>
                        @endif
                    @endif
                @endforeach
            </ul>
        @endif

        @if ($isAddable && $addAction->isVisible())
            <x-page-blocks::page-builder.block-picker
                :action="$addAction"
                :action-alignment="$addActionAlignment"
                :blocks="$blockPickerBlocks"
                :columns="$blockPickerColumns"
                :key="$key"
                :style="$blockPickerStyle"
                :width="$blockPickerWidth"
            >
                <x-slot name="trigger">
                    {{ $addAction }}
                </x-slot>
            </x-page-blocks::page-builder.block-picker>
        @endif
    </div>
</x-dynamic-component>

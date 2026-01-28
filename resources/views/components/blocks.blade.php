@props(['blocks', 'context'=> []])
@use('Illuminate\View\ComponentAttributeBag')
@use('DejoDev\PageBlocks\Facades\PageBlocks')

<div>
    @foreach ($blocks as $blockData)
        @php
          $component= PageBlocks::getBlock($blockData['type']);
          $data = App::call([$component, 'prepareData'], ['data'=>$blockData['data'], 'context'=>$context]);
        @endphp

        @if ($component->isLivewire())
            @livewire($component->getComponent(), $data, key(uniqid()))
        @else
            <x-dynamic-component
                :component="$component->getComponent()"
                :attributes="new ComponentAttributeBag($data)"
            />
        @endif
    @endforeach
</div>

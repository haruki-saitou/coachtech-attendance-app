{{-- 1. レイアウト（外枠）を使う --}}
@props(['text', 'action', 'type' => 'main', 'disabled' => false])

@php
    $classes = $type === 'main'
        ? 'bg-black text-white hover:bg-gray-700'
        : 'bg-white text-black hover:bg-gray-300';
@endphp
<form action="{{ $action }}" method="POST" class="flex-1">
    @csrf
    <button type="submit" {{ $disabled ? 'disabled' : '' }}
        class="{{ $classes }} inline-block w-[170px] px-6 py-4 text-2xl font-bold rounded-2xl transition cursor-pointer">
        {{ $text }}
    </button>
</form>

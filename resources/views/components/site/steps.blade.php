@props(['current' => 1]) {{-- 1 = Keranjang, 2 = Validasi Data, 3 = Pembayaran --}}

@php
    $steps = [1 => 'Keranjang', 2 => 'Validasi Data', 3 => 'Pembayaran'];
@endphp

<ol class="flex w-fit flex-wrap items-center gap-x-3 gap-y-1 rounded-full bg-emerald/10 px-4 py-2 text-xs font-medium text-emerald">
    @foreach ($steps as $number => $label)
        <li class="flex items-center gap-1.5 {{ $number === $current ? 'font-semibold' : 'opacity-60' }}">
            <span class="flex h-5 w-5 items-center justify-center rounded-full {{ $number === $current ? 'bg-emerald text-white' : ($number < $current ? 'bg-emerald/30' : 'bg-white') }}">{{ $number < $current ? '✓' : $number }}</span>
            {{ $label }}
        </li>
        @if (! $loop->last)
            <li aria-hidden="true" class="opacity-40">&rsaquo;</li>
        @endif
    @endforeach
</ol>

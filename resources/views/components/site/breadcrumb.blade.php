@props(['items' => []])

<nav aria-label="Breadcrumb" {{ $attributes->merge(['class' => 'flex flex-wrap items-center gap-2 text-xs text-ink/50']) }}>
    @foreach ($items as $item)
        @if (! $loop->last && ! empty($item['href']))
            <a href="{{ $item['href'] }}" class="hover:text-maroon">{{ $item['label'] }}</a>
            <span>/</span>
        @else
            <span class="text-ink/70">{{ $item['label'] }}</span>
        @endif
    @endforeach
</nav>

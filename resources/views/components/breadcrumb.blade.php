@props(['items' => []])

<div class="breadcrumb-bar">
    <div class="container">
        <nav aria-label="Breadcrumb">
            <ol>
                <li><a href="{{ url('/') }}">Beranda</a></li>
                @foreach ($items as $item)
                    <li>
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8.25 4.5l7.5 7.5-7.5 7.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        @if (!empty($item['href']) && !$loop->last)
                            <a href="{{ $item['href'] }}">{{ $item['label'] }}</a>
                        @else
                            <span class="current">{{ $item['label'] }}</span>
                        @endif
                    </li>
                @endforeach
            </ol>
        </nav>
    </div>
</div>

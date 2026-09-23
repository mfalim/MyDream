{{-- Baris card yang bisa digeser (tombol kiri/kanan) --}}
<div class="md-scroller" data-scroller>
    <button type="button" class="md-scroller__btn md-scroller__btn--prev" data-dir="prev" aria-label="Sebelumnya"><i class="bi bi-chevron-left"></i></button>
    <div class="md-scroller__track">
        @foreach ($items as $vendor)
            <div class="md-scroller__item">@include('mydream.partials.vendor-card', ['vendor' => $vendor])</div>
        @endforeach
    </div>
    <button type="button" class="md-scroller__btn md-scroller__btn--next" data-dir="next" aria-label="Berikutnya"><i class="bi bi-chevron-right"></i></button>
</div>

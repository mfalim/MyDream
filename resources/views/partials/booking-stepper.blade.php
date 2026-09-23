{{-- Props: $active (1|2|3) --}}
@php $active = $active ?? 1; @endphp
<div class="booking-topbar">
    <div class="container row">
        <div class="stepper">
            <span class="label">Tahapan Booking:</span>

            <span class="step {{ $active == 1 ? 'active' : ($active > 1 ? 'done' : '') }}">
                <span class="num">{{ $active > 1 ? '✓' : '1' }}</span> Keranjang
            </span>
            <svg class="chev" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8.25 4.5l7.5 7.5-7.5 7.5" stroke-linecap="round" stroke-linejoin="round"/></svg>

            <span class="step {{ $active == 2 ? 'active' : ($active > 2 ? 'done' : '') }}">
                <span class="num">{{ $active > 2 ? '✓' : '2' }}</span> Validasi Data
            </span>
            <svg class="chev" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8.25 4.5l7.5 7.5-7.5 7.5" stroke-linecap="round" stroke-linejoin="round"/></svg>

            <span class="step {{ $active == 3 ? 'active' : '' }}">
                <span class="num">3</span> Pembayaran
            </span>
        </div>

        <span class="escrow-tag hide-mobile">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 2.25l7.5 3.375v6.375c0 5.06-3.2 8.906-7.5 9.75-4.3-.844-7.5-4.69-7.5-9.75V5.625L12 2.25z"/></svg>
            Bespoke Escrow Protection Active
        </span>
    </div>
</div>

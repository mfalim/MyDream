<footer class="bg-ink text-cream/80">
    <div class="mx-auto grid max-w-7xl gap-12 px-4 py-16 sm:grid-cols-2 sm:px-6 lg:grid-cols-4 lg:px-8">
        <div>
            <p class="font-display text-2xl font-semibold text-cream">MyDream</p>
            <p class="mt-4 text-sm leading-relaxed text-cream/60">Bagian dari My Dream Group - Pasti Nikahmu BEDA! Menghidupkan setiap momen berharga dengan kehangatan dan presisi.</p>
        </div>
        <div>
            <p class="text-sm font-semibold uppercase tracking-wide text-cream">Tautan Cepat</p>
            <ul class="mt-4 space-y-2 text-sm text-cream/60">
                <li><a href="{{ route('home') }}" class="hover:text-cream">Beranda</a></li>
                <li><a href="{{ route('catalog.index') }}" class="hover:text-cream">Paket &amp; Vendor</a></li>
                <li><a href="{{ route('inspiration.index') }}" class="hover:text-cream">Inspirasi</a></li>
                <li><a href="{{ route('blog.index') }}" class="hover:text-cream">Artikel</a></li>
                <li><a href="{{ route('about') }}" class="hover:text-cream">Tentang Kami</a></li>
            </ul>
        </div>
        <div>
            <p class="text-sm font-semibold uppercase tracking-wide text-cream">Cakupan</p>
            <p class="mt-4 text-sm leading-relaxed text-cream/60">Jember, Jawa Timur, Indonesia.<br>Melayani area {{ implode(', ', config('mydream.cities')) }}, dan sekitarnya.</p>
        </div>
        <div>
            <p class="text-sm font-semibold uppercase tracking-wide text-cream">Kontak</p>
            <ul class="mt-4 space-y-2 text-sm text-cream/60">
                <li>Email: {{ config('mydream.email') }}</li>
                <li>WhatsApp: <a href="{{ \App\Support\Format::waLink(config('mydream.whatsapp')) }}" class="hover:text-cream">{{ config('mydream.whatsapp_display') }}</a></li>
                <li>Instagram: {{ config('mydream.instagram') }}</li>
            </ul>
        </div>
    </div>
    <div class="border-t border-cream/10">
        <div class="mx-auto max-w-7xl px-4 py-6 text-xs text-cream/50 sm:px-6 lg:px-8">&copy; {{ date('Y') }} My Dream Organizer. Seluruh hak cipta dilindungi.</div>
    </div>
</footer>

<header class="vendor-topbar-strip">
    <div>
        <span class="wo-eyebrow"><i class="bi bi-circle-fill"></i> Portal Mitra Vendor</span>
        <strong class="vendor-topbar-title">@yield('title', 'Vendor Workspace')</strong>
    </div>

    <div class="vendor-topbar-pill">
        <span class="vendor-status-dot"></span>
        <span>{{ auth()->user()->vendor?->name ?? auth()->user()->name ?? 'Vendor' }}</span>
    </div>
</header>

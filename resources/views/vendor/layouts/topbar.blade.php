<header class="admin-topbar">
    <div>
        <span class="admin-eyebrow">PORTAL MITRA VENDOR</span>
        <strong>@yield('title', 'Vendor Workspace')</strong>
    </div>

    <div class="admin-status">
        <i class="bi bi-circle-fill"></i>
        {{ auth()->user()->vendor?->name ?? auth()->user()->name ?? 'Vendor' }}
    </div>
</header>

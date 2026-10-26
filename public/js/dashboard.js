document.addEventListener("DOMContentLoaded", () => {
    const sidebar = document.getElementById("sidebar");
    const sidebarNav = document.querySelector(".sidebar-nav");
    const sidebarToggle = document.getElementById("sidebarToggle");
    const sidebarOverlay = document.getElementById("sidebarOverlay");

    const closeSidebar = () => {
        sidebar?.classList.remove("open");
        sidebarOverlay?.classList.remove("show");
        document.body.style.overflow = "";
    };

    const openSidebar = () => {
        sidebar?.classList.add("open");
        sidebarOverlay?.classList.add("show");
        document.body.style.overflow = "hidden";
    };
    if (!sidebar) return;

    const storageKey = 'admin_sidebar_scroll';

    const saveSidebarScroll = () => {
        sessionStorage.setItem(storageKey, JSON.stringify({
            sidebar: sidebar.scrollTop,
            sidebarNav: sidebarNav?.scrollTop ?? 0
        }));
    };

    // Pulihkan posisi scroll setelah halaman dimuat
    try {
        const savedPosition = sessionStorage.getItem(storageKey);

        if (savedPosition) {
            const positions = JSON.parse(savedPosition);

            sidebar.scrollTop = positions.sidebar ?? 0;

            if (sidebarNav) {
                sidebarNav.scrollTop = positions.sidebarNav ?? 0;
            }
        }
    } catch (error) {
        console.warn("Gagal memulihkan posisi sidebar:", error);
    }

    // Simpan posisi sebelum berpindah halaman
    document.querySelectorAll(".sidebar a[href]").forEach((link) => {
        link.addEventListener("click", saveSidebarScroll);
    });

    sidebarToggle?.addEventListener("click", () => {
        if (sidebar?.classList.contains("open")) {
            closeSidebar();
        } else {
            openSidebar();
        }
    });

    sidebarOverlay?.addEventListener("click", closeSidebar);

    document.querySelectorAll(".sidebar .nav-item").forEach((item) => {
        item.addEventListener("click", () => {
            if (window.innerWidth <= 991) closeSidebar();
        });
    });

    // Countdown to 25 October 2025.
    // Change this date when connecting the dashboard to real wedding data.
    const targetDate = new Date("2025-10-25T08:00:00+07:00").getTime();

    const updateCountdown = () => {
        const now = Date.now();
        const distance = targetDate - now;

        if (distance <= 0) {
            document.getElementById("days").textContent = "0";
            document.getElementById("hours").textContent = "0";
            document.getElementById("minutes").textContent = "0";
            return;
        }

        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
        const hours = Math.floor((distance / (1000 * 60 * 60)) % 24);
        const minutes = Math.floor((distance / (1000 * 60)) % 60);

        document.getElementById("days").textContent = days;
        document.getElementById("hours").textContent = hours;
        document.getElementById("minutes").textContent = minutes;
    };

    updateCountdown();
    setInterval(updateCountdown, 60000);
});

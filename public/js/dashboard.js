document.addEventListener("DOMContentLoaded", () => {
    const sidebar = document.getElementById("sidebar");
    const sidebarNav = document.querySelector(".sidebar-nav");
    const sidebarToggle = document.getElementById("sidebarToggle");
    const sidebarClose = document.getElementById("sidebarClose");
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

    const storageKey = "client_sidebar_scroll";

    const saveSidebarScroll = () => {
        try {
            sessionStorage.setItem(
                storageKey,
                JSON.stringify({
                    sidebar: sidebar.scrollTop,
                    sidebarNav: sidebarNav?.scrollTop ?? 0,
                })
            );
        } catch (_) {}
    };

    try {
        const savedPosition = sessionStorage.getItem(storageKey);
        if (savedPosition) {
            const positions = JSON.parse(savedPosition);
            sidebar.scrollTop = positions.sidebar ?? 0;
            if (sidebarNav) {
                sidebarNav.scrollTop = positions.sidebarNav ?? 0;
            }
        }
    } catch (_) {}

    document.querySelectorAll(".sidebar a[href]").forEach((link) => {
        link.addEventListener("click", saveSidebarScroll);
    });

    sidebarToggle?.addEventListener("click", () => {
        if (sidebar.classList.contains("open")) {
            closeSidebar();
        } else {
            openSidebar();
        }
    });

    sidebarClose?.addEventListener("click", closeSidebar);
    sidebarOverlay?.addEventListener("click", closeSidebar);

    document.addEventListener("keydown", (event) => {
        if (event.key === "Escape" && sidebar.classList.contains("open")) {
            closeSidebar();
        }
    });

    document.querySelectorAll(".sidebar .nav-item").forEach((item) => {
        item.addEventListener("click", () => {
            if (window.innerWidth <= 992) closeSidebar();
        });
    });

    window.addEventListener("resize", () => {
        if (window.innerWidth > 992 && sidebar.classList.contains("open")) {
            closeSidebar();
        }
    });

    // Optional legacy countdown elements (only run if present in DOM)
    const daysEl = document.getElementById("days");
    const hoursEl = document.getElementById("hours");
    const minutesEl = document.getElementById("minutes");
    if (daysEl && hoursEl && minutesEl) {
        const targetDate = new Date("2025-10-25T08:00:00+07:00").getTime();
        const updateCountdown = () => {
            const distance = targetDate - Date.now();
            if (distance <= 0) {
                daysEl.textContent = "0";
                hoursEl.textContent = "0";
                minutesEl.textContent = "0";
                return;
            }
            daysEl.textContent = String(Math.floor(distance / (1000 * 60 * 60 * 24)));
            hoursEl.textContent = String(Math.floor((distance / (1000 * 60 * 60)) % 24));
            minutesEl.textContent = String(Math.floor((distance / (1000 * 60)) % 60));
        };
        updateCountdown();
        setInterval(updateCountdown, 60000);
    }
});

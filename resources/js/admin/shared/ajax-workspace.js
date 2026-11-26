/**
 * Shared AJAX Partial Workspace Swapper for Admin Index Pages.
 *
 * Usage:
 *   <div id="myWorkspace" data-ajax-workspace> ... </div>
 *
 * Any GET <form>, <a href> matching the current/base pathname (tabs, filters,
 * pagination links), or [data-ajax-auto-submit="true"] select inside the
 * container will swap only that container's innerHTML without a full page reload,
 * while keeping browser URL & history (pushState/popstate) in sync.
 */
function initAjaxWorkspaces() {
    const workspaces = document.querySelectorAll('[data-ajax-workspace][id]');
    if (!workspaces.length) return;

    workspaces.forEach(function (workspaceEl) {
        if (workspaceEl.dataset.ajaxBound === 'true') return;
        workspaceEl.dataset.ajaxBound = 'true';

        const workspaceId = workspaceEl.id;
        const basePath = workspaceEl.dataset.ajaxBase
            ? new URL(workspaceEl.dataset.ajaxBase, window.location.origin).pathname
            : window.location.pathname;

        async function loadWorkspace(url, pushHistory = true) {
            workspaceEl.style.opacity = '0.55';
            workspaceEl.style.pointerEvents = 'none';

            try {
                const response = await fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                });
                if (!response.ok) throw new Error('Network response was not ok');

                const html = await response.text();
                const doc = new DOMParser().parseFromString(html, 'text/html');
                const nextWorkspace = doc.getElementById(workspaceId);
                if (!nextWorkspace) {
                    window.location.href = url;
                    return;
                }

                // Bersihkan modal yang sebelumnya di-hoist ke <body> dari workspace ini
                // agar tidak terjadi duplikasi ID setelah konten diperbarui via AJAX.
                document
                    .querySelectorAll(
                        `body > .modal[data-hoisted-from-workspace="${workspaceId}"]`
                    )
                    .forEach((el) => {
                        const instance = window.bootstrap?.Modal?.getInstance(el);
                        if (instance) instance.dispose();
                        el.remove();
                    });

                workspaceEl.innerHTML = nextWorkspace.innerHTML;
                if (pushHistory) {
                    window.history.pushState({ ajaxWorkspaceUrl: url }, '', url);
                }

                workspaceEl.dispatchEvent(
                    new CustomEvent('ajax-workspace:updated', { detail: { url } })
                );
            } catch (_) {
                window.location.href = url;
            } finally {
                workspaceEl.style.opacity = '';
                workspaceEl.style.pointerEvents = '';
            }
        }

        workspaceEl.addEventListener('click', function (e) {
            if (e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;
            const link = e.target.closest('a[href]');
            if (
                !link ||
                link.target === '_blank' ||
                link.hasAttribute('download') ||
                link.hasAttribute('data-no-ajax')
            ) {
                return;
            }

            const parsed = new URL(link.href, window.location.origin);
            if (parsed.origin !== window.location.origin || parsed.pathname !== basePath) {
                return;
            }

            e.preventDefault();
            loadWorkspace(parsed.toString(), true);
        });

        workspaceEl.addEventListener('submit', function (e) {
            const form = e.target.closest('form');
            if (!form || (form.method || 'get').toLowerCase() !== 'get') return;
            if (form.hasAttribute('data-no-ajax')) return;

            const actionUrl = new URL(
                form.getAttribute('action') || window.location.href,
                window.location.origin
            );
            if (actionUrl.pathname !== basePath) return;

            e.preventDefault();
            const params = new URLSearchParams(new FormData(form));
            actionUrl.search = params.toString();
            loadWorkspace(actionUrl.toString(), true);
        });

        workspaceEl.addEventListener('change', function (e) {
            const select = e.target.closest('[data-ajax-auto-submit="true"]');
            if (!select || !select.form) return;
            select.form.requestSubmit();
        });

        window.addEventListener('popstate', function () {
            if (window.location.pathname === basePath) {
                loadWorkspace(window.location.href, false);
            }
        });
    });
}

document.addEventListener('DOMContentLoaded', initAjaxWorkspaces);

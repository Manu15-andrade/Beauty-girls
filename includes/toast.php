<?php
/**
 * Beauty Girls - Sistema de Notificações Toast
 */

function set_toast(string $type, string $title, string $message): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION['toast'] = [
        'type'    => $type,
        'title'   => $title,
        'message' => $message,
    ];
}

function render_toast(): void
{
    if (!empty($_SESSION['toast'])) {
        $toast = $_SESSION['toast'];
        unset($_SESSION['toast']);
        $type = json_encode($toast['type'] ?? 'info', JSON_UNESCAPED_UNICODE);
        $title = json_encode($toast['title'] ?? '', JSON_UNESCAPED_UNICODE);
        $message = json_encode($toast['message'] ?? '', JSON_UNESCAPED_UNICODE);
        echo "<script>document.addEventListener('DOMContentLoaded', function() { if (window.showToast) { window.showToast($type, $title, $message); } });</script>\n";
    }
}
?>

<!-- Container de Notificações Toast -->
<div id="toast-container" class="fixed top-20 right-4 z-[70] flex flex-col space-y-3 max-w-sm w-full pointer-events-none px-4 sm:px-0"></div>

<script>
    window.showToast = function (type, title, message, duration = 4500) {
        const container = document.getElementById('toast-container');
        if (!container) return;

        const styles = {
            success: {
                bg: 'bg-white border-emerald-200',
                iconBg: 'bg-emerald-100 text-emerald-600',
                titleColor: 'text-emerald-900',
                bar: 'bg-emerald-500',
                icon: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>'
            },
            error: {
                bg: 'bg-white border-red-200',
                iconBg: 'bg-red-100 text-red-600',
                titleColor: 'text-red-900',
                bar: 'bg-red-500',
                icon: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>'
            },
            warning: {
                bg: 'bg-white border-amber-200',
                iconBg: 'bg-amber-100 text-amber-600',
                titleColor: 'text-amber-900',
                bar: 'bg-amber-500',
                icon: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>'
            },
            info: {
                bg: 'bg-white border-rose-200',
                iconBg: 'bg-rose-100 text-beauty-600',
                titleColor: 'text-gray-900',
                bar: 'bg-beauty-600',
                icon: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
            }
        };

        const cfg = styles[type] || styles.info;
        const toast = document.createElement('div');
        toast.className = `pointer-events-auto relative overflow-hidden rounded-2xl border ${cfg.bg} shadow-xl p-4 flex items-start space-x-3 transform translate-x-full opacity-0 transition-all duration-300`;

        toast.innerHTML = `
            <div class="w-9 h-9 rounded-xl ${cfg.iconBg} flex items-center justify-center flex-shrink-0">
                ${cfg.icon}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-xs font-bold ${cfg.titleColor}">${title}</p>
                <p class="text-xs text-gray-600 mt-0.5 leading-relaxed">${message}</p>
            </div>
            <button type="button" class="text-gray-400 hover:text-gray-600 transition-colors" onclick="this.parentElement.remove()">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
            <div class="absolute bottom-0 left-0 h-1 ${cfg.bar} transition-all" style="width: 100%"></div>
        `;

        container.appendChild(toast);

        requestAnimationFrame(() => {
            toast.classList.remove('translate-x-full', 'opacity-0');
            toast.classList.add('translate-x-0', 'opacity-100');
        });

        setTimeout(() => {
            toast.classList.remove('translate-x-0', 'opacity-100');
            toast.classList.add('translate-x-full', 'opacity-0');
            setTimeout(() => toast.remove(), 300);
        }, duration);
    };
</script>

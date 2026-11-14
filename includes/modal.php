<!-- Universal Modal (Substitui alert() e confirm() nativos) -->
<div id="universalModal" class="fixed inset-0 z-[80] hidden overflow-y-auto" role="dialog" aria-modal="true">
    <div id="universalModalBackdrop" class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity opacity-0"></div>

    <div class="flex min-h-screen items-center justify-center p-4">
        <div id="universalModalCard" class="relative w-full max-w-md transform rounded-3xl bg-white p-6 shadow-2xl border border-rose-100 transition-all scale-95 opacity-0">
            <div class="flex items-start space-x-3">
                <div id="universalModalIcon" class="w-11 h-11 rounded-2xl bg-rose-100 text-beauty-600 flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div class="flex-1">
                    <h3 id="universalModalTitle" class="font-serif text-lg font-bold text-gray-900"></h3>
                    <p id="universalModalMessage" class="text-xs text-gray-600 mt-1.5 leading-relaxed"></p>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end space-x-2">
                <button
                    type="button"
                    id="universalModalCancel"
                    class="hidden px-4 py-2 rounded-xl border border-gray-200 text-xs font-semibold text-gray-600 hover:bg-gray-50 transition-colors"
                >
                    Cancelar
                </button>
                <button
                    type="button"
                    id="universalModalConfirm"
                    class="px-5 py-2 rounded-xl bg-beauty-600 hover:bg-beauty-700 text-white text-xs font-semibold shadow-md transition-all"
                >
                    Confirmar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    (function () {
        const modal = document.getElementById('universalModal');
        const backdrop = document.getElementById('universalModalBackdrop');
        const card = document.getElementById('universalModalCard');
        const titleEl = document.getElementById('universalModalTitle');
        const msgEl = document.getElementById('universalModalMessage');
        const iconEl = document.getElementById('universalModalIcon');
        const btnCancel = document.getElementById('universalModalCancel');
        const btnConfirm = document.getElementById('universalModalConfirm');

        let confirmCallback = null;

        function closeModal() {
            backdrop.classList.remove('opacity-100');
            backdrop.classList.add('opacity-0');
            card.classList.remove('scale-100', 'opacity-100');
            card.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                modal.classList.add('hidden');
                confirmCallback = null;
            }, 200);
        }

        function openModal() {
            modal.classList.remove('hidden');
            requestAnimationFrame(() => {
                backdrop.classList.remove('opacity-0');
                backdrop.classList.add('opacity-100');
                card.classList.remove('scale-95', 'opacity-0');
                card.classList.add('scale-100', 'opacity-100');
            });
        }

        btnCancel.addEventListener('click', closeModal);
        backdrop.addEventListener('click', closeModal);
        btnConfirm.addEventListener('click', () => {
            const cb = confirmCallback;
            closeModal();
            if (typeof cb === 'function') cb();
        });

        window.customAlert = function (title, message) {
            titleEl.textContent = title;
            msgEl.innerHTML = message;
            iconEl.className = 'w-11 h-11 rounded-2xl bg-rose-100 text-beauty-600 flex items-center justify-center flex-shrink-0';
            btnCancel.classList.add('hidden');
            btnConfirm.textContent = 'Entendi';
            btnConfirm.className = 'px-5 py-2 rounded-xl bg-beauty-600 hover:bg-beauty-700 text-white text-xs font-semibold shadow-md transition-all';
            confirmCallback = null;
            openModal();
        };

        window.customConfirm = function (title, message, onConfirm, isDanger = false) {
            titleEl.textContent = title;
            msgEl.innerHTML = message;
            btnCancel.classList.remove('hidden');
            btnConfirm.textContent = isDanger ? 'Sim, Confirmar' : 'Confirmar';

            if (isDanger) {
                iconEl.className = 'w-11 h-11 rounded-2xl bg-red-100 text-red-600 flex items-center justify-center flex-shrink-0';
                btnConfirm.className = 'px-5 py-2 rounded-xl bg-red-600 hover:bg-red-700 text-white text-xs font-semibold shadow-md transition-all';
            } else {
                iconEl.className = 'w-11 h-11 rounded-2xl bg-rose-100 text-beauty-600 flex items-center justify-center flex-shrink-0';
                btnConfirm.className = 'px-5 py-2 rounded-xl bg-beauty-600 hover:bg-beauty-700 text-white text-xs font-semibold shadow-md transition-all';
            }

            confirmCallback = onConfirm;
            openModal();
        };
    })();
</script>

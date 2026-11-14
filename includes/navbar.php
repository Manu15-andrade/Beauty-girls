<?php
$usuario = usuario_atual();
$buscaAtual = trim($_GET['busca'] ?? '');
?>
<header class="sticky top-0 z-40 bg-white/90 backdrop-blur-md border-b border-rose-100 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16 sm:h-20 gap-4">

            <!-- Logo -->
            <a href="<?= BASE_URL ?>/index.php" class="flex items-center space-x-2.5 flex-shrink-0">
                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-2xl bg-gradient-to-tr from-beauty-600 via-rose-500 to-amber-300 flex items-center justify-center text-white font-serif font-bold text-lg shadow-md shadow-rose-200">
                    B
                </div>
                <div>
                    <span class="font-serif text-lg sm:text-xl font-bold text-gray-900 tracking-tight block leading-none">
                        Beauty <span class="text-beauty-600">Girls</span>
                    </span>
                    <span class="text-[10px] text-gray-400 font-medium hidden sm:block">Cosméticos & Autocuidado</span>
                </div>
            </a>

            <!-- Barra de Busca (Desktop) -->
            <form action="<?= BASE_URL ?>/index.php" method="GET" class="hidden md:flex flex-1 max-w-md mx-4">
                <div class="relative w-full">
                    <input
                        type="text"
                        name="busca"
                        value="<?= htmlspecialchars($buscaAtual) ?>"
                        placeholder="Buscar séruns, gloss, paletas..."
                        class="w-full pl-10 pr-4 py-2 rounded-2xl border border-rose-100 bg-rose-50/40 text-xs focus:bg-white focus:ring-2 focus:ring-beauty-400 focus:outline-none transition-all"
                    >
                    <button type="submit" class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-gray-400 hover:text-beauty-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </button>
                </div>
            </form>

            <!-- Navegação Desktop & Ações -->
            <div class="flex items-center space-x-2 sm:space-x-3">
                <a href="<?= BASE_URL ?>/index.php#catalogo" class="hidden lg:inline-flex text-xs font-semibold text-gray-600 hover:text-beauty-600 px-3 py-2 rounded-xl transition-colors">
                    Catálogo
                </a>

                <!-- Sacola -->
                <a href="<?= BASE_URL ?>/carrinho.php" class="relative inline-flex items-center space-x-1.5 px-3.5 py-2 rounded-2xl bg-rose-50 hover:bg-rose-100 text-beauty-700 text-xs font-bold transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    <span class="hidden sm:inline">Sacola</span>
                    <span id="cart-badge-header" class="inline-flex items-center justify-center min-w-[18px] h-[18px] px-1 rounded-full bg-beauty-600 text-white text-[10px] font-extrabold">0</span>
                </a>

                <?php if ($usuario): ?>
                    <?php if (eh_admin()): ?>
                        <a href="<?= BASE_URL ?>/admin/index.php" class="hidden sm:inline-flex items-center space-x-1 px-3.5 py-2 rounded-2xl bg-gray-900 hover:bg-gray-800 text-white text-xs font-semibold transition-all shadow-sm">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span>Painel Admin</span>
                        </a>
                    <?php endif; ?>

                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" type="button" class="inline-flex items-center space-x-1.5 px-3 py-2 rounded-2xl border border-rose-100 bg-white hover:bg-rose-50 text-xs font-semibold text-gray-700 transition-all">
                            <span class="w-6 h-6 rounded-full bg-beauty-100 text-beauty-700 flex items-center justify-center font-bold text-[11px]">
                                <?= strtoupper(substr($usuario['nome'], 0, 1)) ?>
                            </span>
                            <span class="hidden sm:inline max-w-[100px] truncate"><?= htmlspecialchars(explode(' ', $usuario['nome'])[0]) ?></span>
                            <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>

                        <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-48 rounded-2xl bg-white shadow-xl border border-rose-100 py-2 z-50" style="display: none;">
                            <div class="px-4 py-2 border-b border-rose-50">
                                <p class="text-xs font-bold text-gray-800 truncate"><?= htmlspecialchars($usuario['nome']) ?></p>
                                <p class="text-[10px] text-gray-400 truncate"><?= htmlspecialchars($usuario['email']) ?></p>
                            </div>
                            <?php if (eh_admin()): ?>
                                <a href="<?= BASE_URL ?>/admin/index.php" class="block px-4 py-2 text-xs text-gray-700 hover:bg-rose-50 hover:text-beauty-700 font-medium">Painel Administrativo</a>
                                <a href="<?= BASE_URL ?>/admin/historico.php" class="block px-4 py-2 text-xs text-gray-700 hover:bg-rose-50 hover:text-beauty-700 font-medium">Logs & Auditoria</a>
                            <?php endif; ?>
                            <a href="<?= BASE_URL ?>/logout.php" class="block px-4 py-2 text-xs text-red-600 hover:bg-red-50 font-semibold">Sair da Conta</a>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="<?= BASE_URL ?>/login.php" class="px-4 py-2 rounded-2xl bg-gradient-to-r from-beauty-600 to-rose-600 text-white text-xs font-semibold shadow-md shadow-rose-200 hover:shadow-lg transition-all">
                        Entrar
                    </a>
                <?php endif; ?>
            </div>

        </div>

        <!-- Busca Mobile -->
        <form action="<?= BASE_URL ?>/index.php" method="GET" class="md:hidden pb-3">
            <div class="relative w-full">
                <input
                    type="text"
                    name="busca"
                    value="<?= htmlspecialchars($buscaAtual) ?>"
                    placeholder="O que você procura hoje?"
                    class="w-full pl-9 pr-4 py-2 rounded-xl border border-rose-100 bg-rose-50/50 text-xs focus:bg-white focus:ring-2 focus:ring-beauty-400 focus:outline-none"
                >
                <button type="submit" class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </button>
            </div>
        </form>
    </div>
</header>

<!-- Bottom Navigation Bar (Mobile-First) -->
<nav class="md:hidden fixed bottom-0 inset-x-0 z-40 bg-white/95 backdrop-blur-md border-t border-rose-100 shadow-[0_-4px_20px_rgba(0,0,0,0.05)]">
    <div class="grid grid-cols-4 h-14">
        <a href="<?= BASE_URL ?>/index.php" class="flex flex-col items-center justify-center text-[10px] font-semibold text-gray-600 hover:text-beauty-600">
            <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            <span>Início</span>
        </a>
        <a href="<?= BASE_URL ?>/index.php#catalogo" class="flex flex-col items-center justify-center text-[10px] font-semibold text-gray-600 hover:text-beauty-600">
            <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
            <span>Catálogo</span>
        </a>
        <a href="<?= BASE_URL ?>/carrinho.php" class="relative flex flex-col items-center justify-center text-[10px] font-semibold text-gray-600 hover:text-beauty-600">
            <div class="relative">
                <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                <span id="cart-badge-mobile" class="absolute -top-1.5 -right-2 min-w-[16px] h-4 px-1 rounded-full bg-beauty-600 text-white text-[9px] font-extrabold flex items-center justify-center">0</span>
            </div>
            <span>Sacola</span>
        </a>
        <?php if (eh_admin()): ?>
            <a href="<?= BASE_URL ?>/admin/index.php" class="flex flex-col items-center justify-center text-[10px] font-semibold text-beauty-700">
                <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                <span>Admin</span>
            </a>
        <?php else: ?>
            <a href="<?= BASE_URL ?>/<?= $usuario ? 'logout.php' : 'login.php' ?>" class="flex flex-col items-center justify-center text-[10px] font-semibold text-gray-600 hover:text-beauty-600">
                <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                <span><?= $usuario ? 'Sair' : 'Conta' ?></span>
            </a>
        <?php endif; ?>
    </div>
</nav>

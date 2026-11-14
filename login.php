<?php
/**
 * Beauty Girls - Tela de Login de Usuários & Administradores
 * Requisitos:
 * - Autenticação protegida
 * - Sessão PHP nativa
 * - Verificação de senha com BCRYPT
 */

require_once __DIR__ . '/config/constantes.php';
require_once __DIR__ . '/includes/auth.php';

// Se já estiver logado, redireciona
if (esta_logado()) {
    if (eh_admin()) {
        header("Location: " . BASE_URL . "/admin/index.php");
    } else {
        header("Location: " . BASE_URL . "/index.php");
    }
    exit;
}

$page_title = 'Acesso à Plataforma | ' . NOME_LOJA;
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<main class="flex-1 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-gradient-to-b from-rose-50/50 to-[#fcfaf8]">
    <div class="max-w-md w-full space-y-6">
        
        <!-- Cartão de Login -->
        <div class="bg-white p-8 sm:p-10 rounded-3xl border border-rose-100 shadow-xl space-y-6">
            
            <div class="text-center space-y-2">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-beauty-600 via-rose-500 to-amber-300 flex items-center justify-center text-white shadow-lg shadow-rose-200 mx-auto">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                </div>
                <h2 class="font-serif text-2xl sm:text-3xl font-bold text-gray-900">
                    Bem-vinda de volta
                </h2>
                <p class="text-xs text-gray-500">
                    Acesse sua conta para gerenciar cosméticos e pedidos
                </p>
            </div>

            <!-- Dica de Credencial Padrão para Avaliação / Teste -->
            <div class="p-3.5 rounded-2xl bg-rose-50/80 border border-rose-200 text-xs text-gray-700 space-y-1">
                <p class="font-bold text-beauty-700 flex items-center">
                    <svg class="w-4 h-4 mr-1 text-beauty-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Acesso Administrativo (Teste):
                </p>
                <p class="text-[11px] text-gray-600">Email: <strong class="text-gray-900">admin@beautygirls.com</strong></p>
                <p class="text-[11px] text-gray-600">Senha: <strong class="text-gray-900">admin123</strong> (protegida com bcrypt)</p>
            </div>

            <!-- Formulário de Login -->
            <form action="<?= BASE_URL ?>/actions/login_action.php" method="POST" class="space-y-4">
                <div>
                    <label for="email" class="block text-xs font-semibold text-gray-700 mb-1">E-mail Cadastrado</label>
                    <div class="relative">
                        <input 
                            type="email" 
                            name="email" 
                            id="email" 
                            required 
                            placeholder="seu@email.com"
                            class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 text-xs focus:ring-2 focus:ring-beauty-400 focus:outline-none transition-all shadow-sm"
                        >
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/></svg>
                        </div>
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label for="senha" class="block text-xs font-semibold text-gray-700">Sua Senha</label>
                    </div>
                    <div class="relative">
                        <input 
                            type="password" 
                            name="senha" 
                            id="senha" 
                            required 
                            placeholder="••••••••"
                            class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 text-xs focus:ring-2 focus:ring-beauty-400 focus:outline-none transition-all shadow-sm"
                        >
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        </div>
                    </div>
                </div>

                <button 
                    type="submit" 
                    class="w-full py-3 px-4 rounded-2xl bg-gradient-to-r from-beauty-600 via-rose-500 to-beauty-700 text-white font-semibold text-xs shadow-lg shadow-rose-200 hover:shadow-xl hover:from-beauty-700 hover:to-rose-600 transition-all transform active:scale-95"
                >
                    Entrar na Plataforma
                </button>
            </form>

            <div class="text-center pt-2 border-t border-gray-100">
                <p class="text-xs text-gray-500">
                    Ainda não tem cadastro? 
                    <a href="<?= BASE_URL ?>/cadastro.php" class="font-bold text-beauty-600 hover:text-beauty-800 transition-colors">
                        Criar Conta Grátis
                    </a>
                </p>
            </div>

        </div>

    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

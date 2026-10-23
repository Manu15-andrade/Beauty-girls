<?php
/**
 * Beauty Girls - Tela de Cadastro de Usuários
 * Requisito CORE:
 * "Tratar senhas de usuários com hash bcript"
 */

require_once __DIR__ . '/config/constantes.php';
require_once __DIR__ . '/includes/auth.php';

if (esta_logado()) {
    header("Location: " . BASE_URL . "/index.php");
    exit;
}

$page_title = 'Criar Conta | ' . NOME_LOJA;
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<main class="flex-1 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-gradient-to-b from-rose-50/50 to-[#fcfaf8]">
    <div class="max-w-md w-full space-y-6">
        
        <div class="bg-white p-8 sm:p-10 rounded-3xl border border-rose-100 shadow-xl space-y-6">
            
            <div class="text-center space-y-2">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-beauty-600 via-rose-500 to-amber-300 flex items-center justify-center text-white shadow-lg shadow-rose-200 mx-auto">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                </div>
                <h2 class="font-serif text-2xl sm:text-3xl font-bold text-gray-900">
                    Crie sua Conta
                </h2>
                <p class="text-xs text-gray-500">
                    Junte-se à Beauty Girls e aproveite benefícios exclusivos
                </p>
            </div>

            <form action="<?= BASE_URL ?>/actions/register_action.php" method="POST" class="space-y-4">
                <div>
                    <label for="nome" class="block text-xs font-semibold text-gray-700 mb-1">Nome Completo *</label>
                    <input 
                        type="text" 
                        name="nome" 
                        id="nome" 
                        required 
                        placeholder="Ex: Beatriz Lima"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-xs focus:ring-2 focus:ring-beauty-400 focus:outline-none transition-all shadow-sm"
                    >
                </div>

                <div>
                    <label for="email" class="block text-xs font-semibold text-gray-700 mb-1">E-mail *</label>
                    <input 
                        type="email" 
                        name="email" 
                        id="email" 
                        required 
                        placeholder="beatriz@exemplo.com"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-xs focus:ring-2 focus:ring-beauty-400 focus:outline-none transition-all shadow-sm"
                    >
                </div>

                <div>
                    <label for="telefone" class="block text-xs font-semibold text-gray-700 mb-1">WhatsApp / Telefone (opcional)</label>
                    <input 
                        type="text" 
                        name="telefone" 
                        id="telefone" 
                        placeholder="(11) 98765-4321"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-xs focus:ring-2 focus:ring-beauty-400 focus:outline-none transition-all shadow-sm"
                    >
                </div>

                <div>
                    <label for="senha" class="block text-xs font-semibold text-gray-700 mb-1">Senha Segura *</label>
                    <input 
                        type="password" 
                        name="senha" 
                        id="senha" 
                        required 
                        minlength="6"
                        placeholder="Mínimo de 6 caracteres"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-xs focus:ring-2 focus:ring-beauty-400 focus:outline-none transition-all shadow-sm"
                    >
                    <p class="text-[10px] text-gray-400 mt-1">Sua senha será criptografada com algoritmo BCRYPT no banco de dados.</p>
                </div>

                <button 
                    type="submit" 
                    class="w-full py-3 px-4 rounded-2xl bg-gradient-to-r from-beauty-600 via-rose-500 to-beauty-700 text-white font-semibold text-xs shadow-lg shadow-rose-200 hover:shadow-xl hover:from-beauty-700 hover:to-rose-600 transition-all transform active:scale-95"
                >
                    Concluir Cadastro
                </button>
            </form>

            <div class="text-center pt-2 border-t border-gray-100">
                <p class="text-xs text-gray-500">
                    Já possui conta? 
                    <a href="<?= BASE_URL ?>/login.php" class="font-bold text-beauty-600 hover:text-beauty-800 transition-colors">
                        Fazer Login
                    </a>
                </p>
            </div>

        </div>

    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

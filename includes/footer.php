<footer class="bg-white border-t border-rose-100 mt-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="space-y-3">
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-beauty-600 via-rose-500 to-amber-300 flex items-center justify-center text-white font-serif font-bold">
                        B
                    </div>
                    <span class="font-serif text-lg font-bold text-gray-900">Beauty <span class="text-beauty-600">Girls</span></span>
                </div>
                <p class="text-xs text-gray-500 leading-relaxed max-w-xs">
                    Cosméticos selecionados para realçar a sua beleza natural com praticidade, segurança e entrega direto na sua residência.
                </p>
            </div>

            <div>
                <h4 class="text-xs font-bold text-gray-900 uppercase tracking-wider mb-3">Navegação</h4>
                <ul class="space-y-2 text-xs text-gray-500">
                    <li><a href="<?= BASE_URL ?>/index.php" class="hover:text-beauty-600 transition-colors">Vitrine de Cosméticos</a></li>
                    <li><a href="<?= BASE_URL ?>/carrinho.php" class="hover:text-beauty-600 transition-colors">Minha Sacola</a></li>
                    <li><a href="<?= BASE_URL ?>/login.php" class="hover:text-beauty-600 transition-colors">Acessar Conta</a></li>
                    <li><a href="<?= BASE_URL ?>/cadastro.php" class="hover:text-beauty-600 transition-colors">Criar Cadastro</a></li>
                </ul>
            </div>

            <div>
                <h4 class="text-xs font-bold text-gray-900 uppercase tracking-wider mb-3">Atendimento</h4>
                <p class="text-xs text-gray-500 mb-3">Dúvidas sobre tons, skincare ou pedidos? Fale com nossas consultoras.</p>
                <a href="https://wa.me/<?= WHATSAPP_LOJA ?>?text=Olá!%20Gostaria%20de%20atendimento%20na%20Beauty%20Girls." target="_blank" class="inline-flex items-center space-x-2 px-4 py-2 rounded-xl bg-emerald-50 text-emerald-700 hover:bg-emerald-100 text-xs font-bold transition-colors">
                    <span>Chamar no WhatsApp</span>
                </a>
            </div>
        </div>

        <div class="border-t border-rose-50 mt-8 pt-6 flex flex-col sm:flex-row items-center justify-between gap-2 text-[11px] text-gray-400">
            <p>&copy; <?= date('Y') ?> <?= NOME_LOJA ?>. Todos os direitos reservados.</p>
            <p>Plataforma segura com criptografia BCRYPT e auditoria contínua.</p>
        </div>
    </div>
</footer>

<script src="<?= BASE_URL ?>/assets/js/main.js"></script>
<?php
if (function_exists('render_toast')) {
    render_toast();
}
?>
</body>
</html>

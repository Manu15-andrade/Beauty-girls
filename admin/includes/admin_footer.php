<footer class="bg-white border-t border-rose-100 mt-auto py-5">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between text-[11px] text-gray-400 gap-2">
        <p>&copy; <?= date('Y') ?> <?= NOME_LOJA ?> — Painel Administrativo Seguro</p>
        <p>Logado como: <strong class="text-gray-600"><?= htmlspecialchars($usuarioAdmin['nome'] ?? 'Admin') ?></strong></p>
    </div>
</footer>

<?php
if (function_exists('render_toast')) {
    render_toast();
}
?>
</body>
</html>

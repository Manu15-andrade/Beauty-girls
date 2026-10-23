<?php
/**
 * Beauty Girls - Página da Sacola de Compras / Carrinho
 * Requisitos:
 * - MobileFirst e Tailwind CSS
 * - Confirmação via Modal customizado (sem confirm nativo)
 * - Envio direto do pedido para o WhatsApp
 */

require_once __DIR__ . '/config/constantes.php';
require_once __DIR__ . '/config/bd.php';
$page_title = 'Minha Sacola | ' . NOME_LOJA;
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<main class="flex-1 py-8 sm:py-12 bg-[#fcfaf8]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Breadcrumb & Título -->
        <div class="mb-8">
            <nav class="flex items-center space-x-2 text-xs text-gray-400 mb-2">
                <a href="<?= BASE_URL ?>/index.php" class="hover:text-beauty-600 transition-colors">Início</a>
                <span>/</span>
                <span class="text-gray-700 font-semibold">Minha Sacola</span>
            </nav>
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <h1 class="font-serif text-2xl sm:text-3xl font-bold text-gray-900">
                    Sua Sacola de Cosméticos
                </h1>
                <button 
                    type="button" 
                    id="btnLimparCarrinho"
                    onclick="confirmarLimparCarrinho()"
                    class="hidden self-start sm:self-auto text-xs font-semibold text-rose-600 hover:text-rose-800 transition-colors flex items-center"
                >
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Esvaziar Sacola
                </button>
            </div>
        </div>

        <!-- Se o carrinho estiver vazio -->
        <div id="carrinhoVazio" class="hidden text-center py-16 bg-white rounded-3xl border border-rose-100 shadow-sm p-8">
            <div class="w-20 h-20 rounded-full bg-rose-50 text-beauty-500 mx-auto flex items-center justify-center mb-4">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
            </div>
            <h2 class="font-serif text-xl font-bold text-gray-800">Sua sacola está vazia</h2>
            <p class="text-xs text-gray-500 max-w-sm mx-auto mt-1 mb-6">Explore nossos cosméticos incríveis e escolha seus produtos favoritos para realçar sua beleza.</p>
            <a href="<?= BASE_URL ?>/index.php" class="inline-flex px-6 py-3 rounded-2xl bg-gradient-to-r from-beauty-600 to-rose-600 text-white text-xs font-semibold shadow-lg shadow-rose-200 hover:shadow-xl transition-all">
                Explorar Catálogo Agora
            </a>
        </div>

        <!-- Conteúdo do Carrinho (Itens + Resumo) -->
        <div id="carrinhoConteudo" class="hidden grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- Lista de Itens -->
            <div class="lg:col-span-8 space-y-4">
                
                <!-- Barra de Progresso de Frete Grátis -->
                <div class="bg-white p-4 rounded-3xl border border-rose-100 shadow-sm">
                    <div class="flex items-center justify-between text-xs mb-2">
                        <span id="freteGratisTexto" class="font-medium text-gray-700"></span>
                        <span id="freteGratisPercent" class="font-bold text-beauty-600"></span>
                    </div>
                    <div class="w-full bg-gray-100 h-2.5 rounded-full overflow-hidden">
                        <div id="freteGratisBarra" class="bg-gradient-to-r from-rose-400 to-beauty-600 h-full rounded-full transition-all duration-500" style="width: 0%"></div>
                    </div>
                </div>

                <!-- Container dos Itens -->
                <div id="listaItens" class="space-y-3">
                    <!-- Renderizado dinamicamente via JS -->
                </div>

            </div>

            <!-- Resumo do Pedido e Finalização via WhatsApp -->
            <div class="lg:col-span-4 bg-white rounded-3xl border border-rose-100 shadow-lg p-6 space-y-5 sticky top-28">
                <h3 class="font-serif text-lg font-bold text-gray-900 border-b border-rose-50 pb-3">
                    Resumo do Pedido
                </h3>

                <div class="space-y-2 text-xs">
                    <div class="flex justify-between text-gray-600">
                        <span>Subtotal dos cosméticos</span>
                        <span id="resumoSubtotal" class="font-semibold text-gray-800">R$ 0,00</span>
                    </div>
                    <div class="flex justify-between text-gray-600">
                        <span>Entrega / Frete</span>
                        <span id="resumoFrete" class="font-semibold text-emerald-600">A combinar</span>
                    </div>
                    <div class="border-t border-rose-100 pt-3 flex justify-between text-base font-extrabold text-gray-900">
                        <span>Total</span>
                        <span id="resumoTotal" class="text-beauty-700">R$ 0,00</span>
                    </div>
                </div>

                <!-- Formulário de Finalização Rápida -->
                <div class="border-t border-rose-100 pt-4 space-y-3">
                    <h4 class="text-xs font-bold text-gray-800 uppercase tracking-wide">Dados para Entrega</h4>
                    
                    <div>
                        <label class="block text-[11px] font-semibold text-gray-600 mb-1">Seu Nome Completo *</label>
                        <input 
                            type="text" 
                            id="clienteNome" 
                            placeholder="Ex: Amanda Silva"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-xs focus:ring-2 focus:ring-beauty-400 focus:outline-none"
                            value="<?= htmlspecialchars(usuario_atual()['nome'] ?? '') ?>"
                        >
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-gray-600 mb-1">Endereço de Entrega *</label>
                        <input 
                            type="text" 
                            id="clienteEndereco" 
                            placeholder="Rua, número, bairro e cidade"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-xs focus:ring-2 focus:ring-beauty-400 focus:outline-none"
                        >
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-gray-600 mb-1">Observações (opcional)</label>
                        <textarea 
                            id="clienteObs" 
                            rows="2" 
                            placeholder="Ex: Entregar à tarde, tonalidade do batom, etc."
                            class="w-full px-3.5 py-2 rounded-xl border border-gray-200 text-xs focus:ring-2 focus:ring-beauty-400 focus:outline-none resize-none"
                        ></textarea>
                    </div>

                    <button 
                        type="button" 
                        onclick="enviarPedidoWhatsApp()"
                        class="w-full py-3.5 px-4 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold text-xs shadow-lg shadow-emerald-200 transition-all flex items-center justify-center space-x-2"
                    >
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
                        <span>Finalizar Pedido no WhatsApp</span>
                    </button>
                    <p class="text-[10px] text-gray-400 text-center">Você será direcionada para falar com nossa atendente.</p>
                </div>

            </div>

        </div>

    </div>
</main>

<script>
    function renderizarCarrinhoPagina() {
        const items = Cart.getItems();
        const vazioEl = document.getElementById('carrinhoVazio');
        const conteudoEl = document.getElementById('carrinhoConteudo');
        const btnLimpar = document.getElementById('btnLimparCarrinho');
        const listaEl = document.getElementById('listaItens');

        if (!items || items.length === 0) {
            vazioEl.classList.remove('hidden');
            conteudoEl.classList.add('hidden');
            btnLimpar.classList.add('hidden');
            return;
        }

        vazioEl.classList.add('hidden');
        conteudoEl.classList.remove('hidden');
        btnLimpar.classList.remove('hidden');

        let html = '';
        const baseUrl = window.APP_CONFIG.baseUrl;

        items.forEach(item => {
            const precoEfetivo = item.preco_promocional || item.preco;
            const subtotal = precoEfetivo * item.quantidade;
            const img = item.imagem ? `${baseUrl}/${item.imagem}` : 'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?w=800&q=80';

            html += `
            <div class="bg-white p-4 sm:p-5 rounded-3xl border border-rose-100 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center space-x-4 w-full sm:w-auto">
                    <img src="${img}" alt="${item.nome}" class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl object-cover bg-rose-50 flex-shrink-0">
                    <div class="flex-1">
                        <h4 class="font-serif font-bold text-sm sm:text-base text-gray-900 leading-snug">${item.nome}</h4>
                        <p class="text-xs text-beauty-600 font-extrabold mt-0.5">${Cart.formatMoney(precoEfetivo)} un.</p>
                        <p class="text-[10px] text-gray-400">Estoque restante: ${item.estoque}</p>
                    </div>
                </div>

                <div class="flex items-center justify-between w-full sm:w-auto space-x-6 border-t sm:border-t-0 pt-3 sm:pt-0 border-rose-50">
                    <!-- Controle de Quantidade -->
                    <div class="flex items-center border border-gray-200 rounded-xl overflow-hidden bg-gray-50">
                        <button type="button" onclick="alterarQtdCarrinho(${item.id}, ${item.quantidade - 1})" class="px-2.5 py-1 text-gray-600 hover:bg-gray-200 font-bold text-xs">-</button>
                        <span class="w-10 text-center text-xs font-semibold text-gray-800">${item.quantidade}</span>
                        <button type="button" onclick="alterarQtdCarrinho(${item.id}, ${item.quantidade + 1})" class="px-2.5 py-1 text-gray-600 hover:bg-gray-200 font-bold text-xs">+</button>
                    </div>

                    <!-- Subtotal do Item -->
                    <div class="text-right min-w-[90px]">
                        <span class="text-xs text-gray-400 block sm:hidden">Total item:</span>
                        <span class="text-sm font-extrabold text-gray-900">${Cart.formatMoney(subtotal)}</span>
                    </div>

                    <!-- Remover Item (Substitui confirm nativo pelo UniversalModal) -->
                    <button 
                        type="button" 
                        onclick="confirmarRemoverItem(${item.id}, '${item.nome.replace(/'/g, "\\'")}')"
                        class="text-gray-400 hover:text-red-500 transition-colors p-1"
                        title="Remover produto"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </div>
            </div>`;
        });

        listaEl.innerHTML = html;

        // Atualiza Totais
        const total = Cart.getTotal();
        document.getElementById('resumoSubtotal').textContent = Cart.formatMoney(total);
        document.getElementById('resumoTotal').textContent = Cart.formatMoney(total);

        // Barra de Frete Grátis
        const freteAlvo = <?= FRETE_GRATIS_VALOR ?>;
        const faltam = freteAlvo - total;
        const textoFrete = document.getElementById('freteGratisTexto');
        const percentFrete = document.getElementById('freteGratisPercent');
        const barraFrete = document.getElementById('freteGratisBarra');
        const freteResumo = document.getElementById('resumoFrete');

        if (faltam <= 0) {
            textoFrete.innerHTML = '🎉 <strong>Parabéns!</strong> Você ganhou <strong>FRETE GRÁTIS</strong>!';
            percentFrete.textContent = '100%';
            barraFrete.style.width = '100%';
            barraFrete.className = 'bg-emerald-500 h-full rounded-full transition-all duration-500';
            freteResumo.textContent = 'GRÁTIS';
            freteResumo.className = 'font-bold text-emerald-600';
        } else {
            const perc = Math.min(100, Math.round((total / freteAlvo) * 100));
            textoFrete.innerHTML = `Faltam <strong>${Cart.formatMoney(faltam)}</strong> para <strong>Frete Grátis</strong>`;
            percentFrete.textContent = `${perc}%`;
            barraFrete.style.width = `${perc}%`;
            barraFrete.className = 'bg-gradient-to-r from-rose-400 to-beauty-600 h-full rounded-full transition-all duration-500';
            freteResumo.textContent = 'A combinar';
            freteResumo.className = 'font-semibold text-gray-600';
        }
    }

    function alterarQtdCarrinho(id, novaQtd) {
        Cart.updateQty(id, novaQtd);
        renderizarCarrinhoPagina();
    }

    function confirmarRemoverItem(id, nome) {
        window.customConfirm(
            'Remover Cosmético',
            `Deseja realmente retirar o produto <strong>"${nome}"</strong> da sua sacola?`,
            () => {
                Cart.removeItem(id);
                renderizarCarrinhoPagina();
            },
            true
        );
    }

    function confirmarLimparCarrinho() {
        window.customConfirm(
            'Esvaziar Sacola',
            'Tem certeza de que deseja remover todos os produtos da sua sacola?',
            () => {
                Cart.clear();
                renderizarCarrinhoPagina();
                if (window.showToast) {
                    window.showToast('info', 'Sacola Esvaziada', 'Todos os itens foram retirados com sucesso.');
                }
            },
            true
        );
    }

    function enviarPedidoWhatsApp() {
        const nome = document.getElementById('clienteNome').value.trim();
        const endereco = document.getElementById('clienteEndereco').value.trim();
        const obs = document.getElementById('clienteObs').value.trim();

        if (!nome) {
            window.customAlert('Atenção', 'Por favor, informe o seu nome para identificação do pedido.');
            return;
        }

        if (!endereco) {
            window.customAlert('Atenção', 'Por favor, informe o endereço de entrega para envio dos produtos.');
            return;
        }

        const url = Cart.generateWhatsAppUrl(nome, endereco, obs);
        if (url) {
            window.open(url, '_blank');
        } else {
            window.customAlert('Sacola Vazia', 'Adicione pelo menos um produto antes de finalizar o pedido.');
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        renderizarCarrinhoPagina();
    });

    window.addEventListener('cartUpdated', () => {
        renderizarCarrinhoPagina();
    });
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

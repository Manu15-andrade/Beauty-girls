<?php
/**
 * Beauty Girls - Vitrine Principal / Catálogo Responsivo
 * Requisitos:
 * - Catálogo responsivo de cosméticos
 * - MobileFirst em Tailwind CSS
 * - Uso de PDO para segurança total
 * - Modais e Toasts interativos
 */

require_once __DIR__ . '/config/constantes.php';
require_once __DIR__ . '/config/bd.php';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

// Filtros da vitrine
$categoria_slug = $_GET['categoria'] ?? null;
$busca = trim($_GET['busca'] ?? '');
$ordem = $_GET['ordem'] ?? 'novidades';

// Buscar Categorias Ativas
$categorias = [];
if ($pdo) {
    try {
        $stmtCats = $pdo->query("SELECT * FROM `categorias` ORDER BY `nome` ASC");
        $categorias = $stmtCats->fetchAll();
    } catch (PDOException $e) {
        error_log("Erro ao carregar categorias: " . $e->getMessage());
    }
}

// Montar Query de Produtos com PDO Prepared Statements
$produtos = [];
if ($pdo) {
    try {
        $sql = "SELECT p.*, c.nome AS categoria_nome, c.slug AS categoria_slug 
                FROM `produtos` p 
                LEFT JOIN `categorias` c ON p.categoria_id = c.id 
                WHERE p.ativo = 1";
        $params = [];

        if (!empty($categoria_slug)) {
            $sql .= " AND c.slug = :categoria_slug";
            $params[':categoria_slug'] = $categoria_slug;
        }

        if (!empty($busca)) {
            $sql .= " AND (p.nome LIKE :busca OR p.descricao LIKE :buscaDesc)";
            $params[':busca'] = "%{$busca}%";
            $params[':buscaDesc'] = "%{$busca}%";
        }

        switch ($ordem) {
            case 'preco_asc':
                $sql .= " ORDER BY COALESCE(p.preco_promocional, p.preco) ASC";
                break;
            case 'preco_desc':
                $sql .= " ORDER BY COALESCE(p.preco_promocional, p.preco) DESC";
                break;
            case 'nome':
                $sql .= " ORDER BY p.nome ASC";
                break;
            case 'destaques':
                $sql .= " ORDER BY p.destaque DESC, p.id DESC";
                break;
            case 'novidades':
            default:
                $sql .= " ORDER BY p.id DESC";
                break;
        }

        $stmtProds = $pdo->prepare($sql);
        $stmtProds->execute($params);
        $produtos = $stmtProds->fetchAll();
    } catch (PDOException $e) {
        error_log("Erro ao buscar produtos: " . $e->getMessage());
    }
}
?>

<main class="flex-1">

    <!-- HERO BANNER Mobile-First Glamour -->
    <section class="relative overflow-hidden bg-gradient-to-b from-rose-100/60 via-pink-50/40 to-[#fcfaf8] py-10 sm:py-16 lg:py-20">
        <!-- Elementos decorativos de fundo -->
        <div class="absolute -top-24 -right-24 w-96 h-96 rounded-full bg-rose-200/50 blur-3xl pointer-events-none"></div>
        <div class="absolute top-1/2 -left-20 w-80 h-80 rounded-full bg-amber-100/60 blur-3xl pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                
                <!-- Textos Principais -->
                <div class="lg:col-span-7 text-center lg:text-left space-y-5">
                    <div class="inline-flex items-center space-x-2 px-3.5 py-1.5 rounded-full bg-white/80 border border-rose-200 shadow-sm backdrop-blur-sm">
                        <span class="w-2 h-2 rounded-full bg-beauty-600 animate-ping"></span>
                        <span class="text-xs font-bold text-beauty-700 tracking-wide uppercase">Nova Coleção de Cosméticos</span>
                    </div>

                    <h1 class="font-serif text-3xl sm:text-5xl lg:text-6xl font-bold tracking-tight text-gray-900 leading-tight">
                        Realce a sua beleza natural com <span class="text-gradient">Beauty Girls</span>
                    </h1>

                    <p class="text-sm sm:text-base text-gray-600 max-w-xl mx-auto lg:mx-0 leading-relaxed">
                        Encontre maquiagens de alta pigmentação, séruns de hidratação profunda e acessórios profissionais para cuidar da sua pele e elevar sua autoestima todos os dias.
                    </p>

                    <!-- CTAs Mobile-First -->
                    <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-3 pt-2">
                        <a href="#catalogo" class="w-full sm:w-auto inline-flex items-center justify-center px-7 py-3.5 rounded-2xl bg-gradient-to-r from-beauty-600 via-rose-500 to-beauty-700 text-white font-semibold text-sm shadow-xl shadow-rose-200 hover:shadow-2xl hover:scale-[1.02] transition-all">
                            <span>Ver Produtos</span>
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </a>
                        <a href="https://wa.me/<?= WHATSAPP_LOJA ?>?text=Olá!%20Gostaria%20de%20tirar%20uma%20dúvida%20sobre%20os%20cosméticos%20da%20Beauty%20Girls." target="_blank" class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3.5 rounded-2xl bg-white/90 border border-emerald-300 text-emerald-700 font-semibold text-sm shadow-sm hover:bg-emerald-50 transition-all">
                            <svg class="w-4 h-4 mr-2 text-emerald-500" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
                            <span>Atendimento VIP</span>
                        </a>
                    </div>
                </div>

                <!-- Imagem Destaque Hero -->
                <div class="lg:col-span-5 relative mt-6 lg:mt-0">
                    <div class="relative mx-auto max-w-sm sm:max-w-md">
                        <div class="aspect-[4/5] rounded-3xl overflow-hidden shadow-2xl border-4 border-white bg-rose-50 transform hover:scale-[1.01] transition-all">
                            <img 
                                src="<?= BASE_URL ?>/assets/images/produtos/produto-1.jpg" 
                                alt="Beauty Girls Cosméticos" 
                                class="w-full h-full object-cover object-center"
                                onerror="this.src='https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?w=800&q=80'"
                            >
                        </div>
                        <!-- Card Flutuante 1 -->
                        <div class="absolute -bottom-4 -left-4 sm:-left-6 bg-white/95 backdrop-blur-md p-3.5 rounded-2xl shadow-xl border border-rose-100 flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-xl bg-beauty-100 text-beauty-600 flex items-center justify-center font-bold">
                                ✨
                            </div>
                            <div>
                                <p class="text-xs font-bold text-gray-800">Cruelty-Free</p>
                                <p class="text-[10px] text-gray-500">100% Livre de crueldade</p>
                            </div>
                        </div>
                        <!-- Card Flutuante 2 -->
                        <div class="absolute -top-4 -right-4 sm:-right-6 bg-white/95 backdrop-blur-md p-3.5 rounded-2xl shadow-xl border border-rose-100 flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center font-bold">
                                💖
                            </div>
                            <div>
                                <p class="text-xs font-bold text-gray-800">+10.000 Clientes</p>
                                <p class="text-[10px] text-gray-500">Amam nossos produtos</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- BARRA DE DIFERENCIAIS -->
    <section class="border-y border-rose-100 bg-white py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6 text-center">
                <div class="flex flex-col items-center p-2">
                    <div class="w-10 h-10 rounded-xl bg-rose-50 text-beauty-600 flex items-center justify-center mb-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h4 class="text-xs font-bold text-gray-800">Entrega Ágil</h4>
                    <p class="text-[11px] text-gray-500">Envio seguro para sua residência</p>
                </div>
                <div class="flex flex-col items-center p-2">
                    <div class="w-10 h-10 rounded-xl bg-rose-50 text-beauty-600 flex items-center justify-center mb-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    </div>
                    <h4 class="text-xs font-bold text-gray-800">Qualidade Garantida</h4>
                    <p class="text-[11px] text-gray-500">Produtos dermatologicamente testados</p>
                </div>
                <div class="flex flex-col items-center p-2">
                    <div class="w-10 h-10 rounded-xl bg-rose-50 text-beauty-600 flex items-center justify-center mb-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                    </div>
                    <h4 class="text-xs font-bold text-gray-800">Pagamento Flexível</h4>
                    <p class="text-[11px] text-gray-500">PIX com desconto e cartão</p>
                </div>
                <div class="flex flex-col items-center p-2">
                    <div class="w-10 h-10 rounded-xl bg-rose-50 text-beauty-600 flex items-center justify-center mb-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                    </div>
                    <h4 class="text-xs font-bold text-gray-800">Suporte Dedicado</h4>
                    <p class="text-[11px] text-gray-500">Consultoras prontas no WhatsApp</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CATÁLOGO PRINCIPAL DE PRODUTOS -->
    <section id="catalogo" class="py-12 sm:py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Cabeçalho do Catálogo com Filtros -->
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-8 gap-4">
                <div>
                    <h2 class="font-serif text-2xl sm:text-4xl font-bold text-gray-900">
                        Catálogo de Cosméticos
                    </h2>
                    <p class="text-xs sm:text-sm text-gray-500 mt-1">
                        Explore as melhores opções para sua rotina de beleza
                    </p>
                </div>

                <!-- Ordenação -->
                <div class="flex items-center space-x-2 self-start md:self-auto">
                    <label for="ordemSelect" class="text-xs font-medium text-gray-500 whitespace-nowrap">Ordenar por:</label>
                    <select 
                        id="ordemSelect" 
                        onchange="location.href = this.value;"
                        class="text-xs rounded-xl border border-gray-200 bg-white py-2 px-3 text-gray-700 focus:outline-none focus:ring-2 focus:ring-beauty-400"
                    >
                        <option value="<?= BASE_URL ?>/index.php?categoria=<?= urlencode($categoria_slug ?? '') ?>&busca=<?= urlencode($busca) ?>&ordem=novidades" <?= $ordem === 'novidades' ? 'selected' : '' ?>>Novidades</option>
                        <option value="<?= BASE_URL ?>/index.php?categoria=<?= urlencode($categoria_slug ?? '') ?>&busca=<?= urlencode($busca) ?>&ordem=destaques" <?= $ordem === 'destaques' ? 'selected' : '' ?>>Destaques</option>
                        <option value="<?= BASE_URL ?>/index.php?categoria=<?= urlencode($categoria_slug ?? '') ?>&busca=<?= urlencode($busca) ?>&ordem=preco_asc" <?= $ordem === 'preco_asc' ? 'selected' : '' ?>>Menor Preço</option>
                        <option value="<?= BASE_URL ?>/index.php?categoria=<?= urlencode($categoria_slug ?? '') ?>&busca=<?= urlencode($busca) ?>&ordem=preco_desc" <?= $ordem === 'preco_desc' ? 'selected' : '' ?>>Maior Preço</option>
                        <option value="<?= BASE_URL ?>/index.php?categoria=<?= urlencode($categoria_slug ?? '') ?>&busca=<?= urlencode($busca) ?>&ordem=nome" <?= $ordem === 'nome' ? 'selected' : '' ?>>Nome (A-Z)</option>
                    </select>
                </div>
            </div>

            <!-- CHIPS DE CATEGORIAS (Rolagem Horizontal Suave no Mobile) -->
            <div id="categorias" class="flex items-center space-x-2 overflow-x-auto pb-4 mb-8 scrollbar-none no-scrollbar">
                <a 
                    href="<?= BASE_URL ?>/index.php" 
                    class="px-4 py-2 rounded-2xl text-xs font-semibold whitespace-nowrap transition-all <?= empty($categoria_slug) ? 'bg-beauty-600 text-white shadow-md shadow-rose-200' : 'bg-white text-gray-600 border border-gray-200 hover:border-beauty-300' ?>"
                >
                    Todos os Produtos
                </a>
                <?php foreach ($categorias as $cat): ?>
                    <a 
                        href="<?= BASE_URL ?>/index.php?categoria=<?= urlencode($cat['slug']) ?>" 
                        class="px-4 py-2 rounded-2xl text-xs font-semibold whitespace-nowrap transition-all <?= ($categoria_slug === $cat['slug']) ? 'bg-beauty-600 text-white shadow-md shadow-rose-200' : 'bg-white text-gray-600 border border-gray-200 hover:border-beauty-300' ?>"
                    >
                        <?= htmlspecialchars($cat['nome']) ?>
                    </a>
                <?php endforeach; ?>
            </div>

            <!-- Aviso se houver termo de busca -->
            <?php if (!empty($busca)): ?>
                <div class="mb-6 flex items-center justify-between p-3.5 bg-rose-50/70 border border-rose-200 rounded-2xl text-xs text-rose-800">
                    <span>Resultados para a pesquisa: <strong>"<?= htmlspecialchars($busca) ?>"</strong> (<?= count($produtos) ?> encontrado(s))</span>
                    <a href="<?= BASE_URL ?>/index.php" class="font-bold underline hover:text-rose-950">Limpar busca</a>
                </div>
            <?php endif; ?>

            <!-- GRID RESPONSIVO DE PRODUTOS -->
            <?php if (empty($produtos)): ?>
                <div class="text-center py-16 bg-white rounded-3xl border border-gray-100 shadow-sm p-8">
                    <div class="w-16 h-16 rounded-full bg-rose-50 text-beauty-500 mx-auto flex items-center justify-center mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                    </div>
                    <h3 class="font-serif text-lg font-bold text-gray-800">Nenhum cosmético encontrado</h3>
                    <p class="text-xs text-gray-500 max-w-sm mx-auto mt-1 mb-5">Não encontramos nenhum item com os filtros selecionados. Tente buscar por outros termos.</p>
                    <a href="<?= BASE_URL ?>/index.php" class="inline-flex px-5 py-2.5 rounded-xl bg-beauty-600 text-white text-xs font-semibold shadow hover:bg-beauty-700 transition-colors">
                        Ver Todos os Cosméticos
                    </a>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
                    <?php foreach ($produtos as $p): 
                        $preco = (float)$p['preco'];
                        $precoPromo = $p['preco_promocional'] ? (float)$p['preco_promocional'] : null;
                        $temPromo = ($precoPromo !== null && $precoPromo < $preco);
                        $precoFinal = $temPromo ? $precoPromo : $preco;
                        $esgotado = ($p['estoque'] <= 0);
                        $estoqueBaixo = (!$esgotado && $p['estoque'] <= $p['estoque_minimo']);
                        $imgUrl = !empty($p['imagem']) ? BASE_URL . '/' . $p['imagem'] : 'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?w=800&q=80';
                    ?>
                        <div class="group flex flex-col bg-white rounded-3xl border border-rose-100/70 shadow-sm hover:shadow-xl hover:border-rose-200 transition-all duration-300 overflow-hidden relative">
                            
                            <!-- Badges Superiores -->
                            <div class="absolute top-3 left-3 right-3 flex justify-between items-start z-10 pointer-events-none">
                                <div class="flex flex-col space-y-1">
                                    <?php if ($esgotado): ?>
                                        <span class="px-2.5 py-1 rounded-full bg-gray-900/90 backdrop-blur-sm text-white text-[10px] font-bold tracking-wider uppercase">
                                            Esgotado
                                        </span>
                                    <?php elseif ($estoqueBaixo): ?>
                                        <span class="px-2.5 py-1 rounded-full bg-amber-500/95 backdrop-blur-sm text-white text-[10px] font-bold tracking-wider uppercase badge-pulse">
                                            Últimas <?= $p['estoque'] ?> un.
                                        </span>
                                    <?php elseif ($temPromo): ?>
                                        <span class="px-2.5 py-1 rounded-full bg-beauty-600/95 backdrop-blur-sm text-white text-[10px] font-bold tracking-wider uppercase">
                                            Oferta
                                        </span>
                                    <?php endif; ?>

                                    <?php if ($p['destaque']): ?>
                                        <span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 text-[9px] font-bold uppercase w-max">
                                            ★ Destaque
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Imagem do Produto -->
                            <div class="relative aspect-square w-full bg-rose-50/40 overflow-hidden cursor-pointer" onclick="abrirModalProduto(<?= htmlspecialchars(json_encode($p), ENT_QUOTES, 'UTF-8') ?>)">
                                <img 
                                    src="<?= $imgUrl ?>" 
                                    alt="<?= htmlspecialchars($p['nome']) ?>"
                                    class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-500 <?= $esgotado ? 'opacity-60 grayscale' : '' ?>"
                                    onerror="this.src='https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?w=800&q=80'"
                                >
                                <!-- Botão rápido de visualização no hover -->
                                <div class="absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center p-4">
                                    <span class="px-3 py-1.5 rounded-xl bg-white/90 backdrop-blur-sm text-gray-800 text-xs font-semibold shadow">
                                        Espiar Detalhes
                                    </span>
                                </div>
                            </div>

                            <!-- Conteúdo do Card -->
                            <div class="p-4 sm:p-5 flex-1 flex flex-col justify-between">
                                <div>
                                    <span class="text-[10px] sm:text-xs font-medium text-beauty-600 tracking-wider uppercase block">
                                        <?= htmlspecialchars($p['categoria_nome'] ?? 'Cosmético') ?>
                                    </span>
                                    <h3 
                                        onclick="abrirModalProduto(<?= htmlspecialchars(json_encode($p), ENT_QUOTES, 'UTF-8') ?>)"
                                        class="font-serif text-sm sm:text-base font-bold text-gray-900 group-hover:text-beauty-600 transition-colors line-clamp-2 mt-1 cursor-pointer leading-snug"
                                    >
                                        <?= htmlspecialchars($p['nome']) ?>
                                    </h3>
                                </div>

                                <div class="mt-4 pt-3 border-t border-rose-50 flex flex-col sm:flex-row sm:items-baseline justify-between gap-1">
                                    <div>
                                        <?php if ($temPromo): ?>
                                            <span class="text-[11px] text-gray-400 line-through block">
                                                R$ <?= number_format($preco, 2, ',', '.') ?>
                                            </span>
                                        <?php endif; ?>
                                        <span class="text-base sm:text-lg font-extrabold text-gray-900">
                                            R$ <?= number_format($precoFinal, 2, ',', '.') ?>
                                        </span>
                                    </div>
                                    <span class="text-[10px] text-gray-400">
                                        <?= $esgotado ? '<span class="text-red-500 font-semibold">Sem estoque</span>' : ($p['estoque'] . ' em estoque') ?>
                                    </span>
                                </div>

                                <!-- Botão Adicionar à Sacola -->
                                <div class="mt-3">
                                    <?php if ($esgotado): ?>
                                        <button 
                                            type="button" 
                                            disabled 
                                            class="w-full py-2.5 px-3 rounded-2xl bg-gray-100 text-gray-400 text-xs font-semibold cursor-not-allowed text-center"
                                        >
                                            Indisponível
                                        </button>
                                    <?php else: ?>
                                        <button 
                                            type="button" 
                                            onclick="adicionarAoCarrinhoCard(<?= htmlspecialchars(json_encode($p), ENT_QUOTES, 'UTF-8') ?>)"
                                            class="w-full py-2.5 px-3 rounded-2xl bg-rose-50 hover:bg-beauty-600 text-beauty-700 hover:text-white text-xs font-semibold transition-all flex items-center justify-center space-x-1.5 shadow-sm active:scale-95"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                            <span>Adicionar à Sacola</span>
                                        </button>
                                    <?php endif; ?>
                                </div>

                            </div>

                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

        </div>
    </section>

</main>

<!-- MODAL DE VISUALIZAÇÃO RÁPIDA DO PRODUTO (Substitui navegação pesada) -->
<div id="modalVisualizarProduto" class="fixed inset-0 z-50 hidden overflow-y-auto" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity opacity-0" id="prodModalBackdrop" onclick="fecharModalProduto()"></div>

    <div class="flex min-h-screen items-center justify-center p-4 text-center sm:p-0">
        <div class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-2xl scale-95 opacity-0 border border-rose-100" id="prodModalCard">
            
            <button onclick="fecharModalProduto()" class="absolute top-4 right-4 z-10 w-9 h-9 rounded-full bg-white/90 text-gray-500 hover:text-gray-900 shadow flex items-center justify-center transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>

            <div class="grid grid-cols-1 sm:grid-cols-2">
                <!-- Imagem no Modal -->
                <div class="aspect-square bg-rose-50 flex items-center justify-center overflow-hidden">
                    <img id="modalProdImg" src="" alt="" class="w-full h-full object-cover">
                </div>

                <!-- Detalhes no Modal -->
                <div class="p-6 sm:p-7 flex flex-col justify-between">
                    <div>
                        <span id="modalProdCat" class="text-xs font-semibold text-beauty-600 uppercase tracking-wider block"></span>
                        <h3 id="modalProdNome" class="font-serif text-xl sm:text-2xl font-bold text-gray-900 mt-1 leading-snug"></h3>
                        
                        <div class="mt-3 flex items-baseline space-x-2">
                            <span id="modalProdPreco" class="text-2xl font-extrabold text-gray-900"></span>
                            <span id="modalProdPrecoAntigo" class="text-sm text-gray-400 line-through hidden"></span>
                        </div>

                        <p id="modalProdEstoque" class="text-xs text-emerald-600 font-semibold mt-1"></p>

                        <div class="mt-4 border-t border-gray-100 pt-3">
                            <h4 class="text-xs font-bold text-gray-700 uppercase">Descrição:</h4>
                            <p id="modalProdDesc" class="text-xs text-gray-600 mt-1 leading-relaxed max-h-36 overflow-y-auto pr-1"></p>
                        </div>

                        <div id="modalProdModoBox" class="mt-3 border-t border-gray-100 pt-2 hidden">
                            <h4 class="text-xs font-bold text-gray-700 uppercase">Modo de Uso:</h4>
                            <p id="modalProdModo" class="text-xs text-gray-500 mt-1 italic"></p>
                        </div>
                    </div>

                    <div class="mt-6 pt-4 border-t border-gray-100 space-y-3">
                        <div class="flex items-center space-x-3">
                            <label class="text-xs font-medium text-gray-600">Qtd:</label>
                            <div class="flex items-center border border-gray-200 rounded-xl overflow-hidden bg-gray-50">
                                <button type="button" onclick="alterarQtdModal(-1)" class="px-3 py-1.5 text-gray-600 hover:bg-gray-200 font-bold">-</button>
                                <input type="number" id="modalProdQtd" value="1" min="1" class="w-12 text-center text-xs bg-transparent focus:outline-none" readonly>
                                <button type="button" onclick="alterarQtdModal(1)" class="px-3 py-1.5 text-gray-600 hover:bg-gray-200 font-bold">+</button>
                            </div>
                        </div>

                        <button 
                            type="button" 
                            id="modalBtnComprar"
                            class="w-full py-3 px-4 rounded-2xl bg-gradient-to-r from-beauty-600 to-rose-600 hover:from-beauty-700 hover:to-rose-700 text-white font-semibold text-xs shadow-lg shadow-rose-200 transition-all flex items-center justify-center space-x-2"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                            <span>Adicionar à Sacola</span>
                        </button>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

<script>
    let produtoSelecionadoModal = null;

    function abrirModalProduto(prod) {
        produtoSelecionadoModal = prod;
        const modal = document.getElementById('modalVisualizarProduto');
        const backdrop = document.getElementById('prodModalBackdrop');
        const card = document.getElementById('prodModalCard');

        const baseUrl = window.APP_CONFIG.baseUrl;
        const imgUrl = prod.imagem ? `${baseUrl}/${prod.imagem}` : 'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?w=800&q=80';

        document.getElementById('modalProdImg').src = imgUrl;
        document.getElementById('modalProdCat').textContent = prod.categoria_nome || 'Cosmético';
        document.getElementById('modalProdNome').textContent = prod.nome;
        document.getElementById('modalProdDesc').textContent = prod.descricao || 'Sem descrição cadastrada.';

        const precoFinal = prod.preco_promocional ? parseFloat(prod.preco_promocional) : parseFloat(prod.preco);
        document.getElementById('modalProdPreco').textContent = Cart.formatMoney(precoFinal);

        const precoAntigoEl = document.getElementById('modalProdPrecoAntigo');
        if (prod.preco_promocional && parseFloat(prod.preco_promocional) < parseFloat(prod.preco)) {
            precoAntigoEl.textContent = Cart.formatMoney(prod.preco);
            precoAntigoEl.classList.remove('hidden');
        } else {
            precoAntigoEl.classList.add('hidden');
        }

        const estoqueEl = document.getElementById('modalProdEstoque');
        const btnComprar = document.getElementById('modalBtnComprar');
        const qtdInput = document.getElementById('modalProdQtd');
        qtdInput.value = 1;

        if (prod.estoque <= 0) {
            estoqueEl.textContent = 'Produto Esgotado';
            estoqueEl.className = 'text-xs text-red-500 font-bold mt-1';
            btnComprar.disabled = true;
            btnComprar.className = 'w-full py-3 px-4 rounded-2xl bg-gray-200 text-gray-400 font-semibold text-xs cursor-not-allowed';
            btnComprar.textContent = 'Produto Indisponível';
        } else {
            estoqueEl.textContent = `Em estoque: ${prod.estoque} unidades disponíveis`;
            estoqueEl.className = 'text-xs text-emerald-600 font-semibold mt-1';
            btnComprar.disabled = false;
            btnComprar.className = 'w-full py-3 px-4 rounded-2xl bg-gradient-to-r from-beauty-600 to-rose-600 hover:from-beauty-700 hover:to-rose-700 text-white font-semibold text-xs shadow-lg shadow-rose-200 transition-all flex items-center justify-center space-x-2';
            btnComprar.innerHTML = '<svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg> Adicionar à Sacola';
            
            btnComprar.onclick = () => {
                const qtd = parseInt(qtdInput.value) || 1;
                const ok = Cart.addItem(prod, qtd);
                if (ok) fecharModalProduto();
            };
        }

        const modoBox = document.getElementById('modalProdModoBox');
        const modoEl = document.getElementById('modalProdModo');
        if (prod.modo_uso) {
            modoEl.textContent = prod.modo_uso;
            modoBox.classList.remove('hidden');
        } else {
            modoBox.classList.add('hidden');
        }

        modal.classList.remove('hidden');
        requestAnimationFrame(() => {
            backdrop.classList.remove('opacity-0');
            backdrop.classList.add('opacity-100');
            card.classList.remove('scale-95', 'opacity-0');
            card.classList.add('scale-100', 'opacity-100');
        });
    }

    function fecharModalProduto() {
        const modal = document.getElementById('modalVisualizarProduto');
        const backdrop = document.getElementById('prodModalBackdrop');
        const card = document.getElementById('prodModalCard');

        backdrop.classList.remove('opacity-100');
        backdrop.classList.add('opacity-0');
        card.classList.remove('scale-100', 'opacity-100');
        card.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 200);
    }

    function alterarQtdModal(delta) {
        const input = document.getElementById('modalProdQtd');
        let val = parseInt(input.value) || 1;
        val += delta;
        if (val < 1) val = 1;
        if (produtoSelecionadoModal && val > produtoSelecionadoModal.estoque) {
            val = produtoSelecionadoModal.estoque;
            if (window.showToast) {
                window.showToast('warning', 'Limite de Estoque', `Máximo disponível: ${produtoSelecionadoModal.estoque}`);
            }
        }
        input.value = val;
    }

    function adicionarAoCarrinhoCard(prod) {
        Cart.addItem(prod, 1);
    }
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

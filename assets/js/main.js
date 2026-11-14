/**
 * Beauty Girls - Lógica da Sacola de Compras (localStorage) & WhatsApp
 */
(function () {
    const STORAGE_KEY = 'beauty_girls_cart';

    const Cart = {
        getItems() {
            try {
                const raw = localStorage.getItem(STORAGE_KEY);
                return raw ? JSON.parse(raw) : [];
            } catch (e) {
                return [];
            }
        },

        saveItems(items) {
            localStorage.setItem(STORAGE_KEY, JSON.stringify(items));
            this.updateBadges();
            window.dispatchEvent(new CustomEvent('cartUpdated', { detail: items }));
        },

        addItem(prod, qty = 1) {
            if (!prod || !prod.id) return false;

            const estoque = parseInt(prod.estoque, 10) || 0;
            if (estoque <= 0) {
                if (window.showToast) {
                    window.showToast('error', 'Produto Esgotado', 'Este cosmético está temporariamente indisponível.');
                }
                return false;
            }

            const items = this.getItems();
            const idx = items.findIndex(item => Number(item.id) === Number(prod.id));
            const qtdAdd = Math.max(1, parseInt(qty, 10) || 1);

            if (idx > -1) {
                const novaQtd = items[idx].quantidade + qtdAdd;
                if (novaQtd > estoque) {
                    items[idx].quantidade = estoque;
                    this.saveItems(items);
                    if (window.showToast) {
                        window.showToast('warning', 'Limite de Estoque', `Você já atingiu o estoque máximo (${estoque} un.) para este item.`);
                    }
                    return true;
                }
                items[idx].quantidade = novaQtd;
            } else {
                const qtdFinal = Math.min(qtdAdd, estoque);
                items.push({
                    id: Number(prod.id),
                    nome: prod.nome,
                    preco: parseFloat(prod.preco),
                    preco_promocional: prod.preco_promocional ? parseFloat(prod.preco_promocional) : null,
                    estoque: estoque,
                    imagem: prod.imagem || '',
                    quantidade: qtdFinal
                });
            }

            this.saveItems(items);
            if (window.showToast) {
                window.showToast('success', 'Adicionado à Sacola', `${prod.nome} foi incluído na sua sacola!`);
            }
            return true;
        },

        updateQty(id, newQty) {
            const items = this.getItems();
            const idx = items.findIndex(item => Number(item.id) === Number(id));
            if (idx === -1) return;

            if (newQty <= 0) {
                items.splice(idx, 1);
                this.saveItems(items);
                return;
            }

            const maxEstoque = parseInt(items[idx].estoque, 10) || 0;
            if (newQty > maxEstoque) {
                items[idx].quantidade = maxEstoque;
                if (window.showToast) {
                    window.showToast('warning', 'Limite de Estoque', `Disponível em estoque: ${maxEstoque} unidades.`);
                }
            } else {
                items[idx].quantidade = newQty;
            }
            this.saveItems(items);
        },

        removeItem(id) {
            const items = this.getItems().filter(item => Number(item.id) !== Number(id));
            this.saveItems(items);
            if (window.showToast) {
                window.showToast('info', 'Item Removido', 'O produto foi removido da sua sacola.');
            }
        },

        clear() {
            localStorage.removeItem(STORAGE_KEY);
            this.updateBadges();
            window.dispatchEvent(new CustomEvent('cartUpdated', { detail: [] }));
        },

        getTotal() {
            return this.getItems().reduce((sum, item) => {
                const preco = item.preco_promocional && item.preco_promocional < item.preco
                    ? item.preco_promocional
                    : item.preco;
                return sum + (preco * item.quantidade);
            }, 0);
        },

        getCount() {
            return this.getItems().reduce((sum, item) => sum + item.quantidade, 0);
        },

        formatMoney(value) {
            const num = parseFloat(value) || 0;
            return num.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
        },

        updateBadges() {
            const count = this.getCount();
            const headerBadge = document.getElementById('cart-badge-header');
            const mobileBadge = document.getElementById('cart-badge-mobile');
            if (headerBadge) headerBadge.textContent = count;
            if (mobileBadge) mobileBadge.textContent = count;
        },

        generateWhatsAppUrl(nome, endereco, obs) {
            const items = this.getItems();
            if (!items.length) return null;

            const whatsapp = (window.APP_CONFIG && window.APP_CONFIG.whatsapp) ? window.APP_CONFIG.whatsapp : '5511999999999';
            const freteAlvo = (window.APP_CONFIG && window.APP_CONFIG.freteGratis) ? window.APP_CONFIG.freteGratis : 150;
            const total = this.getTotal();
            const freteTxt = total >= freteAlvo ? 'GRÁTIS' : 'A combinar';

            let msg = `*Novo Pedido - Beauty Girls*\n\n`;
            msg += `*Cliente:* ${nome}\n`;
            msg += `*Endereço:* ${endereco}\n`;
            if (obs) {
                msg += `*Observações:* ${obs}\n`;
            }
            msg += `\n*Itens da Sacola:*\n`;

            items.forEach((item, index) => {
                const preco = item.preco_promocional && item.preco_promocional < item.preco
                    ? item.preco_promocional
                    : item.preco;
                const sub = preco * item.quantidade;
                msg += `${index + 1}. ${item.nome} (${item.quantidade}x ${this.formatMoney(preco)}) = ${this.formatMoney(sub)}\n`;
            });

            msg += `\n*Frete:* ${freteTxt}\n`;
            msg += `*Total do Pedido:* ${this.formatMoney(total)}`;

            return `https://wa.me/${whatsapp}?text=${encodeURIComponent(msg)}`;
        }
    };

    window.Cart = Cart;

    document.addEventListener('DOMContentLoaded', () => {
        Cart.updateBadges();
    });
})();

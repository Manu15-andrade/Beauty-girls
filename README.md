# Beauty Girls - Plataforma de Cosméticos & Painel Administrativo

É uma loja de cosméticos, vendemos todos produtos de autocuidado para diversas pessoas mas nosso maior público alvo é o feminino. Criamos um site para facilitar a compra dos produtos através de sua residência.

**URL Oficial em Produção:**  
[https://beaufity-girls.42web.io/](https://beaufity-girls.42web.io/)

---

## 🎯 Objetivos do Projeto
Unir um catálogo responsivo de cosméticos femininos a um painel administrativo seguro com gestão de estoque, autenticação protegida e auditoria completa de ações, entregando uma solução funcional, segura e pronta para impulsionar o crescimento do negócio.

---

## 🛠️ Stack Tecnológico

| Camada | Tecnologia | Detalhes |
|---|---|---|
| **Backend** | PHP 7.4+ / PHP 8.x | Estruturado com sessões nativas (`session_start()`) |
| **Banco de Dados** | MySQL | Conexões e queries exclusivamente via **PDO com Prepared Statements** |
| **Segurança** | Algoritmo BCRYPT | Hash de senhas via `password_hash()` e `password_verify()` |
| **Frontend** | HTML5, Tailwind CSS | Mobile-First responsivo, tipografia elegante (*Playfair Display* e *Plus Jakarta Sans*) |
| **Interações** | JavaScript Nativo + Alpine.js | Toasts animados e Modais para substituição de alertas nativos |

---

## 🔒 Regras de Negócio & Segurança (CORE)

1. **Criptografia BCRYPT**:
   - Todas as senhas dos usuários e administradores são armazenadas em hash BCRYPT (`PASSWORD_BCRYPT`). Nenhuma senha em texto puro é gravada.
2. **Página de Históricos & Auditoria Contínua**:
   - Qualquer alteração realizada por qualquer usuário (logins, criação de produtos, edições com antes/depois, ajustes de estoque, exclusões) é gravada na tabela `logs_auditoria`.
   - Página dedicada em `/admin/historico.php` com filtros por ação, usuário, busca textual e exportação em CSV.
3. **Prevenção contra SQL Injection**:
   - 100% das consultas utilizam `PDO::prepare()` e parâmetros nomeados (`:param`).
4. **Interface sem Alertas Nativos (`alert`/`confirm`)**:
   - Todos os avisos e confirmações críticas utilizam o componente **UniversalModal** integrado em Tailwind CSS.
   - Mensagens de sucesso, aviso e erro utilizam notificações flutuantes em **Toast**.
5. **Gestão de Estoque e Alertas**:
   - Alerta visual no painel quando um produto atinge ou fica abaixo do seu estoque mínimo.
   - Bloqueio automático de inclusão na sacola caso o produto esteja esgotado (`estoque <= 0`).
   - Cálculo automático de Frete Grátis com barra de progresso visual.
   - Finalização da sacola de compras com redirecionamento direto para o WhatsApp da loja com o pedido formatado.

---

## 🔑 Credenciais Padrão para Testes e Avaliação

- **URL de Login**: `http://localhost/Beauty-girls/login.php`
- **E-mail do Administrador**: `admin@beautygirls.com`
- **Senha**: `admin123` *(Armazenada via hash BCRYPT)*

---

## 📁 Arquitetura Modular dos Arquivos

O projeto segue estritamente a separação lógica de responsabilidades, evitando qualquer monólito:

```
Beauty-girls/
│
├── index.php                      # Vitrine principal e catálogo com busca e filtros
├── produto.php                    # Detalhes do cosmético com fotos ampliadas
├── carrinho.php                   # Sacola de compras e checkout para WhatsApp
├── login.php                      # Tela de login responsiva
├── cadastro.php                   # Cadastro de novos clientes
├── logout.php                     # Encerramento seguro de sessão
│
├── config/
│   ├── constantes.php             # Constantes globais (nome da loja, frete, WhatsApp)
│   └── bd.php                     # Conexão PDO segura com auto-instalação de schema
│
├── includes/
│   ├── auth.php                   # Funções de autenticação e controle de acesso
│   ├── logger.php                 # Registro de logs de auditoria (CORE)
│   ├── toast.php                  # Gerenciador e renderizador de mensagens Toast
│   ├── modal.php                  # Componente universal de Modais (substitui alerts)
│   ├── header.php                 # Header HTML5 com Tailwind CSS e fontes
│   ├── navbar.php                 # Barra de navegação responsiva Mobile-First
│   └── footer.php                 # Rodapé profissional com canais de atendimento
│
├── actions/                       # Ações isoladas de backend
│   ├── login_action.php           # Autenticação com BCRYPT e auditoria de login
│   ├── logout_action.php          # Finalização de sessão com auditoria de saída
│   ├── register_action.php        # Criação de conta com hash BCRYPT
│   ├── produto_create.php         # Inserção de cosmético com upload e auditoria
│   ├── produto_update.php         # Edição de cosmético com diff e auditoria
│   ├── produto_delete.php         # Exclusão segura de cosmético com auditoria
│   └── exportar_logs.php          # Exportação do histórico de auditoria em CSV
│
├── admin/                         # Painel Administrativo Seguro
│   ├── index.php                  # Dashboard com métricas de estoque e auditoria
│   ├── produtos.php               # Gestão de estoque (listagem, status e exclusão)
│   ├── produto_form.php           # Formulário de adição/edição de produtos
│   ├── historico.php              # Página de auditoria completa de logs (CORE)
│   └── includes/
│       ├── admin_header.php       # Cabeçalho protegido do painel admin
│       └── admin_footer.php       # Rodapé do painel admin
│
├── assets/
│   ├── css/custom.css             # Estilizações complementares
│   ├── js/main.js                 # Lógica da sacola, cálculo de totais e WhatsApp
│   └── images/produtos/           # Galeria de fotos reais dos cosméticos
│
├── database/
│   └── schema.sql                 # Script SQL para criação do banco e dados iniciais
│
├── requisitos.md                  # Especificação original dos requisitos
└── LICENSE                        # Licença do projeto
```

---

## 🚀 Como Executar Localmente

### Opção 1: Com XAMPP / WampServer / Laragon
1. Copie a pasta `Beauty-girls` para o diretório raiz do seu servidor web (por exemplo: `C:\xampp\htdocs\Beauty-girls`).
2. Abra o painel do XAMPP e inicie o serviço **Apache** e **MySQL**.
3. Acesse o phpMyAdmin (`http://localhost/phpmyadmin`) e importe o arquivo [database/schema.sql](database/schema.sql) *(Obs: o arquivo `config/bd.php` também possui auto-inicialização que cria o banco automaticamente caso tenha permissão)*.
4. Abra seu navegador em: `http://localhost/Beauty-girls/`.

### Opção 2: Com o Servidor Embutido do PHP
Se o PHP e o MySQL estiverem no PATH do sistema:
```bash
cd Beauty-girls
php -S localhost:8000
```
Acesse `http://localhost:8000/`.

---

## 📋 Auditoria e Conformidade com os Requisitos

- [x] **Tratar senhas de usuários com hash BCRYPT**: Implementado em `cadastro.php`, `login_action.php`, `config/bd.php` e `schema.sql`.
- [x] **Página de Históricos e Logs para Auditorias**: Implementado na página `admin/historico.php` e função `registrar_log()` em `includes/logger.php`.
- [x] **PDO para todas as queries MySQL**: 100% de queries preparadas com tratamento de exceções.
- [x] **Separação lógica em camadas (não monolito)**: Conexão isolada em `config/bd.php`, ações isoladas em `actions/`, views em `includes/` e painel em `admin/`.
- [x] **Tailwind CSS Mobile-First**: Layout fluido, cards com rolagem rápida e bottom navigation bar para celulares.
- [x] **Mensagens de erro e feedback via TOAST**: Componente `includes/toast.php` e disparador dinâmico.
- [x] **Caixa de mensagens nativas tratadas em MODAL**: Componente `includes/modal.php` substituindo `confirm()` e `alert()`.
- [x] **Integração das imagens do repositório**: As fotos de cosméticos do repositório foram integradas na galeria e associadas ao banco de dados.

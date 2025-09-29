# Mia Couro Legítimo - Site Convertido para PHP

## 📋 Sobre o Projeto

Site de e-commerce para produtos artesanais em couro, convertido de HTML estático para PHP dinâmico. O site apresenta uma coleção de bolsas, carteiras e acessórios em couro legítimo com design responsivo e funcionalidades modernas.

## 🚀 Tecnologias Utilizadas

### Backend
- **PHP 7.4+** - Linguagem principal do backend
- **Apache** - Servidor web (XAMPP)
- **Sessões PHP** - Gerenciamento de estado (carrinho, favoritos)

### Frontend
- **HTML5** - Estrutura semântica
- **CSS3** - Estilização responsiva com Grid e Flexbox
- **JavaScript ES6+** - Interatividade e comunicação com API
- **Font BR Sonoma** - Tipografia personalizada

### Funcionalidades
- **Sistema de Produtos Dinâmico** - Gerenciamento centralizado via PHP
- **API RESTful** - Endpoints para carrinho e favoritos
- **URLs Amigáveis** - Reescrita via .htaccess
- **Responsive Design** - Adaptação para mobile e desktop
- **SEO Otimizado** - Meta tags dinâmicas e estrutura semântica

## 📁 Estrutura do Projeto

```
site certo/
├── 📄 index.php              # Página inicial
├── 📄 produtos.php           # Catálogo de produtos
├── 📄 produto.php            # Página individual do produto
├── 📄 config.php             # Configurações centrais e classe ProductManager
├── 📄 api.php                # API para funcionalidades AJAX
├── 📄 app.js                 # JavaScript principal da aplicação
├── 📄 .htaccess              # Configurações do Apache (URLs amigáveis)
├── 📁 includes/              # Componentes reutilizáveis
│   ├── 📄 header.php         # Cabeçalho do site
│   └── 📄 footer.php         # Rodapé do site
├── 📁 img/                   # Imagens do site
├── 📁 icon s/                # Ícones SVG
├── 📄 styles.css             # Estilos principais
├── 📄 produtos-styles.css    # Estilos da página de produtos
├── 📄 produto-styles.css     # Estilos da página individual
├── 📄 image-optimize.css     # Otimizações de imagem
└── 📄 README.md              # Documentação
```

## ⚙️ Configuração e Instalação

### Pré-requisitos
- XAMPP (PHP 7.4+ e Apache)
- Navegador moderno (Chrome, Firefox, Safari, Edge)

### Instalação
1. **Clonar/Baixar o projeto:**
   ```bash
   # Coloque os arquivos em: C:\xampp\htdocs\mia\site certo\
   ```

2. **Inicializar XAMPP:**
   - Abrir o painel do XAMPP
   - Iniciar Apache
   - (MySQL opcional - não usado atualmente)

3. **Acessar o site:**
   ```
   http://localhost/mia/site%20certo/
   ```

## 🏗️ Arquitetura do Sistema

### Classe ProductManager (config.php)
```php
class ProductManager {
    // Métodos disponíveis:
    public static function getAllProducts()           // Buscar todos os produtos
    public static function getProductById($id)        // Buscar produto por ID
    public static function getProductsByCategory($cat) // Filtrar por categoria
    public static function getFeaturedProducts($limit) // Produtos em destaque
    public static function searchProducts($term)       // Busca por termo
    public static function getRelatedProducts($id, $cat, $limit) // Relacionados
    public static function getCategories()            // Lista de categorias
}
```

### API Endpoints (api.php)
```
POST /api.php?action=add_to_cart        # Adicionar ao carrinho
POST /api.php?action=remove_from_cart   # Remover do carrinho
GET  /api.php?action=get_cart           # Obter carrinho
POST /api.php?action=add_to_favorites   # Adicionar aos favoritos
POST /api.php?action=remove_from_favorites # Remover dos favoritos
GET  /api.php?action=get_favorites      # Obter favoritos
GET  /api.php?action=search_products    # Buscar produtos
GET  /api.php?action=get_product        # Obter produto específico
```

### URLs Amigáveis (.htaccess)
```
/produto/1           → produto.php?id=1
/produtos/bolsas     → produtos.php?categoria=bolsas
/buscar/carteira     → produtos.php?busca=carteira
```

## 📱 Funcionalidades

### Página Inicial (index.php)
- Hero section com call-to-action
- Produtos em destaque (dinâmicos)
- Seção de coleções
- Widget do Instagram (Elfsight)
- Depoimentos de clientes
- Design totalmente responsivo

### Catálogo de Produtos (produtos.php)
- Listagem dinâmica de todos os produtos
- Filtros por categoria (Todos, Bolsas, Carteiras, Acessórios)
- Busca por texto
- Cards informativos com preço e características
- Paginação automática
- Contador de produtos encontrados

### Página do Produto (produto.php)
- Galeria de imagens com thumbnails
- Informações detalhadas do produto
- Seleção de cores e tamanhos
- Botão de favoritos
- Produtos relacionados
- Integração direta com WhatsApp
- Breadcrumb de navegação

### Sistema de Favoritos
- Armazenamento em sessão PHP
- Interface JavaScript reativa
- Persistência durante a sessão
- Indicadores visuais nos produtos

### Sistema de Carrinho
- Gerenciamento de itens via API
- Suporte a variações (cor, tamanho)
- Cálculo automático de totais
- Interface AJAX sem recarregamento

## 🎨 Design e UX

### Paleta de Cores
- **Primária:** #520100 (Vinho escuro)
- **Secundária:** #FCF8F1 (Bege claro)
- **Destaque:** #8A4D99 (Roxo)
- **Neutros:** Tons de marrom e preto

### Tipografia
- **Fonte:** BR Sonoma (Google Fonts)
- **Hierarquia:** H1-H3 para títulos, parágrafos para conteúdo
- **Responsividade:** Tamanhos adaptáveis por breakpoint

### Componentes
- Cards de produto com hover effects
- Botões com estados visuais
- Formulários estilizados
- Modal de notificações
- Animações suaves (scroll reveals)

## 📊 SEO e Performance

### Otimizações Implementadas
- **Meta tags dinâmicas** por página
- **URLs semânticas** e amigáveis
- **Lazy loading** de imagens
- **Compressão Gzip** via .htaccess
- **Cache de recursos** estáticos
- **Estrutura HTML semântica**

### Performance
- **Minificação** de CSS e JS (recomendado)
- **Otimização de imagens** via CSS
- **Preload** de fontes críticas
- **Async loading** de widgets externos

## 🔧 Configurações

### Informações de Contato (config.php)
```php
define('CONTACT_WHATSAPP', '5511999999999');
define('CONTACT_EMAIL', 'contato@miacourolego.com');
define('CONTACT_INSTAGRAM', 'https://instagram.com/miacourolego');
```

### Sessões PHP
- **Carrinho:** `$_SESSION['cart']`
- **Favoritos:** `$_SESSION['favorites']`
- **Configurações:** Iniciadas automaticamente

## 🧪 Funcionalidades JavaScript (app.js)

### Classe MiaApp
```javascript
class MiaApp {
    // Principais métodos:
    addToCart(id, qty, color, size)     // Adicionar ao carrinho
    addToFavorites(productId)           // Favoritar produto
    searchProducts(term, category)      // Buscar produtos
    openWhatsApp(product, price)        // Abrir WhatsApp
    showNotification(msg, type)         // Mostrar notificações
}
```

### Recursos Implementados
- **Menu mobile** responsivo
- **Busca em tempo real** (opcional)
- **Smooth scrolling** para links internos
- **Lazy loading** de imagens
- **Notificações toast** para ações
- **Animações on-scroll** para elementos

## 📈 Melhorias Futuras

### Banco de Dados
- Migrar produtos para MySQL/PostgreSQL
- Sistema de usuários e login
- Histórico de pedidos
- Reviews e avaliações

### E-commerce Completo
- Gateway de pagamento (PagSeguro, MercadoPago)
- Controle de estoque
- Painel administrativo
- Relatórios de vendas

### Performance
- CDN para imagens
- Cache Redis/Memcached
- Service Workers
- Progressive Web App (PWA)

## 🐛 Solução de Problemas

### Problemas Comuns

**Erro 500 - Internal Server Error**
- Verificar se o Apache está rodando
- Checar permissões das pastas
- Validar sintaxe do .htaccess

**Sessões não funcionam**
- Verificar se `session_start()` está sendo chamado
- Checar permissões da pasta de sessões
- Confirmar configurações PHP

**URLs não amigáveis**
- Ativar mod_rewrite no Apache
- Verificar configurações .htaccess
- Testar httpd.conf do Apache

**Imagens não carregam**
- Verificar caminhos relativos
- Confirmar existência dos arquivos
- Checar permissões das pastas

## 📄 Licença

Este projeto é proprietário da Mia Couro Legítimo. Todos os direitos reservados.

## 👥 Contribuição

Desenvolvido por GitHub Copilot para modernização do site institucional.

---

**Versão:** 2.0.0  
**Data:** Dezembro 2024  
**Status:** ✅ Produção  

Para suporte técnico ou dúvidas, consulte a documentação ou entre em contato com a equipe de desenvolvimento.
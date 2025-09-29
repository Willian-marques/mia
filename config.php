<?php
// config.php - Configurações centrais do site

// Informações do site
define('SITE_NAME', 'Mia Couro Legítimo');
define('SITE_DESCRIPTION', 'Produtos artesanais em couro genuíno, feitos à mão por artesãos');
define('SITE_URL', 'http://localhost/mia/site%20certo/');

// Informações de contato
define('CONTACT_WHATSAPP', '5541973382889');
define('CONTACT_EMAIL', 'contato@miacourolego.com');
define('CONTACT_INSTAGRAM', 'https://instagram.com/miacourolego');
define('CONTACT_ADDRESS', 'São Paulo, SP - Brasil');

// Configurações de produtos
class ProductManager {
    
    public static function getAllProducts() {
        return [
            [
                'id' => 1,
                'name' => 'Bolsa Siena',
                'slug' => 'bolsa-siena',
                'description' => 'Elegante bolsa em couro legítimo, perfeita para o dia a dia',
                'detailed_description' => 'Bolsa confeccionada em couro bovino de alta qualidade, com acabamento artesanal. Possui compartimentos internos organizados e alça resistente. Ideal para mulheres que buscam elegância e funcionalidade no dia a dia.',
                'price' => 299.00,
                'image' => 'img/bolsa.png',
                'category' => 'bolsas',
                'gallery' => ['img/bolsa.png'],
                'colors' => ['Marrom', 'Preto', 'Natural'],
                'sizes' => ['Pequena', 'Média', 'Grande'],
                'features' => ['Couro legítimo', 'Feita à mão', 'Compartimentos internos', 'Alça ajustável', 'Acabamento artesanal'],
                'dimensions' => ['Pequena: 25x20x10cm', 'Média: 30x25x12cm', 'Grande: 35x30x15cm'],
                'care_instructions' => 'Limpe com pano seco. Evite contato prolongado com água. Use produtos específicos para couro.',
                'stock' => 10,
                'featured' => true,
                'active' => true
            ],
            [
                'id' => 2,
                'name' => 'Carteira Madrid',
                'slug' => 'carteira-madrid',
                'description' => 'Carteira masculina em couro premium com design clássico',
                'detailed_description' => 'Carteira masculina confeccionada em couro de primeira qualidade. Design clássico e funcional com múltiplos compartimentos para cartões e dinheiro. Perfeita para o homem moderno que aprecia qualidade.',
                'price' => 99.00,
                'image' => 'img/carteira.png',
                'category' => 'carteiras',
                'gallery' => ['img/carteira.png'],
                'colors' => ['Marrom', 'Preto'],
                'sizes' => ['Único'],
                'features' => ['Couro premium', 'Múltiplos compartimentos', 'Design clássico', 'Costura reforçada', '6 porta-cartões'],
                'dimensions' => ['11x9x2cm'],
                'care_instructions' => 'Limpe com pano seco. Use hidratante para couro mensalmente.',
                'stock' => 15,
                'featured' => true,
                'active' => true
            ],
            [
                'id' => 3,
                'name' => 'Mouse Pad Premium',
                'slug' => 'mouse-pad-premium',
                'description' => 'Mouse pad em couro natural para escritório',
                'detailed_description' => 'Mouse pad confeccionado em couro natural, proporcionando elegância ao seu ambiente de trabalho. Surface lisa e durável, ideal para uso profissional.',
                'price' => 45.00,
                'image' => 'img/mousepad.png',
                'category' => 'acessorios',
                'gallery' => ['img/mousepad.png'],
                'colors' => ['Natural', 'Marrom'],
                'sizes' => ['Médio', 'Grande'],
                'features' => ['Couro natural', 'Surface lisa', 'Antiderrapante', 'Design elegante', 'Bordas costuradas'],
                'dimensions' => ['Médio: 25x20cm', 'Grande: 30x25cm'],
                'care_instructions' => 'Limpe com pano úmido. Seque naturalmente.',
                'stock' => 20,
                'featured' => true,
                'active' => true
            ],
            [
                'id' => 4,
                'name' => 'Porta Cartão Minimalista',
                'slug' => 'porta-cartao-minimalista',
                'description' => 'Porta cartão minimalista em couro natural',
                'detailed_description' => 'Porta cartão com design minimalista, ideal para quem busca praticidade. Feito em couro natural com acabamento premium. Perfeito para carregar apenas o essencial.',
                'price' => 35.00,
                'image' => 'img/portacartao.png',
                'category' => 'carteiras',
                'gallery' => ['img/portacartao.png'],
                'colors' => ['Natural', 'Marrom', 'Preto'],
                'sizes' => ['Único'],
                'features' => ['Design minimalista', 'Couro natural', 'Compacto', 'Acabamento premium', '4 compartimentos'],
                'dimensions' => ['10x7x1cm'],
                'care_instructions' => 'Limpe com pano seco. Evite dobrar excessivamente.',
                'stock' => 25,
                'featured' => true,
                'active' => true
            ],
            [
                'id' => 5,
                'name' => 'Case Notebook Executive',
                'slug' => 'case-notebook-executive',
                'description' => 'Case protetor para notebook em couro resistente',
                'detailed_description' => 'Case protetor confeccionado em couro resistente, ideal para proteger seu notebook com estilo. Interior forrado e fechamento seguro. Disponível em vários tamanhos.',
                'price' => 180.00,
                'image' => 'img/pasta.png',
                'category' => 'acessorios',
                'gallery' => ['img/pasta.png'],
                'colors' => ['Marrom', 'Preto'],
                'sizes' => ['13"', '15"', '17"'],
                'features' => ['Couro resistente', 'Interior forrado', 'Fechamento seguro', 'Proteção total', 'Bolso externo'],
                'dimensions' => ['13": 35x25x3cm', '15": 40x28x3cm', '17": 45x32x3cm'],
                'care_instructions' => 'Limpe com pano seco. Use hidratante para couro regularmente.',
                'stock' => 8,
                'featured' => true,
                'active' => true
            ],
            [
                'id' => 6,
                'name' => 'Tag Identificação Bagagem',
                'slug' => 'tag-identificacao-bagagem',
                'description' => 'Identificador de bagagem em couro personalizado',
                'detailed_description' => 'Tag para identificação de bagagem em couro legítimo. Personalizável com nome e contato. Resistente e durável, ideal para viajantes.',
                'price' => 25.00,
                'image' => 'img/marcapagina.png',
                'category' => 'acessorios',
                'gallery' => ['img/marcapagina.png'],
                'colors' => ['Marrom', 'Preto', 'Natural'],
                'sizes' => ['Único'],
                'features' => ['Personalizável', 'Couro legítimo', 'Resistente', 'Design funcional', 'Cordão incluído'],
                'dimensions' => ['9x5x0.5cm'],
                'care_instructions' => 'Limpe com pano úmido. Resistente a intempéries.',
                'stock' => 30,
                'featured' => true,
                'active' => true
            ],
            [
                'id' => 7,
                'name' => 'Porta Vinho Elegante',
                'slug' => 'porta-vinho-elegante',
                'description' => 'Porta garrafa de vinho em couro premium',
                'detailed_description' => 'Elegante porta garrafa de vinho confeccionado em couro premium. Ideal para presentes especiais e ocasiões únicas. Design sofisticado e funcional.',
                'price' => 120.00,
                'image' => 'img/bolsavinho.jpg',
                'category' => 'acessorios',
                'gallery' => ['img/bolsavinho.jpg'],
                'colors' => ['Marrom', 'Preto'],
                'sizes' => ['Único'],
                'features' => ['Couro premium', 'Design elegante', 'Proteção total', 'Ideal para presentes', 'Alça de mão'],
                'dimensions' => ['35x12x12cm'],
                'care_instructions' => 'Limpe com pano seco. Ideal para presentes especiais.',
                'stock' => 5,
                'featured' => false,
                'active' => true
            ],
            [
                'id' => 8,
                'name' => 'Lixeira Automotiva',
                'slug' => 'lixeira-automotiva',
                'description' => 'Lixeira para carro em couro com fixação prática',
                'detailed_description' => 'Lixeira para automóvel confeccionada em couro resistente. Sistema de fixação prático e design que combina funcionalidade com elegância no interior do veículo.',
                'price' => 65.00,
                'image' => 'img/bolsas.jpg',
                'category' => 'acessorios',
                'gallery' => ['img/bolsas.jpg'],
                'colors' => ['Preto', 'Marrom'],
                'sizes' => ['Único'],
                'features' => ['Couro resistente', 'Fixação prática', 'Fácil limpeza', 'Design funcional', 'Interior impermeável'],
                'dimensions' => ['20x15x15cm'],
                'care_instructions' => 'Limpe com pano úmido. Interior lavável com água e sabão neutro.',
                'stock' => 12,
                'featured' => false,
                'active' => true
            ]
        ];
    }

    public static function getProductById($id) {
        $products = self::getAllProducts();
        foreach ($products as $product) {
            if ($product['id'] === (int)$id) {
                return $product;
            }
        }
        return null;
    }

    public static function getProductsByCategory($category) {
        $products = self::getAllProducts();
        if ($category === 'todos') {
            return array_filter($products, function($product) {
                return $product['active'];
            });
        }
        return array_filter($products, function($product) use ($category) {
            return $product['category'] === $category && $product['active'];
        });
    }

    public static function getFeaturedProducts($limit = 6) {
        $products = self::getAllProducts();
        $featured = array_filter($products, function($product) {
            return $product['featured'] && $product['active'];
        });
        return array_slice($featured, 0, $limit);
    }

    public static function searchProducts($term) {
        $products = self::getAllProducts();
        $term = strtolower($term);
        
        return array_filter($products, function($product) use ($term) {
            return $product['active'] && (
                strpos(strtolower($product['name']), $term) !== false ||
                strpos(strtolower($product['description']), $term) !== false ||
                strpos(strtolower($product['detailed_description']), $term) !== false
            );
        });
    }

    public static function getCategories() {
        return [
            'todos' => 'Todos os Produtos',
            'bolsas' => 'Bolsas',
            'carteiras' => 'Carteiras',
            'acessorios' => 'Acessórios'
        ];
    }

    public static function getRelatedProducts($productId, $category, $limit = 3) {
        $products = self::getAllProducts();
        $related = array_filter($products, function($product) use ($productId, $category) {
            return $product['id'] !== $productId && 
                   $product['category'] === $category && 
                   $product['active'];
        });
        return array_slice($related, 0, $limit);
    }
}

// Funções utilitárias
function formatPrice($price) {
    return 'R$ ' . number_format($price, 2, ',', '.');
}

function generateWhatsAppLink($message) {
    return 'https://wa.me/' . CONTACT_WHATSAPP . '?text=' . urlencode($message);
}

function getPageTitle($page, $extra = '') {
    $base = SITE_NAME;
    
    switch($page) {
        case 'home':
            return $base . ' - Produtos Artesanais em Couro';
        case 'produtos':
            return 'Produtos - ' . $base;
        case 'produto':
            return ($extra ? $extra . ' - ' : '') . $base;
        case 'sobre':
            return ($extra ? $extra . ' - ' : 'Sobre Nós - ') . $base;
        case 'contato':
            return ($extra ? $extra . ' - ' : 'Fale Conosco - ') . $base;
        default:
            return $base;
    }
}

// Configurações de sessão
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Inicializar carrinho se não existir
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Inicializar favoritos se não existir
if (!isset($_SESSION['favorites'])) {
    $_SESSION['favorites'] = [];
}
?>

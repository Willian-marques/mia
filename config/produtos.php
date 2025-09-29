<?php
// Função para carregar produtos do JSON
function loadProductsFromJson() {
    $jsonFile = __DIR__ . '/../data/produtos.json';
    
    if (file_exists($jsonFile)) {
        $content = file_get_contents($jsonFile);
        $produtos = json_decode($content, true);
        return $produtos ?: getDefaultProducts();
    }
    
    return getDefaultProducts();
}

// Base de dados padrão dos produtos (fallback)
function getDefaultProducts() {
    return [
    1 => [
        'id' => 1,
        'slug' => 'bolsa-sienna',
        'title' => 'Bolsa de Couro Premium Artesanal',
        'category' => 'bolsas', 
        'price' => 899.00,
        'oldPrice' => 1299.00,
        'discount' => 31,
        'images' => [
            'img/bolsa.png',
            'img/bolsaviagem.jpg', 
            'img/bolsas.jpg',
            'img/bolsavinho.jpg'
        ],
        'colors' => [
            ['name' => 'marrom', 'color' => '#92400E', 'title' => 'Marrom'],
            ['name' => 'preto', 'color' => '#1F2937', 'title' => 'Preto'],
            ['name' => 'vinho', 'color' => '#7F1D1D', 'title' => 'Vinho']
        ],
        'sizes' => ['Médio', 'Grande'],
        'description' => 'A Bolsa Sienna é a tradução da sofisticação em forma de acessório. Feita para a mulher que aprecia um design clássico com um toque contemporâneo, esta peça é confeccionada em couro legítimo de alta qualidade, selecionado para garantir um toque suave, durabilidade incomparável e uma pátina que se embeleza com o tempo.',
        'specifications' => 'Material: 100% Couro Legítimo Premium|Detalhes: Acabamentos finos, ferragens com banho dourado de alta resistência.|Interior: Forro em tecido nobre, com bolsos organizadores para manter seus pertences essenciais em segurança e sempre à mão.|Alças: Acompanha uma alça de mão e uma alça longa removível e ajustável, oferecendo múltiplas formas de uso: na mão, no ombro ou transversal.|Feita para durar, desenhada para encantar. Adquira a sua Bolsa Sienna e carregue consigo um símbolo de elegância e qualidade.',
        'status' => 'ativo',
        'stock' => 15,
        'sales' => 47,
        'isFeatured' => true,
        'isBestseller' => true,
        'created_at' => '2025-09-24',
        'updated_at' => '2025-09-24'
    ],
    2 => [
        'id' => 2,
        'slug' => 'carteira-madrid',
        'title' => 'Carteira Madrid',
        'category' => 'carteiras',
        'price' => 99.00,
        'oldPrice' => null,
        'discount' => null,
        'images' => [
            'img/carteira.png',
            'img/carteira.png',
            'img/carteira.png', 
            'img/carteira.png'
        ],
        'colors' => [
            ['name' => 'marrom', 'color' => '#92400E', 'title' => 'Marrom']
        ],
        'sizes' => ['Único'],
        'description' => 'A Carteira Madrid é confeccionada em couro legítimo premium, oferecendo elegância e funcionalidade. Com compartimentos organizados para cartões e dinheiro, é perfeita para o dia a dia.',
        'specifications' => 'Material: 100% Couro Legítimo|Dimensões: 11cm x 9cm x 2cm|Compartimentos: 6 para cartões + 2 para dinheiro|Peso: 120g|Cor: Marrom',
        'status' => 'ativo',
        'stock' => 25,
        'sales' => 32,
        'isFeatured' => false,
        'isBestseller' => true,
        'created_at' => '2025-09-24',
        'updated_at' => '2025-09-24'
    ],
    3 => [
        'id' => 3,
        'slug' => 'porta-cartao',
        'title' => 'Porta Cartão Premium',
        'category' => 'acessorios',
        'price' => 49.00,
        'oldPrice' => null,
        'discount' => null,
        'images' => [
            'img/portacartao.png',
            'img/portacartao.png',
            'img/portacartao.png',
            'img/portacartao.png'
        ],
        'colors' => [
            ['name' => 'marrom', 'color' => '#92400E', 'title' => 'Marrom']
        ],
        'sizes' => ['Único'],
        'description' => 'Porta Cartão em couro legítimo, compacto e elegante. Ideal para organizar seus cartões principais com praticidade.',
        'specifications' => 'Material: 100% Couro Legítimo Premium|Compartimentos: 4 slots para cartões|Dimensões: 8cm x 6cm x 1cm|Peso: 50g',
        'status' => 'ativo',
        'stock' => 30,
        'sales' => 18,
        'isFeatured' => true,
        'isBestseller' => false,
        'created_at' => '2025-09-24',
        'updated_at' => '2025-09-24'
    ],
    4 => [
        'id' => 4,
        'slug' => 'case-notebook',
        'title' => 'Case Notebook Executive',
        'category' => 'cases-capas',
        'price' => 199.00,
        'oldPrice' => null,
        'discount' => null,
        'images' => [
            'img/pasta.png',
            'img/pasta.png',
            'img/pasta.png',
            'img/pasta.png'
        ],
        'colors' => [
            ['name' => 'marrom', 'color' => '#92400E', 'title' => 'Marrom']
        ],
        'sizes' => ['15"', '17"'],
        'description' => 'Case para notebook em couro legítimo, oferece máxima proteção com elegância. Ideal para profissionais que valorizam estilo e segurança.',
        'specifications' => 'Material: 100% Couro Legítimo Premium|Proteção: Acolchoamento interno|Compatibilidade: Notebooks 15" e 17"|Fechamento: Zíper de alta qualidade',
        'status' => 'ativo',
        'stock' => 12,
        'sales' => 8,
        'isFeatured' => false,
        'isBestseller' => false,
        'created_at' => '2025-09-24',
        'updated_at' => '2025-09-24'
    ],
    5 => [
        'id' => 5,
        'slug' => 'porta-vinho',
        'title' => 'Porta Vinho Artesanal',
        'category' => 'acessorios',
        'price' => 89.00,
        'oldPrice' => 99.00,
        'discount' => 10,
        'images' => [
            'img/bolsavinho.jpg',
            'img/bolsavinho.jpg',
            'img/bolsavinho.jpg',
            'img/bolsavinho.jpg'
        ],
        'colors' => [
            ['name' => 'marrom', 'color' => '#92400E', 'title' => 'Marrom']
        ],
        'sizes' => ['Único'],
        'description' => 'Porta Vinho em couro artesanal, perfeito para presente. Combina funcionalidade e sofisticação.',
        'specifications' => 'Material: 100% Couro Legítimo|Capacidade: 1 garrafa padrão|Fechamento: Cordão de couro|Ideal para presentes especiais',
        'status' => 'ativo',
        'stock' => 20,
        'sales' => 15,
        'isFeatured' => false,
        'isBestseller' => false,
        'created_at' => '2025-09-24',
        'updated_at' => '2025-09-24'
    ]
];

}

// Carregar produtos (JSON ou padrão)
$produtos_db = loadProductsFromJson();

// Função para obter produto por ID
function getProdutoById($id) {
    $produtos_db = loadProductsFromJson();
    return isset($produtos_db[$id]) ? $produtos_db[$id] : null;
}

// Função para obter produto por slug
function getProdutoBySlug($slug) {
    $produtos_db = loadProductsFromJson();
    foreach ($produtos_db as $produto) {
        if ($produto['slug'] === $slug) {
            return $produto;
        }
    }
    return null;
}

// Função para obter todos os produtos
function getAllProdutos($status = null) {
    $produtos_db = loadProductsFromJson();
    $produtos = [];
    foreach ($produtos_db as $produto) {
        if ($status === null || $produto['status'] === $status) {
            $produtos[] = $produto;
        }
    }
    return $produtos;
}

// Função para obter produtos por categoria
function getProdutosByCategoria($categoria) {
    $produtos_db = loadProductsFromJson();
    $produtos = [];
    foreach ($produtos_db as $produto) {
        if ($produto['category'] === $categoria && $produto['status'] === 'ativo') {
            $produtos[] = $produto;
        }
    }
    return $produtos;
}

// Função para formatar preço
function formatPrice($price) {
    return 'R$ ' . number_format($price, 2, ',', '.');
}

// Função para calcular desconto
function calculateDiscount($discountPrice, $originalPrice) {
    if (!$originalPrice) return null;
    return round((($originalPrice - $discountPrice) / $originalPrice) * 100);
}
?>
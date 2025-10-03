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
    return [];
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
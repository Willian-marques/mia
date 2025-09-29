<?php
require_once 'config.php';

header('Content-Type: application/json');

// Verificar método HTTP
$method = $_SERVER['REQUEST_METHOD'];
$action = isset($_GET['action']) ? $_GET['action'] : '';

// Função para resposta JSON
function jsonResponse($success, $data = null, $message = '') {
    echo json_encode([
        'success' => $success,
        'data' => $data,
        'message' => $message
    ]);
    exit;
}

// Roteamento das ações
switch ($action) {
    case 'add_to_cart':
        addToCart();
        break;
    case 'remove_from_cart':
        removeFromCart();
        break;
    case 'get_cart':
        getCart();
        break;
    case 'add_to_favorites':
        addToFavorites();
        break;
    case 'remove_from_favorites':
        removeFromFavorites();
        break;
    case 'get_favorites':
        getFavorites();
        break;
    case 'search_products':
        searchProducts();
        break;
    case 'get_product':
        getProduct();
        break;
    default:
        jsonResponse(false, null, 'Ação não encontrada');
}

// Adicionar ao carrinho
function addToCart() {
    $input = json_decode(file_get_contents('php://input'), true);
    
    $product_id = isset($input['product_id']) ? (int)$input['product_id'] : 0;
    $quantity = isset($input['quantity']) ? (int)$input['quantity'] : 1;
    $color = isset($input['color']) ? $input['color'] : '';
    $size = isset($input['size']) ? $input['size'] : '';
    
    if ($product_id <= 0) {
        jsonResponse(false, null, 'ID do produto inválido');
    }
    
    $product = ProductManager::getProductById($product_id);
    if (!$product) {
        jsonResponse(false, null, 'Produto não encontrado');
    }
    
    // Criar chave única para o item no carrinho
    $cart_key = $product_id . '_' . $color . '_' . $size;
    
    // Adicionar ao carrinho na sessão
    if (!isset($_SESSION['cart'][$cart_key])) {
        $_SESSION['cart'][$cart_key] = [
            'product_id' => $product_id,
            'product_name' => $product['name'],
            'product_price' => $product['price'],
            'product_image' => $product['image'],
            'quantity' => 0,
            'color' => $color,
            'size' => $size
        ];
    }
    
    $_SESSION['cart'][$cart_key]['quantity'] += $quantity;
    
    jsonResponse(true, $_SESSION['cart'][$cart_key], 'Produto adicionado ao carrinho');
}

// Remover do carrinho
function removeFromCart() {
    $input = json_decode(file_get_contents('php://input'), true);
    $cart_key = isset($input['cart_key']) ? $input['cart_key'] : '';
    
    if (empty($cart_key)) {
        jsonResponse(false, null, 'Chave do carrinho inválida');
    }
    
    if (isset($_SESSION['cart'][$cart_key])) {
        unset($_SESSION['cart'][$cart_key]);
        jsonResponse(true, null, 'Produto removido do carrinho');
    } else {
        jsonResponse(false, null, 'Produto não encontrado no carrinho');
    }
}

// Obter carrinho
function getCart() {
    $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
    $total = 0;
    
    foreach ($cart as $item) {
        $total += $item['product_price'] * $item['quantity'];
    }
    
    jsonResponse(true, [
        'items' => $cart,
        'total' => $total,
        'count' => count($cart)
    ]);
}

// Adicionar aos favoritos
function addToFavorites() {
    $input = json_decode(file_get_contents('php://input'), true);
    $product_id = isset($input['product_id']) ? (int)$input['product_id'] : 0;
    
    if ($product_id <= 0) {
        jsonResponse(false, null, 'ID do produto inválido');
    }
    
    $product = ProductManager::getProductById($product_id);
    if (!$product) {
        jsonResponse(false, null, 'Produto não encontrado');
    }
    
    if (!in_array($product_id, $_SESSION['favorites'])) {
        $_SESSION['favorites'][] = $product_id;
        jsonResponse(true, null, 'Produto adicionado aos favoritos');
    } else {
        jsonResponse(false, null, 'Produto já está nos favoritos');
    }
}

// Remover dos favoritos
function removeFromFavorites() {
    $input = json_decode(file_get_contents('php://input'), true);
    $product_id = isset($input['product_id']) ? (int)$input['product_id'] : 0;
    
    $key = array_search($product_id, $_SESSION['favorites']);
    if ($key !== false) {
        unset($_SESSION['favorites'][$key]);
        $_SESSION['favorites'] = array_values($_SESSION['favorites']); // Reindexar
        jsonResponse(true, null, 'Produto removido dos favoritos');
    } else {
        jsonResponse(false, null, 'Produto não encontrado nos favoritos');
    }
}

// Obter favoritos
function getFavorites() {
    $favorites = isset($_SESSION['favorites']) ? $_SESSION['favorites'] : [];
    $products = [];
    
    foreach ($favorites as $product_id) {
        $product = ProductManager::getProductById($product_id);
        if ($product) {
            $products[] = $product;
        }
    }
    
    jsonResponse(true, $products);
}

// Buscar produtos
function searchProducts() {
    $term = isset($_GET['term']) ? trim($_GET['term']) : '';
    $category = isset($_GET['category']) ? $_GET['category'] : 'todos';
    
    if (empty($term)) {
        jsonResponse(false, null, 'Termo de busca não informado');
    }
    
    $products = ProductManager::searchProducts($term);
    
    // Filtrar por categoria se especificada
    if ($category !== 'todos') {
        $products = array_filter($products, function($product) use ($category) {
            return $product['category'] === $category;
        });
    }
    
    jsonResponse(true, array_values($products));
}

// Obter produto específico
function getProduct() {
    $product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    
    if ($product_id <= 0) {
        jsonResponse(false, null, 'ID do produto inválido');
    }
    
    $product = ProductManager::getProductById($product_id);
    if (!$product) {
        jsonResponse(false, null, 'Produto não encontrado');
    }
    
    // Adicionar produtos relacionados
    $related = ProductManager::getRelatedProducts($product_id, $product['category'], 4);
    $product['related_products'] = $related;
    
    jsonResponse(true, $product);
}
?>

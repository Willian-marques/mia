<?php

error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__.'/php-error.log');
if (session_status() !== PHP_SESSION_ACTIVE) session_start();


$action = $_POST['action'] ?? $_GET['action'] ?? null;


if ($action === 'test') {
    header('Content-Type: application/json');
    echo json_encode(['success'=>true]);
    exit;
}


// Verificar se está logado
if (!isset($_SESSION['admin_logged']) || !$_SESSION['admin_logged']) {
    http_response_code(401);
    echo json_encode(['error' => 'Não autorizado', 'debug' => 'Session not found']);
    exit;
}

try {
    require_once 'config/produtos.php';
} catch (Exception $e) {
    echo json_encode(['error' => 'Erro ao carregar configurações: ' . $e->getMessage()]);
    exit;
}

// Função para salvar dados no arquivo JSON
function saveProductsToJson($produtos) {
    $jsonFile = __DIR__ . '/data/produtos.json';
    
    // Criar diretório se não existir
    if (!is_dir(__DIR__ . '/data')) {
        mkdir(__DIR__ . '/data', 0755, true);
    }
    
    return file_put_contents($jsonFile, json_encode($produtos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

// Função para carregar dados do arquivo JSON (usando a do config)
function loadProductsData() {
    $jsonFile = __DIR__ . '/data/produtos.json';
    
    if (file_exists($jsonFile)) {
        $content = file_get_contents($jsonFile);
        $produtos = json_decode($content, true);
        return $produtos ?: getDefaultProducts();
    }
    
    // Se não existe arquivo, usar dados padrão
    $defaultProducts = getDefaultProducts();
    saveProductsToJson($defaultProducts);
    return $defaultProducts;
}

// Função para gerar novo ID
function getNextProductId($produtos) {
    $maxId = 0;
    foreach ($produtos as $id => $produto) {
        if (is_numeric($id) && $id > $maxId) {
            $maxId = $id;
        }
    }
    return $maxId + 1;
}

// Função para criar slug
function createSlug($title) {
    $slug = strtolower($title);
    $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
    $slug = preg_replace('/[\s-]+/', '-', $slug);
    return trim($slug, '-');
}

// Processar ações
$action = $_POST['action'] ?? $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'add':
            addProduct();
            break;
        case 'edit':
            editProduct();
            break;
        case 'delete':
            deleteProduct();
            break;
        case 'get':
            getProduct();
            break;
        case 'test':
            echo json_encode(['success' => true, 'message' => 'Conexão OK', 'session' => isset($_SESSION['admin_logged'])]);
            break;
        default:
            http_response_code(400);
            echo json_encode(['error' => 'Ação inválida: ' . $action, 'available_actions' => ['add', 'edit', 'delete', 'get', 'test']]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro interno: ' . $e->getMessage(), 'trace' => $e->getTraceAsString()]);
}

function addProduct() {
    try {
        $produtos = loadProductsData();
        $newId = getNextProductId($produtos);
        
        // Validar dados obrigatórios
        if (empty($_POST['productName']) || empty($_POST['productCategory']) || empty($_POST['productPrice'])) {
            throw new Exception('Campos obrigatórios não preenchidos');
        }
        
        // Processar upload de imagens
        $images = [];
        if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
            $uploadDir = __DIR__ . '/uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            foreach ($_FILES['images']['tmp_name'] as $key => $tmpName) {
                if (!empty($tmpName)) {
                    $fileName = time() . '_' . $key . '_' . $_FILES['images']['name'][$key];
                    $fileName = preg_replace('/[^a-zA-Z0-9._-]/', '', $fileName);
                    $uploadPath = $uploadDir . $fileName;
                    
                    if (move_uploaded_file($tmpName, $uploadPath)) {
                        $images[] = 'uploads/' . $fileName;
                    }
                }
            }
        }
        
        // Se não há imagens, usar imagem padrão
        if (empty($images)) {
            $images = ['img/default-product.png'];
        }
        
        // Criar novo produto
        // NOVA LÓGICA: productPrice = preço original, productOldPrice = preço com desconto
        $originalPrice = floatval($_POST['productPrice']); // Campo principal agora é preço original
        $discountPrice = !empty($_POST['productOldPrice']) ? floatval($_POST['productOldPrice']) : null;
        $discount = $discountPrice ? calculateDiscount($discountPrice, $originalPrice) : null;
        
        // Para manter compatibilidade no JSON: price = preço com desconto, oldPrice = preço original
        $price = $discountPrice ? $discountPrice : $originalPrice; // Se não há desconto, price = original
        $oldPrice = $discountPrice ? $originalPrice : null; // Se há desconto, oldPrice = original
        
        $newProduct = [
            'id' => $newId,
            'slug' => createSlug($_POST['productName']),
            'title' => $_POST['productName'],
            'category' => $_POST['productCategory'],
            'price' => $price,
            'oldPrice' => $oldPrice,
            'discount' => $discount,
            'images' => $images,
            'colors' => !empty($_POST['selectedColors']) ? json_decode($_POST['selectedColors'], true) : [
                ['name' => 'marrom', 'color' => '#92400E', 'title' => 'Marrom']
            ],
            'sizes' => ['Único'],
            'description' => $_POST['productDescription'] ?? '',
            'specifications' => $_POST['productSpecifications'] ?? 'Material: 100% Couro Legítimo Premium',
            'status' => $_POST['productStatus'] ?? 'ativo',
            'stock' => intval($_POST['productStock'] ?? 0),
            'sales' => intval($_POST['productSales'] ?? 0),
            'isFeatured' => isset($_POST['isFeatured']) ? true : false,
            'isBestseller' => isset($_POST['isBestseller']) ? true : false,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        $produtos[$newId] = $newProduct;
        
        if (saveProductsToJson($produtos)) {
            echo json_encode(['success' => true, 'message' => 'Produto adicionado com sucesso!', 'product' => $newProduct]);
        } else {
            throw new Exception('Erro ao salvar produto');
        }
        
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode(['error' => $e->getMessage()]);
    }
}

function editProduct() {
    try {
        $produtos = loadProductsData();
        $productId = intval($_POST['productId']);
        
        if (!isset($produtos[$productId])) {
            throw new Exception('Produto não encontrado');
        }
        
        // Manter imagens existentes se não houver novas
        $images = $produtos[$productId]['images'];
        
        // Carregar imagens existentes (se especificadas)
        $existingImages = [];
        if (!empty($_POST['existingImages'])) {
            $existingImages = json_decode($_POST['existingImages'], true) ?: [];
        }

        // Processar upload de novas imagens
        $newImages = [];
        if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
            $uploadDir = __DIR__ . '/uploads/';
            
            foreach ($_FILES['images']['tmp_name'] as $key => $tmpName) {
                if (!empty($tmpName)) {
                    $fileName = time() . '_' . $key . '_' . $_FILES['images']['name'][$key];
                    $fileName = preg_replace('/[^a-zA-Z0-9._-]/', '', $fileName);
                    $uploadPath = $uploadDir . $fileName;
                    
                    if (move_uploaded_file($tmpName, $uploadPath)) {
                        $newImages[] = 'uploads/' . $fileName;
                    }
                }
            }
        }

        // Combinar imagens existentes + novas imagens
        if (!empty($existingImages) || !empty($newImages)) {
            $images = array_merge($existingImages, $newImages);
        }
        
        // Se não há imagens nenhuma, manter as originais do produto
        if (empty($images)) {
            $images = $produtos[$productId]['images']; // Manter imagens existentes
        }
        
        // Atualizar produto
        // NOVA LÓGICA: productPrice = preço original, productOldPrice = preço com desconto
        $originalPrice = floatval($_POST['productPrice']); // Campo principal agora é preço original
        $discountPrice = !empty($_POST['productOldPrice']) ? floatval($_POST['productOldPrice']) : null;
        $discount = $discountPrice ? calculateDiscount($discountPrice, $originalPrice) : null;
        
        // Para manter compatibilidade no JSON: price = preço com desconto, oldPrice = preço original
        $price = $discountPrice ? $discountPrice : $originalPrice; // Se não há desconto, price = original
        $oldPrice = $discountPrice ? $originalPrice : null; // Se há desconto, oldPrice = original
        
        $produtos[$productId] = array_merge($produtos[$productId], [
            'title' => $_POST['productName'],
            'category' => $_POST['productCategory'],
            'price' => $price,
            'oldPrice' => $oldPrice,
            'discount' => $discount,
            'images' => $images,
            'colors' => !empty($_POST['selectedColors']) ? json_decode($_POST['selectedColors'], true) : $produtos[$productId]['colors'],
            'description' => $_POST['productDescription'] ?? $produtos[$productId]['description'],
            'specifications' => $_POST['productSpecifications'] ?? $produtos[$productId]['specifications'] ?? 'Material: 100% Couro Legítimo Premium',
            'status' => $_POST['productStatus'] ?? $produtos[$productId]['status'],
            'stock' => intval($_POST['productStock'] ?? $produtos[$productId]['stock'] ?? 0),
            'sales' => intval($_POST['productSales'] ?? $produtos[$productId]['sales'] ?? 0),
            'isFeatured' => isset($_POST['isFeatured']) ? true : false,
            'isBestseller' => isset($_POST['isBestseller']) ? true : false,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        
        if (saveProductsToJson($produtos)) {
            echo json_encode(['success' => true, 'message' => 'Produto atualizado com sucesso!', 'product' => $produtos[$productId]]);
        } else {
            throw new Exception('Erro ao salvar produto');
        }
        
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode(['error' => $e->getMessage()]);
    }
}

function deleteProduct() {
    try {
        $produtos = loadProductsData();
        $productId = intval($_POST['productId'] ?? $_GET['productId']);
        
        if (!isset($produtos[$productId])) {
            throw new Exception('Produto não encontrado');
        }
        
        // Remover imagens dos uploads
        foreach ($produtos[$productId]['images'] as $image) {
            if (strpos($image, 'uploads/') === 0) {
                $imagePath = __DIR__ . '/' . $image;
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }
        }
        
        unset($produtos[$productId]);
        
        if (saveProductsToJson($produtos)) {
            echo json_encode(['success' => true, 'message' => 'Produto excluído com sucesso!']);
        } else {
            throw new Exception('Erro ao excluir produto');
        }
        
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode(['error' => $e->getMessage()]);
    }
}

function getProduct() {
    try {
        $produtos = loadProductsData();
        $productId = intval($_GET['productId']);
        
        if (!isset($produtos[$productId])) {
            throw new Exception('Produto não encontrado');
        }
        
        echo json_encode(['success' => true, 'product' => $produtos[$productId]]);
        
    } catch (Exception $e) {
        http_response_code(404);
        echo json_encode(['error' => $e->getMessage()]);
    }
}
?>

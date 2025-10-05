<?php
ob_start();
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php-error.log');

// Configurações para upload de arquivos grandes
ini_set('upload_max_filesize', '200M');
ini_set('post_max_size', '200M');
ini_set('max_execution_time', 600);
ini_set('max_input_time', 600);
ini_set('memory_limit', '512M');

// 🔥 Força o PHP a usar o mesmo nome de sessão que o painel usa
session_name('PHPSESSID');

// 🔥 Define o caminho da sessão para a raiz do site (não só /admin)
session_set_cookie_params([
    'path' => '/',
    'secure' => true,         // importante para HTTPS
    'httponly' => true,
    'samesite' => 'None'      // necessário para cookies entre HTTPS
]);

// 🔥 Corrige HTTPS atrás do proxy (Nginx)
if (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
    $_SERVER['HTTPS'] = 'on';
}

// Inicia a sessão
session_start();

// Log para debug
if (!isset($_SESSION['admin_logged'])) {
    error_log("⚠ Sessão não encontrada no admin_actions.php. Cookies: " . json_encode($_COOKIE));
} else {
    error_log("✅ Sessão reconhecida no admin_actions.php. Usuário logado.");
}

// Limpa qualquer saída antes de mandar JSON
ob_clean();

$action = $_POST['action'] ?? $_GET['action'] ?? null;

// Se não há action, retorna erro
if ($action === null) {
    http_response_code(400);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['error' => 'Parâmetro action é obrigatório']);
    exit;
}

// Sempre definir header JSON para responses
header('Content-Type: application/json; charset=utf-8');

// Verificar se está logado (exceto para action=test que é para verificar conectividade)
if ($action !== 'test' && (!isset($_SESSION['admin_logged']) || !$_SESSION['admin_logged'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Acesso negado', 'code' => 403]);
    exit;
}

try {
    require_once 'config/produtos.php';
} catch (Exception $e) {
    echo json_encode(['error' => 'Erro ao carregar configurações: ' . $e->getMessage()]);
    exit;
}

// Função para salvar dados no arquivo JSON
function saveProductsToJson($produtos)
{
    $jsonFile = __DIR__ . '/data/produtos.json';

    // Criar diretório se não existir
    if (!is_dir(__DIR__ . '/data')) {
        mkdir(__DIR__ . '/data', 0755, true);
    }

    return file_put_contents($jsonFile, json_encode($produtos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

// Função para carregar dados do arquivo JSON (usando a do config)
function loadProductsData()
{
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
function getNextProductId($produtos)
{
    $maxId = 0;
    foreach ($produtos as $id => $produto) {
        if (is_numeric($id) && $id > $maxId) {
            $maxId = $id;
        }
    }
    return $maxId + 1;
}

// Função para criar slug
function createSlug($title)
{
    $slug = strtolower($title);
    $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
    $slug = preg_replace('/[\s-]+/', '-', $slug);
    return trim($slug, '-');
}

// Processar ações
$action = $_POST['action'] ?? $_GET['action'] ?? '';

try {
    // Limpar buffer antes de processar
    ob_clean();

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
        case 'getProductId':
            getProductId();
            break;
        case 'get':
            getProduct();
            break;
        case 'test':
        case 'test1':
        case 'connection_test':
            echo json_encode(['success' => true, 'message' => 'Conexão OK', 'action' => $action, 'session' => isset($_SESSION['admin_logged'])], JSON_UNESCAPED_UNICODE);
            break;
        case 'upload-avatar':
            uploadAvatar();
            break;
        default:
            // Para qualquer action não reconhecida, retorna sucesso genérico
            echo json_encode(['success' => true, 'message' => 'Action processada', 'action' => $action], JSON_UNESCAPED_UNICODE);
            break;
    }
} catch (Exception $e) {
    ob_clean();
    http_response_code(500);
    echo json_encode(['error' => 'Erro interno: ' . $e->getMessage(), 'trace' => $e->getTraceAsString()], JSON_UNESCAPED_UNICODE);
}

function addProduct()
{
    try {
        $produtos = loadProductsData();
        $newId = getNextProductId($produtos);

        // Validar dados obrigatórios
        if (empty($_POST['productName']) || empty($_POST['productCategory']) || empty($_POST['productPrice'])) {
            throw new Exception('Campos obrigatórios não preenchidos');
        }

        // Verificar se usa imagens por cor
        $useColorImages = isset($_POST['useColorImages']) && $_POST['useColorImages'] === '1';

        $images = [];
        $colorImages = [];

        if ($useColorImages) {
            // Processar imagens por cor
            $colorImagesData = !empty($_POST['colorImagesData']) ? json_decode($_POST['colorImagesData'], true) : [];
            $uploadDir = __DIR__ . '/uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            foreach ($colorImagesData as $colorName) {
                $colorImages[$colorName] = [];

                // Verificar se há uploads para esta cor
                if (isset($_FILES["colorImages_{$colorName}"]) && !empty($_FILES["colorImages_{$colorName}"]['name'][0])) {
                    foreach ($_FILES["colorImages_{$colorName}"]['tmp_name'] as $key => $tmpName) {
                        if (!empty($tmpName)) {
                            $fileName = time() . '_' . $key . '_' . $_FILES["colorImages_{$colorName}"]['name'][$key];
                            $fileName = preg_replace('/[^a-zA-Z0-9._-]/', '', $fileName);
                            $uploadPath = $uploadDir . $fileName;

                            if (move_uploaded_file($tmpName, $uploadPath)) {
                                $colorImages[$colorName][] = 'uploads/' . $fileName;
                            }
                        }
                    }
                }
            }

            // Para compatibilidade, criar array de todas as imagens
            foreach ($colorImages as $imgs) {
                $images = array_merge($images, $imgs);
            }
        } else {
            // Processar upload de imagens normal
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

        // Adicionar colorImages se estiver usando
        if ($useColorImages && !empty($colorImages)) {
            $newProduct['colorImages'] = $colorImages;
        }

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

function editProduct()
{
    try {
        $produtos = loadProductsData();
        $productId = intval($_POST['productId']);

        if (!isset($produtos[$productId])) {
            throw new Exception('Produto não encontrado');
        }

        // Verificar se usa imagens por cor
        $useColorImages = isset($_POST['useColorImages']) && $_POST['useColorImages'] === '1';

        $images = $produtos[$productId]['images'];
        $colorImages = isset($produtos[$productId]['colorImages']) ? $produtos[$productId]['colorImages'] : [];

        if ($useColorImages) {
            // Modo de imagens por cor
            $colorImagesData = !empty($_POST['colorImagesData']) ? json_decode($_POST['colorImagesData'], true) : [];
            $existingColorImages = !empty($_POST['existingColorImages']) ? json_decode($_POST['existingColorImages'], true) : [];

            $uploadDir = __DIR__ . '/uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $colorImages = [];

            foreach ($colorImagesData as $colorName) {
                $colorImages[$colorName] = [];

                // Adicionar imagens existentes desta cor
                if (isset($existingColorImages[$colorName])) {
                    $colorImages[$colorName] = $existingColorImages[$colorName];
                }

                // Processar novos uploads para esta cor
                if (isset($_FILES["colorImages_{$colorName}"]) && !empty($_FILES["colorImages_{$colorName}"]['name'][0])) {
                    foreach ($_FILES["colorImages_{$colorName}"]['tmp_name'] as $key => $tmpName) {
                        if (!empty($tmpName)) {
                            $fileName = time() . '_' . $key . '_' . $_FILES["colorImages_{$colorName}"]['name'][$key];
                            $fileName = preg_replace('/[^a-zA-Z0-9._-]/', '', $fileName);
                            $uploadPath = $uploadDir . $fileName;

                            if (move_uploaded_file($tmpName, $uploadPath)) {
                                $colorImages[$colorName][] = 'uploads/' . $fileName;
                            }
                        }
                    }
                }
            }

            // Atualizar array de imagens para compatibilidade
            $images = [];
            foreach ($colorImages as $imgs) {
                $images = array_merge($images, $imgs);
            }
        } else {
            // Modo normal de imagens
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

            // Remover colorImages se não está usando
            $colorImages = [];
        }

        // Atualizar produto
        // NOVA LÓGICA: productPrice = preço original, productOldPrice = preço com desconto
        $originalPrice = floatval($_POST['productPrice']); // Campo principal agora é preço original
        $discountPrice = !empty($_POST['productOldPrice']) ? floatval($_POST['productOldPrice']) : null;
        $discount = $discountPrice ? calculateDiscount($discountPrice, $originalPrice) : null;

        // Para manter compatibilidade no JSON: price = preço com desconto, oldPrice = preço original
        $price = $discountPrice ? $discountPrice : $originalPrice; // Se não há desconto, price = original
        $oldPrice = $discountPrice ? $originalPrice : null; // Se há desconto, oldPrice = original

        $updatedProduct = array_merge($produtos[$productId], [
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

        // Adicionar ou remover colorImages
        if ($useColorImages && !empty($colorImages)) {
            $updatedProduct['colorImages'] = $colorImages;
        } else {
            unset($updatedProduct['colorImages']);
        }

        $produtos[$productId] = $updatedProduct;

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

function deleteProduct()
{
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

function getProduct()
{
    try {
        $produtos = loadProductsData();
        $productId = intval($_GET['productId'] ?? 0);

        if ($productId <= 0) {
            throw new Exception('ID do produto inválido');
        }

        if (!isset($produtos[$productId])) {
            throw new Exception('Produto não encontrado');
        }

        // Garantir que todas as propriedades estão definidas
        $product = $produtos[$productId];
        $product['id'] = $productId;
        $product['title'] = $product['title'] ?? '';
        $product['category'] = $product['category'] ?? '';
        $product['price'] = floatval($product['price'] ?? 0);
        $product['oldPrice'] = isset($product['oldPrice']) ? floatval($product['oldPrice']) : null;
        $product['description'] = $product['description'] ?? '';
        $product['specifications'] = $product['specifications'] ?? '';
        $product['status'] = $product['status'] ?? 'ativo';
        $product['stock'] = intval($product['stock'] ?? 0);
        $product['images'] = $product['images'] ?? [];
        $product['colors'] = $product['colors'] ?? [];
        $product['sizes'] = $product['sizes'] ?? [];

        // Limpar buffer e enviar JSON
        ob_clean();
        echo json_encode(['success' => true, 'product' => $product], JSON_UNESCAPED_UNICODE);

    } catch (Exception $e) {
        ob_clean();
        http_response_code(404);
        echo json_encode(['error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
    }
}

// Função para fazer upload de avatar
function uploadAvatar()
{
    try {
        if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('Nenhum arquivo enviado ou erro no upload');
        }

        $uploadDir = __DIR__ . '/uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $fileName = time() . '_avatar_' . $_FILES['avatar']['name'];
        $uploadPath = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['avatar']['tmp_name'], $uploadPath)) {
            echo json_encode([
                'success' => true,
                'avatar' => 'uploads/' . $fileName,
                'message' => 'Avatar enviado com sucesso'
            ]);
        } else {
            throw new Exception('Erro ao mover arquivo enviado');
        }

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
}

// Função para buscar produto por ID (para evitar erro 404)
function getProductId()
{
    try {
        $produtos = loadProductsData();
        $productId = intval($_GET['productId'] ?? $_POST['productId'] ?? 0);

        if (!isset($produtos[$productId])) {
            throw new Exception('Produto não encontrado');
        }

        echo json_encode(['success' => true, 'product' => $produtos[$productId]], JSON_UNESCAPED_UNICODE);

    } catch (Exception $e) {
        http_response_code(404);
        echo json_encode(['error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
    }
}

// Garantir que o buffer seja enviado
ob_end_flush();
?>
<?php
declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/php-error.log');

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$BASE = __DIR__;

// caminhos ABSOLUTOS para tudo que era relativo
require_once $BASE . '/config/produtos.php';

$arquivo_mensagens = $BASE . '/data/mensagens.json';
$arquivo_destaque = $BASE . '/data/produto-destaque.json';
$arquivo_avaliacoes = $BASE . '/data/avaliacoes.json';

// garante que a pasta data exista (evita 500 em file_put_contents)
if (!is_dir($BASE . '/data')) {
    mkdir($BASE . '/data', 0777, true);
}


// Configurações de login simples (em produção, use hash de senha e banco de dados)
$admin_user = 'admin';
$admin_pass = 'mia2025';

// Verificar login
if (isset($_POST['login'])) {
    if (($_POST['username'] ?? '') === $admin_user && ($_POST['password'] ?? '') === $admin_pass) {
        $_SESSION['admin_logged'] = true;
        header('Location: ./admin.php');
        exit;
    } else {
        $error = 'Usuário ou senha incorretos';
    }
}

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: ./admin.php');
    exit;
}

// Verificar se está logado
// Verificar se está logado
$logged_in = isset($_SESSION['admin_logged']) && $_SESSION['admin_logged'];

// ========================
// Conteúdo restrito (só se logado)
// ========================
if ($logged_in) {
    $hoje = date('Y-m-d'); // define antes de usar no closure

    // --- Processar salvamento da seção destacada ---
    if (isset($_POST['action']) && $_POST['action'] === 'save_destaque') {
        $destaque_config = [
            'ativo' => isset($_POST['destaque_ativo']),
            'titulo' => $_POST['destaque_titulo'] ?? '',
            'descricao' => $_POST['destaque_descricao'] ?? '',
            'produto_id' => $_POST['destaque_produto_id'] ?? '',
            'botao_texto' => 'Compre Já',
            'cor_fundo' => '#520100',
            'cor_texto' => '#ffffff',
            'posicao' => 'antes'
        ];

        file_put_contents($arquivo_destaque, json_encode($destaque_config, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        header('Location: admin.php');
        exit;
    }

    // --- Mensagens ---
    $mensagens_stats = ['total' => 0, 'novas' => 0, 'hoje' => 0];

    if (file_exists($arquivo_mensagens)) {
        $conteudo = file_get_contents($arquivo_mensagens);
        $mensagens = json_decode($conteudo, true) ?: [];

        $mensagens_stats['total'] = count($mensagens);

        $mensagens_stats['novas'] = count(array_filter($mensagens, function ($m) {
            return (($m['status'] ?? '') === 'nova');
        }));

        $mensagens_stats['hoje'] = count(array_filter($mensagens, function ($m) use ($hoje) {
            $ts = strtotime($m['data_envio'] ?? '');
            return $ts !== false && date('Y-m-d', $ts) === $hoje;
        }));
    }
    // Roteamento removido - agora o layout completo é renderizado abaixo


    // --- Produto destacado ---
    $produto_destaque = [
        'ativo' => false,
        'titulo' => 'Produto Especial',
        'descricao' => 'Descubra nossa peça mais exclusiva',
        'produto_id' => '',
        'botao_texto' => 'Ver Produto',
        'cor_fundo' => '#520100',
        'cor_texto' => '#ffffff',
        'posicao' => 'antes'
    ];

    if (file_exists($arquivo_destaque)) {
        $dados_destaque = json_decode(file_get_contents($arquivo_destaque), true);
        if (is_array($dados_destaque)) {
            $produto_destaque = array_merge($produto_destaque, $dados_destaque);
        }
    }
}


?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Administrativo - MIA</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap');
        @import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css');

        :root {
            --primary: #520100;
            --secondary: #8a4d99;
            --accent: #fcf8f1;
            --dark: #1a1a1a;
            --white: #ffffff;
            --glass: rgba(252, 248, 241, 0.9);
            --glass-border: rgba(82, 1, 0, 0.2);
            --success: #00d4aa;
            --warning: #ff6b35;
            --danger: #ff4757;
            --info: #3742fa;
            --sidebar-width: 260px;
            --header-height: 80px;
            --border-radius: 24px;
            --border-radius-sm: 12px;
            --shadow-glass: 0 8px 32px rgba(0, 0, 0, 0.1);
            --transition: all 0.4s cubic-bezier(0.23, 1, 0.32, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--accent);
            color: var(--primary);
            line-height: 1.6;
            font-weight: 400;
            min-height: 100vh;
            overflow-x: hidden;
        }



        /* LOGIN STYLES */
        .login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
            position: relative;
            z-index: 2;
        }

        .login-form {
            background: var(--glass);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            padding: 60px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-glass);
            width: 100%;
            max-width: 500px;
            position: relative;
            overflow: hidden;
        }

        .login-form::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 2px;
            background: var(--primary);
            animation: shimmer 3s ease-in-out infinite;
        }

        @keyframes shimmer {

            0%,
            100% {
                opacity: 0;
            }

            50% {
                opacity: 1;
            }
        }

        .login-form h1 {
            text-align: center;
            color: var(--primary);
            margin-bottom: 50px;
            font-weight: 800;
            font-size: 36px;
            letter-spacing: -1px;
            text-shadow: 0 2px 10px rgba(82, 1, 0, 0.2);
            position: relative;
        }

        .login-form h1::after {
            content: '🔐';
            position: absolute;
            top: -60px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 48px;
            filter: drop-shadow(0 4px 20px rgba(0, 0, 0, 0.3));
        }

        .form-group {
            margin-bottom: 30px;
            position: relative;
        }

        .form-group label {
            display: block;
            margin-bottom: 12px;
            color: var(--primary);
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
            opacity: 1;
        }

        .form-group input {
            width: 100%;
            padding: 18px 24px;
            border: 2px solid var(--secondary);
            border-radius: var(--border-radius-sm);
            font-size: 16px;
            transition: var(--transition);
            background: var(--white);
            color: var(--primary);
            font-family: 'Poppins', sans-serif;
            font-weight: 500;
        }

        .form-group input::placeholder {
            color: var(--secondary);
            opacity: 0.7;
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--primary);
            background: var(--accent);
            transform: translateY(-2px);
            box-shadow:
                0 8px 32px rgba(82, 1, 0, 0.2),
                0 0 0 3px rgba(82, 1, 0, 0.1);
        }

        .btn {
            width: 100%;
            padding: 20px 24px;
            background: var(--primary);
            color: var(--white);
            border: none;
            border-radius: var(--border-radius-sm);
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: var(--transition);
            text-transform: uppercase;
            letter-spacing: 1px;
            position: relative;
            overflow: hidden;
            margin-top: 20px;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.3);
            transition: left 0.6s ease;
        }

        .btn:hover::before {
            left: 100%;
        }

        .btn:hover {
            transform: translateY(-4px);
            box-shadow:
                0 15px 35px rgba(82, 1, 0, 0.4),
                0 5px 15px rgba(0, 0, 0, 0.3);
        }

        .btn:active {
            transform: translateY(-2px);
        }

        .error {
            color: #721c24;
            text-align: center;
            margin-bottom: 25px;
            padding: 16px;
            background: #f8d7da;
            border-radius: var(--border-radius-sm);
            border: 2px solid #f5c6cb;
            font-weight: 600;
        }

        /* ADMIN LAYOUT */
        .admin-layout {
            display: flex;
            min-height: 100vh;
            position: relative;
        }

        /* SIDEBAR */
        .admin-sidebar {
            background: var(--primary);
            border-right: 2px solid var(--secondary);
            padding: 40px 0;
            position: fixed;
            left: 0;
            top: 0;
            width: var(--sidebar-width);
            height: 100vh;
            overflow-y: auto;
            z-index: 100;
        }

        .sidebar-header {
            padding: 0 30px 40px;
            border-bottom: 2px solid var(--secondary);
            margin-bottom: 40px;
        }

        .sidebar-logo {
            font-size: 24px;
            font-weight: 800;
            color: var(--white);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 12px;
            letter-spacing: -0.5px;
        }

        .sidebar-logo::before {
            content: '🚀';
            font-size: 28px;
            filter: drop-shadow(0 2px 10px rgba(0, 0, 0, 0.3));
        }

        .sidebar-nav {
            padding: 0 20px;
        }

        .nav-item {
            margin-bottom: 8px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 16px 20px;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            border-radius: var(--border-radius-sm);
            transition: var(--transition);
            font-weight: 500;
            position: relative;
            overflow: hidden;
        }

        .nav-link::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: var(--primary);
            transform: scaleY(0);
            transition: var(--transition);
        }

        .nav-link:hover,
        .nav-link.active {
            background: rgba(255, 255, 255, 0.1);
            color: var(--white);
            transform: translateX(8px);
        }

        .nav-link:hover::before,
        .nav-link.active::before {
            transform: scaleY(1);
        }

        .nav-icon {
            width: 20px;
            text-align: center;
            font-size: 18px;
        }

        /* MAIN CONTENT */
        .admin-main {
            flex: 1;
            min-height: 100vh;
            margin-left: var(--sidebar-width);
            width: calc(100% - var(--sidebar-width));
        }

        .admin-header {
            background: transparent;
            border-bottom: none;
            padding: 0 40px;
            height: var(--header-height);
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 90;
        }

        .header-title {
            font-size: 28px;
            font-weight: 700;
            color: var(--primary);
            letter-spacing: -0.5px;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .header-btn {
            padding: 10px 20px;
            background: var(--secondary);
            border: 2px solid var(--primary);
            color: var(--white);
            text-decoration: none;
            border-radius: var(--border-radius-sm);
            font-weight: 500;
            transition: var(--transition);
        }

        .header-btn:hover {
            background: var(--primary);
            transform: translateY(-2px);
            text-decoration: none;
            color: var(--white);
        }

        /* ADMIN CONTENT */
        .admin-content {
            padding: 40px;
        }

        /* GLASS CARDS */
        .glass-card {
            background: var(--white);
            border: 2px solid var(--secondary);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-glass);
            overflow: hidden;
            transition: var(--transition);
        }



        .products-table {
            background: var(--white);
            border-radius: var(--border-radius);
            overflow: hidden;
            border: 2px solid var(--secondary);
            width: 100%;
            margin-top: 30px;
            position: relative;
        }

        .table-wrapper {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .table-wrapper::-webkit-scrollbar {
            height: 8px;
        }

        .table-wrapper::-webkit-scrollbar-track {
            background: var(--accent);
            border-radius: 10px;
        }

        .table-wrapper::-webkit-scrollbar-thumb {
            background: var(--secondary);
            border-radius: 10px;
        }

        .table-wrapper::-webkit-scrollbar-thumb:hover {
            background: var(--primary);
        }

        .table-header {
            background: var(--secondary);
            color: var(--white);
            padding: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid var(--primary);
            border-radius: var(--border-radius-sm) var(--border-radius-sm) 0 0;
        }

        .table-header h2 {
            font-size: 24px;
            font-weight: 700;
            letter-spacing: -0.5px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .table-header h2::before {
            content: '';
            font-size: 28px;
            filter: drop-shadow(0 2px 10px rgba(0, 0, 0, 0.3));
        }

        .reviews-section .table-header h2::before {
            content: '';
        }

        .add-btn {
            background: var(--secondary);
            color: var(--white);
            padding: 14px 28px;
            text-decoration: none;
            border-radius: var(--border-radius-sm);
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            position: relative;
        }

        .add-btn::before {
            content: '';
            font-size: 16px;
        }

        .add-btn:hover {
            background: var(--primary);
            text-decoration: none;
            color: var(--white);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 800px;
        }

        th,
        td {
            padding: 20px;
            text-align: left;
            border-bottom: 1px solid var(--secondary);
        }

        th {
            background: var(--accent);
            font-weight: 700;
            color: var(--primary);
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        td {
            color: var(--primary);
            font-weight: 500;
            background: var(--white);
        }

        tr {
            transition: var(--transition);
        }

        tr:hover td {
            background: var(--accent);
            color: var(--primary);
        }

        .product-img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: var(--border-radius-sm);
            border: 2px solid var(--gray-medium);
            transition: var(--transition);
        }

        .product-img:hover {
            transform: scale(1.1);
            border-color: var(--primary);
            box-shadow: var(--shadow);
        }

        .action-btns {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .view-btn,
        .edit-btn,
        .delete-btn {
            padding: 8px 16px;
            border: none;
            border-radius: var(--border-radius-sm);
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .view-btn {
            background: var(--info);
            color: var(--white);
        }

        .view-btn:hover {
            background: #138496;
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(23, 162, 184, 0.3);
            color: var(--white);
            text-decoration: none;
        }

        .edit-btn {
            background: var(--secondary);
            color: var(--white);
        }

        .edit-btn:hover {
            background: #7a4187;
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(138, 77, 153, 0.3);
            color: var(--white);
            text-decoration: none;
        }

        .delete-btn {
            background: var(--danger);
            color: var(--white);
        }

        .delete-btn:hover {
            background: #c82333;
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(220, 53, 69, 0.3);
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-ativo {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .status-inativo {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        /* MODAL STYLES */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(38, 37, 35, 0.8);
            backdrop-filter: blur(10px);
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .modal-content {
            background: var(--white);
            margin: 2% auto;
            padding: 0;
            border-radius: var(--border-radius);
            width: 95%;
            max-width: 1000px;
            max-height: 90vh;
            overflow-y: auto;
            overflow-x: hidden;
            box-shadow: var(--shadow-hover);
            animation: modalSlideIn 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid var(--gray-medium);
        }

        @keyframes modalSlideIn {
            from {
                transform: translateY(-100px) scale(0.9);
                opacity: 0;
            }

            to {
                transform: translateY(0) scale(1);
                opacity: 1;
            }
        }

        .modal-header {
            background: var(--primary);
            color: var(--white);
            padding: 30px 40px;
            border-radius: var(--border-radius) var(--border-radius) 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h3 {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        .close {
            color: var(--white);
            font-size: 32px;
            font-weight: 300;
            cursor: pointer;
            transition: var(--transition);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.1);
        }

        .close:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: rotate(90deg);
        }

        .modal-body {
            padding: 50px;
            background: var(--accent);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }

        .form-col {
            display: flex;
            flex-direction: column;
        }

        .form-group {
            margin-bottom: 30px;
        }

        .form-group label {
            display: block;
            margin-bottom: 12px;
            color: var(--dark);
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 18px 24px;
            border: 2px solid var(--gray-medium);
            border-radius: var(--border-radius-sm);
            font-size: 16px;
            transition: var(--transition);
            background: var(--white);
            font-family: 'Inter', sans-serif;
            color: #212529;
            font-weight: 500;
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--primary);
            transform: translateY(-2px);
            box-shadow: 0 8px 32px rgba(82, 1, 0, 0.1);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 140px;
            line-height: 1.6;
        }

        .form-group input::placeholder,
        .form-group textarea::placeholder,
        .form-group select::placeholder {
            color: #6c757d;
            opacity: 0.7;
            font-weight: 400;
        }

        .form-group input[type="number"] {
            -moz-appearance: textfield;
            font-variant-numeric: tabular-nums;
        }

        .form-group input[type="number"]::-webkit-inner-spin-button,
        .form-group input[type="number"]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        .image-upload {
            border: 3px dashed var(--gray-medium);
            border-radius: var(--border-radius-sm);
            padding: 40px;
            text-align: center;
            cursor: pointer;
            transition: var(--transition);
            background: var(--white);
        }

        .image-upload:hover {
            border-color: var(--primary);
            background: rgba(82, 1, 0, 0.02);
            transform: translateY(-2px);
        }

        .image-upload input {
            display: none;
        }

        .image-preview {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 16px;
            margin-top: 20px;
        }

        .preview-item {
            position: relative;
            aspect-ratio: 1;
            border-radius: var(--border-radius-sm);
            overflow: hidden;
            box-shadow: var(--shadow);
        }

        .preview-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: var(--transition);
        }

        .preview-item:hover img {
            transform: scale(1.1);
        }

        .remove-image {
            position: absolute;
            top: 8px;
            right: 8px;
            background: var(--danger);
            color: var(--white);
            border: none;
            border-radius: 50%;
            width: 32px;
            height: 32px;
            cursor: pointer;
            font-size: 14px;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .remove-image:hover {
            background: #c82333;
            transform: scale(1.1);
        }

        .existing-image {
            border: 3px solid var(--success);
        }

        .image-type {
            position: absolute;
            bottom: 8px;
            left: 8px;
            background: rgba(40, 167, 69, 0.9);
            color: var(--white);
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .modal-footer {
            padding: 30px 50px;
            border-top: 1px solid var(--gray-medium);
            display: flex;
            gap: 16px;
            justify-content: flex-end;
            background: var(--white);
            border-radius: 0 0 var(--border-radius) var(--border-radius);
        }

        .btn-secondary,
        .btn-primary {
            padding: 14px 28px;
            border: none;
            border-radius: var(--border-radius-sm);
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: var(--transition);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn-secondary {
            background: var(--secondary);
            color: var(--white);
        }

        .btn-secondary:hover {
            background: var(--primary);
        }

        .btn-primary {
            background: var(--primary);
            color: var(--white);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 32px rgba(82, 1, 0, 0.3);
        }

        /* STATS CARDS */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 24px;
            margin-bottom: 50px;
            width: 100%;
        }

        @media (max-width: 1400px) {
            .stats-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (max-width: 1000px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        .stat-card {
            background: var(--white);
            border: none;
            padding: 40px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-glass);
            position: relative;
            overflow: hidden;
            transition: var(--transition);
            animation: fadeInUp 0.6s ease-out forwards;
            opacity: 0;
            transform: translateY(30px);
        }

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .stat-card:nth-child(1) {
            animation-delay: 0.1s;
        }

        .stat-card:nth-child(2) {
            animation-delay: 0.2s;
        }

        .stat-card:nth-child(3) {
            animation-delay: 0.3s;
        }

        .stat-card:nth-child(4) {
            animation-delay: 0.4s;
        }

        .stat-card:nth-child(5) {
            animation-delay: 0.5s;
        }

        .stat-card:nth-child(6) {
            animation-delay: 0.6s;
        }

        /* Featured Section Input Styles */
        .featured-product-section input:focus,
        .featured-product-section select:focus,
        .featured-product-section textarea:focus {
            outline: none;
            border-color: var(--primary) !important;
            background: var(--accent) !important;
            transform: translateY(-1px);
        }

        .featured-product-section input::placeholder,
        .featured-product-section textarea::placeholder {
            color: var(--secondary);
        }

        .featured-product-section select option {
            background: var(--white);
            color: var(--primary);
        }

        .stat-card:nth-child(7) {
            animation-delay: 0.7s;
        }

        .stat-card:nth-child(8) {
            animation-delay: 0.8s;
        }



        .stat-card .stat-icon {
            position: absolute;
            top: 20px;
            right: 20px;
            width: 60px;
            height: 60px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: rgba(255, 255, 255, 0.8);
            transition: var(--transition);
        }





        .stat-card h3 {
            color: var(--primary);
            font-size: 48px;
            font-weight: 800;
            margin-bottom: 12px;
            letter-spacing: -2px;
        }

        .stat-card p {
            color: var(--secondary);
            margin: 0;
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .messages-section .message-preview {
            transition: var(--transition);
            border-radius: var(--border-radius-sm);
            overflow: hidden;
        }

        .messages-section .message-preview:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow);
        }

        .messages-card,
        .new-messages-card {
            cursor: pointer;
            transition: var(--transition);
            border-radius: var(--border-radius);
            overflow: hidden;
            position: relative;
        }

        .messages-card::before,
        .new-messages-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(82, 1, 0, 0.1);
            opacity: 0;
            transition: var(--transition);
        }

        .messages-card:hover::before,
        .new-messages-card:hover::before {
            opacity: 1;
        }

        .messages-card:hover,
        .new-messages-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-hover);
        }

        .search-bar {
            padding: 12px 20px;
            border: 2px solid var(--white);
            border-radius: var(--border-radius-sm);
            font-size: 14px;
            width: 250px;
            transition: var(--transition);
            background: var(--white);
            color: var(--primary);
            font-family: 'Poppins', sans-serif;
        }

        .search-bar::placeholder {
            color: var(--secondary);
        }

        .search-bar:focus {
            outline: none;
            border-color: var(--primary);
            background: var(--accent);
        }

        /* SPECIAL SECTIONS */
        .discount-section,
        .colors-section {
            background: var(--white);
            border: 2px solid var(--gray-medium);
            border-radius: var(--border-radius);
            padding: 35px;
            margin-bottom: 30px;
            box-shadow: var(--shadow);
            position: relative;
            overflow: hidden;
        }

        .discount-section::before,
        .colors-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: var(--primary);
        }

        .section-header {
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .section-header h4 {
            margin: 0;
            font-size: 20px;
            color: var(--primary);
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        .colors-counter {
            background: var(--primary);
            color: var(--white);
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .colors-hint {
            margin: 15px 0 25px 0;
            padding: 16px 20px;
            background: #e7f3ff;
            border-left: none;
            border-radius: var(--border-radius-sm);
            font-size: 14px;
            color: var(--info);
            font-weight: 500;
        }

        .status-priority-section {
            background: var(--accent);
            border: 2px solid var(--gray-medium);
            border-radius: var(--border-radius);
            padding: 30px;
            box-shadow: var(--shadow);
            position: relative;
            overflow: hidden;
        }

        .status-priority-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: var(--warning);
        }

        /* MOBILE SIDEBAR */
        .mobile-menu-btn {
            display: none;
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 200;
            background: var(--primary);
            border: 2px solid var(--primary);
            border-radius: var(--border-radius-sm);
            padding: 12px;
            color: var(--white);
            font-size: 20px;
            cursor: pointer;
            transition: var(--transition);
            box-shadow: 0 4px 12px rgba(82, 1, 0, 0.3);
        }

        .mobile-menu-btn::before,
        .mobile-menu-btn::after {
            content: none !important;
        }

        .mobile-menu-btn:hover {
            background: #6b0100;
            border-color: #6b0100;
            transform: scale(1.05);
            box-shadow: 0 6px 16px rgba(82, 1, 0, 0.4);
        }

        /* RESPONSIVE */
        @media (max-width: 1200px) {
            :root {
                --sidebar-width: 250px;
            }

            .admin-content {
                padding: 30px;
            }
        }

        @media (max-width: 992px) {
            .login-form {
                padding: 40px;
                margin: 20px;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 20px;
            }

            .form-row {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .table-header {
                flex-direction: column;
                gap: 20px;
                text-align: center;
            }

            .action-btns {
                justify-content: center;
            }
        }

        @media (max-width: 768px) {
            .mobile-menu-btn {
                display: block;
            }

            .admin-layout {
                grid-template-columns: 1fr;
            }

            .admin-sidebar {
                position: fixed;
                left: -100%;
                transition: left 0.3s ease;
                z-index: 150;
            }

            .admin-sidebar.open {
                left: 0;
            }

            .admin-main {
                margin-left: 0;
            }

            .admin-header {
                padding: 0 20px 0 70px;
            }

            .header-title {
                font-size: 24px;
            }

            .admin-content {
                padding: 20px;
            }

            .login-form {
                padding: 30px;
                margin: 15px;
            }

            .login-form h1 {
                font-size: 28px;
                margin-bottom: 40px;
            }

            .stats-grid {
                grid-template-columns: 1fr;
                gap: 16px;
            }

            .stat-card {
                padding: 30px;
                text-align: center;
            }

            .stat-card h3 {
                font-size: 40px;
            }

            /* Responsividade da tabela de produtos */
            .products-table {
                margin-top: 20px;
            }

            .table-wrapper {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                position: relative;
                padding-bottom: 45px;
            }

            .table-wrapper::after {
                content: '← Deslize para ver mais →';
                position: absolute;
                bottom: 10px;
                left: 50%;
                transform: translateX(-50%);
                background: rgba(82, 1, 0, 0.9);
                color: white;
                padding: 8px 16px;
                border-radius: 20px;
                font-size: 11px;
                font-weight: 600;
                pointer-events: none;
                opacity: 0;
                animation: fadeInOut 3s ease-in-out infinite;
                white-space: nowrap;
                z-index: 10;
            }

            .table-wrapper.scrolled::after {
                display: none;
            }

            @keyframes fadeInOut {

                0%,
                100% {
                    opacity: 0;
                }

                50% {
                    opacity: 1;
                }
            }

            .table-header {
                flex-direction: column;
                gap: 15px;
                padding: 20px;
                align-items: stretch;
            }

            .table-header h2 {
                font-size: 20px;
                text-align: center;
            }

            .table-header>div {
                flex-direction: column;
                width: 100%;
            }

            .search-bar {
                width: 100%;
            }

            .add-btn {
                width: 100%;
                justify-content: center;
                padding: 12px 20px;
            }

            table {
                min-width: 800px;
            }

            th,
            td {
                padding: 12px 8px;
                font-size: 12px;
            }

            .product-img {
                width: 50px;
                height: 50px;
            }

            .action-btns {
                flex-direction: column;
                gap: 5px;
            }

            .view-btn,
            .edit-btn,
            .delete-btn {
                padding: 6px 12px;
                font-size: 11px;
                width: 100%;
                justify-content: center;
            }

            .modal-body {
                padding: 30px 20px;
            }

            .modal-content {
                width: 98%;
                margin: 1% auto;
                max-height: 95vh;
            }

            .modal-header {
                padding: 20px;
            }

            .modal-header h3 {
                font-size: 22px;
            }

            .modal-footer {
                padding: 20px;
                flex-direction: column;
            }

            .modal-footer button {
                width: 100%;
                margin: 5px 0;
            }

            .discount-section,
            .colors-section,
            .status-priority-section {
                padding: 20px;
                margin-bottom: 20px;
            }

            .form-row {
                grid-template-columns: 1fr;
                gap: 15px;
            }

            .discount-section .form-row {
                gap: 20px;
            }

            .discount-preview input {
                font-size: 14px;
                padding: 14px 12px;
                word-break: break-word;
                white-space: normal;
                text-align: left;
            }

            .form-group label {
                font-size: 13px;
            }

            .form-group input {
                font-size: 15px;
                padding: 14px 16px;
            }

            .form-group input,
            .form-group textarea,
            .form-group select {
                font-size: 16px;
                padding: 14px 16px;
                color: #212529;
                font-weight: 500;
                -webkit-text-size-adjust: 100%;
            }

            .form-group textarea {
                font-size: 16px;
                line-height: 1.5;
            }

            .form-group input::placeholder,
            .form-group textarea::placeholder {
                color: #6c757d;
                opacity: 1;
            }

            .colors-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 12px;
            }

            .highlights-section {
                padding: 20px;
            }

            .highlights-grid {
                grid-template-columns: 1fr;
                gap: 15px;
            }

            .highlight-option {
                padding: 15px;
            }

            .checkbox-container {
                gap: 10px;
            }

            .checkmark {
                height: 20px;
                width: 20px;
            }

            .highlight-text strong {
                font-size: 14px;
                line-height: 20px;
            }

            .highlight-text small {
                font-size: 12px;
            }
        }

        @media (max-width: 480px) {
            .login-form {
                padding: 25px;
                margin: 10px;
            }

            .login-form h1 {
                font-size: 24px;
            }

            .header-title {
                font-size: 20px;
            }

            .stat-card h3 {
                font-size: 32px;
            }

            .modal-header h3 {
                font-size: 20px;
            }

            .table-header {
                padding: 15px;
            }

            .table-header h2 {
                font-size: 16px;
            }

            .table-header h2::before {
                font-size: 20px;
            }

            .search-bar {
                font-size: 14px;
                padding: 10px;
            }

            .add-btn {
                font-size: 12px;
                padding: 10px 16px;
            }

            th,
            td {
                padding: 10px 6px;
                font-size: 11px;
            }

            .product-img {
                width: 45px;
                height: 45px;
            }

            .action-btns {
                gap: 4px;
            }

            .view-btn,
            .edit-btn,
            .delete-btn {
                padding: 5px 10px;
                font-size: 10px;
            }

            .admin-content {
                padding: 15px;
            }

            .discount-section,
            .colors-section,
            .status-priority-section {
                padding: 15px;
            }

            .highlights-section {
                padding: 15px;
            }

            .highlights-grid {
                gap: 12px;
            }

            .highlight-option {
                padding: 12px;
            }

            .section-header h4 {
                font-size: 16px;
            }

            .discount-preview input {
                font-size: 13px;
                padding: 12px 10px;
                line-height: 1.4;
            }

            .form-group label {
                font-size: 12px;
                margin-bottom: 8px;
            }

            .form-group input,
            .form-group textarea,
            .form-group select {
                font-size: 14px;
                padding: 12px 14px;
            }

            .form-group input,
            .form-group textarea,
            .form-group select {
                font-size: 16px;
                padding: 14px 16px;
                color: #212529;
                font-weight: 500;
                -webkit-text-size-adjust: 100%;
                line-height: 1.5;
            }

            .form-group textarea {
                min-height: 120px;
            }

            .form-group input::placeholder,
            .form-group textarea::placeholder,
            .form-group select::placeholder {
                color: #6c757d;
                opacity: 1;
                font-size: 15px;
            }

            .colors-grid {
                grid-template-columns: 1fr 1fr;
                gap: 10px;
            }

            .color-option {
                padding: 12px 8px;
            }

            .color-option span {
                font-size: 12px;
            }

            .modal-body {
                padding: 20px 15px;
            }

            .modal-content {
                width: 100%;
                margin: 0;
                max-height: 100vh;
                border-radius: 0;
            }

            .modal-header {
                padding: 18px 15px;
            }

            .modal-header h3 {
                font-size: 18px;
            }

            .close {
                font-size: 28px;
            }

            .modal-footer {
                padding: 15px;
            }

            .modal-footer button {
                font-size: 14px;
                padding: 12px 20px;
            }
        }

        @media (max-width: 350px) {
            .highlights-section {
                padding: 12px;
            }

            .highlights-grid {
                grid-template-columns: 1fr;
                gap: 10px;
            }

            .highlight-option {
                padding: 10px;
            }

            .checkbox-container {
                gap: 10px;
                align-items: flex-start;
            }

            .checkmark {
                height: 20px;
                width: 20px;
                margin-top: 0;
                flex-shrink: 0;
            }

            .checkmark:after {
                left: 5px;
                top: 1px;
                width: 5px;
                height: 10px;
            }

            .highlight-text {
                gap: 3px;
            }

            .highlight-text strong {
                font-size: 13px;
                line-height: 20px;
            }

            .highlight-text small {
                font-size: 11px;
                line-height: 1.3;
            }

            .section-header h4 {
                font-size: 14px;
            }

            .discount-section,
            .colors-section,
            .status-priority-section {
                padding: 12px;
            }

            .form-group input,
            .form-group textarea,
            .form-group select {
                font-size: 13px;
                padding: 10px 12px;
            }

            .form-group input,
            .form-group textarea,
            .form-group select {
                font-size: 15px;
                padding: 12px 14px;
                color: #212529;
                font-weight: 500;
                -webkit-text-size-adjust: 100%;
                line-height: 1.5;
                border-width: 2px;
            }

            .form-group textarea {
                min-height: 100px;
                font-size: 15px;
            }

            .form-group input::placeholder,
            .form-group textarea::placeholder,
            .form-group select::placeholder {
                color: #6c757d;
                opacity: 1;
                font-size: 14px;
            }

            .form-group label {
                font-size: 11px;
            }

            .modal-header {
                padding: 15px 12px;
            }

            .modal-header h3 {
                font-size: 16px;
            }

            .modal-body {
                padding: 15px 12px;
            }

            .modal-footer {
                padding: 12px;
            }

            .modal-footer button {
                font-size: 13px;
                padding: 10px 16px;
            }
        }

        /* SIDEBAR OVERLAY */
        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 140;
            opacity: 0;
            visibility: hidden;
            transition: var(--transition);
        }

        .sidebar-overlay.show {
            opacity: 1;
            visibility: visible;
        }

        margin-bottom: 30px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .status-priority-section .form-group {
            margin-bottom: 0;
        }

        .status-priority-section label {
            font-size: 16px;
            font-weight: 700;
            color: #495057;
            margin-bottom: 12px;
        }

        .status-priority-section select {
            font-size: 16px;
            font-weight: 600;
            padding: 15px;
            border: 2px solid #ced4da;
            background: white;
        }

        .status-priority-section select:focus {
            border-color: #520100;
            box-shadow: 0 0 0 3px rgba(82, 1, 0, 0.1);
        }

        /* Switch Toggle */
        .discount-toggle {
            margin-bottom: 20px;
        }

        .switch-container {
            display: flex;
            align-items: center;
            gap: 15px;
            cursor: pointer;
            font-weight: 500;
        }

        .switch-input {
            position: relative;
            width: 50px;
            height: 25px;
            appearance: none;
            background: #ccc;
            border-radius: 50px;
            transition: all 0.3s;
            cursor: pointer;
        }

        .switch-input:checked {
            background: #520100;
        }

        .switch-input::after {
            content: '';
            position: absolute;
            top: 2px;
            left: 2px;
            width: 21px;
            height: 21px;
            background: white;
            border-radius: 50%;
            transition: all 0.3s;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .switch-input:checked::after {
            transform: translateX(25px);
        }

        .switch-text {
            color: #333;
            font-size: 16px;
        }

        /* Campos de Desconto */
        .discount-fields {
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                max-height: 0;
            }

            to {
                opacity: 1;
                max-height: 200px;
            }
        }

        .discount-preview input {
            background: #e8f5e8 !important;
            border: 2px solid #28a745 !important;
            color: #155724 !important;
            font-weight: 600;
            text-align: center;
            min-height: 56px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: visible;
            white-space: normal;
            line-height: 1.5;
        }

        /* Grid de Cores */
        .colors-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 15px;
        }

        .color-option {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            padding: 15px;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s;
            background: white;
            position: relative;
        }

        .color-option:hover {
            border-color: #520100;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(82, 1, 0, 0.15);
        }

        .color-option.active {
            border-color: #520100;
            background: #fff5f5;
            box-shadow: 0 4px 12px rgba(82, 1, 0, 0.2);
        }

        .color-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 3px solid white;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        }

        .color-option span {
            font-size: 14px;
            font-weight: 500;
            color: #333;
        }

        .color-option.active span {
            color: #520100;
            font-weight: 600;
        }

        .color-check {
            position: absolute;
            top: 8px;
            right: 8px;
            background: #28a745;
            color: white;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: bold;
            opacity: 0;
            transform: scale(0);
            transition: all 0.3s;
        }

        .color-option.active .color-check {
            opacity: 1;
            transform: scale(1);
        }

        /* Seção de Destaques */
        .highlights-section {
            background: #fff9e6;
            border: 2px solid #ffc107;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 25px;
        }

        .highlights-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .highlight-option {
            background: white;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 15px;
            transition: all 0.3s;
        }

        .highlight-option:hover {
            border-color: #ffc107;
            box-shadow: 0 2px 8px rgba(255, 193, 7, 0.2);
        }

        .checkbox-container {
            display: grid;
            grid-template-columns: auto 1fr;
            gap: 12px;
            cursor: pointer;
            position: relative;
            align-items: start;
        }

        .checkbox-container input[type="checkbox"] {
            position: absolute;
            opacity: 0;
            cursor: pointer;
            height: 0;
            width: 0;
        }

        .checkmark {
            height: 22px;
            width: 22px;
            background-color: #eee;
            border: 2px solid #ddd;
            border-radius: 4px;
            position: relative;
            flex-shrink: 0;
            margin-top: 0;
            transition: all 0.3s;
            grid-row: 1 / span 2;
            align-self: start;
        }

        .checkbox-container:hover .checkmark {
            background-color: #f8f9fa;
            border-color: #520100;
        }

        .checkbox-container input:checked~.checkmark {
            background-color: #520100;
            border-color: #520100;
        }

        .checkmark:after {
            content: "";
            position: absolute;
            display: none;
            left: 6px;
            top: 2px;
            width: 6px;
            height: 11px;
            border: solid white;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
        }

        .checkbox-container input:checked~.checkmark:after {
            display: block;
        }

        .highlight-text {
            display: contents;
        }

        .highlight-text strong {
            color: #333;
            font-size: 15px;
            line-height: 22px;
            font-weight: 600;
            grid-column: 2;
        }

        .highlight-text small {
            color: #666;
            font-size: 13px;
            line-height: 1.4;
            display: block;
            grid-column: 2;
            margin-top: 4px;
        }

        /* Sistema de Notificações Moderno */
        .notification-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            max-width: 400px;
        }

        .notification {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            border-left: 5px solid;
            transform: translateX(450px);
            opacity: 0;
            transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            position: relative;
            overflow: hidden;
        }

        .notification.show {
            transform: translateX(0);
            opacity: 1;
        }

        .notification.hide {
            transform: translateX(450px);
            opacity: 0;
        }

        .notification-success {
            border-left-color: #28a745;
            background: #d4edda;
        }

        .notification-error {
            border-left-color: #dc3545;
            background: #f8d7da;
        }

        .notification-warning {
            border-left-color: #ffc107;
            background: #fff3cd;
        }

        .notification-info {
            border-left-color: #17a2b8;
            background: #d1ecf1;
        }

        .notification-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .notification-icon {
            font-size: 24px;
            margin-right: 12px;
        }

        .notification-title {
            font-weight: 600;
            font-size: 16px;
            color: #2c3e50;
            display: flex;
            align-items: center;
        }

        .notification-message {
            color: #495057;
            font-size: 14px;
            line-height: 1.4;
            margin-left: 36px;
        }

        .notification-close {
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
            color: #6c757d;
            transition: color 0.3s;
            padding: 0;
            width: 25px;
            height: 25px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .notification-close:hover {
            color: #495057;
        }

        .notification-progress {
            position: absolute;
            bottom: 0;
            left: 0;
            height: 3px;
            background: rgba(0, 0, 0, 0.2);
            animation: notificationProgress 5s linear forwards;
        }

        @keyframes notificationProgress {
            from {
                width: 100%;
            }

            to {
                width: 0%;
            }
        }

        /* Estilos para seleção de tamanhos */
        .sizes-section {
            margin: 20px 0;
        }

        .size-option:hover {
            border-color: #8A4D99 !important;
            background: #f3e8f7 !important;
        }

        .size-option input[type="checkbox"]:checked + span {
            color: #8A4D99;
        }

        .size-option:has(input[type="checkbox"]:checked) {
            border-color: #8A4D99 !important;
            background: #f3e8f7 !important;
        }
    </style>
</head>

<body>
    <!-- Container de Notificações -->
    <div id="notificationContainer" class="notification-container"></div>

    <?php if (!$logged_in): ?>
        <!-- Tela de Login -->
        <div class="login-container">
            <form method="POST" class="login-form">
                <h1>Painel Administrativo</h1>
                <img src="icon s/logotipo.svg" alt="MIA" style="width: 100px; display: block; margin: 0 auto 30px;">

                <?php if (isset($error)): ?>
                    <div class="error"><?php echo $error; ?></div>
                <?php endif; ?>

                <div class="form-group">
                    <label for="username">Usuário:</label>
                    <input type="text" id="username" name="username" required>
                </div>

                <div class="form-group">
                    <label for="password">Senha:</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <button type="submit" name="login" class="btn">Entrar</button>
            </form>
        </div>
    <?php else: ?>
        <!-- Painel Administrativo -->
        <button class="mobile-menu-btn" onclick="toggleSidebar()">
            <i class="fas fa-bars"></i>
        </button>
        <div class="sidebar-overlay" onclick="closeSidebar()"></div>

        <div class="admin-layout">
            <!-- Sidebar -->
            <aside class="admin-sidebar">
                <div class="sidebar-header">
                    <a href="#" class="sidebar-logo">MIA Admin</a>
                </div>

                <nav class="sidebar-nav">
                    <div class="nav-item">
                        <a href="#dashboard" class="nav-link active">
                            <i class="nav-icon fas fa-chart-line"></i>
                            <span>Dashboard</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="#produtos" class="nav-link">
                            <i class="nav-icon fas fa-box"></i>
                            <span>Produtos</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="/admin-mensagens.php" class="nav-link">
                            <i class="nav-icon fas fa-envelope"></i>
                            <span>Mensagens</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="index.php" class="nav-link">
                            <i class="nav-icon fas fa-external-link-alt"></i>
                            <span>Ver Site</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="?logout=1" class="nav-link">
                            <i class="nav-icon fas fa-sign-out-alt"></i>
                            <span>Sair</span>
                        </a>
                    </div>
                </nav>
            </aside>

            <!-- Main Content -->
            <main class="admin-main">
                <header class="admin-header">
                </header>

                <div class="admin-content">
                    <!-- Mensagem de Sucesso -->
                    <?php if (isset($success_message)): ?>
                        <div
                            style="background: var(--white); color: var(--primary); padding: 20px; border-radius: var(--border-radius-sm); margin-bottom: 30px; border: 2px solid var(--success); text-align: center;">
                            ✅ <?php echo $success_message; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Dashboard Overview -->
                    <div id="dashboard" style="margin-bottom: 40px;">
                        <h2
                            style="color: var(--primary); font-size: 32px; font-weight: 700; margin-bottom: 8px; letter-spacing: -1px;">
                            Visão Geral do Sistema
                        </h2>
                        <p style="color: var(--secondary); font-size: 16px; margin-bottom: 0;">
                            Acompanhe as principais métricas do seu e-commerce em tempo real
                        </p>
                    </div>

                    <!-- Dashboard Stats -->
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-icon"><i class="fas fa-box"></i></div>
                            <h3><?php echo count(getAllProdutos()); ?></h3>
                            <p>Total de Produtos</p>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                            <h3><?php echo count(array_filter(getAllProdutos(), function ($p) {
                                return $p['status'] === 'ativo';
                            })); ?>
                            </h3>
                            <p>Produtos Ativos</p>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon"><i class="fas fa-tags"></i></div>
                            <h3><?php echo count(array_unique(array_column(getAllProdutos(), 'category'))); ?></h3>
                            <p>Categorias</p>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon"><i class="fas fa-percentage"></i></div>
                            <h3><?php echo count(array_filter(getAllProdutos(), function ($p) {
                                return !empty($p['oldPrice']);
                            })); ?>
                            </h3>
                            <p>Promoções Ativas</p>
                        </div>
                        <?php
                        $avaliacoes_stats = ['total' => 0, 'ativas' => 0];
                        if (file_exists('data/avaliacoes.json')) {
                            $conteudo_avaliacoes = file_get_contents('data/avaliacoes.json');
                            $avaliacoes_dados = json_decode($conteudo_avaliacoes, true) ?: [];
                            $avaliacoes_stats['total'] = count($avaliacoes_dados);
                            $avaliacoes_stats['ativas'] = count(array_filter($avaliacoes_dados, function ($a) {
                                return $a['ativo'] === true;
                            }));
                        }
                        ?>
                        <div class="stat-card">
                            <div class="stat-icon"><i class="fas fa-star"></i></div>
                            <h3><?php echo $avaliacoes_stats['total']; ?></h3>
                            <p>Total de Avaliações</p>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon"><i class="fas fa-star-half-alt"></i></div>
                            <h3><?php echo $avaliacoes_stats['ativas']; ?></h3>
                            <p>Avaliações Ativas</p>
                        </div>
                        <div class="stat-card messages-card">
                            <div class="stat-icon"><i class="fas fa-envelope"></i></div>
                            <h3><?php echo $mensagens_stats['total']; ?></h3>
                            <p>Total de Mensagens</p>
                        </div>
                        <div class="stat-card new-messages-card">
                            <div class="stat-icon"><i class="fas fa-envelope-open"></i></div>
                            <h3><?php echo $mensagens_stats['novas']; ?></h3>
                            <p>Mensagens Não Lidas</p>
                        </div>
                    </div>

                    <div id="produtos" class="products-table glass-card">
                        <div class="table-header">
                            <h2>Gerenciar Produtos</h2>
                            <div style="display: flex; gap: 15px; align-items: center;">
                                <input type="text" id="searchProducts" class="search-bar" placeholder="Buscar produtos..."
                                    onkeyup="filterProducts()">
                                <a href="#" class="add-btn" onclick="openAddProductModal()">+ Adicionar Produto</a>
                            </div>
                        </div>

                        <div class="table-wrapper">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Imagem</th>
                                        <th>Nome</th>
                                        <th>Categoria</th>
                                        <th>Preço</th>
                                        <th>Status</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach (getAllProdutos() as $produto): ?>
                                        <tr>
                                            <td>
                                                <img src="<?php echo htmlspecialchars($produto['images'][0]); ?>"
                                                    alt="<?php echo htmlspecialchars($produto['title']); ?>"
                                                    class="product-img">
                                            </td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($produto['title']); ?></strong><br>
                                                <small><?php echo htmlspecialchars(substr($produto['description'], 0, 50)); ?>...</small>
                                            </td>
                                            <td><?php
                                            $categoryNames = [
                                                'bolsas' => 'Bolsas',
                                                'carteiras' => 'Carteiras',
                                                'cases-capas' => 'Cases & Capas',
                                                'escritorio' => 'Escritório',
                                                'viagem' => 'Viagem',
                                                'acessorios' => 'Acessórios'
                                            ];
                                            echo htmlspecialchars($categoryNames[$produto['category']] ?? ucfirst($produto['category']));
                                            ?></td>
                                            <td>
                                                <strong><?php echo formatPrice($produto['price']); ?></strong>
                                                <?php if ($produto['oldPrice']): ?>
                                                    <br><small style="text-decoration: line-through; color: #999;">
                                                        <?php echo formatPrice($produto['oldPrice']); ?>
                                                    </small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="status-badge status-<?php echo $produto['status']; ?>">
                                                    <?php echo ucfirst($produto['status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="action-btns">
                                                    <a href="/produto-unico.php?id=<?php echo $produto['id']; ?>"
                                                        target="_blank" class="view-btn" title="Visualizar produto">Ver</a>
                                                    <a href="#" class="edit-btn"
                                                        onclick="editProduct(<?php echo $produto['id']; ?>)">Editar</a>
                                                    <button class="delete-btn"
                                                        onclick="deleteProduct(<?php echo $produto['id']; ?>)">Excluir</button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Seção de Mensagens de Contato -->
                    <div class="messages-section" style="margin-top: 40px;">
                        <div class="table-header">
                            <h2>Mensagens de Contato - Últimas 5</h2>
                            <div style="display: flex; gap: 15px; align-items: center;">
                                <a href="/admin-mensagens" class="btn-secondary" target="_blank"
                                    style="text-decoration: none;">
                                    Ver Todas as Mensagens
                                </a>
                            </div>
                        </div>

                        <?php if ($mensagens_stats['total'] > 0): ?>
                            <?php
                            $arquivo_mensagens = 'data/mensagens.json';
                            $mensagens_recentes = [];
                            if (file_exists($arquivo_mensagens)) {
                                $conteudo = file_get_contents($arquivo_mensagens);
                                $todas_mensagens = json_decode($conteudo, true) ?: [];

                                usort($todas_mensagens, function ($a, $b) {
                                    return strtotime($b['data_envio']) - strtotime($a['data_envio']);
                                });
                                $mensagens_recentes = array_slice($todas_mensagens, 0, 5);
                            }

                            function getStatusColorAdmin($status)
                            {
                                switch ($status) {
                                    case 'nova':
                                        return '#dc2626';
                                    case 'lida':
                                        return '#f59e0b';
                                    case 'respondida':
                                        return '#16a34a';
                                    default:
                                        return '#6b7280';
                                }
                            }

                            function getStatusTextAdmin($status)
                            {
                                switch ($status) {
                                    case 'nova':
                                        return 'Nova';
                                    case 'lida':
                                        return 'Lida';
                                    case 'respondida':
                                        return 'Respondida';
                                    default:
                                        return 'Desconhecido';
                                }
                            }

                            function formatarDataAdmin($data)
                            {
                                $timestamp = strtotime($data);
                                return date('d/m/Y H:i', $timestamp);
                            }
                            ?>

                            <div class="messages-grid" style="display: grid; gap: 15px; margin-top: 20px;">
                                <?php foreach ($mensagens_recentes as $msg): ?>
                                    <div class="message-preview" style="
                            background: white; 
                            border-radius: 8px; 
                            padding: 20px; 
                            border-left: none;
                            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                            transition: all 0.2s;
                        ">
                                        <div
                                            style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px; flex-wrap: wrap; gap: 10px;">
                                            <div>
                                                <h4 style="margin: 0; color: #520100; font-size: 16px;">
                                                    <?php echo htmlspecialchars($msg['nome'] . ' ' . $msg['sobrenome']); ?>
                                                </h4>
                                                <div style="font-size: 13px; color: #666; margin-top: 3px;">
                                                    <?php echo formatarDataAdmin($msg['data_envio']); ?>
                                                    | <?php echo htmlspecialchars($msg['email']); ?>
                                                    | 🆔 <?php echo substr($msg['id'], -6); ?>
                                                </div>
                                            </div>
                                            <span style="
                                    background: <?php echo getStatusColorAdmin($msg['status']); ?>;
                                    color: white;
                                    padding: 4px 8px;
                                    border-radius: 12px;
                                    font-size: 12px;
                                    font-weight: bold;
                                    text-transform: uppercase;
                                ">
                                                <?php echo getStatusTextAdmin($msg['status']); ?>
                                            </span>
                                        </div>

                                        <div style="background: #f8f9fa; padding: 10px; border-radius: 6px; margin-bottom: 10px;">
                                            <strong style="color: #520100;">
                                                <?php
                                                $assuntos = [
                                                    'duvida' => 'Dúvida sobre produto',
                                                    'orcamento' => 'Solicitação de orçamento',
                                                    'suporte' => 'Suporte técnico',
                                                    'outro' => 'Outro assunto'
                                                ];
                                                echo $assuntos[$msg['assunto']] ?? 'Assunto não especificado';
                                                ?>
                                            </strong>
                                        </div>

                                        <div style="color: #333; font-size: 14px; line-height: 1.5; margin-bottom: 15px;">
                                            <?php
                                            $mensagem = htmlspecialchars($msg['mensagem']);
                                            echo strlen($mensagem) > 120 ? substr($mensagem, 0, 120) . '...' : $mensagem;
                                            ?>
                                        </div>

                                        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                                            <?php if (!empty($msg['telefone'])): ?>
                                                <a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', $msg['telefone']); ?>"
                                                    target="_blank"
                                                    style="background: #25d366; color: white; padding: 6px 12px; border-radius: 4px; text-decoration: none; font-size: 12px;">
                                                    WhatsApp
                                                </a>
                                            <?php endif; ?>
                                            <form method="POST" action="delete-message.php" style="display: inline;"
                                                onsubmit="return confirm('Tem certeza que deseja excluir esta mensagem? Esta ação não pode ser desfeita.')">
                                                <input type="hidden" name="message_id" value="<?php echo $msg['id']; ?>">
                                                <button type="submit"
                                                    style="background: #dc2626; color: white; padding: 6px 12px; border-radius: 4px; border: none; cursor: pointer; font-size: 12px;">
                                                    Excluir
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <?php if (count($todas_mensagens) > 5): ?>
                                <div style="text-align: center; margin-top: 20px;">
                                    <a href="/admin-mensagens" target="_blank" style="
                            background: #520100; 
                            color: white; 
                            padding: 12px 24px; 
                            border-radius: 6px; 
                            text-decoration: none;
                            font-weight: 500;
                        ">
                                        Ver mais <?php echo count($todas_mensagens) - 5; ?> mensagem(s) →
                                    </a>
                                </div>
                            <?php endif; ?>

                        <?php else: ?>
                            <div style="text-align: center; padding: 40px 20px; color: #666;">
                                <div style="font-size: 48px; margin-bottom: 15px; opacity: 0.5;"></div>
                                <h3>Nenhuma mensagem ainda</h3>
                                <p>Quando os clientes enviarem mensagens pelo formulário de contato, elas aparecerão aqui.</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Seção Produto Destacado -->
                    <div class="featured-product-section glass-card" style="margin-top: 40px;">
                        <div class="table-header">
                            <h2 style="color: var(--primary);">🌟 Seção Destacada</h2>
                            <div style="font-size: 14px; color: var(--secondary); margin-top: 5px;">
                                Configure o título, descrição e produto da seção destacada na página inicial
                            </div>
                        </div>

                        <div
                            style="background: var(--white); border-radius: 0 0 var(--border-radius-sm) var(--border-radius-sm); padding: 30px; margin-top: 0; border: 2px solid var(--secondary); border-top: none;">
                            <form method="POST" style="display: grid; gap: 25px;">
                                <input type="hidden" name="action" value="save_destaque">

                                <!-- Status -->
                                <div
                                    style="display: flex; align-items: center; gap: 15px; padding: 20px; background: #f8f9fa; border-radius: 8px;">
                                    <label
                                        style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-weight: 500;">
                                        <input type="checkbox" name="destaque_ativo" <?php echo $produto_destaque['ativo'] ? 'checked' : ''; ?> style="width: 20px; height: 20px; accent-color: #520100;">
                                        <span style="color: #520100;">Ativar Seção Destacada</span>
                                    </label>
                                </div>

                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
                                    <!-- Título -->
                                    <div>
                                        <label
                                            style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--primary);">
                                            <i class="fas fa-star"></i> Título da Seção
                                        </label>
                                        <input type="text" name="destaque_titulo"
                                            value="<?php echo htmlspecialchars($produto_destaque['titulo']); ?>"
                                            placeholder="Ex: BOLSA SIENNA"
                                            style="width: 100%; padding: 14px 16px; background: var(--white); border: 2px solid var(--secondary); border-radius: var(--border-radius-sm); font-size: 16px; color: var(--primary); transition: all 0.3s;">
                                    </div>

                                    <!-- Produto -->
                                    <div>
                                        <label
                                            style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--primary);">
                                            <i class="fas fa-box"></i> Produto (para imagem e link)
                                        </label>
                                        <select name="destaque_produto_id"
                                            style="width: 100%; padding: 14px 16px; background: var(--white); border: 2px solid var(--secondary); border-radius: var(--border-radius-sm); font-size: 16px; color: var(--primary); transition: all 0.3s;">
                                            <option value="">Selecione um produto</option>
                                            <?php foreach (getAllProdutos() as $produto): ?>
                                                <option value="<?php echo $produto['id']; ?>" <?php echo $produto_destaque['produto_id'] == $produto['id'] ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($produto['title']); ?> - R$
                                                    <?php echo number_format($produto['price'], 2, ',', '.'); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <!-- Descrição -->
                                <div style="margin-top: 25px;">
                                    <label
                                        style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--primary);">
                                        <i class="fas fa-edit"></i> Descrição do Produto
                                    </label>
                                    <textarea name="destaque_descricao" rows="3"
                                        placeholder="Descreva o produto destacado..."
                                        style="width: 100%; padding: 14px 16px; background: var(--white); border: 2px solid var(--secondary); border-radius: var(--border-radius-sm); font-size: 16px; resize: vertical; color: var(--primary); transition: all 0.3s;"><?php echo htmlspecialchars($produto_destaque['descricao']); ?></textarea>
                                </div>

                                <!-- Informações -->
                                <div
                                    style="background: var(--white); border-left: 4px solid var(--secondary); padding: 20px; border-radius: var(--border-radius-sm); margin-top: 25px; border: 2px solid var(--secondary);">
                                    <h4
                                        style="margin: 0 0 15px 0; color: var(--primary); display: flex; align-items: center; gap: 10px;">
                                        <i class="fas fa-info-circle"></i> Informações Importantes:
                                    </h4>
                                    <ul style="margin: 0; padding-left: 20px; color: var(--primary); line-height: 1.6;">
                                        <li>O design, cores e botão são fixos (fundo vermelho escuro com botão roxo)</li>
                                        <li>Você pode alterar apenas o título, descrição e qual produto será mostrado</li>
                                        <li>A imagem será automaticamente carregada do produto selecionado</li>
                                        <li>O botão "Compre Já" direcionará para a página do produto selecionado</li>
                                    </ul>
                                </div>

                                <!-- Botão Salvar -->
                                <div
                                    style="text-align: center; padding-top: 30px; border-top: 2px solid var(--secondary); margin-top: 30px;">
                                    <button type="submit" style="
                            background: var(--secondary); 
                            color: white; 
                            padding: 18px 50px; 
                            border: none; 
                            border-radius: var(--border-radius-sm); 
                            font-size: 16px; 
                            font-weight: 600; 
                            cursor: pointer;
                            transition: all 0.3s;
                            display: flex;
                            align-items: center;
                            gap: 10px;
                            margin: 0 auto;
                        " onmouseover="this.style.transform='translateY(-2px)'; this.style.background='var(--primary)'"
                                        onmouseout="this.style.transform='translateY(0)'; this.style.background='var(--secondary)'">
                                        <i class="fas fa-save"></i> Salvar Configurações
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Seção Gerenciar Avaliações -->
                    <div class="reviews-section glass-card" style="margin-top: 40px;">
                        <div class="table-header">
                            <h2>📝 Gerenciar Avaliações</h2>
                            <div style="font-size: 14px; color: rgba(255, 255, 255, 0.9); margin-top: 5px;">
                                Gerencie os depoimentos que aparecem na página inicial
                            </div>
                        </div>

                        <?php
                        $arquivo_avaliacoes = 'data/avaliacoes.json';
                        $avaliacoes = [];
                        if (file_exists($arquivo_avaliacoes)) {
                            $conteudo = file_get_contents($arquivo_avaliacoes);
                            $avaliacoes = json_decode($conteudo, true) ?: [];
                        }

                        if (isset($_POST['action']) && $_POST['action'] == 'save_review') {
                            $tipo_foto = $_POST['review_tipo_foto'] ?? 'iniciais';
                            $foto_data = $_POST['review_foto'] ?? '';

                            if ($tipo_foto === 'iniciais') {
                                $nome_partes = explode(' ', trim($_POST['review_nome']));
                                $iniciais = '';
                                if (count($nome_partes) >= 2) {
                                    $iniciais = strtoupper(substr($nome_partes[0], 0, 1) . substr(end($nome_partes), 0, 1));
                                } else {
                                    $iniciais = strtoupper(substr($nome_partes[0], 0, 2));
                                }

                                $cor_inicial = $_POST['cor_inicial'] ?? '#e91e63';
                                $foto_final = 'iniciais';
                            } else {
                                $foto_final = '';
                                if (isset($_FILES['review_foto']) && $_FILES['review_foto']['error'] === UPLOAD_ERR_OK) {
                                    $uploadDir = __DIR__ . '/uploads/';
                                    if (!is_dir($uploadDir))
                                        mkdir($uploadDir, 0755, true);

                                    $fileName = preg_replace('/[^a-zA-Z0-9._-]/', '', basename($_FILES['review_foto']['name'])) ?: 'sem_nome.jpg';
                                    $uploadPath = $uploadDir . $fileName;

                                    if (move_uploaded_file($_FILES['review_foto']['tmp_name'], $uploadPath)) {
                                        $foto_final = 'uploads/' . $fileName;
                                        if (!file_exists(__DIR__ . '/../' . $foto_final)) {
                                            $foto_final = 'admin/' . $foto_final;
                                        }
                                    } else {
                                        $foto_final = 'img/default-product.png';
                                    }
                                } else {
                                    $foto_final = $_POST['existing_foto'] ?? 'img/default-product.png';
                                }

                                $iniciais = '';
                                $cor_inicial = '';
                            }

                            $review_data = [
                                'nome' => $_POST['review_nome'],
                                'foto' => $foto_final,
                                'tipo_foto' => $tipo_foto,
                                'iniciais' => $iniciais,
                                'cor_inicial' => $cor_inicial,
                                'avaliacao' => $_POST['review_texto'],
                                'estrelas' => intval($_POST['review_estrelas']),
                                'produto_relacionado' => $_POST['review_produto'],
                                'ativo' => isset($_POST['review_ativo']),
                                'data_criacao' => date('Y-m-d'),
                                'ordem' => count($avaliacoes) + 1
                            ];

                            if (!empty($_POST['review_id'])) {
                                $review_id = $_POST['review_id'];
                                $review_data['id'] = intval($review_id);
                                $avaliacoes[$review_id] = $review_data;
                                $success_message = "Avaliação atualizada com sucesso!";
                            } else {
                                $new_id = count($avaliacoes) + 1;
                                while (isset($avaliacoes[$new_id]))
                                    $new_id++;
                                $review_data['id'] = $new_id;
                                $avaliacoes[$new_id] = $review_data;
                                $success_message = "Avaliação adicionada com sucesso!";
                            }

                            file_put_contents($arquivo_avaliacoes, json_encode($avaliacoes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                        }


                        if (isset($_POST['action']) && $_POST['action'] == 'delete_review') {
                            $review_id = $_POST['review_id'];
                            if (isset($avaliacoes[$review_id])) {
                                unset($avaliacoes[$review_id]);
                                file_put_contents($arquivo_avaliacoes, json_encode($avaliacoes, JSON_PRETTY_PRINT));
                                $success_message = "Avaliação excluída com sucesso!";
                            }
                        }

                        if (isset($success_message)) {
                            if (file_exists($arquivo_avaliacoes)) {
                                $conteudo = file_get_contents($arquivo_avaliacoes);
                                $avaliacoes = json_decode($conteudo, true) ?: [];
                            }
                        }
                        ?>

                        <div
                            style="background: var(--white); border-radius: 0 0 var(--border-radius-sm) var(--border-radius-sm); padding: 30px; margin-top: 0; border: 2px solid var(--secondary); border-top: none;">

                            <!-- Botão Adicionar -->
                            <div style="margin-bottom: 30px; text-align: right;">
                                <button onclick="openReviewModal()" style="
                        background: #25D366;
                        color: white;
                        padding: 12px 20px;
                        border: none;
                        border-radius: 6px;
                        cursor: pointer;
                        font-size: 14px;
                        font-weight: 500;
                    ">
                                    Adicionar Avaliação
                                </button>
                            </div>

                            <!-- Lista de Avaliações -->
                            <div class="reviews-grid" style="display: grid; gap: 20px;">
                                <?php if (empty($avaliacoes)): ?>
                                    <div style="text-align: center; padding: 40px; color: #666;">
                                        <div style="font-size: 48px; margin-bottom: 15px; opacity: 0.5;"></div>
                                        <h3>Nenhuma avaliação ainda</h3>
                                        <p>Clique em "Adicionar Avaliação" para criar a primeira avaliação.</p>
                                    </div>
                                <?php else: ?>
                                    <?php foreach ($avaliacoes as $avaliacao): ?>
                                        <div class="review-item" style="
                                border: 1px solid #e9ecef;
                                border-radius: 10px;
                                padding: 20px;
                                background: #f8f9fa;
                                display: flex;
                                gap: 20px;
                                align-items: flex-start;
                                <?php echo $avaliacao['ativo'] ? '' : 'opacity: 0.6; border-color: #dc3545;'; ?>
                            ">
                                            <div style="flex-shrink: 0;">
                                                <?php if (($avaliacao['tipo_foto'] ?? 'upload') === 'iniciais'): ?>
                                                    <!-- Avatar com iniciais -->
                                                    <div style="
                                            width: 60px; 
                                            height: 60px; 
                                            border-radius: 50%; 
                                            background: <?php echo htmlspecialchars($avaliacao['cor_inicial'] ?? '#e91e63'); ?>; 
                                            display: flex; 
                                            align-items: center; 
                                            justify-content: center; 
                                            color: white; 
                                            font-weight: bold; 
                                            font-size: 22px;
                                            border: 2px solid #ddd;
                                        ">
                                                        <?php echo htmlspecialchars($avaliacao['iniciais'] ?? 'XX'); ?>
                                                    </div>
                                                <?php else: ?>
                                                    <!-- Foto carregada -->
                                                    <?php
                                                    $baseUrl = 'https://miamianet.com.br/';
                                                    $fotoPath = $avaliacao['foto'] ?? 'img/default-product.png';
                                                    if (strpos($fotoPath, 'uploads/') === 0) {
                                                        $fotoPath = $baseUrl . ltrim($fotoPath, '/');
                                                    }
                                                    ?>
                                                    <img src="<?php echo htmlspecialchars($fotoPath); ?>"
                                                        alt="<?php echo htmlspecialchars($avaliacao['nome']); ?>"
                                                        style="width: 60px; height: 60px; border-radius: 50%; object-fit: cover; border: 2px solid #ddd;">
                                                <?php endif; ?>
                                            </div>

                                            <div style="flex: 1;">
                                                <div
                                                    style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px;">
                                                    <div>
                                                        <h4 style="margin: 0; font-size: 16px; color: #520100;">
                                                            <?php echo htmlspecialchars($avaliacao['nome']); ?>
                                                            <?php if (!$avaliacao['ativo']): ?>
                                                                <span
                                                                    style="background: #dc3545; color: white; padding: 2px 6px; border-radius: 10px; font-size: 10px; margin-left: 8px;">INATIVO</span>
                                                            <?php endif; ?>
                                                        </h4>
                                                        <div style="margin: 5px 0;">
                                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                                <span
                                                                    style="color: <?php echo $i <= $avaliacao['estrelas'] ? '#ffc107' : '#e9ecef'; ?>; font-size: 16px;">★</span>
                                                            <?php endfor; ?>
                                                        </div>
                                                        <small style="color: #666;">
                                                            Produto:
                                                            <?php echo htmlspecialchars($avaliacao['produto_relacionado']); ?> |
                                                            Criado:
                                                            <?php echo date('d/m/Y', strtotime($avaliacao['data_criacao'])); ?>
                                                        </small>
                                                    </div>
                                                </div>

                                                <div
                                                    style="background: white; padding: 15px; border-radius: 8px; margin-bottom: 15px; border-left: 4px solid #520100;">
                                                    "<?php echo htmlspecialchars($avaliacao['avaliacao']); ?>"
                                                </div>

                                                <div style="display: flex; gap: 10px;">
                                                    <button onclick="editReview(<?php echo $avaliacao['id']; ?>)" style="
                                            background: #8A4D99;
                                            color: white;
                                            padding: 6px 12px;
                                            border: none;
                                            border-radius: 4px;
                                            cursor: pointer;
                                            font-size: 12px;
                                        ">
                                                        Editar
                                                    </button>
                                                    <button onclick="deleteReview(<?php echo $avaliacao['id']; ?>)" style="
                                            background: #dc3545;
                                            color: white;
                                            padding: 6px 12px;
                                            border: none;
                                            border-radius: 4px;
                                            cursor: pointer;
                                            font-size: 12px;
                                        ">
                                                        Excluir
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Avaliações -->
                <div id="reviewModal" class="modal" style="display: none;">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3 id="reviewModalTitle">Adicionar Avaliação</h3>
                            <span class="close" onclick="closeReviewModal()">&times;</span>
                        </div>
                        <div class="modal-body">
                            <form id="reviewForm" method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="action" value="save_review">
                                <input type="hidden" id="reviewId" name="review_id">

                                <!-- Status -->
                                <div style="margin-bottom: 25px; padding: 20px; background: #f8f9fa; border-radius: 8px;">
                                    <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                                        <input type="checkbox" id="reviewAtivo" name="review_ativo" checked
                                            style="width: 20px; height: 20px;">
                                        <span style="font-weight: 500;">Avaliação ativa (aparece no site)</span>
                                    </label>
                                </div>

                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                                    <!-- Nome -->
                                    <div>
                                        <label style="display: block; margin-bottom: 8px; font-weight: 500;">Nome do Cliente
                                            *</label>
                                        <input type="text" id="reviewNome" name="review_nome" required style="
                                width: 100%; 
                                padding: 12px; 
                                border: 1px solid #ddd; 
                                border-radius: 6px;
                            " placeholder="Ex: Maria Silva">
                                    </div>

                                    <!-- Estrelas -->
                                    <div>
                                        <label style="display: block; margin-bottom: 8px; font-weight: 500;">Avaliação
                                            (estrelas) *</label>
                                        <select id="reviewEstrelas" name="review_estrelas" required style="
                                width: 100%; 
                                padding: 12px; 
                                border: 1px solid #ddd; 
                                border-radius: 6px;
                            ">
                                            <option value="5">5 estrelas</option>
                                            <option value="4">4 estrelas</option>
                                            <option value="3">3 estrelas</option>
                                            <option value="2">2 estrelas</option>
                                            <option value="1">1 estrela</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Opções de Foto -->
                                <div style="margin-bottom: 20px;">
                                    <label style="display: block; margin-bottom: 12px; font-weight: 500;">Avatar do
                                        Cliente</label>

                                    <!-- Radio para escolher tipo de foto -->
                                    <div style="display: flex; gap: 20px; margin-bottom: 15px;">
                                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                            <input type="radio" name="foto_tipo" value="iniciais" checked
                                                onchange="toggleFotoOptions()">
                                            <span>Usar iniciais do nome</span>
                                        </label>
                                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                            <input type="radio" name="foto_tipo" value="upload"
                                                onchange="toggleFotoOptions()">
                                            <span>Fazer upload de foto</span>
                                        </label>
                                    </div>

                                    <!-- Seção para Iniciais (padrão) -->
                                    <div id="iniciaisSection" style="
                            background: #f8f9fa; 
                            padding: 20px; 
                            border-radius: 8px; 
                            border: 1px solid #dee2e6;
                        ">
                                        <div style="display: flex; gap: 15px; align-items: center;">
                                            <input type="hidden" id="corInicial" name="cor_inicial" value="#e91e63">
                                            <div>
                                                <label
                                                    style="display: block; margin-bottom: 5px; font-size: 14px;">Preview:</label>
                                                <div id="previewIniciais" style="
                                        width: 50px; 
                                        height: 50px; 
                                        background: #e91e63; 
                                        border-radius: 50%; 
                                        display: flex; 
                                        align-items: center; 
                                        justify-content: center; 
                                        color: white; 
                                        font-weight: bold; 
                                        font-size: 18px;
                                    ">MS</div>
                                            </div>
                                        </div>
                                        <small style="color: #666; margin-top: 10px; display: block;">
                                            As iniciais serão geradas automaticamente a partir do nome (ex: Maria Silva =
                                            MS)
                                        </small>
                                    </div>

                                    <!-- Seção para Upload (oculta por padrão) -->
                                    <div id="uploadSection" style="display: none;">
                                        <div style="
                                border: 2px dashed #ddd; 
                                border-radius: 8px; 
                                padding: 20px; 
                                text-align: center; 
                                cursor: pointer;
                                background: white;
                            " onclick="document.getElementById('avatarUpload').click()">
                                            <div style="margin-bottom: 10px; font-size: 24px;"></div>
                                            <p style="margin: 0; color: #666;">Clique para selecionar uma foto</p>
                                            <small style="color: #999;">JPG, PNG, GIF ou WebP - máx. 2MB</small>
                                        </div>

                                        <!-- Preview da foto carregada -->
                                        <div id="avatarPreview"
                                            style="display: none; text-align: center; margin-top: 15px;">
                                            <img id="previewImg" style="
                                    width: 80px; 
                                    height: 80px; 
                                    border-radius: 50%; 
                                    object-fit: cover; 
                                    border: 3px solid #ddd;
                                ">
                                            <div style="margin-top: 10px;">
                                                <button type="button" onclick="removeAvatar()" style="
                                        background: #dc3545; 
                                        color: white; 
                                        border: none; 
                                        padding: 5px 10px; 
                                        border-radius: 4px; 
                                        cursor: pointer; 
                                        font-size: 12px;
                                    ">Remover</button>
                                            </div>
                                        </div>
                                    </div>

                                    <input type="file" id="reviewFile" name="review_foto" accept="image/*"
                                        onchange="previewAvatar(this)">
                                    <input type="hidden" id="reviewFoto" name="review_foto_hidden">

                                </div>

                                <!-- Produto Relacionado -->
                                <div style="margin-bottom: 20px;">
                                    <label style="display: block; margin-bottom: 8px; font-weight: 500;">Produto
                                        Relacionado</label>
                                    <select id="reviewProduto" name="review_produto" style="
                            width: 100%; 
                            padding: 12px; 
                            border: 1px solid #ddd; 
                            border-radius: 6px;
                        ">
                                        <option value="">Produto geral</option>
                                        <?php foreach (getAllProdutos() as $produto): ?>
                                            <option value="<?php echo htmlspecialchars($produto['title']); ?>">
                                                <?php echo htmlspecialchars($produto['title']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- Texto da Avaliação -->
                                <div style="margin-bottom: 20px;">
                                    <label style="display: block; margin-bottom: 8px; font-weight: 500;">Depoimento
                                        *</label>
                                    <textarea id="reviewTexto" name="review_texto" required rows="4" style="
                            width: 100%; 
                            padding: 12px; 
                            border: 1px solid #ddd; 
                            border-radius: 6px;
                            resize: vertical;
                        " placeholder="Ex: Qualidade excepcional! Minha bolsa já tem 3 anos e continua como nova."></textarea>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" onclick="closeReviewModal()" style="
                    background: #6c757d; 
                    color: white; 
                    padding: 10px 20px; 
                    border: none; 
                    border-radius: 6px; 
                    cursor: pointer;
                ">Cancelar</button>
                            <button type="button" onclick="saveReview()" style="
                    background: #520100; 
                    color: white; 
                    padding: 10px 20px; 
                    border: none; 
                    border-radius: 6px; 
                    cursor: pointer;
                ">Salvar Avaliação</button>
                        </div>
                    </div>
                </div>

                <!-- Modal Adicionar/Editar Produto -->
                <div id="productModal" class="modal">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3 id="modalTitle">Adicionar Produto</h3>
                            <span class="close" onclick="closeProductModal()">&times;</span>
                        </div>
                        <div class="modal-body">
                            <form id="productForm">
                                <input type="hidden" id="productId" name="productId">

                                <!-- Status do Produto - Primeira seção -->
                                <div class="status-priority-section">
                                    <div class="form-group">
                                        <label for="productStatus">Status do Produto *</label>
                                        <select id="productStatus" name="productStatus" required>
                                            <option value="ativo">Ativo (Visível no site)</option>
                                            <option value="inativo">Inativo (Oculto do site)</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-col">
                                        <div class="form-group">
                                            <label for="productName">Nome do Produto *</label>
                                            <input type="text" id="productName" name="productName" required>
                                        </div>
                                    </div>
                                    <div class="form-col">
                                        <div class="form-group">
                                            <label for="productCategory">Categoria *</label>
                                            <select id="productCategory" name="productCategory" required>
                                                <option value="">Selecione uma categoria</option>
                                                <option value="viagem">Viagem</option>
                                                <option value="carteiras">Dia a Dia</option>
                                                <option value="bolsas">Bolsa</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="productDescription">Descrição do Produto *</label>
                                    <textarea id="productDescription" name="productDescription" required
                                        placeholder="Descreva as principais características do produto..."
                                        rows="4"></textarea>
                                </div>

                                <div class="form-group">
                                    <label for="productSpecifications">Especificações Técnicas *</label>
                                    <textarea id="productSpecifications" name="productSpecifications" required
                                        placeholder="Ex: Material: 100% Couro Legítimo|Dimensões: 20x15x5cm|Peso: 300g"
                                        rows="4"></textarea>
                                    <small style="color: #666; font-size: 12px;">Separe cada especificação com "|" (pipe).
                                        Ex:
                                        Material: Couro|Cor: Marrom|Peso: 200g</small>
                                </div>

                                <div class="form-group">
                                    <label for="productPrice">Preço do Produto - Original (R$) *</label>
                                    <input type="number" id="productPrice" name="productPrice" step="0.01" required
                                        min="0.01">
                                    <small style="color: #666; font-size: 12px;">Preço padrão do produto (sem
                                        desconto)</small>
                                </div>

                                <!-- Seção de Desconto -->
                                <div class="discount-section">
                                    <div class="section-header">
                                        <h4>Desconto e Promoções</h4>
                                        <small style="color: #666; font-size: 12px;">Configure o preço promocional para
                                            ativar o desconto</small>
                                    </div>
                                    <div class="discount-toggle">
                                        <label class="switch-container">
                                            <input type="checkbox" id="hasDiscount" class="switch-input">
                                            <span class="switch-slider"></span>
                                            <span class="switch-text">Aplicar desconto neste produto</span>
                                        </label>
                                    </div>

                                    <div id="discountSection" class="discount-fields" style="display: none;">
                                        <div class="form-row">
                                            <div class="form-col">
                                                <div class="form-group">
                                                    <label for="productOldPrice">Preço com Desconto - Promocional (R$)
                                                        *</label>
                                                    <input type="number" id="productOldPrice" name="productOldPrice"
                                                        step="0.01" min="0.01" placeholder="Ex: 150.00">
                                                    <small style="color: #666; font-size: 12px;">Preço promocional que o
                                                        cliente irá pagar</small>
                                                </div>
                                            </div>
                                            <div class="form-col">
                                                <div class="form-group">
                                                    <label for="discountPreview">Desconto Calculado</label>
                                                    <div class="discount-preview">
                                                        <input type="text" id="discountPreview" readonly
                                                            placeholder="Desconto será calculado automaticamente">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Seção de Cores Disponíveis -->
                                <div class="colors-section">
                                    <div class="section-header">
                                        <h4>Cores Disponíveis</h4>
                                        <small class="colors-counter">1 cor selecionada</small>
                                    </div>
                                    <p class="colors-hint"><strong>Clique nas cores</strong> para selecionar múltiplas
                                        opções
                                        disponíveis do produto</p>
                                    <div class="colors-grid">
                                        <div class="color-option active" data-color="preto" data-hex="#000000">
                                            <div class="color-circle" style="background-color: #000000;"></div>
                                            <span>Preto</span>
                                            <div class="color-check">✓</div>
                                        </div>
                                        <div class="color-option" data-color="vermelho" data-hex="#DC143C">
                                            <div class="color-circle" style="background-color: #DC143C;"></div>
                                            <span>Vermelho</span>
                                            <div class="color-check">✓</div>
                                        </div>
                                        <div class="color-option" data-color="caramelo" data-hex="#D2691E">
                                            <div class="color-circle" style="background-color: #D2691E;"></div>
                                            <span>Caramelo</span>
                                            <div class="color-check">✓</div>
                                        </div>
                                        <div class="color-option" data-color="azul-marinho" data-hex="#001F3F">
                                            <div class="color-circle" style="background-color: #001F3F;"></div>
                                            <span>Azul Marinho</span>
                                            <div class="color-check">✓</div>
                                        </div>
                                        <div class="color-option" data-color="verde" data-hex="#228B22">
                                            <div class="color-circle" style="background-color: #228B22;"></div>
                                            <span>Verde</span>
                                            <div class="color-check">✓</div>
                                        </div>
                                        <div class="color-option" data-color="framboesa" data-hex="#C72C48">
                                            <div class="color-circle" style="background-color: #C72C48;"></div>
                                            <span>Framboesa</span>
                                            <div class="color-check">✓</div>
                                        </div>
                                        <div class="color-option" data-color="amarelo" data-hex="#FFD700">
                                            <div class="color-circle" style="background-color: #FFD700;"></div>
                                            <span>Amarelo</span>
                                            <div class="color-check">✓</div>
                                        </div>
                                        <div class="color-option" data-color="gelo" data-hex="#E0F2F7">
                                            <div class="color-circle"
                                                style="background-color: #E0F2F7; border: 1px solid #ccc;"></div>
                                            <span>Gelo</span>
                                            <div class="color-check">✓</div>
                                        </div>
                                        <div class="color-option" data-color="grafite" data-hex="#4A5568">
                                            <div class="color-circle" style="background-color: #4A5568;"></div>
                                            <span>Grafite</span>
                                            <div class="color-check">✓</div>
                                        </div>
                                        <div class="color-option" data-color="champanhe" data-hex="#F7E7CE">
                                            <div class="color-circle"
                                                style="background-color: #F7E7CE; border: 1px solid #ccc;"></div>
                                            <span>Champanhe</span>
                                            <div class="color-check">✓</div>
                                        </div>
                                        <div class="color-option" data-color="pitaya" data-hex="#E91E63">
                                            <div class="color-circle" style="background-color: #E91E63;"></div>
                                            <span>Pitaya</span>
                                            <div class="color-check">✓</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-col">
                                        <div class="form-group">
                                            <label for="productStock">Estoque *</label>
                                            <input type="number" id="productStock" name="productStock" min="0" required
                                                placeholder="Quantidade disponível">
                                        </div>
                                    </div>
                                    <div class="form-col">
                                        <div class="form-group">
                                            <label for="productSales">Vendas Estimadas</label>
                                            <input type="number" id="productSales" name="productSales" min="0"
                                                placeholder="Ex: 150 (usado para ordenação 'Mais Vendidos')">
                                        </div>
                                    </div>
                                </div>

                                <!-- Seção de Tamanhos -->
                                <div class="sizes-section">
                                    <div class="section-header">
                                        <h4>Tamanhos Disponíveis</h4>
                                        <small style="color: #666; font-size: 12px;">Selecione os tamanhos disponíveis para este produto</small>
                                    </div>
                                    <div class="sizes-grid" style="display: flex; gap: 15px; margin-top: 10px;">
                                        <label class="size-option" style="display: flex; align-items: center; gap: 8px; cursor: pointer; padding: 8px 12px; border: 2px solid #ddd; border-radius: 5px; background: #f9f9f9;">
                                            <input type="checkbox" id="sizeP" name="sizes[]" value="P" style="margin: 0;">
                                            <span style="font-weight: 600; font-size: 16px;">P</span>
                                        </label>
                                        <label class="size-option" style="display: flex; align-items: center; gap: 8px; cursor: pointer; padding: 8px 12px; border: 2px solid #ddd; border-radius: 5px; background: #f9f9f9;">
                                            <input type="checkbox" id="sizeM" name="sizes[]" value="M" style="margin: 0;">
                                            <span style="font-weight: 600; font-size: 16px;">M</span>
                                        </label>
                                        <label class="size-option" style="display: flex; align-items: center; gap: 8px; cursor: pointer; padding: 8px 12px; border: 2px solid #ddd; border-radius: 5px; background: #f9f9f9;">
                                            <input type="checkbox" id="sizeG" name="sizes[]" value="G" style="margin: 0;">
                                            <span style="font-weight: 600; font-size: 16px;">G</span>
                                        </label>
                                    </div>
                                </div>

                                <!-- Seção de Destaques -->
                                <div class="highlights-section">
                                    <div class="section-header">
                                        <h4>Destaques e Prioridade</h4>
                                    </div>
                                    <div class="highlights-grid">
                                        <div class="highlight-option">
                                            <label class="checkbox-container">
                                                <input type="checkbox" id="isFeatured" name="isFeatured">
                                                <span class="checkmark"></span>
                                                <span class="highlight-text">
                                                    <strong>Produto em Destaque</strong>
                                                    <small>Aparece na homepage e recebe prioridade</small>
                                                </span>
                                            </label>
                                        </div>
                                        <div class="highlight-option">
                                            <label class="checkbox-container">
                                                <input type="checkbox" id="isBestseller" name="isBestseller">
                                                <span class="checkmark"></span>
                                                <span class="highlight-text">
                                                    <strong>Mais Vendido</strong>
                                                    <small>Marca como produto mais vendido</small>
                                                </span>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Imagens do Produto * <small>(Pelo menos 1 imagem obrigatória)</small></label>

                                    <!-- Upload simples (para produtos com 1 cor) -->
                                    <div id="simpleImageUpload">
                                        <div class="image-upload"
                                            onclick="document.getElementById('productImages').click()">
                                            <p>📸 Clique para adicionar imagens</p>
                                            <small>Suporte: JPG, PNG, WebP (máx. 10MB cada) - Mínimo 1 imagem</small>
                                            <input type="file" id="productImages" accept="image/*" multiple>
                                        </div>
                                        <div id="imagePreview" class="image-preview"></div>
                                    </div>

                                    <!-- Upload por cor (aparece automaticamente quando há 2+ cores) -->
                                    <div id="colorImageUpload" style="display: none;">
                                        <div
                                            style="margin-bottom: 20px; padding: 15px; background: #e7f3ff; border: 2px solid #2196f3; border-radius: 8px;">
                                            <p style="margin: 0; color: #1976d2; font-weight: 600;">
                                                🎨 Modo de Imagens por Cor Ativado
                                            </p>
                                            <small style="color: #1565c0; display: block; margin-top: 8px;">
                                                Como você selecionou múltiplas cores, adicione imagens específicas para cada
                                                uma.
                                            </small>
                                        </div>
                                        <div id="colorImagesContainer"></div>
                                    </div>

                                    <div id="imageHelp" style="margin-top: 10px;">
                                        <small style="color: #666; font-size: 12px;">
                                            <strong>💡 Dica:</strong> Se selecionar 2 ou mais cores acima, você poderá
                                            adicionar imagens específicas para cada cor.
                                        </small>
                                    </div>
                                    <div id="imageError"
                                        style="color: #e74c3c; font-size: 14px; margin-top: 8px; display: none;">
                                        Pelo menos uma imagem é obrigatória!
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn-secondary" onclick="closeProductModal()">Cancelar</button>
                            <button type="button" class="btn-primary" onclick="saveProduct()">Salvar Produto</button>
                        </div>
                    </div>
                </div>

                <script>
                    let currentProductId = null;
                    let selectedImages = [];
                    let colorImages = {}; // Armazena imagens por cor: { 'preto': [File, File], 'vermelho': [File] }
                    let useColorSpecificImages = false;

                    class NotificationSystem {
                        constructor() {
                            this.container = document.getElementById('notificationContainer');
                            this.notifications = [];
                        }

                        show(type, title, message, duration = 5000) {
                            const notification = this.createNotification(type, title, message);
                            this.container.appendChild(notification);
                            this.notifications.push(notification);

                            setTimeout(() => {
                                notification.classList.add('show');
                            }, 10);

                            if (duration > 0) {
                                setTimeout(() => {
                                    this.hide(notification);
                                }, duration);
                            }

                            return notification;
                        }

                        createNotification(type, title, message) {
                            const notification = document.createElement('div');
                            notification.className = `notification notification-${type}`;

                            const icons = {
                                success: '',
                                error: '',
                                warning: '',
                                info: ''
                            };

                            notification.innerHTML = `
                <div class="notification-header">
                    <div class="notification-title">
                        <span class="notification-icon">${icons[type]}</span>
                        ${title}
                    </div>
                    <button class="notification-close" onclick="notifications.hide(this.closest('.notification'))">&times;</button>
                </div>
                <div class="notification-message">${message}</div>
                <div class="notification-progress"></div>
            `;

                            return notification;
                        }

                        hide(notification) {
                            notification.classList.add('hide');
                            setTimeout(() => {
                                if (notification.parentNode) {
                                    notification.parentNode.removeChild(notification);
                                }
                                const index = this.notifications.indexOf(notification);
                                if (index > -1) {
                                    this.notifications.splice(index, 1);
                                }
                            }, 400);
                        }

                        success(title, message, duration) {
                            return this.show('success', title, message, duration);
                        }

                        error(title, message, duration) {
                            return this.show('error', title, message, duration);
                        }

                        warning(title, message, duration) {
                            return this.show('warning', title, message, duration);
                        }

                        info(title, message, duration) {
                            return this.show('info', title, message, duration);
                        }

                        clear() {
                            this.notifications.forEach(notification => {
                                this.hide(notification);
                            });
                        }
                    }

                    const notifications = new NotificationSystem();

                    function toggleDiscountField() {
                        const hasDiscount = document.getElementById('hasDiscount').checked;
                        const discountSection = document.getElementById('discountSection');
                        const oldPriceField = document.getElementById('productOldPrice');

                        if (hasDiscount) {
                            discountSection.style.display = 'block';
                            oldPriceField.required = true;
                        } else {
                            discountSection.style.display = 'none';
                            oldPriceField.required = false;
                            oldPriceField.value = '';
                            document.getElementById('discountPreview').value = '';
                        }
                    }

                    function initColorSelection() {
                        const colorOptions = document.querySelectorAll('.color-option');

                        colorOptions.forEach(option => {
                            option.addEventListener('click', function () {
                                this.classList.toggle('active');

                                const activeColors = document.querySelectorAll('.color-option.active');
                                if (activeColors.length === 0) {
                                    document.querySelector('.color-option[data-color="preto"]').classList.add(
                                        'active');
                                }

                                this.style.transform = 'scale(0.95)';
                                setTimeout(() => {
                                    this.style.transform = '';
                                }, 150);

                                updateColorCounter();

                                // Verificar automaticamente o modo de imagens
                                checkAndToggleImageMode();
                            });
                        });
                    }

                    function updateColorCounter() {
                        const activeColors = document.querySelectorAll('.color-option.active');
                        const counter = document.querySelector('.colors-counter');
                        if (counter) {
                            counter.textContent =
                                `${activeColors.length} cor${activeColors.length !== 1 ? 'es' : ''} selecionada${activeColors.length !== 1 ? 's' : ''}`;
                        }
                    }

                    function getSelectedColors() {
                        const activeColors = document.querySelectorAll('.color-option.active');
                        return Array.from(activeColors).map(option => ({
                            name: option.dataset.color,
                            color: option.dataset.hex,
                            title: option.querySelector('span').textContent
                        }));
                    }

                    function calculateDiscountPreview() {
                        const originalPrice = parseFloat(document.getElementById('productPrice').value) || 0;
                        const discountPrice = parseFloat(document.getElementById('productOldPrice').value) || 0;
                        const previewField = document.getElementById('discountPreview');

                        if (originalPrice > 0 && discountPrice > 0 && originalPrice > discountPrice) {
                            const discount = Math.round(((originalPrice - discountPrice) / originalPrice) * 100);
                            const savings = originalPrice - discountPrice;
                            previewField.value = `${discount}% OFF (Economia: R$ ${savings.toFixed(2)})`;
                        } else if (discountPrice > 0 && discountPrice >= originalPrice) {
                            previewField.value = "Preço original deve ser maior que o preço promocional";
                        } else {
                            previewField.value = '';
                        }
                    }

                    function openAddProductModal() {
                        document.getElementById('modalTitle').textContent = 'Adicionar Novo Produto';
                        document.getElementById('productForm').reset();
                        document.getElementById('productId').value = '';
                        document.getElementById('imagePreview').innerHTML = '';
                        document.getElementById('imageError').style.display = 'none';
                        document.getElementById('discountSection').style.display = 'none';
                        document.getElementById('hasDiscount').checked = false;
                        document.getElementById('discountPreview').value = '';
                        document.getElementById('productSales').value = '0';
                        document.getElementById('isFeatured').checked = false;
                        document.getElementById('isBestseller').checked = false;
                        selectedImages = [];
                        colorImages = {};
                        useColorSpecificImages = false;
                        window.colorImages = {};
                        currentProductId = null;

                        // Resetar modo de imagens - sempre começa no simples
                        document.getElementById('simpleImageUpload').style.display = 'block';
                        document.getElementById('colorImageUpload').style.display = 'none';

                        // Selecionar apenas preto por padrão
                        document.querySelectorAll('.color-option').forEach(option => {
                            option.classList.remove('active');
                        });
                        document.querySelector('.color-option[data-color="preto"]').classList.add('active');
                        updateColorCounter();

                        document.getElementById('productModal').style.display = 'block';
                    }

                    function editProduct(id) {
                        currentProductId = id;
                        document.getElementById('modalTitle').textContent = 'Editar Produto';

                        console.log('Tentando carregar produto ID:', id);
                        const url = `/admin_actions.php?action=get&productId=${id}`;
                        console.log('URL da requisição:', url);

                        fetch(url, {
                            method: 'GET',
                            credentials: 'include', // envia cookies da sessão
                            headers: {
                                'Cache-Control': 'no-cache'
                            }
                        })

                            .then(response => {
                                console.log('Status da resposta:', response.status);
                                console.log('URL final:', response.url);

                                if (!response.ok) {
                                    throw new Error(`Erro HTTP ${response.status}: ${response.statusText}`);
                                }
                                return response.text();
                            })
                            .then(text => {
                                console.log('Resposta recebida:', text);
                                try {
                                    // Verificar se a resposta é válida antes de fazer parse
                                    if (!text || text.trim() === '') {
                                        throw new Error('Resposta vazia do servidor');
                                    }

                                    // Remover possíveis caracteres invisíveis
                                    const cleanText = text.trim().replace(/^\uFEFF/, '');
                                    console.log('Texto limpo:', cleanText);

                                    const data = JSON.parse(cleanText);
                                    if (data.success) {
                                        const product = data.product;
                                        document.getElementById('productId').value = product.id;
                                        document.getElementById('productName').value = product.title;
                                        document.getElementById('productCategory').value = product.category;
                                        document.getElementById('productDescription').value = product.description || '';
                                        document.getElementById('productSpecifications').value = product.specifications ||
                                            '';
                                        const hasDiscount = product.oldPrice && product.oldPrice > 0;
                                        if (hasDiscount) {
                                            document.getElementById('productPrice').value = product.oldPrice;
                                            document.getElementById('productOldPrice').value = product.price;
                                        } else {
                                            document.getElementById('productPrice').value = product.price;
                                        }

                                        document.getElementById('productStatus').value = product.status;
                                        document.getElementById('productStock').value = product.stock || 0;
                                        document.getElementById('productSales').value = product.sales || 0;
                                        document.getElementById('isFeatured').checked = product.isFeatured || false;
                                        document.getElementById('isBestseller').checked = product.isBestseller || false;

                                        document.getElementById('hasDiscount').checked = hasDiscount;
                                        if (hasDiscount) {
                                            document.getElementById('discountSection').style.display = 'block';
                                            calculateDiscountPreview();
                                        } else {
                                            document.getElementById('discountSection').style.display = 'none';
                                        }

                                        document.querySelectorAll('.color-option').forEach(option => {
                                            option.classList.remove('active');
                                        });
                                        if (product.colors && product.colors.length > 0) {
                                            product.colors.forEach(color => {
                                                const colorOption = document.querySelector(
                                                    `.color-option[data-color="${color.name}"]`);
                                                if (colorOption) {
                                                    colorOption.classList.add('active');
                                                }
                                            });
                                        } else {
                                            document.querySelector('.color-option[data-color="preto"]').classList.add(
                                                'active');
                                        }
                                        updateColorCounter();

                                        // Carregar tamanhos selecionados
                                        document.querySelectorAll('input[name="sizes[]"]').forEach(checkbox => {
                                            checkbox.checked = false;
                                        });
                                        if (product.sizes && Array.isArray(product.sizes)) {
                                            product.sizes.forEach(size => {
                                                const sizeCheckbox = document.querySelector(`input[name="sizes[]"][value="${size}"]`);
                                                if (sizeCheckbox) {
                                                    sizeCheckbox.checked = true;
                                                }
                                            });
                                        }

                                        // Verificar se o produto usa imagens por cor
                                        const hasColorImages = product.colorImages && Object.keys(product.colorImages).length > 0;

                                        if (hasColorImages) {
                                            // Produto tem imagens por cor
                                            document.getElementById('simpleImageUpload').style.display = 'none';
                                            document.getElementById('colorImageUpload').style.display = 'block';
                                            useColorSpecificImages = true;

                                            // Carregar imagens por cor
                                            window.colorImages = {};
                                            Object.entries(product.colorImages).forEach(([colorName, images]) => {
                                                window.colorImages[colorName] = images.map(img => img); // Strings de path
                                            });
                                            window.existingColorImages = JSON.parse(JSON.stringify(product.colorImages));

                                            renderColorImageSections();
                                        } else {
                                            // Produto usa modo simples de imagens
                                            document.getElementById('simpleImageUpload').style.display = 'block';
                                            document.getElementById('colorImageUpload').style.display = 'none';
                                            useColorSpecificImages = false;

                                            loadExistingImages(product.images || []);
                                        }

                                        // Verificar automaticamente se deve ativar modo por cor baseado nas cores selecionadas
                                        setTimeout(() => {
                                            checkAndToggleImageMode();
                                        }, 100);

                                        document.getElementById('imageError').style.display = 'none';

                                        document.getElementById('productModal').style.display = 'block';
                                    } else {
                                        notifications.error('Erro ao Carregar',
                                            'Não foi possível carregar os dados do produto: ' + data.error);
                                    }
                                } catch (e) {
                                    console.error('JSON Parse Error:', e);
                                    console.error('Resposta completa:', text);
                                    console.error('Primeiro 500 caracteres:', text.substring(0, 500));

                                    let errorMsg = `Erro de JSON Parse:\n`;
                                    errorMsg += `Erro: ${e.message}\n`;
                                    errorMsg += `Tipo de erro: ${e.name}\n`;
                                    errorMsg += `Resposta (primeiros 200 caracteres): ${text.substring(0, 200)}\n`;
                                    errorMsg += `Tamanho da resposta: ${text.length} caracteres`;

                                    alert(errorMsg);
                                    notifications.error('Erro JSON', 'Resposta inválida do servidor. Verifique o console.');
                                }
                            })
                            .catch(error => {
                                console.error('Erro ao carregar produto:', error);
                                console.error('Stack trace:', error.stack);

                                let mensagem = `Erro ao carregar produto:\n`;
                                mensagem += `ID do produto: ${id}\n`;
                                mensagem += `Erro: ${error.message}\n`;
                                mensagem += `URL tentada: ./admin_actions.php?action=get&productId=${id}`;

                                alert(mensagem);
                                notifications.error('Erro ao Carregar', 'Não foi possível carregar os dados do produto. Verifique o console para mais detalhes.');
                            });
                    } function closeProductModal() {
                        document.getElementById('productModal').style.display = 'none';
                    }

                    function saveProduct() {
                        const form = document.getElementById('productForm');
                        const formData = new FormData(form);

                        if (!form.checkValidity()) {
                            notifications.warning('Campos Obrigatórios',
                                'Por favor, preencha todos os campos obrigatórios marcados com *');
                            return;
                        }

                        // Validação de imagens - determinar automaticamente o modo baseado nas cores selecionadas
                        const selectedColors = getSelectedColors();
                        const useColorImages = selectedColors.length >= 2;

                        document.getElementById('imageError').style.display = 'none';

                        const hasDiscount = document.getElementById('hasDiscount').checked;
                        if (hasDiscount) {
                            const originalPrice = parseFloat(document.getElementById('productPrice').value);
                            const discountPrice = parseFloat(document.getElementById('productOldPrice').value);

                            if (!discountPrice || discountPrice >= originalPrice) {
                                notifications.warning('Desconto Inválido',
                                    'Para aplicar desconto, o preço promocional deve ser menor que o preço original!');
                                return;
                            }
                        }

                        formData.append('action', currentProductId ? 'edit' : 'add');

                        formData.append('selectedColors', JSON.stringify(selectedColors));

                        // Enviar informação sobre o modo de imagens
                        formData.append('useColorImages', useColorImages ? '1' : '0');

                        if (useColorImages) {
                            // Enviar imagens por cor
                            formData.append('colorImagesData', JSON.stringify(Object.keys(window.colorImages || {})));

                            Object.entries(window.colorImages || {}).forEach(([colorName, images]) => {
                                images.forEach((image, index) => {
                                    if (typeof image === 'string') {
                                        // Imagem existente
                                        if (!formData.has('existingColorImages')) {
                                            formData.append('existingColorImages', JSON.stringify({}));
                                        }
                                    } else {
                                        // Nova imagem
                                        formData.append(`colorImages_${colorName}[]`, image);
                                    }
                                });
                            });

                            // Enviar imagens existentes por cor
                            if (currentProductId && window.existingColorImages) {
                                formData.append('existingColorImages', JSON.stringify(window.existingColorImages));
                            }
                        } else {
                            // Modo simples - enviar imagens normalmente
                            selectedImages.forEach((image) => {
                                formData.append('images[]', image);
                            });

                            if (currentProductId && window.existingImages) {
                                formData.append('existingImages', JSON.stringify(window.existingImages));
                            }
                        }

                        const saveBtn = document.querySelector('.btn-primary');
                        const originalText = saveBtn.textContent;
                        saveBtn.textContent = 'Salvando...';
                        saveBtn.disabled = true;

                        fetch('/admin_actions.php', {
                            method: 'POST',
                            body: formData
                        })
                            .then(response => {
                                console.log('Response status:', response.status);
                                if (!response.ok) {
                                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                                }
                                return response.text();
                            })
                            .then(text => {
                                console.log('Response text:', text);
                                try {
                                    const data = JSON.parse(text);
                                    if (data.success) {
                                        notifications.success('Produto Salvo!', data.message);
                                        closeProductModal();
                                        setTimeout(() => location.reload(), 1500);
                                    } else {
                                        notifications.error('Erro ao Salvar', data.error || 'Erro desconhecido');
                                        if (data.debug) console.log('Debug:', data.debug);
                                    }
                                } catch (e) {
                                    console.error('JSON Parse Error:', e);
                                    notifications.error('Erro de Servidor',
                                        'Resposta inválida do servidor. Verifique o console para detalhes.');
                                }
                            })
                            .catch(error => {
                                console.error('Fetch Error:', error);
                                notifications.error('Erro de Conexão',
                                    'Não foi possível conectar ao servidor. Verifique se o XAMPP está rodando.');
                            })
                            .finally(() => {
                                saveBtn.textContent = originalText;
                                saveBtn.disabled = false;
                            });
                    }

                    function deleteProduct(id) {
                        if (confirm('Tem certeza que deseja excluir este produto?\n\nEsta ação não pode ser desfeita!')) {
                            const formData = new FormData();
                            formData.append('action', 'delete');
                            formData.append('productId', id);

                            fetch('/admin_actions.php', {
                                method: 'POST',
                                body: formData
                            })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        notifications.success('Produto Excluído!', data.message);
                                        setTimeout(() => location.reload(), 1500);
                                    } else {
                                        notifications.error('Erro ao Excluir', data.error);
                                    }
                                })
                                .catch(error => {
                                    console.error('Erro:', error);
                                    notifications.error('Erro de Conexão',
                                        'Não foi possível excluir o produto. Verifique a conexão.');
                                });
                        }
                    }

                    document.getElementById('productImages').addEventListener('change', function (e) {
                        const files = Array.from(e.target.files);
                        const preview = document.getElementById('imagePreview');

                        files.forEach(file => {
                            if (file.type.startsWith('image/') && file.size <= 10 * 1024 * 1024) {
                                selectedImages.push(file);

                                const reader = new FileReader();
                                reader.onload = function (e) {
                                    const previewItem = document.createElement('div');
                                    previewItem.className = 'preview-item';
                                    previewItem.innerHTML = `
                            <img src="${e.target.result}" alt="Preview">
                            <button type="button" class="remove-image" onclick="removeImage(${selectedImages.length - 1})">×</button>
                        `;
                                    preview.appendChild(previewItem);
                                };
                                reader.readAsDataURL(file);
                            } else {
                                notifications.warning('Arquivo Inválido',
                                    `O arquivo "${file.name}" não é válido. Apenas imagens até 10MB são permitidas.`
                                );
                            }
                        });
                    });

                    function removeImage(index) {
                        selectedImages.splice(index, 1);
                        updateImagePreview();
                    }

                    function removeExistingImage(index, imagePath) {
                        if (window.existingImages) {
                            window.existingImages.splice(index, 1);
                            loadExistingImages(window.existingImages);
                        }
                    }

                    function updateImagePreview() {
                        const preview = document.getElementById('imagePreview');
                        preview.innerHTML = '';

                        selectedImages.forEach((file, index) => {
                            const reader = new FileReader();
                            reader.onload = function (e) {
                                const previewItem = document.createElement('div');
                                previewItem.className = 'preview-item';
                                previewItem.innerHTML = `
                        <img src="${e.target.result}" alt="Preview">
                        <button type="button" class="remove-image" onclick="removeImage(${index})">×</button>
                    `;
                                preview.appendChild(previewItem);
                            };
                            reader.readAsDataURL(file);
                        });
                    }

                    function loadExistingImages(existingImages) {
                        const preview = document.getElementById('imagePreview');
                        preview.innerHTML = '';
                        selectedImages = [];

                        existingImages.forEach((imagePath, index) => {
                            const previewItem = document.createElement('div');
                            previewItem.className = 'preview-item existing-image';
                            previewItem.innerHTML = `
                <img src="${imagePath.startsWith('/') ? imagePath : '/' + imagePath}" 
                    alt="Imagem existente" 
                    onerror="this.src='/img/default-product.png'">
                <button type="button" class="remove-image" onclick="removeExistingImage(${index}, '${imagePath}')">×</button>
                <span class="image-type">Existente</span>
            `;
                            preview.appendChild(previewItem);
                        });

                        window.existingImages = existingImages.slice();
                    }

                    // ============================================
                    // FUNÇÕES PARA IMAGENS POR COR
                    // ============================================

                    function checkAndToggleImageMode() {
                        const selectedColors = getSelectedColors();
                        const simpleUpload = document.getElementById('simpleImageUpload');
                        const colorUpload = document.getElementById('colorImageUpload');

                        // Se tem 2 ou mais cores, usar modo por cor
                        if (selectedColors.length >= 2) {
                            useColorSpecificImages = true;
                            simpleUpload.style.display = 'none';
                            colorUpload.style.display = 'block';
                            renderColorImageSections();
                        } else {
                            // Se tem 1 cor ou nenhuma, usar modo simples
                            useColorSpecificImages = false;
                            simpleUpload.style.display = 'block';
                            colorUpload.style.display = 'none';

                            // Converter imagens por cor de volta para lista simples se necessário
                            if (Object.keys(colorImages).length > 0) {
                                selectedImages = [];
                                Object.values(colorImages).forEach(images => {
                                    selectedImages.push(...images);
                                });
                                colorImages = {};
                            }
                            updateImagePreview();
                        }
                    }

                    function renderColorImageSections() {
                        const container = document.getElementById('colorImagesContainer');
                        const selectedColors = getSelectedColors();

                        if (selectedColors.length === 0) {
                            container.innerHTML = `
                        <div style="padding: 20px; background: #fff3cd; border: 2px solid #ffc107; border-radius: 8px; text-align: center;">
                            <p style="margin: 0; color: #856404; font-weight: 600;">
                                ⚠️ Selecione pelo menos uma cor na seção "Cores Disponíveis" acima
                            </p>
                        </div>
                    `;
                            return;
                        }

                        container.innerHTML = '';

                        selectedColors.forEach(color => {
                            const colorSection = document.createElement('div');
                            colorSection.className = 'color-image-section';
                            colorSection.style.cssText = `
                        margin-bottom: 30px;
                        padding: 20px;
                        background: white;
                        border: 2px solid ${color.color};
                        border-radius: 12px;
                        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
                    `;

                            const colorImages = window.colorImages?.[color.name] || [];

                            colorSection.innerHTML = `
                        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 15px;">
                            <div style="width: 30px; height: 30px; border-radius: 50%; background: ${color.color}; border: 2px solid #ddd;"></div>
                            <h4 style="margin: 0; color: #520100; font-size: 18px; font-weight: 600;">${color.title}</h4>
                            <span style="background: ${color.color}20; color: ${color.color}; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 600;">
                                ${colorImages.length || 0} imagem(ns)
                            </span>
                        </div>
                        
                        <div class="image-upload" onclick="document.getElementById('colorInput_${color.name}').click()" 
                            style="cursor: pointer; padding: 30px; border: 2px dashed ${color.color}; border-radius: 8px; text-align: center; background: ${color.color}05;">
                            <p style="margin: 0 0 8px 0; color: #520100; font-weight: 600;">📸 Adicionar imagens para ${color.title}</p>
                            <small style="color: #666;">Clique para selecionar as imagens desta cor</small>
                            <input type="file" id="colorInput_${color.name}" accept="image/*" multiple style="display: none;" 
                                onchange="handleColorImages('${color.name}', this.files)">
                        </div>
                        
                        <div id="colorPreview_${color.name}" class="image-preview" style="margin-top: 15px; display: grid; grid-template-columns: repeat(auto-fill, minmax(100px, 1fr)); gap: 10px;">
                            ${renderColorImagePreviews(color.name, colorImages)}
                        </div>
                    `;

                            container.appendChild(colorSection);
                        });
                    }

                    function renderColorImagePreviews(colorName, images) {
                        if (!images || images.length === 0) return '';

                        return images.map((img, index) => {
                            const isExisting = typeof img === 'string';
                            const src = isExisting ? (img.startsWith('/') ? img : '/' + img) : URL.createObjectURL(img);

                            return `
                        <div class="preview-item ${isExisting ? 'existing-image' : ''}" style="position: relative; aspect-ratio: 1; border-radius: 8px; overflow: hidden;">
                            <img src="${src}" alt="Preview" style="width: 100%; height: 100%; object-fit: cover;">
                            <button type="button" class="remove-image" onclick="removeColorImage('${colorName}', ${index})" 
                                style="position: absolute; top: 5px; right: 5px; background: #dc3545; color: white; border: none; border-radius: 50%; width: 24px; height: 24px; cursor: pointer; font-size: 16px; line-height: 1; display: flex; align-items: center; justify-content: center;">×</button>
                            ${isExisting ? '<span class="image-type" style="position: absolute; bottom: 5px; left: 5px; background: #28a745; color: white; padding: 2px 6px; border-radius: 4px; font-size: 10px; font-weight: bold;">Existente</span>' : ''}
                        </div>
                    `;
                        }).join('');
                    }

                    function handleColorImages(colorName, files) {
                        if (!files || files.length === 0) return;

                        if (!window.colorImages) window.colorImages = {};
                        if (!window.colorImages[colorName]) window.colorImages[colorName] = [];

                        Array.from(files).forEach(file => {
                            if (file.type.startsWith('image/') && file.size <= 10 * 1024 * 1024) {
                                window.colorImages[colorName].push(file);
                            } else {
                                notifications.warning('Arquivo Inválido',
                                    `O arquivo "${file.name}" não é válido. Apenas imagens até 10MB são permitidas.`);
                            }
                        });

                        renderColorImageSections();
                    }

                    function removeColorImage(colorName, index) {
                        if (window.colorImages && window.colorImages[colorName]) {
                            window.colorImages[colorName].splice(index, 1);
                            renderColorImageSections();
                        }
                    }

                    function filterProducts() {
                        const searchValue = document.getElementById('searchProducts').value.toLowerCase();
                        const rows = document.querySelectorAll('tbody tr');

                        rows.forEach(row => {
                            const productName = row.cells[1].textContent.toLowerCase();
                            const category = row.cells[2].textContent.toLowerCase();

                            if (productName.includes(searchValue) || category.includes(searchValue)) {
                                row.style.display = '';
                            } else {
                                row.style.display = 'none';
                            }
                        });
                    }

                    window.onclick = function (event) {
                        const modal = document.getElementById('productModal');
                        if (event.target === modal) {
                            closeProductModal();
                        }
                    }

                    document.getElementById('productPrice').addEventListener('input', function () {
                        formatCurrency(this);
                    });

                    document.getElementById('productOldPrice').addEventListener('input', function () {
                        formatCurrency(this);
                    });

                    function formatCurrency(input) {
                        let value = input.value.replace(/\D/g, '');
                        value = (value / 100).toFixed(2);
                        input.value = value;
                    }

                    function testConnection() {
                        const url = 'https://miamianet.com.br/admin_actions.php?action=test';

                        fetch(url, {
                            method: 'GET',
                            credentials: 'include',           // <- garante envio do cookie de sessão
                            cache: 'no-store',                // <- evita resposta velha
                        })
                            .then(async (response) => {
                                console.log('Status:', response.status);
                                console.log('URL final:', response.url);
                                const text = await response.text();
                                console.log('Raw response:', text);

                                if (!response.ok) {
                                    throw new Error(`HTTP ${response.status}`);
                                }

                                // Se vier HTML (ex: <!DOCTYPE), é porque o Apache te redirecionou pro login
                                if (text.trim().startsWith('<!DOCTYPE')) {
                                    throw new Error('Recebi HTML em vez de JSON (provável redirect para login).');
                                }

                                const data = JSON.parse(text);
                                if (data.success) {
                                    console.log('Conexão OK com o servidor');
                                } else {
                                    console.error('Erro na conexão:', data.error);
                                    alert('Erro de conexão com o servidor: ' + (data.error || 'desconhecido'));
                                }
                            })
                            .catch((err) => {
                                console.error('Erro testConnection:', err);
                                alert(
                                    'Falha no teste de conexão:\n' +
                                    err.message +
                                    '\n\nDica: verifique se /admin_actions.php NÃO está sendo reescrito e se o Nginx está forçando HTTPS no proxy.'
                                );
                            });
                    }


                    document.addEventListener('DOMContentLoaded', function () {
                        // testConnection(); // Removido: só faz teste quando explicitamente solicitado

                        document.getElementById('hasDiscount').addEventListener('change', toggleDiscountField);
                        document.getElementById('productPrice').addEventListener('input', calculateDiscountPreview);
                        document.getElementById('productOldPrice').addEventListener('input', calculateDiscountPreview);

                        initColorSelection();

                        const statCards = document.querySelectorAll('.stat-card h3');
                        statCards.forEach((stat, index) => {
                            const finalValue = parseInt(stat.textContent);
                            let currentValue = 0;
                            const increment = finalValue / 50;

                            const timer = setInterval(() => {
                                currentValue += increment;
                                if (currentValue >= finalValue) {
                                    stat.textContent = finalValue;
                                    clearInterval(timer);
                                } else {
                                    stat.textContent = Math.floor(currentValue);
                                }
                            }, 20);
                        });
                    });

                    document.addEventListener('DOMContentLoaded', function () {
                        const messagesCard = document.querySelector('.messages-card');
                        const newMessagesCard = document.querySelector('.new-messages-card');

                        if (messagesCard) {
                            messagesCard.addEventListener('click', function () {
                                window.open('admin-mensagens.php', '_blank');
                            });
                        }

                        if (newMessagesCard) {
                            newMessagesCard.addEventListener('click', function () {
                                window.open('admin-mensagens.php', '_blank');
                            });
                        }

                        function updateDestaquePreview() {
                            const titulo = document.querySelector('input[name="destaque_titulo"]')?.value ||
                                'Título do Produto';
                            const descricao = document.querySelector('textarea[name="destaque_descricao"]')?.value ||
                                'Descrição do produto destacado';
                            const botaoTexto = document.querySelector('input[name="destaque_botao_texto"]')?.value ||
                                'Ver Produto';
                            const corFundo = document.querySelector('input[name="destaque_cor_fundo"]')?.value ||
                                '#520100';
                            const corTexto = document.querySelector('input[name="destaque_cor_texto"]')?.value ||
                                '#ffffff';

                            const preview = document.getElementById('destaque-preview');
                            if (preview) {
                                preview.style.background = corFundo;
                                preview.style.color = corTexto;

                                preview.innerHTML = `
                    <h3 style="margin: 0 0 10px 0; font-size: 24px;">
                        ${titulo}
                    </h3>
                    <p style="margin: 0 0 20px 0; opacity: 0.9; font-size: 16px;">
                        ${descricao}
                    </p>
                    <div style="
                        background: rgba(255,255,255,0.2); 
                        color: inherit; 
                        padding: 12px 24px; 
                        border-radius: 8px; 
                        display: inline-block;
                        font-weight: bold;
                    ">
                        ${botaoTexto}
                    </div>
                `;
                            }
                        }

                        const inputs = [
                            'input[name="destaque_titulo"]',
                            'textarea[name="destaque_descricao"]',
                            'input[name="destaque_botao_texto"]',
                            'input[name="destaque_cor_fundo"]',
                            'input[name="destaque_cor_texto"]'
                        ];

                        inputs.forEach(selector => {
                            const element = document.querySelector(selector);
                            if (element) {
                                element.addEventListener('input', updateDestaquePreview);
                            }
                        });
                    });


                    function openReviewModal() {
                        document.getElementById('reviewModalTitle').textContent = 'Adicionar Nova Avaliação';
                        document.getElementById('reviewForm').reset();
                        document.getElementById('reviewId').value = '';
                        document.getElementById('reviewAtivo').checked = true;
                        document.getElementById('reviewEstrelas').value = '5';

                        document.querySelector('input[name="foto_tipo"][value="iniciais"]').checked = true;
                        document.getElementById('reviewTipoFoto').value = 'iniciais';

                        toggleFotoOptions();

                        document.getElementById('avatarUpload').value = '';
                        document.getElementById('avatarPreview').style.display = 'none';

                        document.getElementById('reviewModal').style.display = 'block';
                    }

                    function closeReviewModal() {
                        document.getElementById('reviewModal').style.display = 'none';
                    }

                    function editReview(reviewId) {
                        document.getElementById('reviewModalTitle').textContent = 'Editar Avaliação';
                        document.getElementById('reviewId').value = reviewId;

                        const reviewItems = document.querySelectorAll('.review-item');
                        const targetReview = Array.from(reviewItems).find(item => {
                            return item.querySelector('button[onclick*="' + reviewId + '"]');
                        });

                        if (targetReview) {
                            const nome = targetReview.querySelector('h4').textContent.trim().replace(/INATIVO/g, '').trim();
                            const avaliacao = targetReview.querySelector('[style*="border-left: 4px solid"]').textContent
                                .replace(/"/g, '').trim();
                            const estrelas = targetReview.querySelectorAll('[style*="color: rgb(255, 193, 7)"]').length;
                            const ativo = !targetReview.querySelector('[style*="INATIVO"]');

                            document.getElementById('reviewNome').value = nome;
                            document.getElementById('reviewTexto').value = avaliacao;
                            document.getElementById('reviewEstrelas').value = estrelas;
                            document.getElementById('reviewAtivo').checked = ativo;
                        }

                        document.getElementById('reviewModal').style.display = 'block';
                    }

                    async function saveReview() {
                        const form = document.getElementById('reviewForm');

                        if (!form.checkValidity()) {
                            notifications.warning('Campos Obrigatórios',
                                'Por favor, preencha todos os campos obrigatórios.');
                            return;
                        }

                        const nome = document.getElementById('reviewNome').value.trim();
                        const texto = document.getElementById('reviewTexto').value.trim();
                        const tipoFoto = document.getElementById('reviewTipoFoto').value;

                        if (!nome || !texto) {
                            notifications.warning('Campos Obrigatórios', 'Nome e depoimento são obrigatórios.');
                            return;
                        }

                        if (tipoFoto === 'upload') {
                            const fileInput = document.getElementById('avatarUpload');

                            if (!fileInput.files || fileInput.files.length === 0) {
                                notifications.warning('Foto Obrigatória', 'Selecione uma foto ou escolha usar iniciais.');
                                return;
                            }

                            try {
                                notifications.info('Enviando...', 'Fazendo upload da foto...');

                                const uploadData = new FormData();
                                uploadData.append('action', 'upload-avatar');
                                uploadData.append('avatar', fileInput.files[0]);

                                const uploadResponse = await fetch('/admin_actions.php', {
                                    method: 'POST',
                                    body: uploadData
                                });

                                const uploadResult = await uploadResponse.json();

                                if (!uploadResult.success) {
                                    notifications.error('Erro no Upload', uploadResult.error);
                                    return;
                                }

                                document.getElementById('reviewFoto').value = uploadResult.path;

                            } catch (error) {
                                notifications.error('Erro no Upload', 'Erro ao enviar a foto: ' + error.message);
                                return;
                            }
                        }

                        notifications.info('Salvando...', 'Salvando avaliação...');
                        form.submit();
                    }

                    function deleteReview(reviewId) {
                        if (confirm('Tem certeza que deseja excluir esta avaliação? Esta ação não pode ser desfeita.')) {
                            const form = document.createElement('form');
                            form.method = 'POST';
                            form.innerHTML = `
                <input type="hidden" name="action" value="delete_review">
                <input type="hidden" name="review_id" value="${reviewId}">
            `;
                            document.body.appendChild(form);
                            notifications.info('Excluindo...', 'Excluindo avaliação...');
                            form.submit();
                        }
                    }


                    function toggleFotoOptions() {
                        const tipoFoto = document.querySelector('input[name="foto_tipo"]:checked').value;
                        const iniciaisSection = document.getElementById('iniciaisSection');
                        const uploadSection = document.getElementById('uploadSection');

                        if (tipoFoto === 'iniciais') {
                            iniciaisSection.style.display = 'block';
                            uploadSection.style.display = 'none';
                            document.getElementById('reviewTipoFoto').value = 'iniciais';
                            updateIniciais();
                        } else {
                            iniciaisSection.style.display = 'none';
                            uploadSection.style.display = 'block';
                            document.getElementById('reviewTipoFoto').value = 'upload';
                        }
                    }

                    function updateIniciais() {
                        const nome = document.getElementById('reviewNome').value.trim();
                        let cor = generateColorFromName(nome);

                        if (nome) {
                            const palavras = nome.split(' ').filter(p => p.length > 0);
                            let iniciais = '';

                            if (palavras.length >= 2) {
                                iniciais = (palavras[0][0] + palavras[palavras.length - 1][0]).toUpperCase();
                            } else if (palavras.length === 1) {
                                iniciais = palavras[0].substring(0, 2).toUpperCase();
                            }

                            const currentReviewId = document.getElementById('reviewId').value;
                            if (!currentReviewId) {
                                cor = generateColorFromName(nome);
                                document.getElementById('corInicial').value = cor;
                            }

                            const preview = document.getElementById('previewIniciais');
                            preview.textContent = iniciais;
                            preview.style.background = cor;

                            document.getElementById('reviewFoto').value = JSON.stringify({
                                tipo: 'iniciais',
                                iniciais: iniciais,
                                cor: cor
                            });
                        }
                    }

                    function previewAvatar(input) {
                        if (input.files && input.files[0]) {
                            const file = input.files[0];

                            // 🔹 Validação de tamanho
                            if (file.size > 2 * 1024 * 1024) {
                                notifications.warning('Arquivo Grande', 'A foto deve ter no máximo 2MB');
                                input.value = '';
                                return;
                            }

                            // 🔹 Validação de tipo
                            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
                            if (!allowedTypes.includes(file.type.toLowerCase())) {
                                notifications.warning('Formato Inválido', 'Use apenas JPG, PNG, GIF ou WebP');
                                input.value = '';
                                return;
                            }

                            // 🔹 Preview da imagem
                            const reader = new FileReader();
                            reader.onload = function (e) {
                                const previewImg = document.getElementById('previewImg');
                                if (previewImg) {
                                    previewImg.src = e.target.result;
                                    previewImg.onerror = () => previewImg.src = '/img/default-product.png';
                                }

                                const avatarPreview = document.getElementById('avatarPreview');
                                if (avatarPreview) avatarPreview.style.display = 'block';

                                // ✅ Usa o input hidden agora, não o de arquivo
                                const hiddenInput = document.getElementById('reviewFoto');
                                if (hiddenInput) hiddenInput.value = 'upload_pending';
                            };
                            reader.readAsDataURL(file);
                        }
                    }


                    function removeAvatar() {
                        document.getElementById('avatarUpload').value = '';
                        document.getElementById('avatarPreview').style.display = 'none';
                        document.getElementById('reviewFoto').value = '';
                    }

                    function getRandomColor() {
                        const colors = ['#e91e63', '#2196f3', '#9c27b0', '#ff9800', '#4caf50', '#f44336', '#607d8b', '#795548'];
                        return colors[Math.floor(Math.random() * colors.length)];
                    }

                    function generateColorFromName(name) {
                        const colors = [
                            '#e91e63',
                            '#2196f3',
                            '#9c27b0',
                            '#ff9800',
                            '#4caf50',
                            '#f44336',
                            '#607d8b',
                            '#795548',
                            '#00bcd4',
                            '#ff5722',
                            '#3f51b5',
                            '#8bc34a',
                            '#ffc107',
                            '#e91e63',
                            '#673ab7',
                            '#009688'
                        ];

                        let hash = 0;
                        for (let i = 0; i < name.length; i++) {
                            const char = name.charCodeAt(i);
                            hash = ((hash << 5) - hash) + char;
                            hash = hash & hash;
                        }

                        const index = Math.abs(hash) % colors.length;
                        return colors[index];
                    }

                    document.addEventListener('DOMContentLoaded', function () {
                        const nomeInput = document.getElementById('reviewNome');
                        if (nomeInput) {
                            nomeInput.addEventListener('input', updateIniciais);
                        }

                    });

                    function toggleSidebar() {
                        const sidebar = document.querySelector('.admin-sidebar');
                        const overlay = document.querySelector('.sidebar-overlay');

                        sidebar.classList.toggle('open');
                        overlay.classList.toggle('show');
                    }

                    function closeSidebar() {
                        const sidebar = document.querySelector('.admin-sidebar');
                        const overlay = document.querySelector('.sidebar-overlay');

                        sidebar.classList.remove('open');
                        overlay.classList.remove('show');
                    }

                    document.querySelectorAll('.nav-link').forEach(link => {
                        link.addEventListener('click', () => {
                            if (window.innerWidth <= 768) {
                                closeSidebar();
                            }
                        });
                    });

                    window.onclick = function (event) {
                        const productModal = document.getElementById('productModal');
                        const reviewModal = document.getElementById('reviewModal');

                        if (event.target == productModal) {
                            closeProductModal();
                        }
                        if (event.target == reviewModal) {
                            closeReviewModal();
                        }
                    }

                    document.addEventListener('DOMContentLoaded', function () {
                        const navLinks = document.querySelectorAll('.nav-link[href^="#"]');

                        navLinks.forEach(link => {
                            link.addEventListener('click', function (e) {
                                e.preventDefault();

                                const targetId = this.getAttribute('href').substring(1);
                                const targetElement = document.getElementById(targetId);

                                if (targetElement) {
                                    navLinks.forEach(navLink => navLink.classList.remove('active'));
                                    this.classList.add('active');

                                    targetElement.scrollIntoView({
                                        behavior: 'smooth',
                                        block: 'start'
                                    });

                                    if (window.innerWidth <= 768) {
                                        closeSidebar();
                                    }
                                }
                            });
                        });

                        window.addEventListener('scroll', function () {
                            const sections = ['dashboard', 'produtos'];
                            let current = '';

                            sections.forEach(sectionId => {
                                const section = document.getElementById(sectionId);
                                if (section) {
                                    const sectionTop = section.offsetTop - 100;
                                    const sectionHeight = section.offsetHeight;

                                    if (window.pageYOffset >= sectionTop && window.pageYOffset <
                                        sectionTop + sectionHeight) {
                                        current = sectionId;
                                    }
                                }
                            });

                            navLinks.forEach(link => {
                                link.classList.remove('active');
                                if (link.getAttribute('href') === '#' + current) {
                                    link.classList.add('active');
                                }
                            });
                        });

                        // Gerenciar dica de scroll da tabela em mobile
                        const tableWrapper = document.querySelector('.table-wrapper');
                        if (tableWrapper && window.innerWidth <= 768) {
                            let scrollTimeout;
                            tableWrapper.addEventListener('scroll', function () {
                                // Remove a dica quando o usuário começar a rolar
                                if (this.scrollLeft > 0) {
                                    this.classList.add('scrolled');
                                }

                                clearTimeout(scrollTimeout);
                                scrollTimeout = setTimeout(() => {
                                    if (this.scrollLeft === 0) {
                                        this.classList.remove('scrolled');
                                    }
                                }, 1000);
                            });
                        }
                    });
                </script>

        </div>
        </main>
        </div>
    <?php endif; ?>

</body>

</html>
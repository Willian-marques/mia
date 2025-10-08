<?php
require_once 'config.php';

// Processamento do formulário
if ($_POST) {
    $nome = isset($_POST['nome']) ? htmlspecialchars($_POST['nome']) : '';
    $sobrenome = isset($_POST['sobrenome']) ? htmlspecialchars($_POST['sobrenome']) : '';
    $email = isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '';
    $telefone = isset($_POST['telefone']) ? htmlspecialchars($_POST['telefone']) : '';
    $assunto = isset($_POST['assunto']) ? htmlspecialchars($_POST['assunto']) : '';
    $mensagem = isset($_POST['mensagem']) ? htmlspecialchars($_POST['mensagem']) : '';

    if (!empty($nome) && !empty($sobrenome) && !empty($email) && !empty($assunto) && !empty($mensagem)) {
        // Salvar os dados em um arquivo JSON
        $data = [
            'id' => uniqid(),
            'data_envio' => date('Y-m-d H:i:s'),
            'nome' => $nome,
            'sobrenome' => $sobrenome,
            'email' => $email,
            'telefone' => $telefone,
            'assunto' => $assunto,
            'mensagem' => $mensagem,
            'status' => 'nova'
        ];

        // Criar diretório se não existir
        if (!file_exists('data')) {
            mkdir('data', 0777, true);
        }

        // Carregar mensagens existentes
        $arquivo_mensagens = 'data/mensagens.json';
        $mensagens = [];
        if (file_exists($arquivo_mensagens)) {
            $conteudo = file_get_contents($arquivo_mensagens);
            $mensagens = json_decode($conteudo, true) ?: [];
        }

        // Adicionar nova mensagem
        $mensagens[] = $data;

        // Salvar arquivo
        file_put_contents($arquivo_mensagens, json_encode($mensagens, JSON_PRETTY_PRINT));

        // Redirecionar para página de sucesso
        header('Location: obrigado?success=1&id=' . $data['id']);
        exit;
    } else {
        $erro = "Por favor, preencha todos os campos obrigatórios.";
    }
}

// Configurações da página
$current_page = "contato";
$site_title = getPageTitle('contato', 'Fale Conosco');
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contato - Mia Couro Legítimo | Fale Conosco</title>
    
    <!-- SEO Meta Tags -->
    <meta name="description" content="Entre em contato com a Mia Couro Legítimo. WhatsApp, e-mail ou redes sociais. Estamos aqui para atendê-lo com excelência e tirar suas dúvidas.">
    <meta name="keywords" content="contato mia couro, fale conosco, whatsapp, email, atendimento, suporte">
    <meta name="robots" content="index, follow">
    
    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://miamianet.com.br/contato">
    <meta property="og:title" content="Contato - Mia Couro Legítimo">
    <meta property="og:description" content="Entre em contato conosco. Estamos aqui para atendê-lo!">
    <meta property="og:image" content="https://miamianet.com.br/img/MiaCourolegitimo 1.svg">
    
    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="Contato - Mia Couro Legítimo">
    <meta name="twitter:description" content="Entre em contato conosco.">
    
    <!-- Canonical URL -->
    <link rel="canonical" href="https://miamianet.com.br/contato">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="img/logo.png">
    <link rel="alternate icon" type="image/png" href="img/logo.png">
    <link rel="apple-touch-icon" href="img/logo.png">
    
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="responsive-global.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <style>
        /* Reset e Base */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'BR Sonoma', Arial, sans-serif;
            background-color: #FCF8F1;
            color: #262523;
            line-height: 1.6;
            overflow-x: hidden;
        }

        /* Garantir que o header seja visível */
        .header {
            display: flex !important;
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            z-index: 1000 !important;
        }

        /* Container Principal */
        .contact-container {
            width: 100vw;
            min-height: calc(100vh - 68px);
            position: relative;
            background: #FCF8F1;
            margin: 0;
            padding-top: 88px;
            /* 68px do header + 20px de espaço */
        }

        /* Header Spacing não necessário */
        .header-spacer {
            display: none;
        }

        /* Hero Section */
        .hero-section {
            width: 100%;
            height: 112px;
            position: relative;
            max-width: 1152px;
            margin: 0 auto;
            /* Removido margin-top já que temos padding no container */
            text-align: center;
        }

        .hero-title {
            width: 100%;
            text-align: center;
            color: #520100;
            font-size: 36px;
            font-family: 'BR Sonoma';
            font-weight: 700;
            line-height: 40px;
            margin-bottom: 16px;
        }

        .hero-subtitle {
            width: 100%;
            max-width: 654px;
            margin: 0 auto;
            text-align: center;
            color: rgba(38, 37, 35, 0.80);
            font-size: 18px;
            font-family: 'BR Sonoma';
            font-weight: 400;
            line-height: 28px;
        }

        /* Cards Container */
        .cards-section {
            position: relative;
            margin: 50px auto;
            max-width: 1360px;
            display: flex;
            gap: 50px;
            justify-content: center;
            align-items: flex-start;
            padding: 0 20px;
        }

        /* Card Nossos Canais */
        .channels-card {
            width: 552px;
            min-height: 832px;
            position: relative;
            background: rgba(82, 1, 0, 0.90);
            border-radius: 30px;
            box-shadow: 0px 10px 15px rgba(0, 0, 0, 0.10);
            flex-shrink: 0;
        }

        .channels-title {
            text-align: center;
            color: #FCF8F1;
            font-size: 24px;
            font-family: 'BR Sonoma';
            font-weight: 600;
            padding: 31px 0 20px;
            margin: 0;
        }

        /* WhatsApp Method */
        .whatsapp-method {
            width: calc(100% - 64px);
            height: 128px;
            position: relative;
            margin: 20px 32px;
            background: #F0FDF4;
            border-radius: 12px;
        }

        .whatsapp-icon {
            width: 56px;
            height: 56px;
            position: absolute;
            left: 24px;
            top: 36px;
            background: #16A34A;
            border-radius: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .whatsapp-info {
            position: absolute;
            left: 104px;
            top: 24px;
        }

        .whatsapp-title {
            color: #262523;
            font-size: 18px;
            font-family: 'BR Sonoma';
            font-weight: 600;
            line-height: 28px;
        }

        .whatsapp-number {
            color: #16A34A;
            font-size: 18px;
            font-family: 'BR Sonoma';
            font-weight: 500;
            line-height: 28px;
            margin-top: 4px;
        }

        .whatsapp-desc {
            color: rgba(38, 37, 35, 0.80);
            font-size: 14px;
            font-family: 'BR Sonoma';
            font-weight: 400;
            line-height: 20px;
            margin-top: 4px;
        }

        /* Telefone Method */
        .phone-method {
            width: calc(100% - 64px);
            height: 128px;
            position: relative;
            margin: 20px 32px;
            background: #EFF6FF;
            border-radius: 12px;
        }

        .phone-icon {
            width: 56px;
            height: 56px;
            position: absolute;
            left: 24px;
            top: 36px;
            background: #8A4D99;
            border-radius: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .phone-info {
            position: absolute;
            left: 104px;
            top: 24px;
        }

        .phone-title {
            color: #262523;
            font-size: 18px;
            font-family: 'BR Sonoma';
            font-weight: 600;
            line-height: 28px;
        }

        .phone-number {
            color: #8A4D99;
            font-size: 18px;
            font-family: 'BR Sonoma';
            font-weight: 500;
            line-height: 28px;
            margin-top: 4px;
        }

        .phone-desc {
            color: rgba(38, 37, 35, 0.80);
            font-size: 14px;
            font-family: 'BR Sonoma';
            font-weight: 400;
            line-height: 20px;
            margin-top: 4px;
        }

        /* Hover effects for clickable contact methods */
        .whatsapp-method:hover,
        .phone-method:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
        }

        .whatsapp-method,
        .phone-method {
            transition: all 0.3s ease;
            cursor: pointer;
        }

        /* Instagram Method */
        .instagram-method {
            width: calc(100% - 64px);
            height: 128px;
            position: relative;
            margin: 20px 32px;
            background: #EFF6FF;
            border-radius: 12px;
        }

        .instagram-icon {
            width: 56px;
            height: 56px;
            position: absolute;
            left: 24px;
            top: 36px;
            background: linear-gradient(90deg, #9333EA 0%, #DB2777 100%);
            border-radius: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .instagram-info {
            position: absolute;
            left: 104px;
            top: 24px;
        }

        .instagram-title {
            color: #262523;
            font-size: 18px;
            font-family: 'BR Sonoma';
            font-weight: 600;
            line-height: 28px;
        }

        .instagram-handle {
            color: #DB2777;
            font-size: 18px;
            font-family: 'BR Sonoma';
            font-weight: 500;
            line-height: 28px;
            margin-top: 4px;
        }

        .instagram-desc {
            color: rgba(38, 37, 35, 0.80);
            font-size: 14px;
            font-family: 'BR Sonoma';
            font-weight: 400;
            line-height: 20px;
            margin-top: 4px;
        }

        /* Additional Info */
        .additional-info {
            width: calc(100% - 64px);
            min-height: 176px;
            position: relative;
            margin: 20px 32px;
            background: white;
            border-radius: 12px;
            padding: 24px;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .info-title {
            color: #111827;
            font-size: 18px;
            font-family: 'BR Sonoma';
            font-weight: 600;
            margin-bottom: 16px;
        }

        .info-item {
            display: flex;
            align-items: center;
            color: rgba(38, 37, 35, 0.80);
            font-size: 16px;
            font-family: 'BR Sonoma';
            font-weight: 400;
            line-height: 24px;
            margin-bottom: 12px;
            padding: 8px 0;
            width: 100%;
        }

        .info-item svg {
            width: 16px;
            height: 16px;
            margin-right: 12px;
        }

        /* Form Card */
        .form-card {
            width: 552px;
            min-height: 832px;
            position: relative;
            background: rgba(82, 1, 0, 0.90);
            border-radius: 30px;
            box-shadow: 0px 10px 15px rgba(0, 0, 0, 0.10);
            flex-shrink: 0;
        }

        .form-title {
            text-align: center;
            color: #FCF8F1;
            padding: 31px 0 10px;
            margin: 0;
            font-size: 24px;
            font-family: 'BR Sonoma';
            font-weight: 600;
        }

        .form-subtitle {
            text-align: center;
            color: #FCF8F1;
            font-size: 16px;
            font-family: 'BR Sonoma';
            font-weight: 400;
            margin: 0 40px 20px;
            padding-bottom: 10px;
        }

        /* Form Styles */
        .contact-form {
            position: relative;
            margin: 0 32px;
            width: calc(100% - 64px);
            padding-bottom: 40px;
        }

        .form-row {
            display: flex;
            gap: 24px;
            margin-bottom: 24px;
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-group.half {
            width: 232px;
        }

        .form-group label {
            display: block;
            color: rgba(252, 248, 241, 0.80);
            font-size: 14px;
            font-family: 'BR Sonoma';
            font-weight: 500;
            margin-bottom: 8px;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            height: 50px;
            background: white;
            border: 1px solid #D1D5DB;
            border-radius: 8px;
            padding: 13px 16px;
            font-size: 16px;
            font-family: 'BR Sonoma';
            font-weight: 400;
            color: #262523;
            box-sizing: border-box;
        }

        .form-group textarea {
            height: 146px;
            resize: none;
        }

        .form-group input::placeholder,
        .form-group textarea::placeholder {
            color: #ADAEBC;
        }

        .submit-btn {
            width: 488px;
            height: 48px;
            background: #8A4D99;
            border: none;
            border-radius: 8px;
            color: white;
            font-size: 18px;
            font-family: 'BR Sonoma';
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 16px;
        }

        .submit-btn:hover {
            background: #7C3E8F;
        }

        /* Icons */
        .icon-white {
            width: 18px;
            height: 20px;
            fill: white;
        }

        /* Responsive */
        @media (max-width: 1920px) {
            .contact-container {
                width: 100%;
                height: auto;
                padding: 0 20px;
            }

            .hero-section {
                position: relative;
                width: 100%;
                left: 0;
                top: 20px;
                text-align: center;
                margin-bottom: 60px;
            }

            .hero-title,
            .hero-subtitle {
                position: relative;
                left: 0;
                width: 100%;
                max-width: 800px;
                margin: 0 auto;
            }

            .cards-section {
                position: relative;
                width: 100%;
                height: auto;
                display: flex;
                gap: 40px;
                justify-content: center;
                flex-wrap: wrap;
            }

            .channels-card,
            .form-card {
                position: relative;
                left: 0;
                top: 0;
                margin-bottom: 40px;
            }
        }

        @media (max-width: 1400px) {
            .cards-section {
                flex-direction: column;
                align-items: center;
                gap: 30px;
            }
        }

        @media (max-width: 1200px) {

            .channels-card,
            .form-card {
                width: 500px;
            }

            .form-row {
                flex-direction: column;
                gap: 0;
            }

            .form-group.half {
                width: 100%;
            }
        }

        @media (max-width: 600px) {

            .cards-section {
                padding: 0 20px;
                gap: 30px;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
            }

            .channels-card,
            .form-card {
                width: 100%;
                max-width: 420px;
                min-height: auto;
                margin: 0 auto;
                display: block;
            }

            .hero-title {
                font-size: 28px;
            }

            .hero-subtitle {
                font-size: 16px;
            }

            .contact-container {
                padding: 20px 15px;
            }

            /* Melhorar cards de canais */
            .whatsapp-method,
            .phone-method,
            .instagram-method {
                width: calc(100% - 40px);
                margin: 15px 20px;
                height: auto;
                min-height: 110px;
                padding: 20px;
            }

            .whatsapp-icon,
            .phone-icon,
            .instagram-icon {
                position: relative;
                left: 0;
                top: 0;
                margin-bottom: 12px;
            }

            .whatsapp-info,
            .phone-info,
            .instagram-info {
                position: relative;
                left: 0;
                top: 0;
            }

            .additional-info {
                width: calc(100% - 40px);
                margin: 15px 20px;
                padding: 20px;
            }
        }

        /* Responsividade melhorada */
        @media (max-width: 768px) {
            .contact-container {
                padding: 88px 15px 30px;
                /* Padding-top ajustado para header fixo (68px header + 20px espaço) */
            }

            .cards-section {
                padding: 0 15px;
                gap: 25px;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
            }

            .hero-section {
                padding: 0 15px;
            }

            .hero-title {
                font-size: 30px;
                margin-bottom: 15px;
            }

            .hero-subtitle {
                font-size: 17px;
                margin-bottom: 30px;
                line-height: 1.5;
            }

            .contact-grid {
                gap: 30px;
                flex-direction: column;
                align-items: center;
            }

            .channels-card,
            .form-card {
                width: 100%;
                max-width: 500px;
                margin: 0 auto;
                display: block;
            }

            .channels-title,
            .form-title {
                font-size: 22px;
            }

            .form-subtitle {
                font-size: 15px;
            }

            /* Melhorar layout dos cards de contato em tablet */
            .whatsapp-method,
            .phone-method,
            .instagram-method {
                width: calc(100% - 50px);
                margin: 18px 25px;
                height: auto;
                min-height: 120px;
                padding: 20px;
            }

            .whatsapp-icon,
            .phone-icon,
            .instagram-icon {
                top: 32px;
            }

            .whatsapp-info,
            .phone-info,
            .instagram-info {
                top: 20px;
            }

            .additional-info {
                width: calc(100% - 50px);
                margin: 18px 25px;
                padding: 20px !important;
            }

            .form-group label {
                font-size: 14px;
            }

            .form-group input,
            .form-group select,
            .form-group textarea {
                font-size: 14px;
                padding: 12px;
            }

            .submit-btn {
                font-size: 16px;
                padding: 14px 28px;
            }

            .channel-item {
                padding: 15px;
            }

            .channel-item h4 {
                font-size: 16px;
            }

            .channel-item p {
                font-size: 14px;
            }
        }

        @media (max-width: 480px) {
            .contact-container {
                padding: 88px 15px 20px;
                /* Padding-top ajustado para header fixo mobile (68px header + 20px espaço) */
            }

            .cards-section {
                padding: 0 15px;
                gap: 20px;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                width: 100%;
            }

            .hero-section {
                padding: 0 15px;
            }

            .hero-title {
                font-size: 24px;
                margin-bottom: 12px;
            }

            .hero-subtitle {
                font-size: 14px;
                margin-bottom: 25px;
                line-height: 1.5;
            }

            .contact-grid {
                gap: 20px;
                padding: 0;
            }

            .channels-card,
            .form-card {
                width: 100%;
                max-width: 380px;
                margin-left: auto;
                margin-right: auto;
                padding: 20px 15px;
                border-radius: 20px;
                display: block;
            }

            .channels-title,
            .form-title {
                font-size: 20px;
                padding: 20px 0 15px;
            }

            .form-subtitle {
                font-size: 14px;
                margin: 0 20px 15px;
            }

            /* Reorganizar cards de contato para mobile */
            .whatsapp-method,
            .phone-method,
            .instagram-method {
                width: calc(100% - 30px);
                margin: 12px 15px;
                height: auto;
                min-height: auto;
                padding: 16px;
                display: flex;
                flex-direction: column;
                align-items: center;
                text-align: center;
            }

            .whatsapp-icon,
            .phone-icon,
            .instagram-icon {
                position: relative;
                left: 0;
                top: 0;
                margin-bottom: 12px;
                width: 48px;
                height: 48px;
            }

            .whatsapp-info,
            .phone-info,
            .instagram-info {
                position: relative;
                left: 0;
                top: 0;
                width: 100%;
                text-align: center;
            }

            .whatsapp-title,
            .phone-title,
            .instagram-title {
                font-size: 16px;
                line-height: 1.4;
            }

            .whatsapp-number,
            .phone-number,
            .instagram-handle {
                font-size: 16px;
                line-height: 1.4;
                margin-top: 6px;
            }

            .whatsapp-desc,
            .phone-desc,
            .instagram-desc {
                font-size: 13px;
                line-height: 1.4;
                margin-top: 6px;
            }

            /* Additional Info */
            .additional-info {
                width: calc(100% - 30px);
                margin: 12px 15px;
                height: auto !important;
                min-height: auto !important;
                padding: 16px !important;
            }

            .info-title {
                font-size: 16px;
                margin-bottom: 12px;
            }

            .info-item {
                font-size: 14px;
                line-height: 1.5;
                margin-bottom: 10px;
                padding: 6px 0;
                flex-wrap: wrap;
            }

            .info-item svg {
                flex-shrink: 0;
                margin-right: 10px;
            }

            /* Form adjustments */
            .contact-form {
                margin: 0 15px;
                width: calc(100% - 30px);
                padding-bottom: 25px;
            }

            .form-group {
                margin-bottom: 16px;
            }

            .form-group label {
                font-size: 13px;
                margin-bottom: 6px;
            }

            .form-group input,
            .form-group select,
            .form-group textarea {
                font-size: 14px;
                padding: 12px;
                min-height: 44px;
                border-radius: 8px;
            }

            .form-group textarea {
                min-height: 120px;
            }

            .submit-btn {
                font-size: 15px;
                padding: 14px 28px;
                width: 100%;
                border-radius: 10px;
                min-height: 48px;
            }

            .channel-item {
                padding: 12px;
                margin-bottom: 12px;
            }

            .channel-item h4 {
                font-size: 15px;
                margin-bottom: 5px;
            }

            .channel-item p {
                font-size: 13px;
            }

            .channel-icon {
                width: 20px;
                height: 20px;
            }
        }

        /* Telas extra pequenas (celulares pequenos) */
        @media (max-width: 380px) {
            .contact-container {
                padding: 85px 10px 15px;
            }

            .cards-section {
                padding: 0 10px;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                width: 100%;
            }

            .hero-title {
                font-size: 22px;
            }

            .hero-subtitle {
                font-size: 13px;
                line-height: 1.4;
            }

            .channels-card,
            .form-card {
                width: 100%;
                max-width: 340px;
                margin-left: auto;
                margin-right: auto;
                padding: 18px 12px;
                border-radius: 18px;
                display: block;
            }

            .channels-title,
            .form-title {
                font-size: 18px;
                padding: 18px 0 12px;
            }

            .form-subtitle {
                font-size: 13px;
                margin: 0 15px 12px;
            }

            .whatsapp-method,
            .phone-method,
            .instagram-method {
                width: calc(100% - 24px);
                margin: 10px 12px;
                padding: 14px;
            }

            .whatsapp-icon,
            .phone-icon,
            .instagram-icon {
                width: 44px;
                height: 44px;
                margin-bottom: 10px;
            }

            .whatsapp-title,
            .phone-title,
            .instagram-title,
            .whatsapp-number,
            .phone-number,
            .instagram-handle {
                font-size: 15px;
            }

            .whatsapp-desc,
            .phone-desc,
            .instagram-desc {
                font-size: 12px;
            }

            .additional-info {
                width: calc(100% - 24px);
                margin: 10px 12px;
                padding: 14px !important;
            }

            .info-title {
                font-size: 15px;
            }

            .info-item {
                font-size: 13px;
            }

            .contact-form {
                margin: 0 12px;
                width: calc(100% - 24px);
            }

            .form-group input,
            .form-group select,
            .form-group textarea {
                font-size: 13px;
                padding: 10px;
            }

            .submit-btn {
                font-size: 14px;
                padding: 12px 24px;
                min-height: 44px;
            }
        }

        /* Landscape mobile */
        @media (max-width: 767px) and (orientation: landscape) {
            .contact-container {
                padding: 20px;
            }

            .hero-title {
                font-size: 28px;
            }

            .hero-subtitle {
                font-size: 16px;
                margin-bottom: 20px;
            }

            .contact-grid {
                gap: 25px;
            }
        }
    </style>
</head>

<body>
    <!-- Header -->
    <header class="header">
        <div class="menu-toggle" id="menuToggle">
            <span></span>
            <span></span>
            <span></span>
        </div>
        <div class="logo">
            <a href="index.php" style="cursor: pointer; display: block;">
                <img src="icon s/logotipo.svg" alt="Mia Couro Legítimo">
            </a>
        </div>
        <nav class="nav-menu" id="navMenu">
            <div class="menu-title">Menu</div>
            <a href="index">Início</a>
            <a href="produtos">Produtos</a>
            <a href="sobre">Sobre nós</a>
            <a href="contato" class="active">Contato</a>
            <a href="produtos?filter=desconto" class="sale-link">Sale</a>
        </nav>
    </header>

    <div class="contact-container">
        <!-- Hero Section -->
        <div class="hero-section">
            <div class="hero-title">Fale Conosco</div>
            <div class="hero-subtitle">Estamos aqui para ajudar! Entre em contato através dos nossos canais
                ou<br>preencha o formulário abaixo.</div>
        </div>

        <!-- Cards Section -->
        <div class="cards-section">
            <!-- Nossos Canais Card -->
            <div class="channels-card">
                <div class="channels-title">Nossos Canais</div>

                <!-- WhatsApp -->
                <div class="whatsapp-method">
                    <div class="whatsapp-icon">
                        <svg class="icon-white" viewBox="0 0 24 24" fill="none">
                            <path
                                d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.464 3.488"
                                fill="white" />
                        </svg>
                    </div>
                    <div class="whatsapp-info">
                        <div class="whatsapp-title">WhatsApp</div>
                        <div class="whatsapp-number">+55 41 9733-8289</div>
                        <div class="whatsapp-desc">Resposta rápida 24/7</div>
                    </div>
                </div>


                <!-- Telefone -->
                <div class="phone-method">
                    <div class="phone-icon">
                        <svg class="icon-white" viewBox="0 0 24 24" fill="none">
                            <path
                                d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"
                                fill="white" />
                        </svg>
                    </div>
                    <div class="phone-info">
                        <div class="phone-title">Telefone</div>
                        <div class="phone-number">+55 41 9733-8289</div>
                        <div class="phone-desc">Seg - Sex: 9h às 18h</div>
                    </div>
                </div>

                <!-- Instagram -->
                <div class="instagram-method">
                    <div class="instagram-icon">
                        <svg class="icon-white" viewBox="0 0 24 24" fill="none">
                            <path
                                d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"
                                fill="white" />
                        </svg>
                    </div>
                    <div class="instagram-info">
                        <div class="instagram-title">Instagram</div>
                        <div class="instagram-handle">@Mia.mianet</div>
                        <div class="instagram-desc">Siga-nos para novidades</div>
                    </div>
                </div>

                <!-- Additional Info -->
                <div class="additional-info">
                    <div class="info-title">Informações Adicionais</div>
                    <div class="info-item">
                        <span>⏰ Horário: Seg - Sex 9h às 18h</span>
                    </div>
                    <div class="info-item">
                        <span>📧 contato@mia.com.br</span>
                    </div>
                    <div class="info-item">
                        <span>📍 Curitiba, PR - Brasil</span>
                    </div>
                </div>
            </div>

            <!-- Form Card -->
            <div class="form-card">
                <div class="form-title">Envie uma Mensagem</div>
                <div class="form-subtitle">Preencha o formulário e entraremos em contato em breve</div>

                <?php if (isset($erro)): ?>
                    <div
                        style="background: #fef2f2; color: #dc2626; padding: 15px; border-radius: 8px; margin: 20px 32px; border: 1px solid #fecaca;">
                        <?php echo $erro; ?>
                    </div>
                <?php endif; ?>

                <form class="contact-form" method="POST" action="">
                    <div class="form-row">
                        <div class="form-group half">
                            <label for="nome">Nome *</label>
                            <input type="text" id="nome" name="nome" placeholder="Seu nome" required>
                        </div>
                        <div class="form-group half">
                            <label for="sobrenome">Sobrenome *</label>
                            <input type="text" id="sobrenome" name="sobrenome" placeholder="Seu sobrenome" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="email">Email *</label>
                        <input type="email" id="email" name="email" placeholder="seu@email.com" required>
                    </div>

                    <div class="form-group">
                        <label for="telefone">Telefone</label>
                        <input type="tel" id="telefone" name="telefone" placeholder="+55 41 9733-8289">
                    </div>

                    <div class="form-group">
                        <label for="assunto">Assunto *</label>
                        <select id="assunto" name="assunto" required>
                            <option value="">Selecione um assunto</option>
                            <option value="duvida">Dúvida sobre produto</option>
                            <option value="orcamento">Orçamento</option>
                            <option value="suporte">Suporte</option>
                            <option value="outro">Outro</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="mensagem">Mensagem *</label>
                        <textarea id="mensagem" name="mensagem" placeholder="Como podemos ajudá-lo?"
                            required></textarea>
                    </div>

                    <button type="submit" class="submit-btn">
                        📨 Enviar Mensagem
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer" id="contato">
        <div class="container">
            <div class="footer-content">
                <div class="footer-brand">
                    <img src="icon s/MiaCourolegitimo 1.svg" alt="Mia Couro Legítimo" class="footer-logo">
                    <p>Única, para quem também é</p>
                    <div class="social-links">

                        <a href="https://www.instagram.com/mia.mianet" target="_blank" aria-label="Instagram">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.40s-.644-1.44-1.439-1.44z" />
                            </svg>
                        </a>
                        <a href="https://wa.me/554197338289" target="_blank" aria-label="WhatsApp"
                            title="+55 41 9733-8289">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.464 3.488" />
                            </svg>
                        </a>

                    </div>
                </div>
                <div class="footer-links">
                    <div class="footer-column">
                        <h4>Produtos</h4>
                        <ul>
                            <li><a href="produtos?categoria=viagem">Viagem</a></li>
                            <li><a href="produtos?categoria=carteiras">Dia a Dia</a></li>
                            <li><a href="produtos?categoria=bolsas">Bolsa</a></li>
                            <li><a href="produtos?filter=desconto">Desconto</a></li>
                        </ul>
                    </div>
                    <div class="footer-column">
                        <h4>Atendimento</h4>
                        <ul>
                            <li><a href="contato">Contato</a></li>
                            <li><a href="sobre">Sobre Nós</a></li>
                        </ul>
                    </div>
                    <div class="footer-column">
                        <h4>Contato</h4>
                        <ul>
                            <li>contato@mia.com.br</li>
                            <li><a href="https://wa.me/554197338289" target="_blank"
                                    style="color: #9CA3AF; text-decoration: none;">+55 (41) 9733-8289</a></li>
                            <li>Curitiba, PR</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>© 2025 Mia. Todos os direitos reservados.</p>
                <p>Desenvolvido por <strong>Aether Design</strong></p>
            </div>
        </div>
    </footer>

    <script>
        // Menu suave - Versão simplificada e funcional
        document.addEventListener('DOMContentLoaded', function () {
            console.log('Script carregado!'); // Debug

            const menuToggle = document.getElementById('menuToggle');
            const navMenu = document.getElementById('navMenu');

            if (menuToggle && navMenu) {
                console.log('Elementos encontrados!'); // Debug

                // Toggle do menu
                menuToggle.addEventListener('click', function (e) {
                    e.preventDefault();
                    console.log('Menu toggle clicado!'); // Debug

                    if (navMenu.classList.contains('active')) {
                        // Fechar menu
                        menuToggle.classList.remove('active');
                        navMenu.classList.remove('active');

                        setTimeout(() => {
                            navMenu.style.display = 'none';
                        }, 400);
                    } else {
                        // Abrir menu
                        navMenu.style.display = 'flex';
                        setTimeout(() => {
                            menuToggle.classList.add('active');
                            navMenu.classList.add('active');
                        }, 10);
                    }
                });

                // Fechar menu ao redimensionar
                window.addEventListener('resize', function () {
                    if (window.innerWidth > 768 && navMenu.classList.contains('active')) {
                        menuToggle.classList.remove('active');
                        navMenu.classList.remove('active');

                        setTimeout(() => {
                            navMenu.style.display = 'none';
                        }, 400);
                    }
                });

                // Fechar menu ao clicar nos links
                const navLinks = navMenu.querySelectorAll('a');
                navLinks.forEach(link => {
                    link.addEventListener('click', function () {
                        console.log('Link clicado, fechando menu...'); // Debug
                        menuToggle.classList.remove('active');
                        navMenu.classList.remove('active');
                        setTimeout(() => {
                            navMenu.style.display = 'none';
                        }, 400);
                    });
                });

                // Fechar menu ao clicar fora
                document.addEventListener('click', function (e) {
                    if (!menuToggle.contains(e.target) && !navMenu.contains(e.target) && navMenu.classList
                        .contains('active')) {
                        console.log('Clique fora detectado, fechando menu...'); // Debug
                        menuToggle.classList.remove('active');
                        navMenu.classList.remove('active');
                        setTimeout(() => {
                            navMenu.style.display = 'none';
                        }, 400);
                    }
                });

            } else {
                console.error('Elementos do menu não encontrados!');
            }

            // Tornar cartões de contato clicáveis
            const whatsappMethod = document.querySelector('.whatsapp-method');
            const phoneMethod = document.querySelector('.phone-method');

            if (whatsappMethod) {
                whatsappMethod.style.cursor = 'pointer';
                whatsappMethod.addEventListener('click', function () {
                    window.open('https://wa.me/554197338289', '_blank');
                });
            }

            if (phoneMethod) {
                phoneMethod.style.cursor = 'pointer';
                phoneMethod.addEventListener('click', function () {
                    window.open('tel:+554197338289', '_self');
                });
            }
        });

        // Carregar script original para outras funcionalidades
        const script = document.createElement('script');
        script.src = 'script.js';
        script.onload = function () {
            console.log('Script.js carregado como backup');
        };
        document.head.appendChild(script);
    </script>
</body>

</html>
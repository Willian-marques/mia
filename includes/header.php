<?php
// Configurações do header
$navigation_items = [
    ['label' => 'Início', 'url' => 'index.php', 'id' => 'home'],
    ['label' => 'Produtos', 'url' => 'produtos.php', 'id' => 'produtos'],
    ['label' => 'Sobre', 'url' => 'sobre.php', 'id' => 'sobre'],
    ['label' => 'Contato', 'url' => 'contato.php', 'id' => 'contato']
];

// Verificar se existe $current_page no escopo global
$current_page = isset($current_page) ? $current_page : '';

// Verificar se as constantes estão definidas
$site_name = defined('SITE_NAME') ? SITE_NAME : 'Mia Couro Legítimo';

// Determinar o caminho base relativo
$request_uri = $_SERVER['REQUEST_URI'];
$script_name = $_SERVER['SCRIPT_NAME'];
$base_path = str_replace('\\', '/', dirname($script_name));
if ($base_path === '/') {
    $base_path = '';
}
?>

<header class="site-header">
    <nav class="navbar">
        <div class="navbar-brand">
            <a href="<?php echo $base_path; ?>/index.php" style="cursor: pointer;">
                <img src="<?php echo $base_path; ?>/img/MiaCourolegitimo 1.svg" alt="<?php echo $site_name; ?>" class="logo">
            </a>
        </div>

        <div class="navbar-toggle" id="navbar-toggle">
            <span></span>
            <span></span>
            <span></span>
        </div>

        <ul class="navbar-nav" id="navbar-nav">
            <?php foreach ($navigation_items as $item): ?>
            <li class="nav-item">
                <a href="<?php echo $item['url']; ?>"
                    class="nav-link <?php echo ($current_page === $item['id']) ? 'active' : ''; ?>">
                    <?php echo $item['label']; ?>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>
    </nav>
</header>
<?php
// ACESSO RESTRITO - Página apenas para administradores
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verificar se está logado como admin
$admin_logged = isset($_SESSION['admin_logged']) && $_SESSION['admin_logged'];

// Se não estiver logado, redirecionar para página de login
if (!$admin_logged) {
    header('Location: admin.php');
    exit();
}

require_once 'config.php';

// Verificar se é uma requisição AJAX para atualizar status
if ($_POST && isset($_POST['action']) && $_POST['action'] === 'update_status') {
    $message_id = $_POST['message_id'] ?? '';
    $new_status = $_POST['status'] ?? '';

    if ($message_id && in_array($new_status, ['nova', 'lida', 'respondida'])) {
        $mensagens_file = 'data/mensagens.json';
        if (file_exists($mensagens_file)) {
            $mensagens = json_decode(file_get_contents($mensagens_file), true) ?: [];

            // Encontrar e atualizar a mensagem
            foreach ($mensagens as &$msg) {
                if ($msg['id'] === $message_id) {
                    $msg['status'] = $new_status;
                    break;
                }
            }

            // Salvar as mensagens atualizadas
            file_put_contents($mensagens_file, json_encode($mensagens, JSON_PRETTY_PRINT));
        }
    }

    echo json_encode(['success' => true]);
    exit;
}

// Carregar mensagens
$mensagens_file = 'data/mensagens.json';
$mensagens = [];

if (file_exists($mensagens_file)) {
    $mensagens = json_decode(file_get_contents($mensagens_file), true) ?: [];
}

// Ordenar mensagens por data (mais recentes primeiro)
usort($mensagens, function ($a, $b) {
    return strtotime($b['data_envio']) - strtotime($a['data_envio']);
});

// Função para formatar data
function formatarData($data)
{
    $timestamp = strtotime($data);
    return date('d/m/Y H:i', $timestamp);
}

// Função para obter cor do status
function getStatusColor($status)
{
    switch ($status) {
        case 'nova':
            return '#dc2626';
        case 'lida':
            return '#f59e0b';
        case 'respondida':
            return '#10b981';
        default:
            return '#6b7280';
    }
}

// Contar mensagens por status
$stats = [
    'total' => count($mensagens),
    'nova' => 0,
    'lida' => 0,
    'respondida' => 0
];

foreach ($mensagens as $msg) {
    if (isset($stats[$msg['status']])) {
        $stats[$msg['status']]++;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Mensagens de Contato - Mia Couro Legítimo</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap');

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
            --gray-light: #f8f9fa;
            --gray-medium: #e9ecef;
            --gray-dark: #6c757d;
            --text-primary: #2d3748;
            --text-secondary: #718096;
            --sidebar-width: 260px;
            --header-height: 80px;
            --border-radius: 24px;
            --border-radius-sm: 12px;
            --shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            --shadow-glass: 0 8px 32px rgba(0, 0, 0, 0.1);
            --transition: all 0.4s cubic-bezier(0.23, 1, 0.32, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
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

        /* LAYOUT */
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
            color: var(--text-primary);
            margin: 0;
            letter-spacing: -0.5px;
        }

        .header-subtitle {
            color: var(--text-secondary);
            font-size: 14px;
            font-weight: 500;
            margin-top: 4px;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 40px;
        }

        /* STATS CARDS */
        .stats-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 24px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: var(--white);
            padding: 32px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            text-align: center;
            border: 2px solid var(--secondary);
            position: relative;
            overflow: hidden;
            transition: var(--transition);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
        }

        .stat-card.total::before {
            background: var(--gray-dark);
        }

        .stat-card.nova::before {
            background: var(--danger);
        }

        .stat-card.lida::before {
            background: var(--warning);
        }

        .stat-card.respondida::before {
            background: var(--success);
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(138, 77, 153, 0.15);
        }

        .stat-number {
            font-size: 48px;
            font-weight: 800;
            margin-bottom: 8px;
            color: var(--primary);
        }

        .stat-label {
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--text-secondary);
        }

        /* MESSAGES */
        .messages-list {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .message-item {
            background: var(--white);
            border-radius: var(--border-radius);
            padding: 30px;
            box-shadow: var(--shadow);
            border: 2px solid var(--secondary);
            position: relative;
            overflow: hidden;
            transition: var(--transition);
        }

        .message-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
        }

        .message-item[data-status="nova"]::before {
            background: var(--danger);
        }

        .message-item[data-status="lida"]::before {
            background: var(--warning);
        }

        .message-item[data-status="respondida"]::before {
            background: var(--success);
        }

        .message-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(138, 77, 153, 0.15);
            border-color: var(--primary);
        }

        .message-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .sender-info h3 {
            font-size: 20px;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 8px;
        }

        .sender-meta {
            display: flex;
            gap: 20px;
            color: var(--text-secondary);
            font-size: 14px;
        }

        .message-subject {
            font-size: 16px;
            font-weight: 600;
            color: var(--secondary);
            margin-bottom: 16px;
        }

        .message-content {
            background: var(--accent);
            padding: 24px;
            border-radius: var(--border-radius-sm);
            margin-bottom: 20px;
            white-space: pre-wrap;
            line-height: 1.8;
            font-size: 16px;
            color: var(--dark);
        }

        .contact-details {
            display: flex;
            gap: 24px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .contact-item {
            background: var(--gray-light);
            padding: 12px 16px;
            border-radius: var(--border-radius-sm);
            font-size: 14px;
            color: var(--text-primary);
            border: 1px solid var(--gray-medium);
        }

        .message-actions {
            display: flex;
            gap: 12px;
            align-items: center;
            flex-wrap: wrap;
        }

        .status-select {
            padding: 8px 12px;
            border: 2px solid var(--secondary);
            border-radius: var(--border-radius-sm);
            background: var(--white);
            color: var(--primary);
            font-weight: 500;
            cursor: pointer;
        }

        .btn {
            padding: 10px 16px;
            border: none;
            border-radius: var(--border-radius-sm);
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: var(--transition);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn-success {
            background: var(--success);
            color: var(--white);
        }

        .btn-danger {
            background: var(--danger);
            color: var(--white);
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
        }

        .no-messages {
            text-align: center;
            padding: 60px 20px;
            color: var(--text-secondary);
        }

        .no-messages h3 {
            font-size: 24px;
            margin-bottom: 12px;
            color: var(--primary);
        }

        /* TOOLBAR */
        .toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
            background: var(--white);
            padding: 30px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            border: 2px solid var(--secondary);
        }

        .search-box {
            padding: 12px 20px;
            border: 2px solid var(--gray-medium);
            border-radius: var(--border-radius-sm);
            font-size: 16px;
            width: 300px;
            max-width: 100%;
        }

        .search-box:focus {
            outline: none;
            border-color: var(--secondary);
        }

        /* RESPONSIVE */
        @media (max-width: 768px) {
            .admin-sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }

            .admin-sidebar.active {
                transform: translateX(0);
            }

            .admin-main {
                margin-left: 0;
                width: 100%;
            }

            .stats-cards {
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 16px;
            }

            .container {
                padding: 20px;
            }

            .toolbar {
                flex-direction: column;
                gap: 20px;
            }

            .search-box {
                width: 100%;
            }

            .message-header {
                flex-direction: column;
            }

            .sender-meta {
                flex-direction: column;
                gap: 5px;
            }
        }
    </style>
</head>

<body>
    <div class="admin-layout">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <div class="sidebar-header">
                <a href="#" class="sidebar-logo">MIA Admin</a>
            </div>

            <nav class="sidebar-nav">
                <div class="nav-item">
                    <a href="admin.php#dashboard" class="nav-link">
                        <i class="nav-icon fas fa-chart-line"></i>
                        <span>Dashboard</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="admin.php#produtos" class="nav-link">
                        <i class="nav-icon fas fa-box"></i>
                        <span>Produtos</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="admin-mensagens.php" class="nav-link active">
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
                    <a href="admin.php?logout=1" class="nav-link">
                        <i class="nav-icon fas fa-sign-out-alt"></i>
                        <span>Sair</span>
                    </a>
                </div>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="admin-main">
            <div class="admin-header">
                <div>
                    <h1 class="header-title">💬 Mensagens de Contato</h1>
                    <p class="header-subtitle">Gerencie todas as mensagens recebidas pelo site</p>
                </div>
            </div>

            <div class="container">
                <!-- Stats Cards -->
                <div class="stats-cards">
                    <div class="stat-card total">
                        <div class="stat-number"><?php echo $stats['total']; ?></div>
                        <div class="stat-label">Total de Mensagens</div>
                    </div>
                    <div class="stat-card nova">
                        <div class="stat-number"><?php echo $stats['nova']; ?></div>
                        <div class="stat-label">Novas</div>
                    </div>
                    <div class="stat-card lida">
                        <div class="stat-number"><?php echo $stats['lida']; ?></div>
                        <div class="stat-label">Lidas</div>
                    </div>
                    <div class="stat-card respondida">
                        <div class="stat-number"><?php echo $stats['respondida']; ?></div>
                        <div class="stat-label">Respondidas</div>
                    </div>
                </div>

                <!-- Toolbar -->
                <div class="toolbar">
                    <h2>📋 Lista de Mensagens</h2>
                    <input type="text" class="search-box" placeholder="Buscar por nome, email ou mensagem..."
                        id="searchBox">
                </div>

                <!-- Messages List -->
                <div class="messages-list" id="messagesList">
                    <?php if (empty($mensagens)): ?>
                        <div class="no-messages">
                            <h3>Nenhuma mensagem ainda</h3>
                            <p>Quando os clientes enviarem mensagens pelo formulário de contato, elas aparecerão aqui.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($mensagens as $msg): ?>
                            <div class="message-item" data-status="<?php echo $msg['status']; ?>"
                                data-search="<?php echo strtolower($msg['nome'] . ' ' . $msg['sobrenome'] . ' ' . $msg['email'] . ' ' . $msg['mensagem']); ?>">

                                <div class="message-header">
                                    <div class="sender-info">
                                        <h3><?php echo $msg['nome'] . ' ' . $msg['sobrenome']; ?></h3>
                                        <div class="sender-meta">
                                            <span><?php echo formatarData($msg['data_envio']); ?></span>
                                            <span>🆔 <?php echo substr($msg['id'], -8); ?></span>
                                        </div>
                                    </div>
                                    <div class="status-controls">
                                        <select class="status-select"
                                            onchange="updateStatus('<?php echo $msg['id']; ?>', this.value)">
                                            <option value="nova" <?php echo $msg['status'] === 'nova' ? 'selected' : ''; ?>>Nova
                                            </option>
                                            <option value="lida" <?php echo $msg['status'] === 'lida' ? 'selected' : ''; ?>>Lida
                                            </option>
                                            <option value="respondida" <?php echo $msg['status'] === 'respondida' ? 'selected' : ''; ?>>Respondida</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="message-subject">
                                    <strong>
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

                                <div class="message-content">
                                    <?php echo nl2br(htmlspecialchars($msg['mensagem'])); ?>
                                </div>

                                <div class="contact-details">
                                    <div class="contact-item">
                                        📧 <?php echo $msg['email']; ?>
                                    </div>
                                    <?php if (!empty($msg['telefone'])): ?>
                                        <div class="contact-item">
                                            📱 <?php echo $msg['telefone']; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="message-actions">
                                    <a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', $msg['telefone'] ?? ''); ?>?text=Olá, <?php echo $msg['nome']; ?>! Recebemos sua mensagem sobre: <?php echo urlencode($msg['assunto']); ?>"
                                        target="_blank" class="btn btn-success">
                                        <i class="fab fa-whatsapp"></i> WhatsApp
                                    </a>

                                    <form method="POST" action="delete-message.php" style="display: inline;"
                                        onsubmit="return confirm('Tem certeza que deseja excluir esta mensagem? Esta ação não pode ser desfeita.')">
                                        <input type="hidden" name="message_id" value="<?php echo $msg['id']; ?>">
                                        <button type="submit" class="btn btn-danger">
                                            <i class="fas fa-trash"></i>
                                            Excluir
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Função para atualizar status da mensagem
        function updateStatus(messageId, newStatus) {
            fetch('admin-mensagens.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=update_status&message_id=${messageId}&status=${newStatus}`
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Atualizar a UI
                        const messageItem = document.querySelector(`[data-search*="${messageId.slice(-8)}"]`);
                        if (messageItem) {
                            messageItem.setAttribute('data-status', newStatus);
                        }

                        // Recarregar a página para atualizar os stats
                        setTimeout(() => {
                            location.reload();
                        }, 500);
                    } else {
                        alert('Erro ao atualizar status da mensagem.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Erro ao atualizar status da mensagem.');
                });
        }

        // Busca em tempo real
        document.getElementById('searchBox').addEventListener('input', function () {
            const searchTerm = this.value.toLowerCase();
            const messages = document.querySelectorAll('.message-item');

            messages.forEach(message => {
                const searchData = message.getAttribute('data-search');
                if (searchData.includes(searchTerm)) {
                    message.style.display = 'block';
                } else {
                    message.style.display = 'none';
                }
            });
        });
    </script>
</body>

</html>
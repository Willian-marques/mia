<?php
// fix-admin-errors.php - Script para corrigir erros do admin

echo "Iniciando correções do admin...\n\n";

// 1. Criar arquivo .htaccess se não existir
$htaccess_content = '# Admin MIA - Configuração
RewriteEngine On

# Redirecionar admin para admin.php
RewriteRule ^admin/?$ admin.php [L]

# Redirecionar admin-mensagens para admin-mensagens.php  
RewriteRule ^admin-mensagens/?$ admin-mensagens.php [L]

# Permitir acesso direto aos arquivos PHP
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d

# Configurações de segurança
<Files "*.json">
    Order Allow,Deny
    Allow from 127.0.0.1
    Allow from localhost
</Files>

<Files "php-error.log">
    Order Allow,Deny
    Deny from all
</Files>
';

if (!file_exists('.htaccess')) {
    file_put_contents('.htaccess', $htaccess_content);
    echo "✅ Arquivo .htaccess criado\n";
} else {
    echo "ℹ️ .htaccess já existe\n";
}

// 2. Verificar e criar diretórios necessários
$diretorios = ['data', 'uploads', 'config'];
foreach ($diretorios as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
        echo "✅ Diretório $dir criado\n";
    } else {
        echo "ℹ️ Diretório $dir já existe\n";
    }
}

// 3. Verificar arquivos JSON
$arquivos_json = [
    'data/mensagens.json' => '[]',
    'data/produto-destaque.json' => '{"ativo": false}',
    'data/avaliacoes.json' => '[]'
];

foreach ($arquivos_json as $arquivo => $conteudo_padrao) {
    if (!file_exists($arquivo)) {
        file_put_contents($arquivo, $conteudo_padrao);
        echo "✅ Arquivo $arquivo criado\n";
    } else {
        // Verificar se o JSON é válido
        $conteudo = file_get_contents($arquivo);
        $dados = json_decode($conteudo, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            file_put_contents($arquivo, $conteudo_padrao);
            echo "🔧 Arquivo $arquivo corrigido (JSON inválido)\n";
        } else {
            echo "ℹ️ Arquivo $arquivo válido\n";
        }
    }
}

// 4. Verificar produtos.json
if (!file_exists('data/produtos.json')) {
    $produtos_padrao = [
        "1" => [
            "id" => 1,
            "slug" => "produto-exemplo",
            "title" => "Produto de Exemplo",
            "category" => "acessorios",
            "price" => 99.00,
            "oldPrice" => null,
            "discount" => null,
            "images" => ["img/default-product.png"],
            "colors" => [
                ["name" => "marrom", "color" => "#92400E", "title" => "Marrom"]
            ],
            "sizes" => ["Único"],
            "description" => "Produto de exemplo para teste do sistema",
            "specifications" => "Material: 100% Couro Legítimo Premium",
            "status" => "ativo",
            "stock" => 10,
            "sales" => 0,
            "isFeatured" => false,
            "isBestseller" => false,
            "created_at" => date('Y-m-d'),
            "updated_at" => date('Y-m-d H:i:s')
        ]
    ];
    file_put_contents('data/produtos.json', json_encode($produtos_padrao, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    echo "✅ Arquivo produtos.json criado com produto de exemplo\n";
}

// 5. Teste de permissões
foreach ($diretorios as $dir) {
    if (!is_writable($dir)) {
        echo "⚠️ ATENÇÃO: Diretório $dir não tem permissão de escrita\n";
    }
}

// 6. Criar arquivo de configuração adicional para URLs
$config_urls = '<?php
// Configurações de URLs para o admin
define("BASE_URL", "http://localhost/VOUEXPLODIR/");
define("ADMIN_BASE", BASE_URL . "admin.php");
define("ADMIN_MENSAGENS", BASE_URL . "admin-mensagens.php");
define("ADMIN_ACTIONS", BASE_URL . "admin_actions.php");
define("API_URL", BASE_URL . "api.php");

// Função para resolver URLs
function resolve_admin_url($page, $params = []) {
    $base_urls = [
        "admin" => "admin.php",
        "mensagens" => "admin-mensagens.php", 
        "actions" => "admin_actions.php",
        "api" => "api.php"
    ];
    
    $url = $base_urls[$page] ?? $page;
    
    if (!empty($params)) {
        $url .= "?" . http_build_query($params);
    }
    
    return $url;
}
?>';

file_put_contents('config/urls.php', $config_urls);
echo "✅ Arquivo de configuração de URLs criado\n";

echo "\n🎉 Correções concluídas!\n";
echo "\nPróximos passos:\n";
echo "1. Acesse http://localhost/VOUEXPLODIR/admin.php\n";
echo "2. Use login: admin / senha: mia2025\n";
echo "3. Teste a navegação entre as páginas\n";
echo "4. Se ainda houver erros, verifique o arquivo php-error.log\n";

?>
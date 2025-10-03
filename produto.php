<?php header("Location: produto-unico.php?" . ($_SERVER["QUERY_STRING"] ? $_SERVER["QUERY_STRING"] : ""), true, 301);
exit(); ?>
<?php
// 1. Inicia a sessão para poder manipulá-la
session_start(); 

// 2. Limpa todas as variáveis de sessão
// Este passo é crucial para remover o 'id_usuario'
$_SESSION = array();

// 3. Remove o cookie de sessão do navegador (Melhor prática de segurança)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 4. Destrói a sessão no servidor
session_destroy();

// 5. Redireciona para a página de login
header("Location: login.php"); 
exit;
?>
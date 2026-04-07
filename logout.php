<?php
// logout.php — CORRIGIDO: carrega config.php primeiro
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/conexao.php';

if (!empty($_SESSION['usr_id'])) {
    registraLog($_SESSION['usr_id'], 'LOGOUT', 'Logout realizado', 'tbl_usuarios', $_SESSION['usr_id']);
}

// Destrói sessão corretamente
$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $p = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
}
session_destroy();

header('Location: /crmv/login.php');
exit;

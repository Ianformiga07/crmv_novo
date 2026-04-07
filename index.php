<?php
// index.php — CORRIGIDO: carrega config.php primeiro para ter SESSION_NAME
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/conexao.php';

if (!empty($_SESSION['usr_id'])) {
    $perfil = (int)($_SESSION['usr_perfil'] ?? 0);
    header('Location: ' . ($perfil === 1 ? '/crmv/admin/dashboard.php' : '/crmv/aluno/dashboard.php'));
} else {
    header('Location: /crmv/login.php');
}
exit;

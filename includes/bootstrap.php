<?php
/**
 * bootstrap.php — Ponto único de inicialização
 *
 * Inclua este arquivo no topo de TODAS as páginas:
 *   require_once __DIR__ . '/../../includes/bootstrap.php';
 *
 * Garante que config, banco, auth e helpers estejam disponíveis.
 */

$_base = __DIR__;

require_once $_base . '/config.php';
require_once $_base . '/Database.php';
require_once $_base . '/Auth.php';
require_once $_base . '/helpers.php';

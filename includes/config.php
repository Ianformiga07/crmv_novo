<?php
/**
 * config.php — Configurações globais do sistema CRMV/TO
 * Única fonte de verdade para constantes e parâmetros do ambiente.
 */

// ── Banco de dados ───────────────────────────────────────────
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'crmv_cursos');
define('DB_PORT', '3306');

// ── Segurança ────────────────────────────────────────────────
define('HASH_KEY',      'CRMVTO2025KEY');
define('SESSION_NAME',  'crmv_sess');
define('CSRF_TOKEN_KEY','_csrf');

// ── Caminhos ─────────────────────────────────────────────────
define('BASE_PATH', dirname(__DIR__));          // raiz do projeto
define('BASE_URL',  '/crmv');                   // prefixo da URL

define('UPLOAD_PATH', BASE_PATH . '/uploads');
define('UPLOAD_URL',  BASE_URL  . '/uploads');

// ── Limites de upload ─────────────────────────────────────────
define('MAX_UPLOAD_MB', 10);

// ── Perfis de usuário ─────────────────────────────────────────
define('PERFIL_ADMIN', 1);
define('PERFIL_ALUNO', 2);

// ── Ambiente ─────────────────────────────────────────────────
define('DEBUG_MODE', false); // true em desenvolvimento

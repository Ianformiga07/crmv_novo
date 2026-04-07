<?php
/**
 * config.php — Configurações globais do sistema CRMV/TO (CORRIGIDO)
 *
 * IMPORTANTE: Este arquivo DEVE ser o primeiro a ser carregado.
 * Todas as constantes são protegidas com defined() para evitar
 * erros se o arquivo for incluído mais de uma vez.
 */

// ── Banco de dados ────────────────────────────────────────────
defined('DB_HOST') || define('DB_HOST', 'localhost');
defined('DB_USER') || define('DB_USER', 'root');
defined('DB_PASS') || define('DB_PASS', '');
defined('DB_NAME') || define('DB_NAME', 'crmv_cursos');
defined('DB_PORT') || define('DB_PORT', '3306');

// ── Segurança ─────────────────────────────────────────────────
defined('HASH_KEY')       || define('HASH_KEY',       'CRMVTO2025KEY');
defined('SESSION_NAME')   || define('SESSION_NAME',   'crmv_sess');
defined('CSRF_TOKEN_KEY') || define('CSRF_TOKEN_KEY', '_csrf');

// ── Caminhos ──────────────────────────────────────────────────
defined('BASE_PATH')   || define('BASE_PATH',   dirname(__DIR__));
defined('BASE_URL')    || define('BASE_URL',    '/crmv');
defined('UPLOAD_PATH') || define('UPLOAD_PATH', BASE_PATH . '/uploads');
defined('UPLOAD_URL')  || define('UPLOAD_URL',  BASE_URL  . '/uploads');

// ── Limites ───────────────────────────────────────────────────
defined('MAX_UPLOAD_MB') || define('MAX_UPLOAD_MB', 10);

// ── Perfis de usuário ─────────────────────────────────────────
defined('PERFIL_ADMIN') || define('PERFIL_ADMIN', 1);
defined('PERFIL_ALUNO') || define('PERFIL_ALUNO', 2);

// ── Modo debug ────────────────────────────────────────────────
defined('DEBUG_MODE') || define('DEBUG_MODE', false);

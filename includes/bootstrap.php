<?php
/**
 * bootstrap.php — Ponto único de inicialização (CORRIGIDO)
 *
 * Usado por: admin/dashboard.php, admin/cursos/*, aluno/dashboard.php, aluno/curso.php
 *
 * CORREÇÃO 1: config.php carregado PRIMEIRO (define SESSION_NAME)
 * CORREÇÃO 2: NÃO inclui conexao.php para evitar funções duplicadas.
 *             Database.php fornece acesso ao banco. helpers.php fornece utilidades.
 */

require_once __DIR__ . '/config.php';    // 1º — SESSION_NAME, BASE_URL, constantes
require_once __DIR__ . '/Database.php';  // 2º — classe Database (PDO)
require_once __DIR__ . '/Auth.php';      // 3º — Auth::startSession() usa SESSION_NAME
require_once __DIR__ . '/helpers.php';   // 4º — funções utilitárias (protegidas por function_exists)

// Garante aliases de BD compatíveis com código legado
// sem carregar conexao.php inteiro (evita redeclaração)
if (!function_exists('dbQuery')) {
    function dbQuery(string $sql, array $p = []): array        { return Database::getInstance()->fetchAll($sql, $p); }
    function dbQueryOne(string $sql, array $p = []): array|false { return Database::getInstance()->fetchOne($sql, $p); }
    function dbExecute(string $sql, array $p = []): int        { return Database::getInstance()->execute($sql, $p); }
    function dbLastId(): int                                   { return Database::getInstance()->lastInsertId(); }
}

// Aliases de verificação de senha (usados por login.php via conexao.php,
// mas algumas páginas admin podem precisar — declarar aqui com guard)
if (!function_exists('hashSenha') && defined('HASH_KEY')) {
    function hashSenha(string $senha): string {
        return password_hash($senha . HASH_KEY, PASSWORD_BCRYPT, ['cost' => 12]);
    }
    function verificaSenha(string $senha, string $hash): bool {
        return password_verify($senha . HASH_KEY, $hash);
    }
}

if (!function_exists('geraCodigoCert')) {
    function geraCodigoCert(): string {
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $c = '';
        for ($i = 0; $i < 4; $i++) $c .= $chars[random_int(0, strlen($chars)-1)];
        $c .= '-';
        for ($i = 0; $i < 4; $i++) $c .= $chars[random_int(0, strlen($chars)-1)];
        $c .= '-';
        for ($i = 0; $i < 4; $i++) $c .= $chars[random_int(0, strlen($chars)-1)];
        return $c;
    }
}

if (!function_exists('registraLog')) {
    function registraLog(int|null $usr_id, string $acao, string $descricao = '', string $tabela = '', int|null $reg_id = null): void {
        try {
            Database::getInstance()->execute(
                "INSERT INTO tbl_log_atividades (usuario_id, acao, descricao, tabela_ref, registro_id, ip_address, user_agent) VALUES (?,?,?,?,?,?,?)",
                [$usr_id, $acao, $descricao, $tabela, $reg_id, $_SERVER['REMOTE_ADDR'] ?? '', mb_substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 200)]
            );
        } catch (Exception) {}
    }
}

if (!function_exists('getConfig')) {
    function getConfig(string $chave): string {
        $row = Database::getInstance()->fetchOne("SELECT valor FROM tbl_configuracoes WHERE chave = ?", [$chave]);
        return $row ? (string)$row['valor'] : '';
    }
}

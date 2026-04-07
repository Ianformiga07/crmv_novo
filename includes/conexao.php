<?php
// ============================================================
//  CRMV/TO — includes/conexao.php  (VERSÃO CORRIGIDA)
//  CORREÇÃO: Sessão usa o mesmo nome de Auth.php (SESSION_NAME)
//  para evitar o ERR_TOO_MANY_REDIRECTS.
// ============================================================

// Garante que config.php seja carregado (define SESSION_NAME, DB_*, etc.)
if (!defined('DB_NAME')) {
    require_once __DIR__ . '/config.php';
}
if (!defined('HASH_KEY')) define('HASH_KEY', 'CRMVTO2025KEY');

// ── Conexão PDO ─────────────────────────────────────────────
function getConn(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            die('<div style="font-family:monospace;background:#fee2e2;color:#7f1d1d;padding:20px;margin:20px;border-radius:8px">
                <strong>Erro de conexão:</strong><br>' . htmlspecialchars($e->getMessage()) . '
                <br><br>Verifique se o XAMPP está rodando e o banco <strong>' . DB_NAME . '</strong> existe.
            </div>');
        }
    }
    return $pdo;
}

function dbQuery(string $sql, array $params = []): array {
    $stmt = getConn()->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function dbQueryOne(string $sql, array $params = []): array|false {
    $stmt = getConn()->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetch();
}

function dbExecute(string $sql, array $params = []): int {
    $stmt = getConn()->prepare($sql);
    $stmt->execute($params);
    return $stmt->rowCount();
}

function dbLastId(): string {
    return getConn()->lastInsertId();
}

// ── Utilitários ─────────────────────────────────────────────
function nvl(mixed $val, mixed $def = ''): mixed {
    return ($val === null || $val === '') ? $def : $val;
}
function truncaTexto(string $str, int $tam): string {
    return mb_strlen($str) > $tam ? mb_substr($str, 0, $tam) . '...' : $str;
}
function trunca(string $str, int $tam): string {
    return mb_strlen($str) > $tam ? mb_substr($str, 0, $tam) . '…' : $str;
}
function primeiraLetra(string $nome): string {
    return mb_strtoupper(mb_substr(trim($nome), 0, 1));
}
function fmtData(?string $d): string {
    if (!$d || $d === '0000-00-00') return '-';
    try { return (new DateTime($d))->format('d/m/Y'); }
    catch (Exception) { return '-'; }
}
function fmtDataHora(?string $d): string {
    if (!$d) return '-';
    try { return (new DateTime($d))->format('d/m/Y H:i'); }
    catch (Exception) { return '-'; }
}
function fmtCRMV(?string $numero, string $uf = 'TO'): string {
    return $numero ? trim($numero) . '-' . $uf : 'Não informado';
}
function fmtMoeda(float|string $valor): string {
    return 'R$ ' . number_format((float)$valor, 2, ',', '.');
}
function fmtCarga(float|string $horas): string {
    $h = (int)$horas;
    $min = round(((float)$horas - $h) * 60);
    return $min > 0 ? "{$h}h{$min}" : "{$h}h";
}
function fmtTamanho(int $bytes): string {
    if ($bytes >= 1_048_576) return round($bytes / 1_048_576, 1) . ' MB';
    if ($bytes >= 1_024)     return round($bytes / 1_024) . ' KB';
    return $bytes . ' B';
}
function e(mixed $str): string {
    return htmlspecialchars((string)$str, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

// ── Hash de senha ────────────────────────────────────────────
function hashSenha(string $senha): string {
    return password_hash($senha . HASH_KEY, PASSWORD_BCRYPT, ['cost' => 12]);
}
function verificaSenha(string $senha, string $hash): bool {
    return password_verify($senha . HASH_KEY, $hash);
}
function geraSalt(): string { return bin2hex(random_bytes(16)); }

// ── Certificado ─────────────────────────────────────────────
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

// ── Log / Configuração ──────────────────────────────────────
function registraLog(int|null $usr_id, string $acao, string $descricao = '', string $tabela = '', int|null $reg_id = null): void {
    try {
        dbExecute(
            "INSERT INTO tbl_log_atividades (usuario_id, acao, descricao, tabela_ref, registro_id, ip_address, user_agent) VALUES (?,?,?,?,?,?,?)",
            [$usr_id, $acao, $descricao, $tabela, $reg_id, $_SERVER['REMOTE_ADDR'] ?? '', mb_substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 200)]
        );
    } catch (Exception) {}
}

function getConfig(string $chave): string {
    $row = dbQueryOne("SELECT valor FROM tbl_configuracoes WHERE chave = ?", [$chave]);
    return $row ? (string)$row['valor'] : '';
}

// ── Flash messages ──────────────────────────────────────────
function flash(string $msg, string $tipo = 'sucesso'): void {
    if (session_status() !== PHP_SESSION_ACTIVE) return;
    $_SESSION['flash_msg']  = $msg;
    $_SESSION['flash_tipo'] = $tipo;
}
function getFlash(): array {
    $msg  = $_SESSION['flash_msg']  ?? '';
    $tipo = $_SESSION['flash_tipo'] ?? 'sucesso';
    unset($_SESSION['flash_msg'], $_SESSION['flash_tipo']);
    return ['msg' => $msg, 'tipo' => $tipo];
}

// ── Badges ──────────────────────────────────────────────────
function badgeModalidade(string $mod): string {
    return match(strtoupper(trim($mod))) {
        'EAD'        => '<span class="badge badge-azul"><i class="fa-solid fa-wifi"></i> EAD</span>',
        'HIBRIDO'    => '<span class="badge badge-ouro"><i class="fa-solid fa-layer-group"></i> Híbrido</span>',
        'PRESENCIAL' => '<span class="badge badge-cinza"><i class="fa-solid fa-map-marker-alt"></i> Presencial</span>',
        default      => '<span class="badge badge-cinza">' . e($mod) . '</span>',
    };
}
function badgeStatus(string $s): string {
    return match(strtoupper(trim($s))) {
        'PUBLICADO' => '<span class="badge badge-verde">Publicado</span>',
        'RASCUNHO'  => '<span class="badge badge-cinza">Rascunho</span>',
        'ENCERRADO' => '<span class="badge badge-ouro">Encerrado</span>',
        'CANCELADO','CANCELADA' => '<span class="badge badge-verm">Cancelado</span>',
        'ATIVA'     => '<span class="badge badge-azul">Ativa</span>',
        'CONCLUIDA' => '<span class="badge badge-verde">Concluída</span>',
        'REPROVADO' => '<span class="badge badge-verm">Reprovado</span>',
        default     => '<span class="badge badge-cinza">' . e($s) . '</span>',
    };
}
function badgeCurso(string $s): string    { return badgeStatus($s); }
function badgeMatricula(string $s): string { return badgeStatus($s); }

// ── Controle de acesso (compatibilidade) ────────────────────
function exigeLogin(): void {
    if (class_exists('Auth')) { Auth::require(); return; }
    if (empty($_SESSION['usr_id'])) {
        header('Location: /crmv/login.php?ret=' . urlencode($_SERVER['REQUEST_URI'] ?? ''));
        exit;
    }
}
function exigeAdmin(): void {
    if (class_exists('Auth')) { Auth::requireAdmin(); return; }
    if (empty($_SESSION['usr_id'])) {
        header('Location: /crmv/login.php?ret=' . urlencode($_SERVER['REQUEST_URI'] ?? ''));
        exit;
    }
    if ((int)($_SESSION['usr_perfil'] ?? 0) !== 1) {
        header('Location: /crmv/acesso-negado.php'); exit;
    }
}

// ============================================================
//  INICIA SESSÃO — CORREÇÃO CRÍTICA
//  Usa o mesmo SESSION_NAME de config.php que Auth.php usa.
//  Antes, conexao.php usava session_start() sem nome personalizado
//  (criava cookie PHPSESSID), enquanto Auth.php usava 'crmv_sess'.
//  Isso fazia login.php e aluno/dashboard.php enxergarem sessões
//  diferentes → loop infinito de redirecionamento.
// ============================================================
if (session_status() === PHP_SESSION_NONE) {
    $sessName = defined('SESSION_NAME') ? SESSION_NAME : 'crmv_sess';
    session_name($sessName);
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => false,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

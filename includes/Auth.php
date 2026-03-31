<?php
/**
 * Auth.php — Autenticação, sessão e controle de acesso
 */

require_once __DIR__ . '/config.php';

class Auth
{
    /** Inicializa a sessão com configurações seguras */
    public static function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_name(SESSION_NAME);
            session_set_cookie_params([
                'lifetime' => 0,
                'path'     => '/',
                'secure'   => false, // true em produção com HTTPS
                'httponly' => true,
                'samesite' => 'Lax',
            ]);
            session_start();
        }
    }

    /** Verifica se há usuário logado */
    public static function check(): bool
    {
        return !empty($_SESSION['usr_id']);
    }

    /** Retorna o perfil do usuário logado (1=admin, 2=aluno) */
    public static function perfil(): int
    {
        return (int) ($_SESSION['usr_perfil'] ?? 0);
    }

    /** Retorna o ID do usuário logado */
    public static function id(): int
    {
        return (int) ($_SESSION['usr_id'] ?? 0);
    }

    /** Retorna o nome do usuário logado */
    public static function nome(): string
    {
        return $_SESSION['usr_nome'] ?? '';
    }

    /** Verifica se é admin */
    public static function isAdmin(): bool
    {
        return self::perfil() === PERFIL_ADMIN;
    }

    /** Verifica se é aluno */
    public static function isAluno(): bool
    {
        return self::perfil() === PERFIL_ALUNO;
    }

    /**
     * Exige que o usuário esteja logado.
     * Redireciona para login se não estiver.
     */
    public static function require(): void
    {
        self::startSession();
        if (!self::check()) {
            $url = urlencode($_SERVER['REQUEST_URI'] ?? '');
            self::redirect(BASE_URL . '/login.php?redir=' . $url);
        }
    }

    /**
     * Exige perfil de administrador.
     */
    public static function requireAdmin(): void
    {
        self::require();
        if (!self::isAdmin()) {
            self::redirect(BASE_URL . '/acesso-negado.php');
        }
    }

    /**
     * Exige perfil de aluno.
     */
    public static function requireAluno(): void
    {
        self::require();
        if (self::isAdmin()) {
            self::redirect(BASE_URL . '/admin/dashboard.php');
        }
    }

    /** Faz login: grava dados na sessão */
    public static function login(array $usuario): void
    {
        session_regenerate_id(true);
        $_SESSION['usr_id']     = $usuario['usuario_id'];
        $_SESSION['usr_nome']   = $usuario['nome_completo'];
        $_SESSION['usr_perfil'] = $usuario['perfil_id'];
        $_SESSION['usr_email']  = $usuario['email'];
    }

    /** Faz logout: destrói a sessão */
    public static function logout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
    }

    /** Gera token CSRF e guarda na sessão */
    public static function csrfToken(): string
    {
        self::startSession();
        if (empty($_SESSION[CSRF_TOKEN_KEY])) {
            $_SESSION[CSRF_TOKEN_KEY] = bin2hex(random_bytes(32));
        }
        return $_SESSION[CSRF_TOKEN_KEY];
    }

    /** Verifica token CSRF vindo do POST */
    public static function verifyCsrf(): void
    {
        $token = $_POST[CSRF_TOKEN_KEY] ?? '';
        if (empty($token) || !hash_equals($_SESSION[CSRF_TOKEN_KEY] ?? '', $token)) {
            http_response_code(403);
            die('Token CSRF inválido.');
        }
    }

    /** Redireciona e encerra execução */
    public static function redirect(string $url): never
    {
        header('Location: ' . $url);
        exit;
    }
}

// ─── Atalhos globais (compatibilidade) ─────────────────────
Auth::startSession();

function exigeAdmin(): void  { Auth::requireAdmin(); }
function exigeLogin(): void  { Auth::require(); }

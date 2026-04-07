<?php
// ============================================================
//  login.php — VERSÃO CORRIGIDA
//  CORREÇÃO: carrega config.php ANTES de qualquer outro include
//  para garantir que SESSION_NAME esteja definido antes do
//  session_start() em conexao.php.
// ============================================================
require_once __DIR__ . '/includes/config.php';   // ← PRIMEIRO (define SESSION_NAME)
require_once __DIR__ . '/includes/conexao.php';  // ← usa SESSION_NAME corretamente

// Redireciona se já logado — baseado no perfil
if (!empty($_SESSION['usr_id'])) {
    $perfil = (int)($_SESSION['usr_perfil'] ?? 0);
    if ($perfil === 1) {
        header('Location: /crmv/admin/dashboard.php');
    } else {
        header('Location: /crmv/aluno/dashboard.php');
    }
    exit;
}

$sErro  = '';
$sEmail = '';
$sRet   = $_GET['ret'] ?? '';

// ── POST: processar login ────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sEmail = strtolower(trim($_POST['email'] ?? ''));
    $sSenha = trim($_POST['senha'] ?? '');

    if ($sEmail === '' || $sSenha === '') {
        $sErro = 'Preencha o e-mail e a senha.';
    } else {
        $usuario = dbQueryOne(
            "SELECT usuario_id, nome_completo, perfil_id, senha_hash, ativo
             FROM tbl_usuarios
             WHERE email = ? AND ativo = 1",
            [$sEmail]
        );

        if (!$usuario) {
            $sErro = 'E-mail ou senha incorretos.';
        } else {
            // Tenta verificar com senha_hash novo (bcrypt)
            $ok = false;
            if (!empty($usuario['senha_hash'])) {
                $ok = verificaSenha($sSenha, $usuario['senha_hash']);
            }

            if ($ok) {
                // ── LOGIN OK ──────────────────────────────────
                session_regenerate_id(true);
                $_SESSION['usr_id']     = $usuario['usuario_id'];
                $_SESSION['usr_nome']   = $usuario['nome_completo'];
                $_SESSION['usr_perfil'] = $usuario['perfil_id'];
                $_SESSION['usr_email']  = $sEmail;

                // Atualiza último acesso (sem travar se coluna não existir)
                try {
                    dbExecute(
                        "UPDATE tbl_usuarios SET ultimo_acesso = NOW(), tentativas_login = 0 WHERE usuario_id = ?",
                        [$usuario['usuario_id']]
                    );
                } catch (Exception) {}

                registraLog($usuario['usuario_id'], 'LOGIN', 'Login realizado', 'tbl_usuarios', $usuario['usuario_id']);

                // Redireciona pelo perfil
                $perfil = (int)$usuario['perfil_id'];
                if ($perfil === 1) {
                    $destino = (!empty($sRet) && str_starts_with($sRet, '/crmv/admin'))
                        ? $sRet : '/crmv/admin/dashboard.php';
                } else {
                    $destino = '/crmv/aluno/dashboard.php';
                }
                header('Location: ' . $destino);
                exit;

            } else {
                $sErro = 'E-mail ou senha incorretos.';
                try {
                    $iTent = (int)(dbQueryOne("SELECT tentativas_login FROM tbl_usuarios WHERE usuario_id=?", [$usuario['usuario_id']])['tentativas_login'] ?? 0) + 1;
                    dbExecute("UPDATE tbl_usuarios SET tentativas_login = ? WHERE usuario_id = ?", [$iTent, $usuario['usuario_id']]);
                } catch (Exception) {}
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login — CRMV/TO Educação Continuada</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
:root {
    --azul: #0d2137;
    --azul2: #15385c;
    --verde: #16a34a;
    --ouro: #c9a227;
    --c200: #e2e8f0;
    --c400: #94a3b8;
    --c600: #475569;
    --c700: #334155;
    --c900: #0f172a;
    --radius: 10px;
    --shadow: 0 10px 40px rgba(0,0,0,.15);
}
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
body {
    font-family: 'Inter', sans-serif;
    background: linear-gradient(135deg, var(--azul) 0%, var(--azul2) 60%, #1a4a2e 100%);
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
}
.login-card {
    background: #fff;
    border-radius: 18px;
    box-shadow: var(--shadow);
    width: 100%;
    max-width: 400px;
    overflow: hidden;
}
.login-header {
    background: linear-gradient(135deg, var(--azul) 0%, var(--azul2) 100%);
    padding: 32px 32px 28px;
    text-align: center;
    color: #fff;
}
.login-logo {
    width: 56px; height: 56px;
    border-radius: 14px;
    background: linear-gradient(135deg, var(--verde), var(--azul2));
    display: flex; align-items: center; justify-content: center;
    font-size: 1.4rem;
    margin: 0 auto 14px;
    box-shadow: 0 4px 14px rgba(22,163,74,.4);
}
.login-titulo {
    font-family: 'Playfair Display', serif;
    font-size: 1.3rem; font-weight: 700;
    margin-bottom: 4px;
}
.login-sub {
    font-size: .78rem;
    color: rgba(255,255,255,.6);
    text-transform: uppercase;
    letter-spacing: .08em;
}
.login-body { padding: 28px 32px 32px; }
.form-group { margin-bottom: 16px; }
.form-label {
    display: block;
    font-size: .8rem; font-weight: 600;
    color: var(--c700);
    margin-bottom: 6px;
}
.input-wrap { position: relative; }
.input-icon {
    position: absolute; left: 12px; top: 50%; transform: translateY(-50%);
    color: var(--c400); font-size: .85rem;
}
.form-control {
    width: 100%;
    padding: 10px 12px 10px 36px;
    font-size: .88rem;
    border: 1.5px solid var(--c200);
    border-radius: var(--radius);
    color: var(--c900);
    outline: none;
    transition: border-color .18s, box-shadow .18s;
    font-family: inherit;
}
.form-control:focus {
    border-color: var(--azul2);
    box-shadow: 0 0 0 3px rgba(21,56,92,.1);
}
.btn-login {
    width: 100%;
    padding: 11px;
    background: linear-gradient(135deg, var(--azul) 0%, var(--azul2) 100%);
    color: #fff;
    border: none; border-radius: var(--radius);
    font-size: .92rem; font-weight: 600;
    font-family: inherit;
    cursor: pointer;
    transition: opacity .18s, transform .18s;
    display: flex; align-items: center; justify-content: center; gap: 8px;
    margin-top: 8px;
}
.btn-login:hover { opacity: .92; transform: translateY(-1px); }
.btn-login:active { transform: translateY(0); }
.erro-box {
    background: #fee2e2;
    border: 1px solid #fca5a5;
    border-radius: var(--radius);
    padding: 10px 14px;
    font-size: .82rem;
    color: #991b1b;
    margin-bottom: 16px;
    display: flex; align-items: center; gap: 8px;
}
.login-footer {
    text-align: center;
    padding: 0 32px 24px;
    font-size: .74rem;
    color: var(--c400);
}
</style>
</head>
<body>

<div class="login-card">
    <div class="login-header">
        <div class="login-logo"><i class="fa-solid fa-paw"></i></div>
        <div class="login-titulo">CRMV/TO</div>
        <div class="login-sub">Educação Continuada</div>
    </div>

    <div class="login-body">
        <?php if ($sErro): ?>
        <div class="erro-box">
            <i class="fa-solid fa-circle-xmark"></i>
            <?= htmlspecialchars($sErro) ?>
        </div>
        <?php endif; ?>

        <form method="POST" autocomplete="on">
            <div class="form-group">
                <label class="form-label" for="email">E-mail</label>
                <div class="input-wrap">
                    <i class="fa-solid fa-envelope input-icon"></i>
                    <input type="email" id="email" name="email" class="form-control"
                           value="<?= htmlspecialchars($sEmail) ?>"
                           placeholder="seu@email.com.br"
                           autocomplete="email" required autofocus>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="senha">Senha</label>
                <div class="input-wrap">
                    <i class="fa-solid fa-lock input-icon"></i>
                    <input type="password" id="senha" name="senha" class="form-control"
                           placeholder="••••••••"
                           autocomplete="current-password" required>
                </div>
            </div>

            <?php if ($sRet): ?>
            <input type="hidden" name="ret" value="<?= htmlspecialchars($sRet) ?>">
            <?php endif; ?>

            <button type="submit" class="btn-login">
                <i class="fa-solid fa-right-to-bracket"></i> Entrar
            </button>
        </form>
    </div>

    <div class="login-footer">
        CRMV/TO — Sistema de Educação Continuada<br>
        Problemas? Entre em contato com a administração.
    </div>
</div>

</body>
</html>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($pageTitulo ?? 'Área do Aluno') ?> — CRMV/TO</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/sistema.css">
<?php if (!empty($extraCss)): ?><style><?= $extraCss ?></style><?php endif; ?>
</head>
<body class="layout-aluno">

<!-- ══════════════════ TOPBAR ALUNO ══════════════════ -->
<header class="aluno-topbar">
    <a href="<?= BASE_URL ?>/aluno/dashboard.php" class="aluno-brand">
        <div class="aluno-brand-logo"><i class="fa-solid fa-paw"></i></div>
        <div>
            <div class="aluno-brand-nome">CRMV/TO</div>
            <div class="aluno-brand-sub">Educação Continuada</div>
        </div>
    </a>

    <nav class="aluno-nav" id="alunoNav">
        <a href="<?= BASE_URL ?>/aluno/dashboard.php"
           class="aluno-nav-item <?= ($paginaAtiva??'')==='dashboard'?'ativo':'' ?>">
            <i class="fa-solid fa-house"></i> Início
        </a>
        <a href="<?= BASE_URL ?>/aluno/meus-cursos.php"
           class="aluno-nav-item <?= ($paginaAtiva??'')==='cursos'?'ativo':'' ?>">
            <i class="fa-solid fa-graduation-cap"></i> Meus Cursos
        </a>
        <a href="<?= BASE_URL ?>/aluno/certificados.php"
           class="aluno-nav-item <?= ($paginaAtiva??'')==='certificados'?'ativo':'' ?>">
            <i class="fa-solid fa-certificate"></i> Certificados
        </a>
    </nav>

    <div class="aluno-topbar-right">
        <div class="aluno-user-badge">
            <div class="aluno-user-avatar"><?= primeiraLetra(Auth::nome()) ?></div>
            <span class="aluno-user-nome d-hide-sm"><?= e(trunca(Auth::nome(), 18)) ?></span>
        </div>
        <a href="<?= BASE_URL ?>/logout.php" class="aluno-logout" title="Sair">
            <i class="fa-solid fa-right-from-bracket"></i>
        </a>
        <button class="aluno-menu-toggle" id="alunoMenuBtn" aria-label="Menu">
            <i class="fa-solid fa-bars"></i>
        </button>
    </div>
</header>

<!-- ══════════════════ CONTEÚDO ══════════════════ -->
<main class="aluno-main">
    <div class="aluno-container">
        <?php renderFlash(); ?>

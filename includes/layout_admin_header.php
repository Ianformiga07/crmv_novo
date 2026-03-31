<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($pageTitulo ?? 'Painel') ?> — CRMV/TO</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/sistema.css">
<?php if (!empty($extraCss)): ?>
<style><?= $extraCss ?></style>
<?php endif; ?>
</head>
<body class="layout-admin">

<!-- ══════════════════ SIDEBAR ══════════════════ -->
<aside class="sidebar" id="sidebar">
    <a href="<?= BASE_URL ?>/admin/dashboard.php" class="sb-brand">
        <div class="sb-brand-logo">
            <i class="fa-solid fa-paw"></i>
        </div>
        <div>
            <div class="sb-brand-nome">CRMV/TO</div>
            <div class="sb-brand-sub">Educação Continuada</div>
        </div>
    </a>

    <nav class="sb-nav">
        <div class="sb-group">
            <span class="sb-group-label">Principal</span>
            <a href="<?= BASE_URL ?>/admin/dashboard.php"
               class="sb-item <?= ($paginaAtiva??'')==='dashboard' ? 'ativo' : '' ?>">
                <i class="fa-solid fa-chart-line"></i>
                <span>Dashboard</span>
            </a>
        </div>

        <div class="sb-group">
            <span class="sb-group-label">Cursos</span>
            <a href="<?= BASE_URL ?>/admin/cursos/lista.php?modalidade=EAD"
               class="sb-item <?= ($paginaAtiva??'')==='cursos_ead' ? 'ativo' : '' ?>">
                <i class="fa-solid fa-wifi"></i>
                <span>Cursos EAD</span>
            </a>
            <a href="<?= BASE_URL ?>/admin/cursos/lista.php?modalidade=PRESENCIAL"
               class="sb-item <?= ($paginaAtiva??'')==='cursos_pres' ? 'ativo' : '' ?>">
                <i class="fa-solid fa-map-pin"></i>
                <span>Cursos Presenciais</span>
            </a>
            <a href="<?= BASE_URL ?>/admin/cursos/form.php"
               class="sb-item <?= ($paginaAtiva??'')==='curso_novo' ? 'ativo' : '' ?>">
                <i class="fa-solid fa-plus-circle"></i>
                <span>Novo Curso</span>
            </a>
        </div>

        <div class="sb-group">
            <span class="sb-group-label">Gestão</span>
            <a href="<?= BASE_URL ?>/admin/usuarios/lista.php"
               class="sb-item <?= ($paginaAtiva??'')==='usuarios' ? 'ativo' : '' ?>">
                <i class="fa-solid fa-user-doctor"></i>
                <span>Veterinários</span>
            </a>
            <a href="<?= BASE_URL ?>/admin/matriculas/lista.php"
               class="sb-item <?= ($paginaAtiva??'')==='matriculas' ? 'ativo' : '' ?>">
                <i class="fa-solid fa-list-check"></i>
                <span>Matrículas</span>
            </a>
            <a href="<?= BASE_URL ?>/admin/certificados/lista.php"
               class="sb-item <?= ($paginaAtiva??'')==='certificados' ? 'ativo' : '' ?>">
                <i class="fa-solid fa-certificate"></i>
                <span>Certificados</span>
            </a>
            <a href="<?= BASE_URL ?>/admin/avaliacoes/lista.php"
               class="sb-item <?= ($paginaAtiva??'')==='avaliacoes' ? 'ativo' : '' ?>">
                <i class="fa-solid fa-clipboard-check"></i>
                <span>Avaliações</span>
            </a>
        </div>

        <div class="sb-group">
            <span class="sb-group-label">Sistema</span>
            <a href="<?= BASE_URL ?>/admin/relatorios/index.php"
               class="sb-item <?= ($paginaAtiva??'')==='relatorios' ? 'ativo' : '' ?>">
                <i class="fa-solid fa-chart-bar"></i>
                <span>Relatórios</span>
            </a>
            <a href="<?= BASE_URL ?>/admin/configuracoes.php"
               class="sb-item <?= ($paginaAtiva??'')==='config' ? 'ativo' : '' ?>">
                <i class="fa-solid fa-gear"></i>
                <span>Configurações</span>
            </a>
        </div>
    </nav>

    <div class="sb-user-footer">
        <div class="sb-user-avatar"><?= primeiraLetra(Auth::nome()) ?></div>
        <div class="sb-user-info">
            <div class="sb-user-nome"><?= e(trunca(Auth::nome(), 20)) ?></div>
            <div class="sb-user-papel">Administrador</div>
        </div>
        <a href="<?= BASE_URL ?>/logout.php" class="sb-logout" title="Sair">
            <i class="fa-solid fa-right-from-bracket"></i>
        </a>
    </div>
</aside>

<!-- ══════════════════ CONTEÚDO PRINCIPAL ══════════════════ -->
<div class="main-wrapper">

    <!-- TOPBAR -->
    <header class="topbar">
        <button class="topbar-toggle" id="btnSidebar" aria-label="Menu">
            <i class="fa-solid fa-bars"></i>
        </button>

        <!-- Breadcrumb -->
        <nav class="topbar-breadcrumb" aria-label="Breadcrumb">
            <a href="<?= BASE_URL ?>/admin/dashboard.php">
                <i class="fa-solid fa-house"></i>
            </a>
            <?php if (!empty($breadcrumb)): ?>
                <?php foreach ($breadcrumb as $label => $url): ?>
                    <i class="fa-solid fa-chevron-right sep"></i>
                    <?php if ($url): ?>
                        <a href="<?= e($url) ?>"><?= e($label) ?></a>
                    <?php else: ?>
                        <span class="atual"><?= e($label) ?></span>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php elseif (($pageTitulo ?? '') !== 'Dashboard'): ?>
                <i class="fa-solid fa-chevron-right sep"></i>
                <span class="atual"><?= e($pageTitulo ?? '') ?></span>
            <?php endif; ?>
        </nav>

        <!-- Ações do topo -->
        <div class="topbar-actions">
            <?php if (!empty($topbarActions)): ?>
                <?= $topbarActions ?>
            <?php endif; ?>
        </div>
    </header>

    <!-- PÁGINA -->
    <main class="page-content">
        <?php renderFlash(); ?>

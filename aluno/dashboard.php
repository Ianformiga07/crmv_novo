<?php
/**
 * aluno/dashboard.php — Dashboard do Aluno (Veterinário)
 */
require_once __DIR__ . '/../includes/bootstrap.php';
Auth::requireAluno();

$db     = Database::getInstance();
$usrId  = Auth::id();

// Dados do usuário
$usuario = $db->fetchOne(
    "SELECT nome_completo, crmv_numero, crmv_uf, email FROM tbl_usuarios WHERE usuario_id=?",
    [$usrId]
);

// Totais rápidos
$totais = $db->fetchOne("
    SELECT
        COUNT(*) total,
        SUM(m.status='ATIVA')            AS em_andamento,
        SUM(m.status='CONCLUIDA')        AS concluidos,
        SUM(m.certificado_gerado=1)      AS certificados
    FROM tbl_matriculas m
    INNER JOIN tbl_cursos c ON m.curso_id = c.curso_id AND c.ativo = 1
    WHERE m.usuario_id = ?",
    [$usrId]
);

// Aba ativa
$aba = $_GET['aba'] ?? 'andamento';

$whereAba = match($aba) {
    'concluidos' => "AND m.status = 'CONCLUIDA'",
    'todos'      => '',
    default      => "AND m.status = 'ATIVA'",
};

// Matrículas
$matriculas = $db->fetchAll("
    SELECT m.matricula_id, m.status, m.matriculado_em, m.progresso_ead,
           m.certificado_gerado, m.certificado_codigo, m.nota_final,
           c.curso_id, c.titulo, c.tipo, c.modalidade,
           c.carga_horaria, c.data_inicio, c.data_fim, c.capa,
           c.link_ead, c.youtube_id, c.local_nome, c.local_cidade,
           cat.nome AS cat_nome, cat.cor_hex
    FROM tbl_matriculas m
    INNER JOIN tbl_cursos c ON m.curso_id = c.curso_id AND c.ativo = 1
    LEFT JOIN tbl_categorias cat ON c.categoria_id = cat.categoria_id
    WHERE m.usuario_id = ? $whereAba
    ORDER BY m.matriculado_em DESC",
    [$usrId]
);

$pageTitulo  = 'Início';
$paginaAtiva = 'dashboard';
require_once __DIR__ . '/../includes/layout_aluno_header.php';
?>

<!-- ══ Boas-vindas ══════════════════════════════════════════ -->
<div style="background:linear-gradient(120deg,var(--azul-900) 0%,var(--azul-700) 100%);
            border-radius:var(--radius-xl);padding:28px 32px;color:#fff;
            margin-bottom:24px;position:relative;overflow:hidden">
    <!-- Decoração -->
    <div style="position:absolute;right:-30px;top:-30px;width:160px;height:160px;
                background:rgba(201,162,39,.08);border-radius:50%"></div>
    <div style="position:absolute;right:60px;bottom:-40px;width:110px;height:110px;
                background:rgba(255,255,255,.04);border-radius:50%"></div>
    <div style="position:relative">
        <div style="font-size:.78rem;color:rgba(255,255,255,.55);text-transform:uppercase;
                    letter-spacing:.1em;margin-bottom:6px">Área do Aluno</div>
        <h1 style="font-family:var(--font-titulo);font-size:1.5rem;font-weight:700;margin-bottom:4px">
            Olá, <?= e(explode(' ', $usuario['nome_completo'])[0]) ?>! 👋
        </h1>
        <div style="font-size:.85rem;color:rgba(255,255,255,.7)">
            <?= fmtCRMV($usuario['crmv_numero'], $usuario['crmv_uf']) ?>
            · <?= e($usuario['email']) ?>
        </div>
    </div>
</div>

<!-- ══ Totais ════════════════════════════════════════════════ -->
<div class="stat-grid" style="margin-bottom:24px">
    <div class="stat-card" onclick="location.href='?aba=andamento'" style="cursor:pointer">
        <div class="stat-card-icon stat-icon-azul"><i class="fa-solid fa-play"></i></div>
        <div class="stat-card-valor"><?= (int)$totais['em_andamento'] ?></div>
        <div class="stat-card-label">Em andamento</div>
    </div>
    <div class="stat-card" onclick="location.href='?aba=concluidos'" style="cursor:pointer">
        <div class="stat-card-icon stat-icon-verde"><i class="fa-solid fa-check-circle"></i></div>
        <div class="stat-card-valor"><?= (int)$totais['concluidos'] ?></div>
        <div class="stat-card-label">Concluídos</div>
    </div>
    <div class="stat-card" onclick="location.href='<?= BASE_URL ?>/aluno/certificados.php'" style="cursor:pointer">
        <div class="stat-card-icon stat-icon-ouro"><i class="fa-solid fa-certificate"></i></div>
        <div class="stat-card-valor"><?= (int)$totais['certificados'] ?></div>
        <div class="stat-card-label">Certificados</div>
    </div>
</div>

<!-- ══ Abas de filtragem ══════════════════════════════════════ -->
<div class="tabs-bar mb-16">
    <a href="?aba=andamento" class="tab-btn <?= $aba==='andamento'?'ativo':'' ?>">
        <i class="fa-solid fa-play"></i> Em andamento
        <span class="badge badge-azul"><?= (int)$totais['em_andamento'] ?></span>
    </a>
    <a href="?aba=concluidos" class="tab-btn <?= $aba==='concluidos'?'ativo':'' ?>">
        <i class="fa-solid fa-check"></i> Concluídos
        <span class="badge badge-verde"><?= (int)$totais['concluidos'] ?></span>
    </a>
    <a href="?aba=todos" class="tab-btn <?= $aba==='todos'?'ativo':'' ?>">
        <i class="fa-solid fa-layer-group"></i> Todos
        <span class="badge badge-cinza"><?= (int)$totais['total'] ?></span>
    </a>
</div>

<!-- ══ Grid de cursos ════════════════════════════════════════ -->
<?php if ($matriculas): ?>
<div class="curso-grid">
    <?php foreach ($matriculas as $mat): ?>
    <div class="curso-card">

        <!-- Thumb -->
        <div class="curso-card-thumb">
            <?php if ($mat['capa']): ?>
                <img src="<?= BASE_URL ?>/uploads/capas/<?= e($mat['capa']) ?>" alt="Capa">
            <?php elseif ($mat['youtube_id']): ?>
                <img src="https://img.youtube.com/vi/<?= e($mat['youtube_id']) ?>/mqdefault.jpg" alt="Capa">
            <?php else: ?>
                <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;
                            font-size:2.5rem;opacity:.2">
                    <i class="fa-solid fa-graduation-cap"></i>
                </div>
            <?php endif; ?>

            <!-- Badges sobre a imagem -->
            <div style="position:absolute;top:8px;left:8px;display:flex;gap:4px">
                <?= badgeModalidade($mat['modalidade']) ?>
            </div>
            <?php if ($mat['status'] === 'CONCLUIDA'): ?>
            <div style="position:absolute;top:8px;right:8px">
                <span class="badge badge-verde"><i class="fa-solid fa-check"></i> Concluído</span>
            </div>
            <?php endif; ?>
        </div>

        <!-- Corpo -->
        <div class="curso-card-body">
            <?php if ($mat['cat_nome']): ?>
            <div style="font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;
                        color:<?= e($mat['cor_hex'] ?? 'var(--azul-600)') ?>;margin-bottom:4px">
                <?= e($mat['cat_nome']) ?>
            </div>
            <?php endif; ?>

            <div class="curso-card-titulo"><?= e(trunca($mat['titulo'], 55)) ?></div>

            <div class="curso-card-meta">
                <span><i class="fa-solid fa-clock"></i> <?= fmtCarga($mat['carga_horaria']) ?></span>
                <?php if ($mat['data_inicio'] && $mat['modalidade'] === 'PRESENCIAL'): ?>
                <span><i class="fa-solid fa-calendar"></i> <?= fmtData($mat['data_inicio']) ?></span>
                <?php endif; ?>
                <?php if ($mat['local_cidade'] && $mat['modalidade'] !== 'EAD'): ?>
                <span><i class="fa-solid fa-map-pin"></i> <?= e($mat['local_cidade']) ?></span>
                <?php endif; ?>
            </div>

            <!-- Barra de progresso (EAD) -->
            <?php if ($mat['modalidade'] === 'EAD' && $mat['status'] === 'ATIVA'): ?>
            <div style="margin-top:10px">
                <div class="progress-bar-wrap">
                    <div class="progress-bar-fill" style="width:<?= (int)$mat['progresso_ead'] ?>%"></div>
                </div>
                <div class="progress-info">
                    <span>Progresso</span>
                    <span style="font-weight:600;color:var(--azul-600)"><?= (int)$mat['progresso_ead'] ?>%</span>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Rodapé com ações -->
        <div class="curso-card-footer">
            <?php if ($mat['status'] === 'CONCLUIDA' && $mat['certificado_gerado']): ?>
                <a href="<?= BASE_URL ?>/aluno/certificado-ver.php?codigo=<?= e($mat['certificado_codigo']) ?>"
                   class="btn btn-ouro btn-sm">
                    <i class="fa-solid fa-certificate"></i> Ver Certificado
                </a>
            <?php elseif ($mat['status'] === 'CONCLUIDA'): ?>
                <a href="<?= BASE_URL ?>/aluno/emitir-certificado.php?matricula_id=<?= $mat['matricula_id'] ?>"
                   class="btn btn-verde btn-sm">
                    <i class="fa-solid fa-certificate"></i> Emitir Certificado
                </a>
            <?php else: ?>
                <a href="<?= BASE_URL ?>/aluno/curso.php?id=<?= $mat['curso_id'] ?>"
                   class="btn btn-primario btn-sm">
                    <i class="fa-solid fa-<?= $mat['progresso_ead'] > 0 ? 'play' : 'door-open' ?>"></i>
                    <?= $mat['progresso_ead'] > 0 ? 'Continuar' : 'Acessar' ?> Curso
                </a>
            <?php endif; ?>

            <span class="text-xs text-muted">
                Desde <?= fmtData($mat['matriculado_em']) ?>
            </span>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php else: ?>
<div class="card">
    <div class="card-body">
        <div class="empty-state">
            <i class="fa-solid fa-graduation-cap"></i>
            <h3>Nenhum curso encontrado</h3>
            <p>
                <?php if ($aba === 'andamento'): ?>
                    Você não tem cursos em andamento no momento.
                <?php elseif ($aba === 'concluidos'): ?>
                    Você ainda não concluiu nenhum curso.
                <?php else: ?>
                    Você ainda não está matriculado em nenhum curso.
                <?php endif; ?>
            </p>
        </div>
    </div>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/layout_aluno_footer.php'; ?>

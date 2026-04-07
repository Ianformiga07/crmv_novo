<?php
/**
 * admin/dashboard.php — Painel Administrativo Principal
 */
require_once __DIR__ . '/../includes/bootstrap.php';
Auth::requireAdmin();

$db = Database::getInstance();

// ── Totais gerais ─────────────────────────────────────────
$totais = $db->fetchOne("
    SELECT
        (SELECT COUNT(*) FROM tbl_cursos   WHERE ativo=1 AND status='PUBLICADO') AS cursos_ativos,
        (SELECT COUNT(*) FROM tbl_cursos   WHERE ativo=1 AND modalidade='EAD')   AS cursos_ead,
        (SELECT COUNT(*) FROM tbl_cursos   WHERE ativo=1 AND modalidade='PRESENCIAL') AS cursos_pres,
        (SELECT COUNT(*) FROM tbl_usuarios WHERE perfil_id=2 AND ativo=1)        AS veterinarios,
        (SELECT COUNT(*) FROM tbl_matriculas WHERE status='ATIVA')               AS matriculas_ativas,
        (SELECT COUNT(*) FROM tbl_matriculas WHERE status='CONCLUIDA')           AS concluidos,
        (SELECT COUNT(*) FROM tbl_certificados WHERE valido=1)                   AS certificados,
        (SELECT COUNT(*) FROM tbl_cursos WHERE ativo=1 AND status='PUBLICADO'
         AND data_inicio >= CURDATE())                                            AS proximos_eventos
");

// ── Cursos recentes ───────────────────────────────────────
$cursosRecentes = $db->fetchAll("
    SELECT c.curso_id, c.titulo, c.tipo, c.modalidade, c.status,
           c.data_inicio, c.carga_horaria,
           cat.nome AS cat_nome, cat.cor_hex,
           COUNT(m.matricula_id) AS total_mat
    FROM tbl_cursos c
    LEFT JOIN tbl_categorias cat ON c.categoria_id = cat.categoria_id
    LEFT JOIN tbl_matriculas m   ON m.curso_id = c.curso_id
    WHERE c.ativo = 1
    GROUP BY c.curso_id
    ORDER BY c.criado_em DESC
    LIMIT 6
");

// ── Certificados recentes ─────────────────────────────────
$certsRecentes = $db->fetchAll("
    SELECT cert.codigo, cert.emitido_em,
           u.nome_completo, u.crmv_numero, u.crmv_uf,
           c.titulo AS curso_titulo
    FROM tbl_certificados cert
    INNER JOIN tbl_matriculas mat ON cert.matricula_id = mat.matricula_id
    INNER JOIN tbl_usuarios   u   ON mat.usuario_id   = u.usuario_id
    INNER JOIN tbl_cursos     c   ON mat.curso_id     = c.curso_id
    WHERE cert.valido = 1
    ORDER BY cert.emitido_em DESC
    LIMIT 5
");

// ── Próximos eventos ──────────────────────────────────────
$proximosEventos = $db->fetchAll("
    SELECT curso_id, titulo, tipo, modalidade, data_inicio, local_cidade,
           (SELECT COUNT(*) FROM tbl_matriculas m WHERE m.curso_id = c.curso_id) AS inscritos,
           vagas
    FROM tbl_cursos c
    WHERE status = 'PUBLICADO' AND ativo = 1 AND data_inicio >= CURDATE()
    ORDER BY data_inicio ASC
    LIMIT 4
");

$pageTitulo  = 'Dashboard';
$paginaAtiva = 'dashboard';
require_once __DIR__ . '/../includes/layout_admin_header.php';
?>

<!-- ══ Page Header ══════════════════════════════════════════ -->
<div class="page-header">
    <div class="page-header-info">
        <h1 class="page-title">
            Bom <?= date('H') < 12 ? 'dia' : (date('H') < 18 ? 'tarde' : 'noite') ?>,
            <?= e(explode(' ', Auth::nome())[0]) ?> 👋
        </h1>
        <p class="page-subtitle">Resumo do sistema — <?= fmtData(date('Y-m-d')) ?></p>
    </div>
    <div class="page-actions">
        <a href="<?= BASE_URL ?>/admin/usuarios/form.php" class="btn btn-ghost btn-sm">
            <i class="fa-solid fa-user-plus"></i> Novo Veterinário
        </a>
        <a href="<?= BASE_URL ?>/admin/cursos/form.php" class="btn btn-primario btn-sm">
            <i class="fa-solid fa-plus"></i> Novo Curso
        </a>
    </div>
</div>

<!-- ══ Stat Cards ════════════════════════════════════════════ -->
<div class="stat-grid">
    <div class="stat-card">
        <div class="stat-card-icon stat-icon-azul">
            <i class="fa-solid fa-graduation-cap"></i>
        </div>
        <div class="stat-card-valor"><?= $totais['cursos_ativos'] ?></div>
        <div class="stat-card-label">Cursos Publicados</div>
        <div class="stat-card-delta">
            <i class="fa-solid fa-wifi"></i> <?= $totais['cursos_ead'] ?> EAD &nbsp;
            <i class="fa-solid fa-map-pin"></i> <?= $totais['cursos_pres'] ?> Presenciais
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-card-icon stat-icon-verde">
            <i class="fa-solid fa-user-doctor"></i>
        </div>
        <div class="stat-card-valor"><?= $totais['veterinarios'] ?></div>
        <div class="stat-card-label">Veterinários Cadastrados</div>
    </div>

    <div class="stat-card">
        <div class="stat-card-icon stat-icon-ouro">
            <i class="fa-solid fa-list-check"></i>
        </div>
        <div class="stat-card-valor"><?= $totais['matriculas_ativas'] ?></div>
        <div class="stat-card-label">Matrículas Ativas</div>
        <div class="stat-card-delta"><?= $totais['concluidos'] ?> concluídas</div>
    </div>

    <div class="stat-card">
        <div class="stat-card-icon stat-icon-verm">
            <i class="fa-solid fa-certificate"></i>
        </div>
        <div class="stat-card-valor"><?= $totais['certificados'] ?></div>
        <div class="stat-card-label">Certificados Emitidos</div>
    </div>
</div>

<!-- ══ Grid de conteúdo ════════════════════════════════════ -->
<div style="display:grid;grid-template-columns:1fr 340px;gap:20px;align-items:start">

    <!-- Cursos recentes -->
    <div class="table-container">
        <div class="table-header">
            <span class="table-title">
                <i class="fa-solid fa-graduation-cap" style="color:var(--c500)"></i>
                Cursos Recentes
            </span>
            <a href="<?= BASE_URL ?>/admin/cursos/lista.php" class="btn btn-ghost btn-sm">
                Ver todos
            </a>
        </div>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Curso</th>
                        <th>Modalidade</th>
                        <th>Status</th>
                        <th>Inscritos</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cursosRecentes as $c): ?>
                    <tr>
                        <td>
                            <div style="font-weight:500;color:var(--c900);font-size:.84rem">
                                <?= e(trunca($c['titulo'], 40)) ?>
                            </div>
                            <div class="text-xs text-muted">
                                <?= fmtCarga($c['carga_horaria']) ?>
                                <?php if ($c['data_inicio']): ?>
                                    · <?= fmtData($c['data_inicio']) ?>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td><?= badgeModalidade($c['modalidade']) ?></td>
                        <td><?= badgeCurso($c['status']) ?></td>
                        <td>
                            <span style="font-weight:600"><?= $c['total_mat'] ?></span>
                        </td>
                        <td>
                            <a href="<?= BASE_URL ?>/admin/cursos/form.php?id=<?= $c['curso_id'] ?>"
                               class="btn btn-ghost btn-icon btn-sm" title="Editar">
                                <i class="fa-solid fa-pen"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (!$cursosRecentes): ?>
                    <tr><td colspan="5">
                        <div class="empty-state" style="padding:30px">
                            <i class="fa-solid fa-graduation-cap"></i>
                            <p>Nenhum curso cadastrado ainda.</p>
                        </div>
                    </td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Painel direito -->
    <div style="display:flex;flex-direction:column;gap:16px">

        <!-- Próximos eventos -->
        <?php if ($proximosEventos): ?>
        <div class="card">
            <div class="card-header">
                <span class="card-title">
                    <i class="fa-solid fa-calendar-days"></i> Próximos Eventos
                </span>
            </div>
            <div class="card-body" style="padding:10px 16px">
                <?php foreach ($proximosEventos as $ev): ?>
                <div style="padding:10px 0;border-bottom:1px solid var(--c100);">
                    <div style="font-weight:500;font-size:.83rem;color:var(--c800)">
                        <?= e(trunca($ev['titulo'], 36)) ?>
                    </div>
                    <div class="text-xs text-muted mt-4">
                        <i class="fa-solid fa-calendar"></i> <?= fmtData($ev['data_inicio']) ?>
                        <?php if ($ev['local_cidade']): ?>
                            · <i class="fa-solid fa-map-pin"></i> <?= e($ev['local_cidade']) ?>
                        <?php endif; ?>
                    </div>
                    <div class="text-xs mt-4">
                        <span style="color:var(--azul-600)"><?= $ev['inscritos'] ?> inscrito(s)</span>
                        <?php if ($ev['vagas']): ?>
                            de <?= $ev['vagas'] ?> vagas
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Certificados recentes -->
        <div class="card">
            <div class="card-header">
                <span class="card-title">
                    <i class="fa-solid fa-certificate"></i> Certificados Recentes
                </span>
                <a href="<?= BASE_URL ?>/admin/certificados/lista.php" class="btn btn-ghost btn-sm">
                    Ver todos
                </a>
            </div>
            <div class="card-body" style="padding:10px 16px">
                <?php foreach ($certsRecentes as $cert): ?>
                <div style="padding:10px 0;border-bottom:1px solid var(--c100);display:flex;gap:10px;align-items:flex-start">
                    <div style="width:32px;height:32px;border-radius:50%;background:var(--ouro-50);
                                color:var(--ouro-400);display:flex;align-items:center;justify-content:center;
                                font-size:.8rem;flex-shrink:0">
                        <?= primeiraLetra($cert['nome_completo']) ?>
                    </div>
                    <div style="flex:1;min-width:0">
                        <div style="font-weight:500;font-size:.82rem;color:var(--c800)">
                            <?= e(trunca($cert['nome_completo'], 26)) ?>
                        </div>
                        <div class="text-xs text-muted">
                            <?= e(trunca($cert['curso_titulo'], 30)) ?>
                        </div>
                        <div class="text-xs" style="color:var(--ouro-400);margin-top:2px">
                            <i class="fa-solid fa-barcode"></i> <?= e($cert['codigo']) ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>

                <?php if (!$certsRecentes): ?>
                <div class="empty-state" style="padding:20px">
                    <i class="fa-solid fa-certificate"></i>
                    <p>Nenhum certificado emitido.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>

<?php require_once __DIR__ . '/../includes/layout_admin_footer.php'; ?>

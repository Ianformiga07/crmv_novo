<?php
/**
 * admin/matriculas/lista.php — Gerenciamento de Matrículas
 */
require_once __DIR__ . '/../../includes/bootstrap.php';
Auth::requireAdmin();

$db = Database::getInstance();

// ── Filtros ─────────────────────────────────────────────
$filtros = [
    'busca'    => trim($_GET['busca']    ?? ''),
    'curso_id' => (int)($_GET['curso_id'] ?? 0),
    'status'   => $_GET['status']  ?? '',
    'periodo'  => $_GET['periodo'] ?? '',
];

$where  = ['1=1'];
$params = [];

if ($filtros['busca']) {
    $where[]  = '(u.nome_completo LIKE ? OR u.cpf LIKE ? OR u.email LIKE ?)';
    $params[] = '%'.$filtros['busca'].'%';
    $params[] = '%'.$filtros['busca'].'%';
    $params[] = '%'.$filtros['busca'].'%';
}
if ($filtros['curso_id']) {
    $where[]  = 'm.curso_id = ?';
    $params[] = $filtros['curso_id'];
}
if ($filtros['status']) {
    $where[]  = 'm.status = ?';
    $params[] = $filtros['status'];
}
if ($filtros['periodo'] === '30d') {
    $where[] = 'm.matriculado_em >= DATE_SUB(NOW(), INTERVAL 30 DAY)';
} elseif ($filtros['periodo'] === '7d') {
    $where[] = 'm.matriculado_em >= DATE_SUB(NOW(), INTERVAL 7 DAY)';
}

$whereStr = implode(' AND ', $where);

// ── Ação rápida ──────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Auth::verifyCsrf();
    $matId     = (int)($_POST['matricula_id'] ?? 0);
    $novoStatus= $_POST['novo_status'] ?? '';
    $statusValidos = ['ATIVA','CONCLUIDA','CANCELADA'];

    if ($matId && in_array($novoStatus, $statusValidos)) {
        $db->execute(
            "UPDATE tbl_matriculas SET status=?, atualizado_em=NOW() WHERE matricula_id=?",
            [$novoStatus, $matId]
        );
        flash("Matrícula atualizada para: $novoStatus");
    }
    header('Location: ' . BASE_URL . '/admin/matriculas/lista.php?' . http_build_query($filtros));
    exit;
}

// ── Totais rápidos ─────────────────────────────────────
$totais = $db->fetchOne("
    SELECT
        COUNT(*) total,
        SUM(status='ATIVA') ativas,
        SUM(status='CONCLUIDA') concluidas,
        SUM(status='CANCELADA') canceladas
    FROM tbl_matriculas
");

// ── Paginação + dados ──────────────────────────────────
$pg    = paginar(20);
$total = (int) $db->fetchScalar(
    "SELECT COUNT(*)
     FROM tbl_matriculas m
     INNER JOIN tbl_usuarios u ON m.usuario_id = u.usuario_id
     INNER JOIN tbl_cursos c   ON m.curso_id   = c.curso_id
     WHERE $whereStr",
    $params
);

$matriculas = $db->fetchAll("
    SELECT m.matricula_id, m.status, m.matriculado_em, m.nota_final,
           m.certificado_gerado, m.progresso_ead,
           u.usuario_id, u.nome_completo, u.crmv_numero, u.crmv_uf, u.email,
           c.curso_id, c.titulo AS curso_titulo, c.modalidade, c.tipo
    FROM tbl_matriculas m
    INNER JOIN tbl_usuarios u ON m.usuario_id = u.usuario_id
    INNER JOIN tbl_cursos   c ON m.curso_id   = c.curso_id
    WHERE $whereStr
    ORDER BY m.matriculado_em DESC
    LIMIT {$pg['limit']} OFFSET {$pg['offset']}",
    $params
);

// Cursos para o select de filtro
$cursosSelect = $db->fetchAll("SELECT curso_id, titulo FROM tbl_cursos WHERE ativo=1 ORDER BY titulo");

$cursoFiltrado = null;
if ($filtros['curso_id']) {
    $cursoFiltrado = $db->fetchOne("SELECT titulo FROM tbl_cursos WHERE curso_id=?", [$filtros['curso_id']]);
}

$pageTitulo  = 'Matrículas';
$paginaAtiva = 'matriculas';
$breadcrumb  = ['Matrículas' => null];
require_once __DIR__ . '/../../includes/layout_admin_header.php';
?>

<!-- ══ Page Header ══════════════════════════════════════════ -->
<div class="page-header">
    <div class="page-header-info">
        <h1 class="page-title">
            <?= $cursoFiltrado ? 'Matrículas — '.e(trunca($cursoFiltrado['titulo'],40)) : 'Matrículas' ?>
        </h1>
        <p class="page-subtitle"><?= $total ?> matrícula(s) encontrada(s)</p>
    </div>
    <div class="page-actions">
        <a href="<?= BASE_URL ?>/admin/matriculas/nova.php" class="btn btn-primario btn-sm">
            <i class="fa-solid fa-user-plus"></i> Nova Matrícula
        </a>
    </div>
</div>

<!-- ══ Stat Cards ════════════════════════════════════════════ -->
<div class="stat-grid" style="grid-template-columns:repeat(4,1fr);margin-bottom:20px">
    <div class="stat-card">
        <div class="stat-card-icon stat-icon-azul"><i class="fa-solid fa-list"></i></div>
        <div class="stat-card-valor"><?= $totais['total'] ?></div>
        <div class="stat-card-label">Total de Matrículas</div>
    </div>
    <div class="stat-card">
        <div class="stat-card-icon stat-icon-verde"><i class="fa-solid fa-play"></i></div>
        <div class="stat-card-valor"><?= $totais['ativas'] ?></div>
        <div class="stat-card-label">Ativas</div>
    </div>
    <div class="stat-card">
        <div class="stat-card-icon stat-icon-ouro"><i class="fa-solid fa-check"></i></div>
        <div class="stat-card-valor"><?= $totais['concluidas'] ?></div>
        <div class="stat-card-label">Concluídas</div>
    </div>
    <div class="stat-card">
        <div class="stat-card-icon stat-icon-verm"><i class="fa-solid fa-ban"></i></div>
        <div class="stat-card-valor"><?= $totais['canceladas'] ?></div>
        <div class="stat-card-label">Canceladas</div>
    </div>
</div>

<!-- ══ Filtros ══════════════════════════════════════════════ -->
<form method="GET" class="filters-bar">
    <div class="form-group" style="flex:2;min-width:200px">
        <label class="form-label">Buscar aluno</label>
        <div class="input-group">
            <input type="text" name="busca" class="form-control"
                   placeholder="Nome, CPF ou e-mail..."
                   value="<?= e($filtros['busca']) ?>">
            <button type="submit" class="btn btn-primario"><i class="fa-solid fa-search"></i></button>
        </div>
    </div>
    <div class="form-group">
        <label class="form-label">Curso</label>
        <select name="curso_id" class="form-control" onchange="this.form.submit()">
            <option value="">Todos os cursos</option>
            <?php foreach ($cursosSelect as $cs): ?>
            <option value="<?= $cs['curso_id'] ?>"
                    <?= $filtros['curso_id']==$cs['curso_id']?'selected':'' ?>>
                <?= e(trunca($cs['titulo'], 40)) ?>
            </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <label class="form-label">Status</label>
        <select name="status" class="form-control" onchange="this.form.submit()">
            <option value="">Todos</option>
            <option value="ATIVA"     <?= $filtros['status']==='ATIVA'    ?'selected':'' ?>>Ativo</option>
            <option value="CONCLUIDA" <?= $filtros['status']==='CONCLUIDA'?'selected':'' ?>>Concluído</option>
            <option value="CANCELADA" <?= $filtros['status']==='CANCELADA'?'selected':'' ?>>Cancelado</option>
            <option value="REPROVADO" <?= $filtros['status']==='REPROVADO'?'selected':'' ?>>Reprovado</option>
        </select>
    </div>
    <div class="form-group">
        <label class="form-label">Período</label>
        <select name="periodo" class="form-control" onchange="this.form.submit()">
            <option value="">Todo período</option>
            <option value="7d"  <?= $filtros['periodo']==='7d' ?'selected':'' ?>>Últimos 7 dias</option>
            <option value="30d" <?= $filtros['periodo']==='30d'?'selected':'' ?>>Últimos 30 dias</option>
        </select>
    </div>
    <?php if (array_filter($filtros)): ?>
    <div class="form-group" style="align-self:flex-end">
        <a href="<?= BASE_URL ?>/admin/matriculas/lista.php" class="btn btn-ghost">
            <i class="fa-solid fa-xmark"></i> Limpar
        </a>
    </div>
    <?php endif; ?>
</form>

<!-- ══ Tabela ═══════════════════════════════════════════════ -->
<div class="table-container">
    <div class="table-header">
        <span class="table-title"><i class="fa-solid fa-list-check"></i> Lista de Matrículas</span>
        <span class="text-sm text-muted"><?= $total ?> registro(s)</span>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Veterinário</th>
                    <th>Curso</th>
                    <th>Modalidade</th>
                    <th>Status</th>
                    <th>Progresso</th>
                    <th>Matriculado em</th>
                    <th>Certificado</th>
                    <th style="width:120px">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($matriculas as $m): ?>
                <tr>
                    <td>
                        <div style="font-weight:500;font-size:.84rem;color:var(--c900)">
                            <?= e(trunca($m['nome_completo'], 30)) ?>
                        </div>
                        <div class="text-xs text-muted">
                            <?= fmtCRMV($m['crmv_numero'], $m['crmv_uf']) ?>
                        </div>
                    </td>
                    <td>
                        <div style="font-size:.83rem;color:var(--c700)">
                            <?= e(trunca($m['curso_titulo'], 35)) ?>
                        </div>
                        <div class="text-xs text-muted"><?= e($m['tipo']) ?></div>
                    </td>
                    <td><?= badgeModalidade($m['modalidade']) ?></td>
                    <td><?= badgeMatricula($m['status']) ?></td>
                    <td>
                        <?php if ($m['modalidade'] === 'EAD'): ?>
                        <div style="min-width:90px">
                            <div class="progress-bar-wrap">
                                <div class="progress-bar-fill" style="width:<?= (int)$m['progresso_ead'] ?>%"></div>
                            </div>
                            <div class="progress-info">
                                <span><?= (int)$m['progresso_ead'] ?>%</span>
                            </div>
                        </div>
                        <?php else: ?>
                        <span class="text-xs text-muted">—</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-sm text-muted"><?= fmtData($m['matriculado_em']) ?></td>
                    <td>
                        <?php if ($m['certificado_gerado']): ?>
                        <span class="badge badge-ouro"><i class="fa-solid fa-certificate"></i> Emitido</span>
                        <?php else: ?>
                        <span class="text-xs text-muted">—</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="table-actions">
                            <a href="<?= BASE_URL ?>/admin/usuarios/ver.php?id=<?= $m['usuario_id'] ?>"
                               class="btn btn-ghost btn-icon btn-sm" title="Ver aluno">
                                <i class="fa-solid fa-user"></i>
                            </a>
                            <!-- Ação rápida de status -->
                            <div style="position:relative">
                                <button class="btn btn-ghost btn-icon btn-sm"
                                        onclick="toggleMenuStatus(<?= $m['matricula_id'] ?>)"
                                        title="Alterar status">
                                    <i class="fa-solid fa-ellipsis-v"></i>
                                </button>
                                <div class="menu-status" id="menu-<?= $m['matricula_id'] ?>"
                                     style="display:none;position:absolute;right:0;top:100%;z-index:50;
                                            background:var(--surface);border:1px solid var(--border);
                                            border-radius:var(--radius);box-shadow:var(--shadow-md);
                                            min-width:140px">
                                    <form method="POST">
                                        <?= csrfField() ?>
                                        <input type="hidden" name="matricula_id" value="<?= $m['matricula_id'] ?>">
                                        <?php foreach (['ATIVA'=>'Ativar','CONCLUIDA'=>'Marcar Concluído','CANCELADA'=>'Cancelar'] as $st => $label): ?>
                                        <?php if ($st !== $m['status']): ?>
                                        <button type="submit" name="novo_status" value="<?= $st ?>"
                                                style="display:block;width:100%;text-align:left;padding:8px 14px;
                                                       background:none;border:none;cursor:pointer;font-size:.8rem;
                                                       color:var(--c700)">
                                            <?= $label ?>
                                        </button>
                                        <?php endif; ?>
                                        <?php endforeach; ?>
                                    </form>
                                    <?php if (!$m['certificado_gerado'] && $m['status']==='CONCLUIDA'): ?>
                                    <hr style="margin:4px 0;border-color:var(--c200)">
                                    <a href="<?= BASE_URL ?>/admin/certificados/emitir.php?matricula_id=<?= $m['matricula_id'] ?>"
                                       style="display:block;padding:8px 14px;font-size:.8rem;color:var(--ouro-400)">
                                        <i class="fa-solid fa-certificate"></i> Emitir Certificado
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>

                <?php if (!$matriculas): ?>
                <tr><td colspan="8">
                    <div class="empty-state">
                        <i class="fa-solid fa-list-check"></i>
                        <h3>Nenhuma matrícula encontrada</h3>
                        <p>Tente outros filtros ou <a href="<?= BASE_URL ?>/admin/matriculas/nova.php">crie uma nova matrícula</a>.</p>
                    </div>
                </td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($total > $pg['limit']): ?>
    <div class="table-footer flex-between">
        <span class="table-info">
            Mostrando <?= min($pg['offset']+1,$total) ?>–<?= min($pg['offset']+$pg['limit'],$total) ?> de <?= $total ?>
        </span>
        <?php renderPaginacao($total, $pg['limit'], $pg['page']); ?>
    </div>
    <?php endif; ?>
</div>

<script>
function toggleMenuStatus(id) {
    // Fecha outros menus abertos
    document.querySelectorAll('.menu-status').forEach(m => {
        if (m.id !== 'menu-' + id) m.style.display = 'none';
    });
    const menu = document.getElementById('menu-' + id);
    menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
}
// Fecha ao clicar fora
document.addEventListener('click', function(e) {
    if (!e.target.closest('.table-actions')) {
        document.querySelectorAll('.menu-status').forEach(m => m.style.display = 'none');
    }
});
</script>

<?php require_once __DIR__ . '/../../includes/layout_admin_footer.php'; ?>

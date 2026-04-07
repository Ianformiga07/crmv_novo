<?php
/**
 * admin/cursos/lista.php — Listagem de Cursos com Filtros
 */
require_once __DIR__ . '/../../includes/bootstrap.php';
Auth::requireAdmin();

$db = Database::getInstance();

// ── Filtros ─────────────────────────────────────────────
$filtros = [
    'busca'      => trim($_GET['busca']      ?? ''),
    'modalidade' => $_GET['modalidade']      ?? '',
    'status'     => $_GET['status']          ?? '',
    'tipo'       => $_GET['tipo']            ?? '',
    'categoria'  => (int)($_GET['categoria'] ?? 0),
];

// Identifica contexto de modalidade para título e breadcrumb
$tituloPagina = match($filtros['modalidade']) {
    'EAD'        => 'Cursos EAD',
    'PRESENCIAL' => 'Cursos Presenciais',
    'HIBRIDO'    => 'Cursos Híbridos',
    default      => 'Todos os Cursos',
};

// Monta WHERE dinâmico
$where  = ['c.ativo = 1'];
$params = [];

if ($filtros['busca']) {
    $where[]  = '(c.titulo LIKE ? OR c.descricao LIKE ?)';
    $params[] = '%' . $filtros['busca'] . '%';
    $params[] = '%' . $filtros['busca'] . '%';
}
if ($filtros['modalidade']) {
    $where[]  = 'c.modalidade = ?';
    $params[] = $filtros['modalidade'];
}
if ($filtros['status']) {
    $where[]  = 'c.status = ?';
    $params[] = $filtros['status'];
}
if ($filtros['tipo']) {
    $where[]  = 'c.tipo = ?';
    $params[] = $filtros['tipo'];
}
if ($filtros['categoria']) {
    $where[]  = 'c.categoria_id = ?';
    $params[] = $filtros['categoria'];
}

$whereStr = 'WHERE ' . implode(' AND ', $where);

// Paginação
$pg   = paginar(15);
$totalCursos = (int) $db->fetchScalar(
    "SELECT COUNT(*) FROM tbl_cursos c $whereStr",
    $params
);

$cursos = $db->fetchAll("
    SELECT c.curso_id, c.titulo, c.tipo, c.modalidade, c.status,
           c.carga_horaria, c.data_inicio, c.data_fim, c.vagas,
           c.capa, c.valor, c.requer_avaliacao,
           cat.nome AS cat_nome, cat.cor_hex,
           COUNT(DISTINCT m.matricula_id)                          AS total_mat,
           SUM(m.status = 'CONCLUIDA')                             AS total_concl,
           SUM(m.certificado_gerado = 1)                           AS total_cert
    FROM tbl_cursos c
    LEFT JOIN tbl_categorias cat ON c.categoria_id = cat.categoria_id
    LEFT JOIN tbl_matriculas m   ON m.curso_id = c.curso_id
    $whereStr
    GROUP BY c.curso_id
    ORDER BY c.criado_em DESC
    LIMIT {$pg['limit']} OFFSET {$pg['offset']}",
    $params
);

$categorias = $db->fetchAll("SELECT categoria_id, nome FROM tbl_categorias WHERE ativo=1 ORDER BY ordem");

$pageTitulo  = $tituloPagina;
$paginaAtiva = match($filtros['modalidade']) { 'EAD'=>'cursos_ead', 'PRESENCIAL'=>'cursos_pres', default=>'cursos' };
$breadcrumb  = [$tituloPagina => null];
$topbarActions = '<a href="' . BASE_URL . '/admin/cursos/form.php" class="btn btn-primario btn-sm">
    <i class="fa-solid fa-plus"></i> Novo Curso
</a>';

require_once __DIR__ . '/../../includes/layout_admin_header.php';
?>

<!-- ══ Page Header ══════════════════════════════════════════ -->
<div class="page-header">
    <div class="page-header-info">
        <h1 class="page-title"><?= e($tituloPagina) ?></h1>
        <p class="page-subtitle"><?= $totalCursos ?> curso(s) encontrado(s)</p>
    </div>
    <div class="page-actions">
        <!-- Abas de modalidade -->
        <div class="tabs-bar" style="border:none;margin:0;gap:4px">
            <?php
            $tabs = [
                '' => ['label'=>'Todos', 'icon'=>'fa-layer-group'],
                'EAD' => ['label'=>'EAD', 'icon'=>'fa-wifi'],
                'PRESENCIAL' => ['label'=>'Presencial', 'icon'=>'fa-map-pin'],
                'HIBRIDO' => ['label'=>'Híbrido', 'icon'=>'fa-shuffle'],
            ];
            foreach ($tabs as $val => $t):
                $qs = http_build_query(array_merge($filtros, ['modalidade'=>$val, 'p'=>1]));
                $ativo = $filtros['modalidade'] === $val ? 'ativo' : '';
            ?>
            <a href="?<?= $qs ?>" class="tab-btn <?= $ativo ?>" style="border:1px solid var(--border);border-radius:var(--radius);text-decoration:none">
                <i class="fa-solid <?= $t['icon'] ?>"></i> <?= $t['label'] ?>
            </a>
            <?php endforeach; ?>
        </div>
        <a href="<?= BASE_URL ?>/admin/cursos/form.php" class="btn btn-primario btn-sm">
            <i class="fa-solid fa-plus"></i> Novo
        </a>
    </div>
</div>

<!-- ══ Filtros ══════════════════════════════════════════════ -->
<form method="GET" class="filters-bar">
    <?php if ($filtros['modalidade']): ?>
    <input type="hidden" name="modalidade" value="<?= e($filtros['modalidade']) ?>">
    <?php endif; ?>

    <div class="form-group" style="flex:2;min-width:200px">
        <label class="form-label">Buscar</label>
        <div class="input-group">
            <input type="text" name="busca" class="form-control"
                   placeholder="Título ou descrição..."
                   value="<?= e($filtros['busca']) ?>">
            <button type="submit" class="btn btn-primario">
                <i class="fa-solid fa-search"></i>
            </button>
        </div>
    </div>

    <div class="form-group">
        <label class="form-label">Status</label>
        <select name="status" class="form-control" onchange="this.form.submit()">
            <option value="">Todos</option>
            <option value="PUBLICADO"  <?= $filtros['status']==='PUBLICADO'?'selected':'' ?>>Publicado</option>
            <option value="RASCUNHO"   <?= $filtros['status']==='RASCUNHO' ?'selected':'' ?>>Rascunho</option>
            <option value="ENCERRADO"  <?= $filtros['status']==='ENCERRADO'?'selected':'' ?>>Encerrado</option>
            <option value="CANCELADO"  <?= $filtros['status']==='CANCELADO'?'selected':'' ?>>Cancelado</option>
        </select>
    </div>

    <div class="form-group">
        <label class="form-label">Tipo</label>
        <select name="tipo" class="form-control" onchange="this.form.submit()">
            <option value="">Todos</option>
            <option value="CURSO"     <?= $filtros['tipo']==='CURSO'    ?'selected':'' ?>>Curso</option>
            <option value="PALESTRA"  <?= $filtros['tipo']==='PALESTRA' ?'selected':'' ?>>Palestra</option>
            <option value="WORKSHOP"  <?= $filtros['tipo']==='WORKSHOP' ?'selected':'' ?>>Workshop</option>
            <option value="CONGRESSO" <?= $filtros['tipo']==='CONGRESSO'?'selected':'' ?>>Congresso</option>
            <option value="WEBINAR"   <?= $filtros['tipo']==='WEBINAR'  ?'selected':'' ?>>Webinar</option>
        </select>
    </div>

    <div class="form-group">
        <label class="form-label">Categoria</label>
        <select name="categoria" class="form-control" onchange="this.form.submit()">
            <option value="">Todas</option>
            <?php foreach ($categorias as $cat): ?>
            <option value="<?= $cat['categoria_id'] ?>"
                    <?= $filtros['categoria']==$cat['categoria_id']?'selected':'' ?>>
                <?= e($cat['nome']) ?>
            </option>
            <?php endforeach; ?>
        </select>
    </div>

    <?php if (array_filter($filtros)): ?>
    <div class="form-group" style="align-self:flex-end">
        <a href="<?= BASE_URL ?>/admin/cursos/lista.php<?= $filtros['modalidade'] ? '?modalidade='.$filtros['modalidade'] : '' ?>"
           class="btn btn-ghost">
            <i class="fa-solid fa-xmark"></i> Limpar
        </a>
    </div>
    <?php endif; ?>
</form>

<!-- ══ Tabela ═══════════════════════════════════════════════ -->
<div class="table-container">
    <div class="table-header">
        <span class="table-title">
            <i class="fa-solid fa-list"></i> Resultados
        </span>
        <span class="text-sm text-muted"><?= $totalCursos ?> total</span>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Curso</th>
                    <th>Modalidade</th>
                    <th>Status</th>
                    <th>Carga</th>
                    <th>Data</th>
                    <th>Inscritos</th>
                    <th>Certs.</th>
                    <th style="width:100px">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cursos as $c): ?>
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px">
                            <div class="curso-avatar">
                                <?php if ($c['capa']): ?>
                                    <img src="<?= BASE_URL ?>/uploads/capas/<?= e($c['capa']) ?>" alt="">
                                <?php else: ?>
                                    <i class="fa-solid fa-graduation-cap"></i>
                                <?php endif; ?>
                            </div>
                            <div>
                                <div style="font-weight:600;color:var(--c900);font-size:.84rem">
                                    <?= e(trunca($c['titulo'], 45)) ?>
                                </div>
                                <div class="text-xs text-muted">
                                    <?= e($c['tipo']) ?>
                                    <?php if ($c['cat_nome']): ?>
                                        ·
                                        <span style="color:<?= e($c['cor_hex'] ?? '#64748b') ?>">
                                            <?= e($c['cat_nome']) ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td><?= badgeModalidade($c['modalidade']) ?></td>
                    <td><?= badgeCurso($c['status']) ?></td>
                    <td class="text-sm"><?= fmtCarga($c['carga_horaria']) ?></td>
                    <td class="text-sm text-muted">
                        <?= $c['data_inicio'] ? fmtData($c['data_inicio']) : '—' ?>
                    </td>
                    <td>
                        <strong><?= (int)$c['total_mat'] ?></strong>
                        <?php if ($c['total_concl'] > 0): ?>
                        <span class="text-xs text-muted">(<?= (int)$c['total_concl'] ?> ✓)</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <span style="color:var(--ouro-500);font-weight:600">
                            <?= (int)$c['total_cert'] ?>
                        </span>
                    </td>
                    <td>
                        <div class="table-actions">
                            <a href="<?= BASE_URL ?>/admin/matriculas/lista.php?curso_id=<?= $c['curso_id'] ?>"
                               class="btn btn-ghost btn-icon btn-sm" title="Ver matrículas">
                                <i class="fa-solid fa-users"></i>
                            </a>
                            <a href="<?= BASE_URL ?>/admin/cursos/form.php?id=<?= $c['curso_id'] ?>"
                               class="btn btn-ghost btn-icon btn-sm" title="Editar">
                                <i class="fa-solid fa-pen"></i>
                            </a>
                            <button type="button"
                                    onclick="confirmarExclusao(<?= $c['curso_id'] ?>, '<?= e(addslashes($c['titulo'])) ?>')"
                                    class="btn btn-ghost btn-icon btn-sm" title="Excluir"
                                    style="color:var(--verm-500)">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>

                <?php if (!$cursos): ?>
                <tr><td colspan="8">
                    <div class="empty-state">
                        <i class="fa-solid fa-graduation-cap"></i>
                        <h3>Nenhum curso encontrado</h3>
                        <p>Tente outros filtros ou <a href="<?= BASE_URL ?>/admin/cursos/form.php">cadastre um novo curso</a>.</p>
                    </div>
                </td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($totalCursos > $pg['limit']): ?>
    <div class="table-footer flex-between">
        <span class="table-info">
            Mostrando <?= min($pg['offset']+1, $totalCursos) ?>–<?= min($pg['offset']+$pg['limit'], $totalCursos) ?>
            de <?= $totalCursos ?>
        </span>
        <?php renderPaginacao($totalCursos, $pg['limit'], $pg['page']); ?>
    </div>
    <?php endif; ?>
</div>

<!-- Modal de confirmação de exclusão -->
<div class="modal-overlay" id="modalExcluir">
    <div class="modal" style="max-width:420px">
        <div class="modal-header">
            <span class="modal-title">
                <i class="fa-solid fa-triangle-exclamation" style="color:var(--verm-500)"></i>
                Confirmar exclusão
            </span>
            <button class="modal-close" onclick="fecharModal()">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <div class="modal-body">
            <p>Tem certeza que deseja excluir o curso <strong id="cursoNomeModal"></strong>?</p>
            <p class="text-sm text-muted mt-8">Esta ação não pode ser desfeita. Matrículas e certificados relacionados serão mantidos.</p>
        </div>
        <div class="modal-footer">
            <button class="btn btn-ghost" onclick="fecharModal()">Cancelar</button>
            <a id="linkExcluir" href="#" class="btn btn-verm">
                <i class="fa-solid fa-trash"></i> Excluir
            </a>
        </div>
    </div>
</div>

<script>
function confirmarExclusao(id, nome) {
    document.getElementById('cursoNomeModal').textContent = nome;
    document.getElementById('linkExcluir').href = '<?= BASE_URL ?>/admin/cursos/excluir.php?id=' + id;
    document.getElementById('modalExcluir').classList.add('aberto');
}
function fecharModal() {
    document.getElementById('modalExcluir').classList.remove('aberto');
}
document.getElementById('modalExcluir').addEventListener('click', function(e) {
    if (e.target === this) fecharModal();
});
</script>

<?php require_once __DIR__ . '/../../includes/layout_admin_footer.php'; ?>

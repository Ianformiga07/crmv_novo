<?php
/**
 * aluno/curso.php — Página de consumo do curso (EAD e Presencial)
 */
require_once __DIR__ . '/../includes/bootstrap.php';
Auth::requireAluno();

$db    = Database::getInstance();
$usrId = Auth::id();

$cursoId = (int)($_GET['id'] ?? 0);
if (!$cursoId) {
    header('Location: ' . BASE_URL . '/aluno/dashboard.php');
    exit;
}

// Verifica matrícula ativa
$matricula = $db->fetchOne("
    SELECT m.*, c.titulo, c.descricao, c.modalidade, c.tipo, c.carga_horaria,
           c.data_inicio, c.data_fim, c.horario, c.link_ead, c.youtube_id,
           c.local_nome, c.local_cidade, c.local_uf, c.local_endereco,
           c.requer_avaliacao, c.avaliacao_com_nota, c.nota_minima,
           c.capa, cat.nome AS cat_nome, cat.cor_hex
    FROM tbl_matriculas m
    INNER JOIN tbl_cursos c     ON m.curso_id = c.curso_id
    LEFT  JOIN tbl_categorias cat ON c.categoria_id = cat.categoria_id
    WHERE m.usuario_id = ? AND m.curso_id = ? AND c.ativo = 1",
    [$usrId, $cursoId]
);

if (!$matricula) {
    flash('Você não está matriculado neste curso.', 'erro');
    header('Location: ' . BASE_URL . '/aluno/dashboard.php');
    exit;
}

$matId = $matricula['matricula_id'];

// ── Marcar aula como concluída (AJAX ou POST) ─────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['marcar_aula'])) {
    Auth::verifyCsrf();
    $aulaId = (int)($_POST['aula_id'] ?? 0);
    if ($aulaId) {
        // Upsert: insere ou atualiza o progresso
        $db->execute("
            INSERT INTO tbl_aula_progresso (aula_id, usuario_id, concluida, concluida_em)
            VALUES (?, ?, 1, NOW())
            ON DUPLICATE KEY UPDATE concluida=1, concluida_em=NOW()",
            [$aulaId, $usrId]
        );

        // Recalcula e salva o progresso na matrícula
        $pct = calcularProgressoEAD($cursoId, $usrId);
        $novoStatus = $pct >= 100 ? 'CONCLUIDA' : 'ATIVA';

        $db->execute(
            "UPDATE tbl_matriculas SET progresso_ead=?, status=?, atualizado_em=NOW()
             WHERE matricula_id=?",
            [$pct, $novoStatus, $matId]
        );

        // Se concluiu e não requer avaliação → gera certificado
        if ($novoStatus === 'CONCLUIDA' && !$matricula['requer_avaliacao']) {
            $jaTemCert = $db->fetchScalar(
                "SELECT COUNT(*) FROM tbl_certificados WHERE matricula_id=?",
                [$matId]
            );
            if (!$jaTemCert) {
                $codigo = strtoupper(implode('-', str_split(bin2hex(random_bytes(6)), 4)));
                $db->execute(
                    "INSERT INTO tbl_certificados (matricula_id, codigo) VALUES (?,?)",
                    [$matId, $codigo]
                );
                $db->execute(
                    "UPDATE tbl_matriculas SET certificado_gerado=1, certificado_codigo=?,
                     certificado_emitido_em=NOW() WHERE matricula_id=?",
                    [$codigo, $matId]
                );
            }
        }

        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            header('Content-Type: application/json');
            echo json_encode(['ok'=>true, 'progresso'=>$pct, 'status'=>$novoStatus]);
            exit;
        }

        header('Location: ' . BASE_URL . '/aluno/curso.php?id=' . $cursoId);
        exit;
    }
}

// ── Módulos, aulas e progresso ────────────────────────────
$rawMods = $db->fetchAll(
    "SELECT * FROM tbl_modulos WHERE curso_id=? ORDER BY ordem",
    [$cursoId]
);

// IDs de aulas concluídas pelo usuário
$aulasConcluidasRaw = $db->fetchAll("
    SELECT ap.aula_id
    FROM tbl_aula_progresso ap
    INNER JOIN tbl_aulas a ON ap.aula_id = a.aula_id
    INNER JOIN tbl_modulos m ON a.modulo_id = m.modulo_id
    WHERE ap.usuario_id = ? AND m.curso_id = ? AND ap.concluida = 1",
    [$usrId, $cursoId]
);
$aulasConcluidasIds = array_column($aulasConcluidasRaw, 'aula_id');

$modulos = [];
foreach ($rawMods as $mod) {
    $aulas = $db->fetchAll(
        "SELECT * FROM tbl_aulas WHERE modulo_id=? AND ativo=1 ORDER BY ordem",
        [$mod['modulo_id']]
    );
    $conclMod = count(array_filter($aulas, fn($a) => in_array($a['aula_id'], $aulasConcluidasIds)));
    $modulos[] = array_merge($mod, ['aulas'=>$aulas, 'concluidas'=>$conclMod, 'total'=>count($aulas)]);
}

// Materiais
$materiais = $db->fetchAll(
    "SELECT * FROM tbl_materiais WHERE curso_id=? ORDER BY criado_em",
    [$cursoId]
);

// Avaliação disponível
$avaliacao = null;
if ($matricula['requer_avaliacao']) {
    $avaliacao = $db->fetchOne(
        "SELECT * FROM tbl_avaliacoes WHERE curso_id=? AND (modulo_id IS NULL OR modulo_id=0) AND ativo=1 LIMIT 1",
        [$cursoId]
    );
}

// Aula atualmente visualizada
$aulaAtualId = (int)($_GET['aula'] ?? 0);
$aulaAtual   = null;
if ($aulaAtualId) {
    $aulaAtual = $db->fetchOne(
        "SELECT a.*, m.titulo AS modulo_titulo FROM tbl_aulas a
         INNER JOIN tbl_modulos m ON a.modulo_id = m.modulo_id
         WHERE a.aula_id=? AND m.curso_id=? AND a.ativo=1",
        [$aulaAtualId, $cursoId]
    );
}

$progresso = (int)$matricula['progresso_ead'];

$pageTitulo  = $matricula['titulo'];
$paginaAtiva = 'cursos';
require_once __DIR__ . '/../includes/layout_aluno_header.php';
?>

<!-- Layout: conteúdo + sidebar de aulas -->
<div style="display:grid;grid-template-columns:1fr 300px;gap:20px;align-items:start">

    <!-- ── Coluna principal ────────────────────────────────── -->
    <div>
        <!-- Breadcrumb / título -->
        <div class="flex-center gap-8 mb-16" style="flex-wrap:wrap">
            <a href="<?= BASE_URL ?>/aluno/dashboard.php" class="text-muted text-sm">
                <i class="fa-solid fa-arrow-left"></i> Meus Cursos
            </a>
            <i class="fa-solid fa-chevron-right text-xs" style="color:var(--c400)"></i>
            <span style="font-size:.84rem;color:var(--c600)"><?= e(trunca($matricula['titulo'],50)) ?></span>
        </div>

        <!-- Player / vídeo / iframe -->
        <?php if ($aulaAtual): ?>
            <!-- MODO AULA ESPECÍFICA -->
            <div class="card mb-16">
                <div style="background:var(--c900);border-radius:var(--radius-lg) var(--radius-lg) 0 0;overflow:hidden">
                    <?php if ($aulaAtual['youtube_id']): ?>
                        <div style="position:relative;padding-bottom:56.25%;height:0;overflow:hidden">
                            <iframe src="https://www.youtube.com/embed/<?= e($aulaAtual['youtube_id']) ?>?rel=0"
                                    style="position:absolute;top:0;left:0;width:100%;height:100%;border:none"
                                    allowfullscreen></iframe>
                        </div>
                    <?php elseif ($aulaAtual['link_externo']): ?>
                        <div style="padding:40px;text-align:center;color:#fff">
                            <i class="fa-solid fa-external-link" style="font-size:2rem;opacity:.5;margin-bottom:12px"></i>
                            <p style="font-size:.9rem;margin-bottom:16px">Esta aula está disponível em plataforma externa.</p>
                            <a href="<?= e($aulaAtual['link_externo']) ?>" target="_blank" rel="noopener"
                               class="btn btn-ouro">
                                <i class="fa-solid fa-arrow-up-right-from-square"></i> Acessar Aula
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <div style="font-size:.72rem;color:var(--c500);margin-bottom:4px">
                        <?= e($aulaAtual['modulo_titulo']) ?>
                    </div>
                    <h2 style="font-size:1.1rem;font-weight:700;color:var(--c900);margin-bottom:8px">
                        <?= e($aulaAtual['titulo']) ?>
                    </h2>
                    <?php if ($aulaAtual['descricao']): ?>
                    <p class="text-sm text-muted"><?= e($aulaAtual['descricao']) ?></p>
                    <?php endif; ?>

                    <!-- Botão marcar concluída -->
                    <?php $jaConcluiu = in_array($aulaAtual['aula_id'], $aulasConcluidasIds); ?>
                    <div style="margin-top:16px;display:flex;gap:10px;align-items:center">
                        <?php if ($jaConcluiu): ?>
                        <span class="badge badge-verde">
                            <i class="fa-solid fa-check"></i> Aula concluída
                        </span>
                        <?php else: ?>
                        <form method="POST" id="formConcluir">
                            <?= csrfField() ?>
                            <input type="hidden" name="marcar_aula" value="1">
                            <input type="hidden" name="aula_id" value="<?= $aulaAtual['aula_id'] ?>">
                            <button type="submit" class="btn btn-verde btn-sm">
                                <i class="fa-solid fa-check"></i> Marcar como concluída
                            </button>
                        </form>
                        <?php endif; ?>
                        <a href="<?= BASE_URL ?>/aluno/curso.php?id=<?= $cursoId ?>"
                           class="btn btn-ghost btn-sm">
                            Voltar ao curso
                        </a>
                    </div>
                </div>
            </div>

        <?php else: ?>
            <!-- MODO VISÃO GERAL DO CURSO -->

            <!-- Cabeçalho do curso -->
            <div class="card mb-16">
                <?php if ($matricula['capa'] || $matricula['youtube_id']): ?>
                <div style="height:180px;overflow:hidden;border-radius:var(--radius-lg) var(--radius-lg) 0 0">
                    <?php if ($matricula['youtube_id']): ?>
                        <img src="https://img.youtube.com/vi/<?= e($matricula['youtube_id']) ?>/hqdefault.jpg"
                             style="width:100%;height:100%;object-fit:cover" alt="">
                    <?php elseif ($matricula['capa']): ?>
                        <img src="<?= BASE_URL ?>/uploads/capas/<?= e($matricula['capa']) ?>"
                             style="width:100%;height:100%;object-fit:cover" alt="">
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                <div class="card-body">
                    <div style="display:flex;gap:8px;margin-bottom:8px">
                        <?= badgeModalidade($matricula['modalidade']) ?>
                        <?= badgeMatricula($matricula['status']) ?>
                    </div>
                    <h1 style="font-family:var(--font-titulo);font-size:1.3rem;font-weight:700;
                               color:var(--c900);margin-bottom:8px">
                        <?= e($matricula['titulo']) ?>
                    </h1>
                    <?php if ($matricula['descricao']): ?>
                    <p class="text-sm text-muted"><?= e(trunca($matricula['descricao'], 200)) ?></p>
                    <?php endif; ?>

                    <!-- Meta info -->
                    <div style="display:flex;gap:16px;flex-wrap:wrap;margin-top:12px">
                        <span class="text-sm text-muted">
                            <i class="fa-solid fa-clock"></i> <?= fmtCarga($matricula['carga_horaria']) ?>
                        </span>
                        <?php if ($matricula['data_inicio']): ?>
                        <span class="text-sm text-muted">
                            <i class="fa-solid fa-calendar"></i>
                            <?= fmtData($matricula['data_inicio']) ?>
                            <?php if ($matricula['data_fim']): ?> – <?= fmtData($matricula['data_fim']) ?><?php endif; ?>
                        </span>
                        <?php endif; ?>
                        <?php if ($matricula['local_nome']): ?>
                        <span class="text-sm text-muted">
                            <i class="fa-solid fa-map-pin"></i> <?= e($matricula['local_nome']) ?>
                            <?= $matricula['local_cidade'] ? ', '.$matricula['local_cidade'] : '' ?>
                        </span>
                        <?php endif; ?>
                    </div>

                    <!-- Progresso EAD -->
                    <?php if ($matricula['modalidade'] === 'EAD'): ?>
                    <div style="margin-top:16px;padding-top:16px;border-top:1px solid var(--border)">
                        <div class="flex-between mb-8">
                            <span class="text-sm font-medium">Seu progresso</span>
                            <span style="font-size:.85rem;font-weight:700;color:var(--azul-600)"><?= $progresso ?>%</span>
                        </div>
                        <div class="progress-bar-wrap" style="height:10px">
                            <div class="progress-bar-fill" style="width:<?= $progresso ?>%"></div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Módulos do curso (EAD) -->
            <?php if ($modulos): ?>
            <h2 class="section-title"><i class="fa-solid fa-layer-group"></i> Conteúdo do Curso</h2>
            <div class="accordion">
                <?php foreach ($modulos as $mod): ?>
                <?php $modCompleto = $mod['total'] > 0 && $mod['concluidas'] === $mod['total']; ?>
                <div class="accordion-item aberto">
                    <div class="accordion-header" onclick="toggleAccordion(this.parentElement)">
                        <i class="fa-solid fa-<?= $modCompleto ? 'check-circle' : 'folder' ?>"
                           style="color:<?= $modCompleto ? 'var(--verde-500)' : 'var(--ouro-400)' ?>"></i>
                        <span style="flex:1"><?= e($mod['titulo']) ?></span>
                        <?php if ($mod['total'] > 0): ?>
                        <span class="badge <?= $modCompleto ? 'badge-verde' : 'badge-cinza' ?>">
                            <?= $mod['concluidas'] ?>/<?= $mod['total'] ?>
                        </span>
                        <?php endif; ?>
                        <i class="fa-solid fa-chevron-down acc-toggle"></i>
                    </div>
                    <div class="accordion-body">
                        <?php foreach ($mod['aulas'] as $aula): ?>
                        <?php $concluida = in_array($aula['aula_id'], $aulasConcluidasIds); ?>
                        <div class="aula-item">
                            <div class="aula-item-icon <?= $aula['youtube_id'] ? 'aula-icon-video' : 'aula-icon-link' ?>">
                                <?php if ($concluida): ?>
                                    <i class="fa-solid fa-check" style="color:var(--verde-500)"></i>
                                <?php else: ?>
                                    <i class="fa-solid fa-<?= $aula['youtube_id'] ? 'play' : 'link' ?>"></i>
                                <?php endif; ?>
                            </div>
                            <div class="aula-item-info">
                                <a href="<?= BASE_URL ?>/aluno/curso.php?id=<?= $cursoId ?>&aula=<?= $aula['aula_id'] ?>"
                                   style="color:<?= $concluida ? 'var(--c500)' : 'var(--c900)' ?>;
                                          text-decoration:none;font-weight:500;font-size:.84rem">
                                    <?= e($aula['titulo']) ?>
                                    <?php if ($concluida): ?>
                                    <span style="text-decoration:line-through;opacity:.6">
                                    </span>
                                    <?php endif; ?>
                                </a>
                                <div class="aula-item-meta">
                                    <?php if ($aula['duracao_min']): ?>
                                    <i class="fa-solid fa-clock"></i> <?= $aula['duracao_min'] ?>min
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div style="flex-shrink:0">
                                <?php if ($concluida): ?>
                                <span class="badge badge-verde" style="font-size:.65rem">
                                    <i class="fa-solid fa-check"></i>
                                </span>
                                <?php else: ?>
                                <a href="<?= BASE_URL ?>/aluno/curso.php?id=<?= $cursoId ?>&aula=<?= $aula['aula_id'] ?>"
                                   class="btn btn-primario btn-sm">
                                    <i class="fa-solid fa-play"></i>
                                </a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <?php if (!$mod['aulas']): ?>
                        <div style="padding:14px 16px;font-size:.8rem;color:var(--c400);text-align:center">
                            Nenhuma aula neste módulo.
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- Link EAD externo -->
            <?php if ($matricula['link_ead']): ?>
            <div class="card mt-20">
                <div class="card-body" style="display:flex;align-items:center;gap:16px">
                    <div style="width:44px;height:44px;border-radius:10px;background:var(--azul-50);
                                color:var(--azul-600);display:flex;align-items:center;justify-content:center;
                                font-size:1.2rem;flex-shrink:0">
                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                    </div>
                    <div style="flex:1">
                        <div style="font-weight:600;color:var(--c900);font-size:.9rem">Acessar plataforma do curso</div>
                        <div class="text-sm text-muted">Clique para abrir o ambiente EAD</div>
                    </div>
                    <a href="<?= e($matricula['link_ead']) ?>" target="_blank" rel="noopener"
                       class="btn btn-primario btn-sm">
                        Acessar <i class="fa-solid fa-external-link"></i>
                    </a>
                </div>
            </div>
            <?php endif; ?>

            <!-- Avaliação disponível -->
            <?php if ($avaliacao && $matricula['status'] === 'ATIVA' && $progresso >= 80): ?>
            <div class="card mt-16" style="border-color:var(--azul-200)">
                <div class="card-body" style="display:flex;align-items:center;gap:16px">
                    <div style="width:44px;height:44px;border-radius:10px;background:var(--azul-50);
                                color:var(--azul-600);display:flex;align-items:center;justify-content:center;
                                font-size:1.2rem;flex-shrink:0">
                        <i class="fa-solid fa-clipboard-check"></i>
                    </div>
                    <div style="flex:1">
                        <div style="font-weight:600;color:var(--c900);font-size:.9rem">Avaliação disponível</div>
                        <div class="text-sm text-muted">
                            <?= e($avaliacao['titulo']) ?> —
                            Nota mínima: <?= $avaliacao['nota_minima'] ?>
                        </div>
                    </div>
                    <a href="<?= BASE_URL ?>/aluno/avaliacao.php?id=<?= $avaliacao['avaliacao_id'] ?>&matricula_id=<?= $matId ?>"
                       class="btn btn-primario btn-sm">
                        Fazer Prova
                    </a>
                </div>
            </div>
            <?php endif; ?>

            <!-- Certificado disponível -->
            <?php if ($matricula['status'] === 'CONCLUIDA'): ?>
            <div class="card mt-16" style="border-color:var(--ouro-400);background:var(--ouro-50)">
                <div class="card-body" style="display:flex;align-items:center;gap:16px">
                    <div style="width:44px;height:44px;border-radius:10px;background:var(--ouro-100);
                                color:var(--ouro-400);display:flex;align-items:center;justify-content:center;
                                font-size:1.2rem;flex-shrink:0">
                        <i class="fa-solid fa-certificate"></i>
                    </div>
                    <div style="flex:1">
                        <div style="font-weight:700;color:var(--ouro-600);font-size:.9rem">
                            🎉 Parabéns! Você concluiu este curso.
                        </div>
                        <?php if ($matricula['certificado_gerado']): ?>
                        <div class="text-sm text-muted">Certificado já emitido — clique para visualizar.</div>
                        <?php else: ?>
                        <div class="text-sm text-muted">Seu certificado está disponível para emissão.</div>
                        <?php endif; ?>
                    </div>
                    <?php if ($matricula['certificado_gerado']): ?>
                    <a href="<?= BASE_URL ?>/aluno/certificado-ver.php?codigo=<?= e($matricula['certificado_codigo']) ?>"
                       class="btn btn-ouro btn-sm">
                        <i class="fa-solid fa-certificate"></i> Ver Certificado
                    </a>
                    <?php else: ?>
                    <a href="<?= BASE_URL ?>/aluno/emitir-certificado.php?matricula_id=<?= $matId ?>"
                       class="btn btn-ouro btn-sm">
                        <i class="fa-solid fa-certificate"></i> Emitir Certificado
                    </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

        <?php endif; // fim modo visão geral ?>

    </div>

    <!-- ── Sidebar de navegação ─────────────────────────────── -->
    <div style="position:sticky;top:80px">

        <!-- Progresso card -->
        <?php if ($matricula['modalidade'] === 'EAD'): ?>
        <div class="card mb-16">
            <div class="card-header">
                <span class="card-title"><i class="fa-solid fa-chart-line"></i> Progresso</span>
            </div>
            <div class="card-body">
                <div style="text-align:center;padding:8px 0 12px">
                    <div style="font-size:2rem;font-weight:700;color:var(--azul-600)"><?= $progresso ?>%</div>
                    <div class="text-sm text-muted">concluído</div>
                </div>
                <div class="progress-bar-wrap" style="height:10px">
                    <div class="progress-bar-fill" style="width:<?= $progresso ?>%"></div>
                </div>
                <div style="margin-top:12px;font-size:.78rem;color:var(--c500)">
                    <?php
                    $totalAulas    = array_sum(array_column($modulos, 'total'));
                    $totalConcluid = array_sum(array_column($modulos, 'concluidas'));
                    ?>
                    <?= $totalConcluid ?> de <?= $totalAulas ?> aulas concluídas
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Materiais -->
        <?php if ($materiais): ?>
        <div class="card mb-16">
            <div class="card-header">
                <span class="card-title"><i class="fa-solid fa-paperclip"></i> Materiais</span>
            </div>
            <?php foreach ($materiais as $mat): ?>
            <?php
                $ext  = strtolower(pathinfo($mat['nome_original'], PATHINFO_EXTENSION));
                $icon = match($ext) {
                    'pdf'  => ['fa-file-pdf',  'var(--verm-500)'],
                    'doc','docx' => ['fa-file-word', '#2d5fbd'],
                    'ppt','pptx' => ['fa-file-powerpoint', '#c55a11'],
                    'xls','xlsx' => ['fa-file-excel', '#1f7e45'],
                    'zip'  => ['fa-file-zipper', 'var(--c500)'],
                    default=> ['fa-file', 'var(--c500)'],
                };
            ?>
            <div style="display:flex;align-items:center;gap:10px;padding:10px 16px;border-bottom:1px solid var(--c100)">
                <i class="fa-solid <?= $icon[0] ?>" style="color:<?= $icon[1] ?>;font-size:.9rem;flex-shrink:0"></i>
                <div style="flex:1;min-width:0">
                    <div style="font-size:.8rem;font-weight:500;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;color:var(--c800)">
                        <?= e($mat['nome_original']) ?>
                    </div>
                    <div class="text-xs text-muted"><?= fmtTamanho($mat['tamanho']) ?></div>
                </div>
                <a href="<?= BASE_URL ?>/aluno/download-material.php?id=<?= $mat['material_id'] ?>"
                   class="btn btn-ghost btn-icon btn-sm" title="Baixar">
                    <i class="fa-solid fa-download"></i>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Informações do curso (presencial) -->
        <?php if ($matricula['modalidade'] !== 'EAD' && $matricula['local_nome']): ?>
        <div class="card">
            <div class="card-header">
                <span class="card-title"><i class="fa-solid fa-location-dot"></i> Local</span>
            </div>
            <div class="card-body" style="font-size:.82rem;color:var(--c600)">
                <div style="font-weight:600;color:var(--c800);margin-bottom:4px"><?= e($matricula['local_nome']) ?></div>
                <?php if ($matricula['local_endereco']): ?>
                <div><?= e($matricula['local_endereco']) ?></div>
                <?php endif; ?>
                <div><?= e($matricula['local_cidade'] ?? '') ?><?= $matricula['local_uf'] ? '/'.$matricula['local_uf'] : '' ?></div>
                <?php if ($matricula['horario']): ?>
                <div class="mt-8"><i class="fa-solid fa-clock"></i> <?= e($matricula['horario']) ?></div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
function toggleAccordion(item) {
    item.classList.toggle('aberto');
}
</script>

<?php require_once __DIR__ . '/../includes/layout_aluno_footer.php'; ?>

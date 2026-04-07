<?php
/**
 * admin/cursos/form.php — Cadastro e Edição de Curso
 * Organizado em 4 abas: Informações Gerais | Conteúdo EAD | Avaliação | Certificado
 */
require_once __DIR__ . '/../../includes/bootstrap.php';
Auth::requireAdmin();

$db = Database::getInstance();

$id       = (int)($_GET['id'] ?? 0);
$editando = $id > 0;
$c        = [];
$erros    = [];
$modulos  = [];
$avaliacao = null;
$questoes  = [];
$materiais = [];

// ── Dados para selects ────────────────────────────────────
$categorias  = $db->fetchAll("SELECT categoria_id, nome, cor_hex FROM tbl_categorias WHERE ativo=1 ORDER BY ordem");
$instrutores = $db->fetchAll("SELECT instrutor_id, nome, titulo FROM tbl_instrutores WHERE ativo=1 ORDER BY nome");

// ── Carrega dados do curso ao editar ──────────────────────
if ($editando) {
    $c = $db->fetchOne("SELECT * FROM tbl_cursos WHERE curso_id = ?", [$id]);
    if (!$c) {
        flash('Curso não encontrado.', 'erro');
        header('Location: ' . BASE_URL . '/admin/cursos/lista.php');
        exit;
    }

    // Módulos e aulas (EAD)
    $rawMods = $db->fetchAll("SELECT * FROM tbl_modulos WHERE curso_id=? ORDER BY ordem", [$id]);
    foreach ($rawMods as $mod) {
        $aulas     = $db->fetchAll("SELECT * FROM tbl_aulas WHERE modulo_id=? AND ativo=1 ORDER BY ordem", [$mod['modulo_id']]);
        $modulos[] = array_merge($mod, ['aulas' => $aulas]);
    }

    // Materiais do curso (gerais)
    $materiais = $db->fetchAll(
        "SELECT * FROM tbl_materiais WHERE curso_id=? AND (modulo_id IS NULL OR modulo_id=0) ORDER BY criado_em",
        [$id]
    );

    // Avaliação
    $avaliacao = $db->fetchOne(
        "SELECT * FROM tbl_avaliacoes WHERE curso_id=? AND (modulo_id IS NULL OR modulo_id=0) AND ativo=1 LIMIT 1",
        [$id]
    );
    if ($avaliacao) {
        $rows = $db->fetchAll(
            "SELECT q.*, a.alternativa_id, a.texto AS alt_txt, a.correta, a.ordem AS alt_ord
             FROM tbl_questoes q
             INNER JOIN tbl_alternativas a ON a.questao_id = q.questao_id
             WHERE q.avaliacao_id = ? AND q.ativo = 1
             ORDER BY q.ordem, a.ordem",
            [$avaliacao['avaliacao_id']]
        );
        foreach ($rows as $r) {
            $qid = $r['questao_id'];
            if (!isset($questoes[$qid])) {
                $questoes[$qid] = ['questao_id'=>$qid,'enunciado'=>$r['enunciado'],'pontos'=>$r['pontos'],'alts'=>[],'correta'=>null];
            }
            $questoes[$qid]['alts'][] = ['alternativa_id'=>$r['alternativa_id'],'texto'=>$r['alt_txt'],'correta'=>(bool)$r['correta']];
            if ($r['correta']) $questoes[$qid]['correta'] = count($questoes[$qid]['alts']) - 1;
        }
        $questoes = array_values($questoes);
    }
}

// ════════════════════════════════════════════════════════════
//  PROCESSAMENTO DO POST
// ════════════════════════════════════════════════════════════
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Auth::verifyCsrf();
    $acao = $_POST['_acao'] ?? 'salvar_curso';

    /* ── ABA 1: Salvar dados gerais ─────────────────────── */
    if ($acao === 'salvar_curso') {

        $campos = [
            'categoria_id'   => (int)($_POST['categoria_id'] ?? 0) ?: null,
            'instrutor_id'   => (int)($_POST['instrutor_id'] ?? 0) ?: null,
            'titulo'         => trim($_POST['titulo']         ?? ''),
            'descricao'      => trim($_POST['descricao']      ?? ''),
            'tipo'           => $_POST['tipo']       ?? 'CURSO',
            'modalidade'     => $_POST['modalidade'] ?? 'PRESENCIAL',
            'carga_horaria'  => (float)str_replace(',', '.', $_POST['carga_horaria'] ?? '0'),
            'vagas'          => (int)($_POST['vagas'] ?? 0) ?: null,
            'data_inicio'    => trim($_POST['data_inicio']    ?? '') ?: null,
            'data_fim'       => trim($_POST['data_fim']       ?? '') ?: null,
            'horario'        => trim($_POST['horario']        ?? ''),
            'local_nome'     => trim($_POST['local_nome']     ?? ''),
            'local_cidade'   => trim($_POST['local_cidade']   ?? ''),
            'local_uf'       => strtoupper(trim($_POST['local_uf'] ?? 'TO')),
            'local_endereco' => trim($_POST['local_endereco'] ?? ''),
            'link_ead'       => trim($_POST['link_ead']       ?? ''),
            'youtube_id'     => trim($_POST['youtube_id']     ?? ''),
            'valor'          => (float)str_replace(',', '.', $_POST['valor'] ?? '0'),
            'status'         => $_POST['status'] ?? 'RASCUNHO',
            'observacoes'    => trim($_POST['observacoes']    ?? ''),
            'requer_avaliacao'   => isset($_POST['requer_avaliacao'])   ? 1 : 0,
            'avaliacao_com_nota' => isset($_POST['avaliacao_com_nota']) ? 1 : 0,
            'nota_minima'        => (float)str_replace(',', '.', $_POST['nota_minima'] ?? '70'),
            'tentativas_maximas' => (int)($_POST['tentativas_maximas'] ?? 3),
            'cert_conteudo_programatico' => $_POST['cert_conteudo_programatico'] ?? '',
            'cert_validade'  => ($v = (int)($_POST['cert_validade'] ?? 0)) > 0 ? $v : null,
            'cert_obs'       => trim($_POST['cert_obs'] ?? ''),
        ];

        // Normaliza YouTube ID
        $yt = $campos['youtube_id'];
        if ($yt && str_contains($yt, 'youtu')) {
            preg_match('/(?:v=|\/embed\/|\/shorts\/|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $yt, $m);
            $campos['youtube_id'] = $m[1] ?? substr($yt, 0, 11);
        }

        // Validações
        if ($campos['titulo'] === '')       $erros[] = 'Título é obrigatório.';
        if ($campos['carga_horaria'] <= 0)  $erros[] = 'Carga horária deve ser maior que zero.';

        // Upload de capa
        $nomeCapa = $c['capa'] ?? null;
        if (!empty($_FILES['capa']['name'])) {
            $up = uploadArquivo($_FILES['capa'], 'capas', ['jpg','jpeg','png','webp'], 5);
            if ($up['ok']) {
                if ($nomeCapa) removeUpload('capas/' . $nomeCapa);
                $nomeCapa = $up['nome'];
            } else {
                $erros[] = $up['erro'];
            }
        }
        $campos['capa'] = $nomeCapa;

        if (empty($erros)) {
            if ($editando) {
                $cols = implode(', ', array_map(fn($k) => "$k=?", array_keys($campos)));
                $db->execute(
                    "UPDATE tbl_cursos SET $cols, atualizado_em=NOW() WHERE curso_id=?",
                    [...array_values($campos), $id]
                );
                flash('Curso atualizado com sucesso!');
            } else {
                $campos['criado_por'] = Auth::id();
                $cols   = implode(', ', array_keys($campos));
                $placeholders = implode(', ', array_fill(0, count($campos), '?'));
                $db->execute(
                    "INSERT INTO tbl_cursos ($cols) VALUES ($placeholders)",
                    array_values($campos)
                );
                $id       = $db->lastInsertId();
                $editando = true;
                flash('Curso criado com sucesso! Agora você pode adicionar conteúdo.');
            }
            header('Location: ' . BASE_URL . '/admin/cursos/form.php?id=' . $id . '&aba=' . ($_POST['proxima_aba'] ?? '1'));
            exit;
        }
    }

    /* ── ABA 2: Salvar módulo/aula EAD ──────────────────── */
    if ($acao === 'salvar_modulo' && $editando) {
        $tituloMod = trim($_POST['modulo_titulo'] ?? '');
        $ordemMod  = (int)($_POST['modulo_ordem'] ?? 1);
        if ($tituloMod) {
            $db->execute(
                "INSERT INTO tbl_modulos (curso_id, titulo, ordem) VALUES (?,?,?)",
                [$id, $tituloMod, $ordemMod]
            );
            flash('Módulo adicionado!');
        }
        header('Location: ' . BASE_URL . '/admin/cursos/form.php?id=' . $id . '&aba=2');
        exit;
    }

    if ($acao === 'salvar_aula' && $editando) {
        $moduloId  = (int)($_POST['modulo_id']    ?? 0);
        $tituloAula= trim($_POST['aula_titulo']   ?? '');
        $youtubeId = trim($_POST['aula_youtube']  ?? '');
        $linkExt   = trim($_POST['aula_link']     ?? '');
        $duracao   = (int)($_POST['aula_duracao'] ?? 0) ?: null;
        $ordem     = (int)($_POST['aula_ordem']   ?? 1);

        if ($moduloId && $tituloAula) {
            // Normaliza YouTube
            if ($youtubeId && str_contains($youtubeId, 'youtu')) {
                preg_match('/(?:v=|\/embed\/|\/shorts\/|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $youtubeId, $m);
                $youtubeId = $m[1] ?? substr($youtubeId, 0, 11);
            }
            $db->execute(
                "INSERT INTO tbl_aulas (modulo_id, titulo, youtube_id, link_externo, duracao_min, ordem) VALUES (?,?,?,?,?,?)",
                [$moduloId, $tituloAula, $youtubeId ?: null, $linkExt ?: null, $duracao, $ordem]
            );
            flash('Aula adicionada!');
        }
        header('Location: ' . BASE_URL . '/admin/cursos/form.php?id=' . $id . '&aba=2');
        exit;
    }

    if ($acao === 'upload_material' && $editando) {
        if (!empty($_FILES['material']['name'])) {
            $up = uploadArquivo($_FILES['material'], 'materiais', ['pdf','doc','docx','ppt','pptx','xls','xlsx','zip'], 20);
            if ($up['ok']) {
                $titulo = trim($_POST['material_titulo'] ?? '') ?: $_FILES['material']['name'];
                $db->execute(
                    "INSERT INTO tbl_materiais (curso_id, nome_arquivo, nome_original, tamanho, tipo_mime, criado_por)
                     VALUES (?,?,?,?,?,?)",
                    [$id, $up['nome'], $_FILES['material']['name'], $_FILES['material']['size'],
                     $_FILES['material']['type'], Auth::id()]
                );
                flash('Material enviado!');
            } else {
                flash($up['erro'], 'erro');
            }
        }
        header('Location: ' . BASE_URL . '/admin/cursos/form.php?id=' . $id . '&aba=2');
        exit;
    }
}

// Aba ativa
$abaAtiva = (int)($_GET['aba'] ?? 1);
if (!$editando) $abaAtiva = 1; // Só mostra aba 1 em modo criação

$pageTitulo  = $editando ? 'Editar Curso' : 'Novo Curso';
$paginaAtiva = 'cursos';
$breadcrumb  = ['Cursos' => BASE_URL . '/admin/cursos/lista.php', ($editando ? 'Editar' : 'Novo') => null];
require_once __DIR__ . '/../../includes/layout_admin_header.php';
?>

<!-- ══ Page Header ══════════════════════════════════════════ -->
<div class="page-header">
    <div class="page-header-info">
        <h1 class="page-title"><?= $editando ? 'Editar Curso' : 'Novo Curso' ?></h1>
        <?php if ($editando): ?>
        <p class="page-subtitle"><?= e(trunca($c['titulo'], 60)) ?></p>
        <?php endif; ?>
    </div>
    <div class="page-actions">
        <a href="<?= BASE_URL ?>/admin/cursos/lista.php" class="btn btn-ghost btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Voltar
        </a>
        <?php if ($editando): ?>
        <a href="<?= BASE_URL ?>/admin/matriculas/lista.php?curso_id=<?= $id ?>"
           class="btn btn-ghost btn-sm">
            <i class="fa-solid fa-users"></i> Matrículas
        </a>
        <?php endif; ?>
    </div>
</div>

<?php if ($erros): ?>
<div class="alert-erros">
    <strong><i class="fa-solid fa-circle-xmark"></i> Corrija os erros abaixo:</strong>
    <ul><?php foreach ($erros as $e_msg): ?><li><?= e($e_msg) ?></li><?php endforeach; ?></ul>
</div>
<?php endif; ?>

<!-- ══ Abas de navegação ═════════════════════════════════════ -->
<div class="tabs-bar">
    <button class="tab-btn <?= $abaAtiva===1?'ativo':'' ?>" onclick="irAba(1)">
        <i class="fa-solid fa-circle-info"></i> Informações Gerais
    </button>
    <?php if ($editando): ?>
    <button class="tab-btn <?= $abaAtiva===2?'ativo':'' ?>" onclick="irAba(2)">
        <i class="fa-solid fa-play-circle"></i> Conteúdo EAD
        <?php if ($modulos): ?>
        <span class="badge badge-azul"><?= count($modulos) ?></span>
        <?php endif; ?>
    </button>
    <button class="tab-btn <?= $abaAtiva===3?'ativo':'' ?>" onclick="irAba(3)">
        <i class="fa-solid fa-clipboard-check"></i> Avaliação
    </button>
    <button class="tab-btn <?= $abaAtiva===4?'ativo':'' ?>" onclick="irAba(4)">
        <i class="fa-solid fa-certificate"></i> Certificado
    </button>
    <?php else: ?>
    <span class="tab-btn" style="opacity:.4;cursor:default">
        <i class="fa-solid fa-lock"></i> Conteúdo EAD
    </span>
    <span class="tab-btn" style="opacity:.4;cursor:default">
        <i class="fa-solid fa-lock"></i> Avaliação
    </span>
    <span class="tab-btn" style="opacity:.4;cursor:default">
        <i class="fa-solid fa-lock"></i> Certificado
    </span>
    <?php endif; ?>
</div>

<!-- ══════════════════════════════════════════════════════════
     ABA 1 — INFORMAÇÕES GERAIS
     ══════════════════════════════════════════════════════════ -->
<div class="tab-panel <?= $abaAtiva===1?'ativo':'' ?>" id="aba-1">
<form method="POST" enctype="multipart/form-data">
    <?= csrfField() ?>
    <input type="hidden" name="_acao" value="salvar_curso">
    <input type="hidden" name="proxima_aba" value="2">

    <div style="display:grid;grid-template-columns:1fr 300px;gap:20px;align-items:start">

        <!-- Coluna principal -->
        <div style="display:flex;flex-direction:column;gap:16px">

            <!-- Identificação básica -->
            <div class="card">
                <div class="card-header">
                    <span class="card-title"><i class="fa-solid fa-pen-to-square"></i> Identificação</span>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">Título <span class="req">*</span></label>
                        <input type="text" name="titulo" class="form-control"
                               value="<?= e($c['titulo'] ?? '') ?>"
                               placeholder="Ex: Workshop de Cirurgia Ortopédica" required>
                    </div>

                    <div class="form-row form-row-3">
                        <div class="form-group">
                            <label class="form-label">Tipo <span class="req">*</span></label>
                            <select name="tipo" class="form-control" required>
                                <?php foreach (['CURSO','PALESTRA','WORKSHOP','CONGRESSO','WEBINAR'] as $t): ?>
                                <option value="<?= $t ?>" <?= ($c['tipo']??'')===$t?'selected':'' ?>><?= $t ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Modalidade <span class="req">*</span></label>
                            <select name="modalidade" id="selectModalidade" class="form-control" required>
                                <option value="PRESENCIAL" <?= ($c['modalidade']??'')==='PRESENCIAL'?'selected':'' ?>>Presencial</option>
                                <option value="EAD"        <?= ($c['modalidade']??'')==='EAD'       ?'selected':'' ?>>EAD (Online)</option>
                                <option value="HIBRIDO"    <?= ($c['modalidade']??'')==='HIBRIDO'   ?'selected':'' ?>>Híbrido</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-control">
                                <option value="RASCUNHO"  <?= ($c['status']??'')==='RASCUNHO' ?'selected':'' ?>>Rascunho</option>
                                <option value="PUBLICADO" <?= ($c['status']??'')==='PUBLICADO'?'selected':'' ?>>Publicado</option>
                                <option value="ENCERRADO" <?= ($c['status']??'')==='ENCERRADO'?'selected':'' ?>>Encerrado</option>
                                <option value="CANCELADO" <?= ($c['status']??'')==='CANCELADO'?'selected':'' ?>>Cancelado</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row form-row-2">
                        <div class="form-group">
                            <label class="form-label">Categoria</label>
                            <select name="categoria_id" class="form-control">
                                <option value="">Sem categoria</option>
                                <?php foreach ($categorias as $cat): ?>
                                <option value="<?= $cat['categoria_id'] ?>"
                                        <?= ($c['categoria_id']??0)==$cat['categoria_id']?'selected':'' ?>>
                                    <?= e($cat['nome']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Instrutor Principal</label>
                            <select name="instrutor_id" class="form-control">
                                <option value="">Sem instrutor</option>
                                <?php foreach ($instrutores as $inst): ?>
                                <option value="<?= $inst['instrutor_id'] ?>"
                                        <?= ($c['instrutor_id']??0)==$inst['instrutor_id']?'selected':'' ?>>
                                    <?= e($inst['nome']) ?>
                                    <?= $inst['titulo'] ? '— '.$inst['titulo'] : '' ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Descrição</label>
                        <textarea name="descricao" class="form-control" rows="4"
                                  placeholder="Descreva o curso, objetivos e público-alvo..."><?= e($c['descricao'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Datas e Local (dinâmico conforme modalidade) -->
            <div class="card" id="secaoPresencial" style="<?= ($c['modalidade']??'PRESENCIAL')==='EAD' ? 'display:none' : '' ?>">
                <div class="card-header">
                    <span class="card-title"><i class="fa-solid fa-calendar-days"></i> Datas e Local</span>
                </div>
                <div class="card-body">
                    <div class="form-row form-row-3">
                        <div class="form-group">
                            <label class="form-label">Data Início</label>
                            <input type="date" name="data_inicio" class="form-control"
                                   value="<?= e($c['data_inicio'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Data Fim</label>
                            <input type="date" name="data_fim" class="form-control"
                                   value="<?= e($c['data_fim'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Horário</label>
                            <input type="text" name="horario" class="form-control"
                                   value="<?= e($c['horario'] ?? '') ?>"
                                   placeholder="Ex: 08h às 18h">
                        </div>
                    </div>
                    <div class="form-row form-row-2">
                        <div class="form-group">
                            <label class="form-label">Nome do Local</label>
                            <input type="text" name="local_nome" class="form-control"
                                   value="<?= e($c['local_nome'] ?? '') ?>"
                                   placeholder="Ex: Auditório do CRMV/TO">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Endereço</label>
                            <input type="text" name="local_endereco" class="form-control"
                                   value="<?= e($c['local_endereco'] ?? '') ?>">
                        </div>
                    </div>
                    <div class="form-row form-row-3">
                        <div class="form-group" style="grid-column:1/3">
                            <label class="form-label">Cidade</label>
                            <input type="text" name="local_cidade" class="form-control"
                                   value="<?= e($c['local_cidade'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">UF</label>
                            <input type="text" name="local_uf" class="form-control" maxlength="2"
                                   value="<?= e($c['local_uf'] ?? 'TO') ?>">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Link EAD -->
            <div class="card" id="secaoEAD" style="<?= ($c['modalidade']??'PRESENCIAL')!=='EAD' && ($c['modalidade']??'PRESENCIAL')!=='HIBRIDO' ? 'display:none' : '' ?>">
                <div class="card-header">
                    <span class="card-title"><i class="fa-solid fa-link"></i> Link EAD</span>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">Link da plataforma EAD</label>
                        <input type="url" name="link_ead" class="form-control"
                               value="<?= e($c['link_ead'] ?? '') ?>"
                               placeholder="https://plataforma.ead.com.br/curso/xxx">
                        <span class="form-hint">URL para acesso ao curso em plataforma externa</span>
                    </div>
                    <div class="form-group">
                        <label class="form-label">ID do Vídeo YouTube (apresentação)</label>
                        <input type="text" name="youtube_id" class="form-control"
                               value="<?= e($c['youtube_id'] ?? '') ?>"
                               placeholder="Cole a URL completa ou apenas o ID do vídeo">
                        <span class="form-hint">Será exibido como vídeo de apresentação do curso</span>
                    </div>
                </div>
            </div>

            <!-- Configurações de avaliação -->
            <div class="card">
                <div class="card-header">
                    <span class="card-title"><i class="fa-solid fa-clipboard-check"></i> Avaliação e Aprovação</span>
                </div>
                <div class="card-body">
                    <div style="display:flex;flex-direction:column;gap:12px">
                        <label class="check-item">
                            <input type="checkbox" name="requer_avaliacao" id="chkAvaliacao"
                                   <?= !empty($c['requer_avaliacao']) ? 'checked' : '' ?>>
                            <span class="check-label">
                                Requer avaliação para conclusão
                                <small>O aluno precisa responder uma prova ao final do curso</small>
                            </span>
                        </label>
                        <div id="blocoNota" style="<?= empty($c['requer_avaliacao']) ? 'display:none' : '' ?>;padding-left:24px">
                            <label class="check-item" style="margin-bottom:12px">
                                <input type="checkbox" name="avaliacao_com_nota"
                                       <?= !empty($c['avaliacao_com_nota']) ? 'checked' : '' ?>>
                                <span class="check-label">
                                    Avaliação com nota mínima de aprovação
                                </span>
                            </label>
                            <div class="form-row form-row-2" style="max-width:300px">
                                <div class="form-group">
                                    <label class="form-label">Nota mínima (0–100)</label>
                                    <input type="number" name="nota_minima" class="form-control"
                                           min="0" max="100" step="0.5"
                                           value="<?= e($c['nota_minima'] ?? 70) ?>">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Tentativas máximas</label>
                                    <input type="number" name="tentativas_maximas" class="form-control"
                                           min="1" max="99"
                                           value="<?= e($c['tentativas_maximas'] ?? 3) ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Observações internas -->
            <div class="card">
                <div class="card-header">
                    <span class="card-title"><i class="fa-solid fa-note-sticky"></i> Observações Internas</span>
                </div>
                <div class="card-body">
                    <textarea name="observacoes" class="form-control" rows="3"
                              placeholder="Notas internas (não visíveis ao aluno)..."><?= e($c['observacoes'] ?? '') ?></textarea>
                </div>
            </div>
        </div>

        <!-- Coluna lateral -->
        <div style="display:flex;flex-direction:column;gap:16px">

            <!-- Carga e vagas -->
            <div class="card">
                <div class="card-header">
                    <span class="card-title"><i class="fa-solid fa-sliders"></i> Configurações</span>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">Carga Horária (h) <span class="req">*</span></label>
                        <input type="number" name="carga_horaria" class="form-control"
                               min="0.5" step="0.5" required
                               value="<?= e($c['carga_horaria'] ?? '') ?>"
                               placeholder="Ex: 8">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Vagas</label>
                        <input type="number" name="vagas" class="form-control"
                               min="0"
                               value="<?= e($c['vagas'] ?? '') ?>"
                               placeholder="Deixe vazio para ilimitado">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Valor (R$)</label>
                        <input type="number" name="valor" class="form-control"
                               min="0" step="0.01"
                               value="<?= e($c['valor'] ?? '0') ?>"
                               placeholder="0,00 para gratuito">
                        <span class="form-hint">0 = gratuito</span>
                    </div>
                </div>
            </div>

            <!-- Capa -->
            <div class="card">
                <div class="card-header">
                    <span class="card-title"><i class="fa-solid fa-image"></i> Capa do Curso</span>
                </div>
                <div class="card-body">
                    <?php if (!empty($c['capa'])): ?>
                    <div style="margin-bottom:10px;border-radius:var(--radius);overflow:hidden;border:1px solid var(--border)">
                        <img src="<?= BASE_URL ?>/uploads/capas/<?= e($c['capa']) ?>"
                             alt="Capa" style="width:100%;height:130px;object-fit:cover">
                    </div>
                    <?php endif; ?>
                    <input type="file" name="capa" class="form-control"
                           accept=".jpg,.jpeg,.png,.webp">
                    <span class="form-hint">JPG, PNG ou WEBP — máx. 5MB</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Botões de ação -->
    <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:20px;padding-top:16px;border-top:1px solid var(--border)">
        <a href="<?= BASE_URL ?>/admin/cursos/lista.php" class="btn btn-ghost">Cancelar</a>
        <button type="submit" name="proxima_aba" value="1" class="btn btn-ghost">
            <i class="fa-solid fa-floppy-disk"></i> Salvar
        </button>
        <?php if (!$editando): ?>
        <button type="submit" name="proxima_aba" value="2" class="btn btn-primario">
            Salvar e continuar <i class="fa-solid fa-arrow-right"></i>
        </button>
        <?php else: ?>
        <button type="submit" class="btn btn-primario">
            <i class="fa-solid fa-floppy-disk"></i> Salvar Alterações
        </button>
        <?php endif; ?>
    </div>
</form>
</div>

<!-- ══════════════════════════════════════════════════════════
     ABA 2 — CONTEÚDO EAD (módulos, aulas, materiais)
     ══════════════════════════════════════════════════════════ -->
<?php if ($editando): ?>
<div class="tab-panel <?= $abaAtiva===2?'ativo':'' ?>" id="aba-2">
    <div style="display:grid;grid-template-columns:1fr 320px;gap:20px;align-items:start">

        <!-- Módulos e aulas -->
        <div>
            <div class="flex-between mb-16">
                <h2 class="section-title" style="margin:0">
                    <i class="fa-solid fa-layer-group" style="color:var(--azul-600)"></i>
                    Módulos e Aulas
                </h2>
                <button class="btn btn-primario btn-sm" onclick="abrirModalModulo()">
                    <i class="fa-solid fa-plus"></i> Novo Módulo
                </button>
            </div>

            <?php if ($modulos): ?>
            <div class="accordion">
                <?php foreach ($modulos as $mod): ?>
                <div class="accordion-item <?= count($mod['aulas']) > 0 ? 'aberto' : '' ?>">
                    <div class="accordion-header" onclick="toggleAccordion(this.parentElement)">
                        <i class="fa-solid fa-folder" style="color:var(--ouro-400)"></i>
                        <span style="flex:1"><?= e($mod['titulo']) ?></span>
                        <span class="badge badge-cinza"><?= count($mod['aulas']) ?> aula(s)</span>
                        <button type="button"
                                onclick="event.stopPropagation();abrirModalAula(<?= $mod['modulo_id'] ?>, '<?= e(addslashes($mod['titulo'])) ?>')"
                                class="btn btn-ghost btn-icon btn-sm" title="Adicionar aula">
                            <i class="fa-solid fa-plus"></i>
                        </button>
                        <i class="fa-solid fa-chevron-down acc-toggle"></i>
                    </div>
                    <div class="accordion-body">
                        <?php foreach ($mod['aulas'] as $aula): ?>
                        <div class="aula-item">
                            <div class="aula-item-icon <?= $aula['youtube_id'] ? 'aula-icon-video' : 'aula-icon-link' ?>">
                                <i class="fa-solid fa-<?= $aula['youtube_id'] ? 'play' : 'link' ?>"></i>
                            </div>
                            <div class="aula-item-info">
                                <div class="aula-item-titulo"><?= e($aula['titulo']) ?></div>
                                <div class="aula-item-meta">
                                    <?php if ($aula['duracao_min']): ?>
                                    <i class="fa-solid fa-clock"></i> <?= $aula['duracao_min'] ?>min
                                    <?php endif; ?>
                                    <?php if ($aula['youtube_id']): ?>
                                    · <i class="fa-brands fa-youtube"></i> YouTube
                                    <?php elseif ($aula['link_externo']): ?>
                                    · <i class="fa-solid fa-external-link"></i> Link externo
                                    <?php endif; ?>
                                </div>
                            </div>
                            <a href="<?= BASE_URL ?>/admin/cursos/del_aula.php?id=<?= $aula['aula_id'] ?>&curso_id=<?= $id ?>"
                               class="btn btn-ghost btn-icon btn-sm"
                               style="color:var(--verm-500)"
                               onclick="return confirm('Remover aula?')" title="Remover">
                                <i class="fa-solid fa-trash"></i>
                            </a>
                        </div>
                        <?php endforeach; ?>
                        <?php if (!$mod['aulas']): ?>
                        <div style="padding:16px;text-align:center;color:var(--c400);font-size:.8rem">
                            <i class="fa-solid fa-video-slash"></i> Nenhuma aula adicionada.
                            <button class="btn btn-ghost btn-sm" style="margin-left:8px"
                                    onclick="abrirModalAula(<?= $mod['modulo_id'] ?>, '<?= e(addslashes($mod['titulo'])) ?>')">
                                + Adicionar aula
                            </button>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="card">
                <div class="card-body">
                    <div class="empty-state">
                        <i class="fa-solid fa-layer-group"></i>
                        <h3>Nenhum módulo cadastrado</h3>
                        <p>Organize o conteúdo do curso em módulos.<br>Cada módulo pode ter várias aulas.</p>
                        <button class="btn btn-primario btn-sm" style="margin-top:12px"
                                onclick="abrirModalModulo()">
                            <i class="fa-solid fa-plus"></i> Criar primeiro módulo
                        </button>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Materiais complementares -->
        <div>
            <h2 class="section-title">
                <i class="fa-solid fa-paperclip" style="color:var(--azul-600)"></i>
                Materiais Complementares
            </h2>

            <div class="card mb-16">
                <div class="card-header">
                    <span class="card-title">Enviar material</span>
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <?= csrfField() ?>
                        <input type="hidden" name="_acao" value="upload_material">
                        <div class="form-group">
                            <label class="form-label">Título</label>
                            <input type="text" name="material_titulo" class="form-control"
                                   placeholder="Nome do material">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Arquivo</label>
                            <input type="file" name="material" class="form-control"
                                   accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.zip" required>
                            <span class="form-hint">PDF, Word, PPT, Excel ou ZIP — máx. 20MB</span>
                        </div>
                        <button type="submit" class="btn btn-verde btn-sm btn-block">
                            <i class="fa-solid fa-upload"></i> Enviar Material
                        </button>
                    </form>
                </div>
            </div>

            <?php if ($materiais): ?>
            <div class="card">
                <div class="card-header">
                    <span class="card-title">Materiais (<?= count($materiais) ?>)</span>
                </div>
                <?php foreach ($materiais as $mat): ?>
                <div style="display:flex;align-items:center;gap:10px;padding:10px 16px;border-bottom:1px solid var(--c100)">
                    <i class="fa-solid fa-file-pdf" style="color:var(--verm-500);font-size:.9rem"></i>
                    <div style="flex:1;min-width:0">
                        <div style="font-size:.82rem;font-weight:500;color:var(--c800);
                                    white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                            <?= e($mat['nome_original']) ?>
                        </div>
                        <div class="text-xs text-muted"><?= fmtTamanho($mat['tamanho']) ?></div>
                    </div>
                    <a href="<?= BASE_URL ?>/admin/cursos/del_material.php?id=<?= $mat['material_id'] ?>&curso_id=<?= $id ?>"
                       class="btn btn-ghost btn-icon btn-sm" style="color:var(--verm-500)"
                       onclick="return confirm('Remover?')" title="Remover">
                        <i class="fa-solid fa-trash"></i>
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <div style="margin-top:20px;padding-top:16px;border-top:1px solid var(--border);display:flex;gap:10px;justify-content:flex-end">
        <button onclick="irAba(1)" class="btn btn-ghost">
            <i class="fa-solid fa-arrow-left"></i> Voltar
        </button>
        <button onclick="irAba(3)" class="btn btn-primario">
            Ir para Avaliação <i class="fa-solid fa-arrow-right"></i>
        </button>
    </div>
</div>

<!-- ══════════════════════════════════════════════════════════
     ABA 3 — AVALIAÇÃO
     ══════════════════════════════════════════════════════════ -->
<div class="tab-panel <?= $abaAtiva===3?'ativo':'' ?>" id="aba-3">
    <div class="card">
        <div class="card-header">
            <span class="card-title">
                <i class="fa-solid fa-clipboard-check"></i> Configuração da Avaliação
            </span>
        </div>
        <div class="card-body">
            <?php if ($avaliacao): ?>
            <!-- Avaliação existente: mostra questões -->
            <div class="flex-between mb-16">
                <div>
                    <strong><?= e($avaliacao['titulo']) ?></strong>
                    <span class="badge badge-verde" style="margin-left:8px"><?= $avaliacao['tipo'] ?></span>
                    <div class="text-xs text-muted mt-4">
                        Nota mínima: <?= $avaliacao['nota_minima'] ?> &nbsp;·&nbsp;
                        Tentativas: <?= $avaliacao['tentativas_max'] ?>
                    </div>
                </div>
                <a href="<?= BASE_URL ?>/admin/avaliacoes/form.php?id=<?= $avaliacao['avaliacao_id'] ?>&curso_id=<?= $id ?>"
                   class="btn btn-ghost btn-sm">
                    <i class="fa-solid fa-pen"></i> Editar avaliação
                </a>
            </div>

            <div style="font-weight:600;font-size:.84rem;color:var(--c700);margin-bottom:10px">
                <?= count($questoes) ?> questão(ões) cadastrada(s)
            </div>
            <?php foreach ($questoes as $i => $q): ?>
            <div style="background:var(--c50);border:1px solid var(--border);
                        border-radius:var(--radius-md);padding:14px 16px;margin-bottom:10px">
                <div style="font-weight:500;font-size:.84rem;margin-bottom:8px">
                    <?= ($i+1) ?>. <?= e($q['enunciado']) ?>
                    <span class="text-xs text-muted">(<?= $q['pontos'] ?> pts)</span>
                </div>
                <?php foreach ($q['alts'] as $ai => $alt): ?>
                <div style="padding:4px 8px;font-size:.8rem;border-radius:5px;
                            background:<?= $alt['correta'] ? 'var(--verde-50)':'transparent' ?>;
                            color:<?= $alt['correta'] ? 'var(--verde-600)':'var(--c600)' ?>">
                    <?= chr(65+$ai) ?>. <?= e($alt['texto']) ?>
                    <?php if ($alt['correta']): ?>
                    <i class="fa-solid fa-check" style="margin-left:4px"></i>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endforeach; ?>

            <?php else: ?>
            <div class="empty-state">
                <i class="fa-solid fa-clipboard"></i>
                <h3>Nenhuma avaliação configurada</h3>
                <p>Crie uma prova para que os alunos respondam ao final do curso.</p>
                <a href="<?= BASE_URL ?>/admin/avaliacoes/form.php?curso_id=<?= $id ?>"
                   class="btn btn-primario btn-sm" style="margin-top:12px">
                    <i class="fa-solid fa-plus"></i> Criar Avaliação
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <div style="margin-top:20px;padding-top:16px;border-top:1px solid var(--border);display:flex;gap:10px;justify-content:flex-end">
        <button onclick="irAba(2)" class="btn btn-ghost"><i class="fa-solid fa-arrow-left"></i> Voltar</button>
        <button onclick="irAba(4)" class="btn btn-primario">Ir para Certificado <i class="fa-solid fa-arrow-right"></i></button>
    </div>
</div>

<!-- ══════════════════════════════════════════════════════════
     ABA 4 — CERTIFICADO
     ══════════════════════════════════════════════════════════ -->
<div class="tab-panel <?= $abaAtiva===4?'ativo':'' ?>" id="aba-4">
<form method="POST">
    <?= csrfField() ?>
    <input type="hidden" name="_acao" value="salvar_curso">

    <div class="card">
        <div class="card-header">
            <span class="card-title"><i class="fa-solid fa-certificate"></i> Configurações do Certificado</span>
        </div>
        <div class="card-body">
            <div class="form-row form-row-2">
                <div class="form-group">
                    <label class="form-label">Validade do Certificado (anos)</label>
                    <input type="number" name="cert_validade" class="form-control"
                           min="1" max="99"
                           value="<?= e($c['cert_validade'] ?? '') ?>"
                           placeholder="Ex: 5">
                    <span class="form-hint">Deixe em branco para sem validade</span>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Conteúdo Programático (verso do certificado)</label>
                <textarea name="cert_conteudo_programatico" class="form-control" rows="6"
                          placeholder="Liste os tópicos abordados no curso..."><?= e($c['cert_conteudo_programatico'] ?? '') ?></textarea>
                <span class="form-hint">Aceita HTML básico para formatação</span>
            </div>
            <div class="form-group">
                <label class="form-label">Observações do Certificado (uso interno)</label>
                <textarea name="cert_obs" class="form-control" rows="3"
                          placeholder="Notas internas sobre emissão..."><?= e($c['cert_obs'] ?? '') ?></textarea>
            </div>

            <!-- Campos ocultos para não perder os dados da aba 1 -->
            <input type="hidden" name="titulo"        value="<?= e($c['titulo'] ?? '') ?>">
            <input type="hidden" name="tipo"          value="<?= e($c['tipo'] ?? 'CURSO') ?>">
            <input type="hidden" name="modalidade"    value="<?= e($c['modalidade'] ?? 'PRESENCIAL') ?>">
            <input type="hidden" name="carga_horaria" value="<?= e($c['carga_horaria'] ?? 0) ?>">
            <input type="hidden" name="status"        value="<?= e($c['status'] ?? 'RASCUNHO') ?>">
        </div>
    </div>

    <div style="margin-top:20px;padding-top:16px;border-top:1px solid var(--border);display:flex;gap:10px;justify-content:space-between">
        <button onclick="irAba(3)" type="button" class="btn btn-ghost">
            <i class="fa-solid fa-arrow-left"></i> Voltar
        </button>
        <div style="display:flex;gap:8px">
            <a href="<?= BASE_URL ?>/admin/certificados/emitir.php?curso_id=<?= $id ?>"
               class="btn btn-ouro btn-sm">
                <i class="fa-solid fa-certificate"></i> Ir para Emissão
            </a>
            <button type="submit" class="btn btn-verde">
                <i class="fa-solid fa-floppy-disk"></i> Salvar Configurações
            </button>
        </div>
    </div>
</form>
</div>
<?php endif; ?>

<!-- ── Modais inline ───────────────────────────────────────── -->

<!-- Modal: Novo Módulo -->
<div class="modal-overlay" id="modalModulo">
    <div class="modal" style="max-width:440px">
        <div class="modal-header">
            <span class="modal-title"><i class="fa-solid fa-folder-plus"></i> Novo Módulo</span>
            <button class="modal-close" onclick="document.getElementById('modalModulo').classList.remove('aberto')">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form method="POST">
            <?= csrfField() ?>
            <input type="hidden" name="_acao" value="salvar_modulo">
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Título do Módulo <span class="req">*</span></label>
                    <input type="text" name="modulo_titulo" class="form-control" required
                           placeholder="Ex: Módulo 1 — Introdução">
                </div>
                <div class="form-group">
                    <label class="form-label">Ordem</label>
                    <input type="number" name="modulo_ordem" class="form-control"
                           min="1" value="<?= count($modulos) + 1 ?>">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-ghost"
                        onclick="document.getElementById('modalModulo').classList.remove('aberto')">
                    Cancelar
                </button>
                <button type="submit" class="btn btn-primario">
                    <i class="fa-solid fa-check"></i> Salvar Módulo
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Nova Aula -->
<div class="modal-overlay" id="modalAula">
    <div class="modal">
        <div class="modal-header">
            <span class="modal-title"><i class="fa-solid fa-play-circle"></i> Nova Aula em <span id="moduloNomeModal"></span></span>
            <button class="modal-close" onclick="document.getElementById('modalAula').classList.remove('aberto')">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form method="POST">
            <?= csrfField() ?>
            <input type="hidden" name="_acao" value="salvar_aula">
            <input type="hidden" name="modulo_id" id="modalAulaModuloId">
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Título da Aula <span class="req">*</span></label>
                    <input type="text" name="aula_titulo" class="form-control" required
                           placeholder="Ex: Introdução à Anestesiologia">
                </div>
                <div class="form-row form-row-2">
                    <div class="form-group">
                        <label class="form-label">Duração (minutos)</label>
                        <input type="number" name="aula_duracao" class="form-control" min="1" placeholder="Ex: 45">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Ordem</label>
                        <input type="number" name="aula_ordem" class="form-control" min="1" value="1">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Link YouTube (URL completa ou ID)</label>
                    <input type="text" name="aula_youtube" class="form-control"
                           placeholder="https://youtube.com/watch?v=...">
                </div>
                <div class="form-group">
                    <label class="form-label">Link Externo (alternativo ao YouTube)</label>
                    <input type="url" name="aula_link" class="form-control"
                           placeholder="https://...">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-ghost"
                        onclick="document.getElementById('modalAula').classList.remove('aberto')">
                    Cancelar
                </button>
                <button type="submit" class="btn btn-primario">
                    <i class="fa-solid fa-check"></i> Adicionar Aula
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// ── Navegação de abas ─────────────────────────────────────
function irAba(n) {
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('ativo'));
    document.querySelectorAll('.tab-btn').forEach((b,i) => {
        b.classList.toggle('ativo', i === n - 1);
    });
    const panel = document.getElementById('aba-' + n);
    if (panel) panel.classList.add('ativo');
    window.scrollTo({top: 0, behavior: 'smooth'});
}

// ── Modalidade dinâmica ───────────────────────────────────
document.getElementById('selectModalidade')?.addEventListener('change', function() {
    const isEAD       = this.value === 'EAD';
    const isHibrido   = this.value === 'HIBRIDO';
    const isPresencial = this.value === 'PRESENCIAL';

    document.getElementById('secaoPresencial').style.display = isPresencial || isHibrido ? '' : 'none';
    document.getElementById('secaoEAD').style.display        = isEAD || isHibrido ? '' : 'none';
});

// ── Toggle avaliação ──────────────────────────────────────
document.getElementById('chkAvaliacao')?.addEventListener('change', function() {
    document.getElementById('blocoNota').style.display = this.checked ? '' : 'none';
});

// ── Modais ────────────────────────────────────────────────
function abrirModalModulo() {
    document.getElementById('modalModulo').classList.add('aberto');
}
function abrirModalAula(moduloId, moduloNome) {
    document.getElementById('modalAulaModuloId').value = moduloId;
    document.getElementById('moduloNomeModal').textContent = moduloNome;
    document.getElementById('modalAula').classList.add('aberto');
}

// ── Accordion ─────────────────────────────────────────────
function toggleAccordion(item) {
    item.classList.toggle('aberto');
}

// ── Fechar modal ao clicar fora ───────────────────────────
document.querySelectorAll('.modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', function(e) {
        if (e.target === this) this.classList.remove('aberto');
    });
});
</script>

<?php require_once __DIR__ . '/../../includes/layout_admin_footer.php'; ?>

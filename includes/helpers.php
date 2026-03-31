<?php
/**
 * helpers.php — Funções utilitárias globais
 *
 * Centraliza formatações, mensagens flash, uploads e
 * pequenas funções de apoio usadas em todo o sistema.
 */

// ════════════════════════════════════════════════════════════
//  FORMATAÇÃO
// ════════════════════════════════════════════════════════════

/** Formata data BR: 25/03/2025 */
function fmtData(?string $d): string
{
    if (!$d || $d === '0000-00-00') return '—';
    try { return (new DateTime($d))->format('d/m/Y'); }
    catch (Exception) { return '—'; }
}

/** Formata data+hora BR: 25/03/2025 14:30 */
function fmtDataHora(?string $d): string
{
    if (!$d) return '—';
    try { return (new DateTime($d))->format('d/m/Y H:i'); }
    catch (Exception) { return '—'; }
}

/** Formata CRMV: 12345-TO */
function fmtCRMV(?string $numero, string $uf = 'TO'): string
{
    return $numero ? trim($numero) . '-' . $uf : 'Não informado';
}

/** Formata valor monetário: R$ 1.250,00 */
function fmtMoeda(float|string $valor): string
{
    return 'R$ ' . number_format((float)$valor, 2, ',', '.');
}

/** Formata carga horária: 4h ou 1h30 */
function fmtCarga(float|string $horas): string
{
    $h   = (int) $horas;
    $min = round(((float)$horas - $h) * 60);
    return $min > 0 ? "{$h}h{$min}" : "{$h}h";
}

/** Formata tamanho de arquivo: 1,2 MB */
function fmtTamanho(int $bytes): string
{
    if ($bytes >= 1_048_576) return round($bytes / 1_048_576, 1) . ' MB';
    if ($bytes >= 1_024)     return round($bytes / 1_024)        . ' KB';
    return $bytes . ' B';
}

/** Trunca texto com reticências */
function trunca(string $str, int $max): string
{
    return mb_strlen($str) > $max ? mb_substr($str, 0, $max) . '…' : $str;
}

/** Alias legado */
function truncaTexto(string $str, int $max): string { return trunca($str, $max); }

/** Primeira letra maiúscula (avatar) */
function primeiraLetra(string $nome): string
{
    return mb_strtoupper(mb_substr(trim($nome), 0, 1));
}

/** Valor padrão se nulo/vazio */
function nvl(mixed $val, mixed $def = ''): mixed
{
    return ($val === null || $val === '') ? $def : $val;
}

/** Sanitiza saída HTML */
function e(mixed $str): string
{
    return htmlspecialchars((string)$str, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

// ════════════════════════════════════════════════════════════
//  BADGES / STATUS
// ════════════════════════════════════════════════════════════

/** Badge de status de matrícula */
function badgeMatricula(string $status): string
{
    return match($status) {
        'ATIVA'     => '<span class="badge badge-azul"><i class="fa-solid fa-play"></i> Em andamento</span>',
        'CONCLUIDA' => '<span class="badge badge-verde"><i class="fa-solid fa-check"></i> Concluído</span>',
        'CANCELADA' => '<span class="badge badge-verm"><i class="fa-solid fa-ban"></i> Cancelado</span>',
        'REPROVADO' => '<span class="badge badge-verm"><i class="fa-solid fa-xmark"></i> Reprovado</span>',
        default     => '<span class="badge badge-cinza">' . e($status) . '</span>',
    };
}

/** Badge de status de curso */
function badgeCurso(string $status): string
{
    return match($status) {
        'PUBLICADO' => '<span class="badge badge-verde">Publicado</span>',
        'RASCUNHO'  => '<span class="badge badge-cinza">Rascunho</span>',
        'ENCERRADO' => '<span class="badge badge-ouro">Encerrado</span>',
        'CANCELADO' => '<span class="badge badge-verm">Cancelado</span>',
        default     => '<span class="badge badge-cinza">' . e($status) . '</span>',
    };
}

/** Badge de modalidade */
function badgeModalidade(string $mod): string
{
    return match($mod) {
        'EAD'       => '<span class="badge badge-azul"><i class="fa-solid fa-wifi"></i> EAD</span>',
        'PRESENCIAL'=> '<span class="badge badge-verde"><i class="fa-solid fa-map-pin"></i> Presencial</span>',
        'HIBRIDO'   => '<span class="badge badge-ouro"><i class="fa-solid fa-layer-group"></i> Híbrido</span>',
        default     => '<span class="badge badge-cinza">' . e($mod) . '</span>',
    };
}

// ════════════════════════════════════════════════════════════
//  FLASH MESSAGES
// ════════════════════════════════════════════════════════════

/**
 * Grava mensagem flash na sessão.
 * Tipos: sucesso | erro | info | aviso
 */
function flash(string $msg, string $tipo = 'sucesso'): void
{
    $_SESSION['_flash'] = ['msg' => $msg, 'tipo' => $tipo];
}

/** Lê e limpa a mensagem flash */
function getFlash(): array
{
    $f = $_SESSION['_flash'] ?? ['msg' => '', 'tipo' => ''];
    unset($_SESSION['_flash']);
    return $f;
}

/** Renderiza o bloco flash na view */
function renderFlash(): void
{
    $f = getFlash();
    if (!$f['msg']) return;

    $icon = match($f['tipo']) {
        'sucesso' => 'circle-check',
        'erro'    => 'circle-xmark',
        'aviso'   => 'triangle-exclamation',
        default   => 'circle-info',
    };
    echo <<<HTML
    <div class="flash flash-{$f['tipo']}" role="alert">
        <i class="fa-solid fa-{$icon}"></i>
        <span>{$f['msg']}</span>
        <button class="flash-close" onclick="this.parentElement.remove()">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>
    HTML;
}

// ════════════════════════════════════════════════════════════
//  UPLOADS
// ════════════════════════════════════════════════════════════

/**
 * Faz upload de um arquivo com validação.
 *
 * @param  array  $file       Entrada de $_FILES['campo']
 * @param  string $subdir     Subdiretório dentro de /uploads (ex: 'capas')
 * @param  array  $allowed    Extensões permitidas
 * @param  int    $maxMb      Tamanho máximo em MB
 * @return array{ok:bool, path:string|null, nome:string|null, erro:string|null}
 */
function uploadArquivo(
    array  $file,
    string $subdir,
    array  $allowed = ['jpg', 'jpeg', 'png', 'webp', 'pdf'],
    int    $maxMb   = 0
): array {
    if ($maxMb <= 0) $maxMb = MAX_UPLOAD_MB;

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['ok' => false, 'erro' => 'Falha no upload (código ' . $file['error'] . ').'];
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed)) {
        return ['ok' => false, 'erro' => 'Tipo de arquivo não permitido: .' . $ext];
    }

    if ($file['size'] > $maxMb * 1_048_576) {
        return ['ok' => false, 'erro' => "Arquivo muito grande. Máximo: {$maxMb}MB"];
    }

    $dir  = UPLOAD_PATH . '/' . $subdir;
    if (!is_dir($dir)) mkdir($dir, 0755, true);

    $nome = bin2hex(random_bytes(8)) . '_' . time() . '.' . $ext;
    $dest = $dir . '/' . $nome;

    if (!move_uploaded_file($file['tmp_name'], $dest)) {
        return ['ok' => false, 'erro' => 'Não foi possível salvar o arquivo.'];
    }

    return [
        'ok'   => true,
        'nome' => $nome,
        'path' => $subdir . '/' . $nome,
        'erro' => null,
    ];
}

/** Remove arquivo de upload com segurança */
function removeUpload(string $path): void
{
    $full = UPLOAD_PATH . '/' . ltrim($path, '/');
    if (is_file($full)) @unlink($full);
}

/** URL pública de um arquivo de upload */
function uploadUrl(string $path): string
{
    return UPLOAD_URL . '/' . ltrim($path, '/');
}

// ════════════════════════════════════════════════════════════
//  PAGINAÇÃO
// ════════════════════════════════════════════════════════════

/**
 * Retorna parâmetros de paginação.
 * @return array{offset:int, limit:int, page:int}
 */
function paginar(int $limit = 20): array
{
    $page   = max(1, (int)($_GET['p'] ?? 1));
    $offset = ($page - 1) * $limit;
    return compact('page', 'limit', 'offset');
}

/**
 * Renderiza links de paginação.
 */
function renderPaginacao(int $total, int $limit, int $page): void
{
    $pages = (int) ceil($total / $limit);
    if ($pages <= 1) return;

    $query = $_GET;
    echo '<nav class="paginacao">';
    for ($i = 1; $i <= $pages; $i++) {
        $query['p'] = $i;
        $url  = '?' . http_build_query($query);
        $ativo = $i === $page ? ' ativo' : '';
        echo "<a href=\"{$url}\" class=\"pag-btn{$ativo}\">{$i}</a>";
    }
    echo '</nav>';
}

// ════════════════════════════════════════════════════════════
//  CSRF (campo hidden para forms)
// ════════════════════════════════════════════════════════════

function csrfField(): string
{
    $token = Auth::csrfToken();
    return '<input type="hidden" name="' . CSRF_TOKEN_KEY . '" value="' . e($token) . '">';
}

// ════════════════════════════════════════════════════════════
//  PROGRESSO EAD
// ════════════════════════════════════════════════════════════

/** Calcula percentual de conclusão EAD de uma matrícula */
function calcularProgressoEAD(int $cursoId, int $usuarioId): int
{
    $db = Database::getInstance();

    $totalAulas = (int) $db->fetchScalar(
        "SELECT COUNT(a.aula_id)
         FROM tbl_aulas a
         INNER JOIN tbl_modulos m ON a.modulo_id = m.modulo_id
         WHERE m.curso_id = ? AND a.ativo = 1",
        [$cursoId]
    );

    if ($totalAulas === 0) return 0;

    $concluidas = (int) $db->fetchScalar(
        "SELECT COUNT(*)
         FROM tbl_aula_progresso
         WHERE usuario_id = ? AND concluida = 1
           AND aula_id IN (
               SELECT a.aula_id
               FROM tbl_aulas a
               INNER JOIN tbl_modulos m ON a.modulo_id = m.modulo_id
               WHERE m.curso_id = ? AND a.ativo = 1
           )",
        [$usuarioId, $cursoId]
    );

    return (int) round($concluidas / $totalAulas * 100);
}

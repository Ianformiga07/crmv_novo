<?php
/**
 * helpers.php — Funções utilitárias globais (VERSÃO CORRIGIDA)
 *
 * Todas as funções são protegidas por function_exists() para evitar
 * "Fatal error: Cannot redeclare" quando conexao.php e helpers.php
 * são carregados na mesma requisição.
 */

// ── Formatação ───────────────────────────────────────────────

if (!function_exists('fmtData')) {
    function fmtData(?string $d): string {
        if (!$d || $d === '0000-00-00') return '-';
        try { return (new DateTime($d))->format('d/m/Y'); }
        catch (Exception) { return '-'; }
    }
}

if (!function_exists('fmtDataHora')) {
    function fmtDataHora(?string $d): string {
        if (!$d) return '-';
        try { return (new DateTime($d))->format('d/m/Y H:i'); }
        catch (Exception) { return '-'; }
    }
}

if (!function_exists('fmtCRMV')) {
    function fmtCRMV(?string $numero, string $uf = 'TO'): string {
        return $numero ? trim($numero) . '-' . $uf : 'Não informado';
    }
}

if (!function_exists('fmtMoeda')) {
    function fmtMoeda(float|string $valor): string {
        return 'R$ ' . number_format((float)$valor, 2, ',', '.');
    }
}

if (!function_exists('fmtCarga')) {
    function fmtCarga(float|string $horas): string {
        $h   = (int)$horas;
        $min = round(((float)$horas - $h) * 60);
        return $min > 0 ? "{$h}h{$min}" : "{$h}h";
    }
}

if (!function_exists('fmtTamanho')) {
    function fmtTamanho(int $bytes): string {
        if ($bytes >= 1_048_576) return round($bytes / 1_048_576, 1) . ' MB';
        if ($bytes >= 1_024)     return round($bytes / 1_024) . ' KB';
        return $bytes . ' B';
    }
}

if (!function_exists('trunca')) {
    function trunca(string $str, int $max): string {
        return mb_strlen($str) > $max ? mb_substr($str, 0, $max) . '…' : $str;
    }
}

if (!function_exists('truncaTexto')) {
    function truncaTexto(string $str, int $max): string {
        return mb_strlen($str) > $max ? mb_substr($str, 0, $max) . '...' : $str;
    }
}

if (!function_exists('primeiraLetra')) {
    function primeiraLetra(string $nome): string {
        return mb_strtoupper(mb_substr(trim($nome), 0, 1));
    }
}

if (!function_exists('nvl')) {
    function nvl(mixed $val, mixed $def = ''): mixed {
        return ($val === null || $val === '') ? $def : $val;
    }
}

if (!function_exists('e')) {
    function e(mixed $str): string {
        return htmlspecialchars((string)$str, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

// ── Badges ────────────────────────────────────────────────────

if (!function_exists('badgeMatricula')) {
    function badgeMatricula(string $status): string {
        return match(strtoupper(trim($status))) {
            'ATIVA'     => '<span class="badge badge-azul"><i class="fa-solid fa-play"></i> Em andamento</span>',
            'CONCLUIDA' => '<span class="badge badge-verde"><i class="fa-solid fa-check"></i> Concluído</span>',
            'CANCELADA','CANCELADO' => '<span class="badge badge-verm"><i class="fa-solid fa-ban"></i> Cancelado</span>',
            'REPROVADO' => '<span class="badge badge-verm"><i class="fa-solid fa-xmark"></i> Reprovado</span>',
            default     => '<span class="badge badge-cinza">' . htmlspecialchars($status) . '</span>',
        };
    }
}

if (!function_exists('badgeCurso')) {
    function badgeCurso(string $status): string {
        return match(strtoupper(trim($status))) {
            'PUBLICADO' => '<span class="badge badge-verde">Publicado</span>',
            'RASCUNHO'  => '<span class="badge badge-cinza">Rascunho</span>',
            'ENCERRADO' => '<span class="badge badge-ouro">Encerrado</span>',
            'CANCELADO' => '<span class="badge badge-verm">Cancelado</span>',
            'ATIVA'     => '<span class="badge badge-azul">Ativa</span>',
            'CONCLUIDA' => '<span class="badge badge-verde">Concluída</span>',
            'REPROVADO' => '<span class="badge badge-verm">Reprovado</span>',
            default     => '<span class="badge badge-cinza">' . htmlspecialchars($status) . '</span>',
        };
    }
}

if (!function_exists('badgeModalidade')) {
    function badgeModalidade(string $mod): string {
        return match(strtoupper(trim($mod))) {
            'EAD'        => '<span class="badge badge-azul"><i class="fa-solid fa-wifi"></i> EAD</span>',
            'PRESENCIAL' => '<span class="badge badge-cinza"><i class="fa-solid fa-map-pin"></i> Presencial</span>',
            'HIBRIDO'    => '<span class="badge badge-ouro"><i class="fa-solid fa-layer-group"></i> Híbrido</span>',
            default      => '<span class="badge badge-cinza">' . htmlspecialchars($mod) . '</span>',
        };
    }
}

if (!function_exists('badgeStatus')) {
    function badgeStatus(string $s): string { return badgeCurso($s); }
}

// ── Flash Messages ────────────────────────────────────────────

if (!function_exists('flash')) {
    function flash(string $msg, string $tipo = 'sucesso'): void {
        if (session_status() !== PHP_SESSION_ACTIVE) return;
        // Suporta ambos os formatos de chave (antigo e novo)
        $_SESSION['flash_msg']  = $msg;
        $_SESSION['flash_tipo'] = $tipo;
        $_SESSION['_flash']     = ['msg' => $msg, 'tipo' => $tipo];
    }
}

if (!function_exists('getFlash')) {
    function getFlash(): array {
        // Suporta ambos os formatos
        if (isset($_SESSION['_flash'])) {
            $f = $_SESSION['_flash'];
            unset($_SESSION['_flash'], $_SESSION['flash_msg'], $_SESSION['flash_tipo']);
            return $f;
        }
        $msg  = $_SESSION['flash_msg']  ?? '';
        $tipo = $_SESSION['flash_tipo'] ?? 'sucesso';
        unset($_SESSION['flash_msg'], $_SESSION['flash_tipo']);
        return ['msg' => $msg, 'tipo' => $tipo];
    }
}

if (!function_exists('renderFlash')) {
    function renderFlash(): void {
        $f = getFlash();
        if (!$f['msg']) return;
        $icon = match($f['tipo']) {
            'sucesso' => 'circle-check',
            'erro'    => 'circle-xmark',
            'aviso'   => 'triangle-exclamation',
            default   => 'circle-info',
        };
        $msg  = htmlspecialchars($f['msg']);
        $tipo = htmlspecialchars($f['tipo']);
        echo <<<HTML
        <div class="flash flash-{$tipo}" role="alert">
            <i class="fa-solid fa-{$icon}"></i>
            <span>{$msg}</span>
            <button class="flash-close" onclick="this.parentElement.remove()">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        HTML;
    }
}

// ── Uploads ───────────────────────────────────────────────────

if (!function_exists('uploadArquivo')) {
    function uploadArquivo(
        array  $file,
        string $subdir,
        array  $allowed = ['jpg', 'jpeg', 'png', 'webp', 'pdf'],
        int    $maxMb   = 0
    ): array {
        if ($maxMb <= 0) $maxMb = defined('MAX_UPLOAD_MB') ? MAX_UPLOAD_MB : 10;

        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['ok' => false, 'erro' => 'Falha no upload (código ' . $file['error'] . ').', 'nome' => null, 'path' => null];
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed)) {
            return ['ok' => false, 'erro' => 'Tipo não permitido: .' . $ext, 'nome' => null, 'path' => null];
        }

        if ($file['size'] > $maxMb * 1_048_576) {
            return ['ok' => false, 'erro' => "Arquivo muito grande. Máximo: {$maxMb}MB", 'nome' => null, 'path' => null];
        }

        $baseDir = defined('UPLOAD_PATH') ? UPLOAD_PATH : dirname(__DIR__) . '/uploads';
        $dir     = $baseDir . '/' . $subdir;
        if (!is_dir($dir)) mkdir($dir, 0755, true);

        $nome = bin2hex(random_bytes(8)) . '_' . time() . '.' . $ext;
        $dest = $dir . '/' . $nome;

        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            return ['ok' => false, 'erro' => 'Não foi possível salvar o arquivo.', 'nome' => null, 'path' => null];
        }

        return ['ok' => true, 'nome' => $nome, 'path' => $subdir . '/' . $nome, 'erro' => null];
    }
}

if (!function_exists('removeUpload')) {
    function removeUpload(string $path): void {
        $baseDir = defined('UPLOAD_PATH') ? UPLOAD_PATH : dirname(__DIR__) . '/uploads';
        $full    = $baseDir . '/' . ltrim($path, '/');
        if (is_file($full)) @unlink($full);
    }
}

if (!function_exists('uploadUrl')) {
    function uploadUrl(string $path): string {
        $base = defined('UPLOAD_URL') ? UPLOAD_URL : '/crmv/uploads';
        return $base . '/' . ltrim($path, '/');
    }
}

// ── Paginação ─────────────────────────────────────────────────

if (!function_exists('paginar')) {
    function paginar(int $limit = 20): array {
        $page   = max(1, (int)($_GET['p'] ?? 1));
        $offset = ($page - 1) * $limit;
        return compact('page', 'limit', 'offset');
    }
}

if (!function_exists('renderPaginacao')) {
    function renderPaginacao(int $total, int $limit, int $page): void {
        $pages = (int)ceil($total / $limit);
        if ($pages <= 1) return;
        $query = $_GET;
        echo '<nav class="paginacao">';
        for ($i = 1; $i <= $pages; $i++) {
            $query['p'] = $i;
            $url        = '?' . http_build_query($query);
            $ativo      = $i === $page ? ' ativo' : '';
            echo "<a href=\"{$url}\" class=\"pag-btn{$ativo}\">{$i}</a>";
        }
        echo '</nav>';
    }
}

// ── CSRF ──────────────────────────────────────────────────────

if (!function_exists('csrfField')) {
    function csrfField(): string {
        if (class_exists('Auth')) {
            $token = Auth::csrfToken();
        } else {
            if (session_status() === PHP_SESSION_ACTIVE && empty($_SESSION['_csrf'])) {
                $_SESSION['_csrf'] = bin2hex(random_bytes(32));
            }
            $token = $_SESSION['_csrf'] ?? '';
        }
        $key = defined('CSRF_TOKEN_KEY') ? CSRF_TOKEN_KEY : '_csrf';
        return '<input type="hidden" name="' . $key . '" value="' . htmlspecialchars($token) . '">';
    }
}

// ── Progresso EAD ─────────────────────────────────────────────

if (!function_exists('calcularProgressoEAD')) {
    function calcularProgressoEAD(int $cursoId, int $usuarioId): int {
        if (class_exists('Database')) {
            $db = Database::getInstance();
            $totalAulas = (int)$db->fetchScalar(
                "SELECT COUNT(a.aula_id) FROM tbl_aulas a
                 INNER JOIN tbl_modulos m ON a.modulo_id = m.modulo_id
                 WHERE m.curso_id = ? AND a.ativo = 1",
                [$cursoId]
            );
            if ($totalAulas === 0) return 0;
            $concluidas = (int)$db->fetchScalar(
                "SELECT COUNT(*) FROM tbl_aula_progresso
                 WHERE usuario_id = ? AND concluida = 1
                   AND aula_id IN (
                       SELECT a.aula_id FROM tbl_aulas a
                       INNER JOIN tbl_modulos m ON a.modulo_id = m.modulo_id
                       WHERE m.curso_id = ? AND a.ativo = 1)",
                [$usuarioId, $cursoId]
            );
        } else {
            $totalAulas = (int)(dbQueryOne(
                "SELECT COUNT(a.aula_id) cnt FROM tbl_aulas a
                 INNER JOIN tbl_modulos m ON a.modulo_id = m.modulo_id
                 WHERE m.curso_id = ? AND a.ativo = 1",
                [$cursoId]
            )['cnt'] ?? 0);
            if ($totalAulas === 0) return 0;
            $concluidas = (int)(dbQueryOne(
                "SELECT COUNT(*) cnt FROM tbl_aula_progresso
                 WHERE usuario_id = ? AND concluida = 1
                   AND aula_id IN (
                       SELECT a.aula_id FROM tbl_aulas a
                       INNER JOIN tbl_modulos m ON a.modulo_id = m.modulo_id
                       WHERE m.curso_id = ? AND a.ativo = 1)",
                [$usuarioId, $cursoId]
            )['cnt'] ?? 0);
        }
        return (int)round($concluidas / $totalAulas * 100);
    }
}

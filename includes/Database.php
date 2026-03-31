<?php
/**
 * Database.php — Classe de acesso a dados (PDO Wrapper)
 *
 * Substitui as funções globais do arquivo conexao.php original
 * por uma classe coesa, testável e reutilizável.
 *
 * Uso:
 *   $db = Database::getInstance();
 *   $rows = $db->fetchAll("SELECT * FROM tbl_cursos WHERE ativo = ?", [1]);
 */

require_once __DIR__ . '/config.php';

class Database
{
    private static ?Database $instance = null;
    private PDO $pdo;

    private function __construct()
    {
        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
            DB_HOST, DB_PORT, DB_NAME
        );

        try {
            $this->pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            $this->handleConnectionError($e);
        }
    }

    /** Singleton — garante uma única conexão por requisição */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /** SELECT → array de registros */
    public function fetchAll(string $sql, array $params = []): array
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /** SELECT → único registro (ou false) */
    public function fetchOne(string $sql, array $params = []): array|false
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }

    /** SELECT → valor escalar da primeira coluna */
    public function fetchScalar(string $sql, array $params = []): mixed
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn();
    }

    /** INSERT / UPDATE / DELETE → linhas afetadas */
    public function execute(string $sql, array $params = []): int
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }

    /** Retorna o último ID gerado por INSERT */
    public function lastInsertId(): int
    {
        return (int) $this->pdo->lastInsertId();
    }

    /** Inicia transação */
    public function beginTransaction(): void
    {
        $this->pdo->beginTransaction();
    }

    /** Confirma transação */
    public function commit(): void
    {
        $this->pdo->commit();
    }

    /** Reverte transação */
    public function rollback(): void
    {
        if ($this->pdo->inTransaction()) {
            $this->pdo->rollBack();
        }
    }

    /** Exibe erro de conexão de forma elegante */
    private function handleConnectionError(PDOException $e): never
    {
        $msg = DEBUG_MODE
            ? htmlspecialchars($e->getMessage())
            : 'Não foi possível conectar ao banco de dados. Tente novamente mais tarde.';

        echo <<<HTML
        <div style="font-family:system-ui;background:#fee2e2;color:#7f1d1d;
                    padding:24px;margin:40px auto;max-width:540px;border-radius:10px;
                    border:1px solid #fca5a5">
            <strong>Erro de conexão</strong><br><br>{$msg}
        </div>
        HTML;
        exit;
    }
}

// ─── Funções globais (compatibilidade com código existente) ───
// Mantidas para não quebrar páginas ainda não refatoradas.

function db(): Database { return Database::getInstance(); }

function dbQuery(string $sql, array $p = []): array        { return db()->fetchAll($sql, $p);   }
function dbQueryOne(string $sql, array $p = []): array|false { return db()->fetchOne($sql, $p); }
function dbExecute(string $sql, array $p = []): int        { return db()->execute($sql, $p);    }
function dbLastId(): int                                   { return db()->lastInsertId();        }

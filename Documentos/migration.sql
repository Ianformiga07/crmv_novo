-- ============================================================
--  CRMV/TO — Educação Continuada
--  Script de ajustes e novas tabelas (migration)
--
--  Execute este script NO BANCO EXISTENTE para adicionar
--  as tabelas e colunas necessárias à versão refatorada.
--  O banco original (crmv_cursos.sql) deve ser importado ANTES.
-- ============================================================

USE `crmv_cursos`;

-- ── 1. Tabela de progresso individual por aula ─────────────────
--  Substitui o campo genérico progresso_ead (0–100) por controle
--  granular aula a aula, permitindo barra de progresso precisa.
-- ──────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `tbl_aula_progresso` (
    `progresso_id`  INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `aula_id`       INT UNSIGNED NOT NULL,
    `usuario_id`    INT UNSIGNED NOT NULL,
    `concluida`     TINYINT(1)   NOT NULL DEFAULT 0,
    `concluida_em`  DATETIME              DEFAULT NULL,
    `criado_em`     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`progresso_id`),
    UNIQUE KEY `uq_aula_usuario` (`aula_id`, `usuario_id`),
    KEY `idx_usuario` (`usuario_id`),
    CONSTRAINT `fk_ap_aula`    FOREIGN KEY (`aula_id`)    REFERENCES `tbl_aulas`    (`aula_id`)    ON DELETE CASCADE,
    CONSTRAINT `fk_ap_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `tbl_usuarios` (`usuario_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- ── 2. Tabela de respostas do aluno à avaliação ─────────────────
CREATE TABLE IF NOT EXISTS `tbl_avaliacao_respostas` (
    `resposta_id`    INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `avaliacao_id`   INT UNSIGNED NOT NULL,
    `matricula_id`   INT UNSIGNED NOT NULL,
    `tentativa`      TINYINT UNSIGNED NOT NULL DEFAULT 1,
    `questao_id`     INT UNSIGNED NOT NULL,
    `alternativa_id` INT UNSIGNED          DEFAULT NULL,
    `correta`        TINYINT(1)   NOT NULL DEFAULT 0,
    `respondida_em`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`resposta_id`),
    KEY `idx_matricula_tentativa` (`matricula_id`, `tentativa`),
    CONSTRAINT `fk_ar_avaliacao`   FOREIGN KEY (`avaliacao_id`)   REFERENCES `tbl_avaliacoes`   (`avaliacao_id`)   ON DELETE CASCADE,
    CONSTRAINT `fk_ar_matricula`   FOREIGN KEY (`matricula_id`)   REFERENCES `tbl_matriculas`   (`matricula_id`)   ON DELETE CASCADE,
    CONSTRAINT `fk_ar_questao`     FOREIGN KEY (`questao_id`)     REFERENCES `tbl_questoes`     (`questao_id`)     ON DELETE CASCADE,
    CONSTRAINT `fk_ar_alternativa` FOREIGN KEY (`alternativa_id`) REFERENCES `tbl_alternativas` (`alternativa_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- ── 3. Tabela de tentativas de avaliação ───────────────────────
CREATE TABLE IF NOT EXISTS `tbl_avaliacao_tentativas` (
    `tentativa_id`  INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `avaliacao_id`  INT UNSIGNED NOT NULL,
    `matricula_id`  INT UNSIGNED NOT NULL,
    `numero`        TINYINT UNSIGNED NOT NULL DEFAULT 1,
    `nota`          DECIMAL(5,2)      DEFAULT NULL,
    `aprovado`      TINYINT(1)        DEFAULT NULL,
    `iniciada_em`   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `finalizada_em` DATETIME          DEFAULT NULL,
    PRIMARY KEY (`tentativa_id`),
    UNIQUE KEY `uq_avaliacao_matricula_numero` (`avaliacao_id`, `matricula_id`, `numero`),
    CONSTRAINT `fk_at_avaliacao`  FOREIGN KEY (`avaliacao_id`)  REFERENCES `tbl_avaliacoes`  (`avaliacao_id`)  ON DELETE CASCADE,
    CONSTRAINT `fk_at_matricula`  FOREIGN KEY (`matricula_id`)  REFERENCES `tbl_matriculas`  (`matricula_id`)  ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- ── 4. Adicionar colunas que podem faltar em tbl_usuarios ──────
ALTER TABLE `tbl_usuarios`
    ADD COLUMN IF NOT EXISTS `ativo`      TINYINT(1)   NOT NULL DEFAULT 1   AFTER `crmv_uf`,
    ADD COLUMN IF NOT EXISTS `senha_hash` VARCHAR(255)          DEFAULT NULL AFTER `ativo`;


-- ── 5. Adicionar colunas que podem faltar em tbl_cursos ────────
ALTER TABLE `tbl_cursos`
    ADD COLUMN IF NOT EXISTS `requer_avaliacao`   TINYINT(1)      NOT NULL DEFAULT 0   AFTER `cert_obs`,
    ADD COLUMN IF NOT EXISTS `avaliacao_com_nota` TINYINT(1)      NOT NULL DEFAULT 0   AFTER `requer_avaliacao`,
    ADD COLUMN IF NOT EXISTS `nota_minima`        DECIMAL(5,2)             DEFAULT 70  AFTER `avaliacao_com_nota`,
    ADD COLUMN IF NOT EXISTS `tentativas_maximas` SMALLINT(6)     NOT NULL DEFAULT 3   AFTER `nota_minima`,
    ADD COLUMN IF NOT EXISTS `cert_frente_html`   TEXT                     DEFAULT NULL AFTER `tentativas_maximas`,
    ADD COLUMN IF NOT EXISTS `cert_verso_html`    TEXT                     DEFAULT NULL AFTER `cert_frente_html`;


-- ── 6. View para dashboard de totais ──────────────────────────
CREATE OR REPLACE VIEW `vw_dashboard_totais` AS
SELECT
    (SELECT COUNT(*) FROM tbl_cursos   WHERE ativo=1 AND status='PUBLICADO') AS cursos_publicados,
    (SELECT COUNT(*) FROM tbl_cursos   WHERE ativo=1 AND modalidade='EAD')   AS cursos_ead,
    (SELECT COUNT(*) FROM tbl_cursos   WHERE ativo=1 AND modalidade='PRESENCIAL') AS cursos_presencial,
    (SELECT COUNT(*) FROM tbl_usuarios WHERE perfil_id=2 AND ativo=1)        AS total_veterinarios,
    (SELECT COUNT(*) FROM tbl_matriculas WHERE status='ATIVA')               AS matriculas_ativas,
    (SELECT COUNT(*) FROM tbl_matriculas WHERE status='CONCLUIDA')           AS matriculas_concluidas,
    (SELECT COUNT(*) FROM tbl_certificados WHERE valido=1)                   AS certificados_emitidos;


-- ── 7. View para relatório de matrículas ──────────────────────
CREATE OR REPLACE VIEW `vw_matriculas_completo` AS
SELECT
    m.matricula_id,
    m.status,
    m.matriculado_em,
    m.progresso_ead,
    m.nota_final,
    m.certificado_gerado,
    m.certificado_codigo,
    m.certificado_emitido_em,
    u.usuario_id,
    u.nome_completo,
    u.cpf,
    u.email,
    u.crmv_numero,
    u.crmv_uf,
    c.curso_id,
    c.titulo   AS curso_titulo,
    c.tipo     AS curso_tipo,
    c.modalidade,
    c.carga_horaria,
    c.data_inicio,
    c.data_fim,
    cat.nome   AS categoria_nome
FROM tbl_matriculas m
INNER JOIN tbl_usuarios   u   ON m.usuario_id   = u.usuario_id
INNER JOIN tbl_cursos     c   ON m.curso_id     = c.curso_id
LEFT  JOIN tbl_categorias cat ON c.categoria_id = cat.categoria_id
WHERE c.ativo = 1;


-- ── 8. View para certificados ──────────────────────────────────
CREATE OR REPLACE VIEW `vw_certificados` AS
SELECT
    cert.cert_id,
    cert.codigo,
    cert.emitido_em,
    cert.valido,
    u.nome_completo,
    u.cpf,
    u.crmv_numero,
    u.crmv_uf,
    c.titulo    AS curso_titulo,
    c.tipo      AS curso_tipo,
    c.carga_horaria,
    c.data_inicio,
    c.data_fim,
    m.nota_final,
    m.matricula_id
FROM tbl_certificados cert
INNER JOIN tbl_matriculas m   ON cert.matricula_id = m.matricula_id
INNER JOIN tbl_usuarios   u   ON m.usuario_id      = u.usuario_id
INNER JOIN tbl_cursos     c   ON m.curso_id        = c.curso_id;


-- ── 9. Índices de performance ──────────────────────────────────
-- Acelera buscas por status e usuário em matrículas
CREATE INDEX IF NOT EXISTS `idx_mat_status`   ON `tbl_matriculas` (`status`);
CREATE INDEX IF NOT EXISTS `idx_mat_usuario`  ON `tbl_matriculas` (`usuario_id`);
CREATE INDEX IF NOT EXISTS `idx_mat_curso`    ON `tbl_matriculas` (`curso_id`);

-- Acelera busca de cursos por status e modalidade
CREATE INDEX IF NOT EXISTS `idx_curso_status`     ON `tbl_cursos` (`status`);
CREATE INDEX IF NOT EXISTS `idx_curso_modalidade` ON `tbl_cursos` (`modalidade`);
CREATE INDEX IF NOT EXISTS `idx_curso_data`       ON `tbl_cursos` (`data_inicio`);

-- Acelera busca de usuários por CPF, email, CRMV
CREATE INDEX IF NOT EXISTS `idx_usr_cpf`   ON `tbl_usuarios` (`cpf`);
CREATE INDEX IF NOT EXISTS `idx_usr_email` ON `tbl_usuarios` (`email`);
CREATE INDEX IF NOT EXISTS `idx_usr_crmv`  ON `tbl_usuarios` (`crmv_numero`);

-- ── FIM ────────────────────────────────────────────────────────

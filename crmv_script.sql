-- --------------------------------------------------------
-- Servidor:                     127.0.0.1
-- Versão do servidor:           8.4.3 - MySQL Community Server - GPL
-- OS do Servidor:               Win64
-- HeidiSQL Versão:              12.8.0.6908
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Copiando dados para a tabela crmv_cursos.tbl_alternativas: ~16 rows (aproximadamente)
INSERT IGNORE INTO `tbl_alternativas` (`alternativa_id`, `questao_id`, `texto`, `correta`, `ordem`) VALUES
	(1, 1, 'asdasdsad', 1, 1),
	(2, 1, 'asdasdasd', 0, 2),
	(3, 1, 'asdasd', 0, 3),
	(4, 1, 'asdasda', 0, 4),
	(5, 2, 'sadasdas', 0, 1),
	(6, 2, 'asdasdasd', 1, 2),
	(7, 2, 'sdaasdsad', 0, 3),
	(8, 2, 'asdasda', 0, 4),
	(9, 3, 'asdasdas', 0, 1),
	(10, 3, 'asdasda', 0, 2),
	(11, 3, 'asdsada', 1, 3),
	(12, 3, 'asdasd', 0, 4),
	(13, 4, 'asdasdas', 1, 1),
	(14, 4, 'asdasdas', 0, 2),
	(15, 4, 'sadasd', 0, 3),
	(16, 4, 'asdasd', 0, 4);

-- Copiando dados para a tabela crmv_cursos.tbl_aulas: ~7 rows (aproximadamente)
INSERT IGNORE INTO `tbl_aulas` (`aula_id`, `modulo_id`, `titulo`, `descricao`, `youtube_id`, `link_externo`, `arquivo_video`, `duracao_min`, `ordem`, `ativo`, `criado_em`) VALUES
	(4, 2, 'Aula 1', NULL, 'dyBmhdLMkac', NULL, NULL, NULL, 1, 1, '2026-03-09 23:21:40'),
	(5, 2, 'Aula 2', NULL, 'dyBmhdLMkac', NULL, NULL, NULL, 2, 1, '2026-03-09 23:21:40'),
	(6, 2, 'Aula 3', NULL, NULL, NULL, NULL, NULL, 3, 1, '2026-03-09 23:21:40'),
	(7, 1, 'Aula 1', NULL, 'dyBmhdLMkac', NULL, NULL, NULL, 1, 1, '2026-03-09 23:25:34'),
	(10, 3, 'Aula 1', NULL, 'dyBmhdLMkac', NULL, NULL, NULL, 1, 1, '2026-03-09 23:28:26'),
	(11, 3, 'Aula 2', NULL, 'dyBmhdLMkac', NULL, NULL, NULL, 2, 1, '2026-03-09 23:28:26'),
	(12, 3, 'Aula 3', NULL, NULL, NULL, 'video_8_1773109706_32c70a.mp4', NULL, 3, 1, '2026-03-09 23:28:26');

-- Copiando dados para a tabela crmv_cursos.tbl_aula_progresso: ~0 rows (aproximadamente)

-- Copiando dados para a tabela crmv_cursos.tbl_avaliacao_respostas: ~0 rows (aproximadamente)

-- Copiando dados para a tabela crmv_cursos.tbl_avaliacao_tentativas: ~0 rows (aproximadamente)

-- Copiando dados para a tabela crmv_cursos.tbl_avaliacoes: ~2 rows (aproximadamente)
INSERT IGNORE INTO `tbl_avaliacoes` (`avaliacao_id`, `curso_id`, `modulo_id`, `titulo`, `descricao`, `tipo`, `nota_minima`, `tempo_limite`, `tentativas_max`, `randomizar`, `ativo`, `criado_em`) VALUES
	(1, 7, NULL, 'Avaliação Final', '', 'PROVA', 70.00, NULL, 3, 0, 1, '2026-03-09 23:04:00'),
	(2, 8, NULL, 'Avaliação Final', '', 'PROVA', 70.00, NULL, 3, 0, 1, '2026-03-09 23:27:59');

-- Copiando dados para a tabela crmv_cursos.tbl_categorias: ~8 rows (aproximadamente)
INSERT IGNORE INTO `tbl_categorias` (`categoria_id`, `nome`, `descricao`, `cor_hex`, `icone_fa`, `ordem`, `ativo`, `criado_em`) VALUES
	(1, 'Clínica Veterinária', 'Cursos de clínica geral', '#1a6b3c', 'fa-stethoscope', 1, 1, '2026-03-06 17:14:27'),
	(2, 'Cirurgia', 'Cursos e workshops de cirurgia', '#15385c', 'fa-scalpel', 2, 1, '2026-03-06 17:14:27'),
	(3, 'Diagnóstico por Imagem', 'Ultrassonografia, radiologia', '#c9a227', 'fa-x-ray', 3, 1, '2026-03-06 17:14:27'),
	(4, 'Medicina de Animais Silvestres', 'Fauna silvestre e exóticos', '#2d6a4f', 'fa-paw', 4, 1, '2026-03-06 17:14:27'),
	(5, 'Saúde Pública', 'Vigilância sanitária e zoonoses', '#6d3b47', 'fa-shield-virus', 5, 1, '2026-03-06 17:14:27'),
	(6, 'Administração e Ética', 'Gestão e deontologia veterinária', '#374151', 'fa-balance-scale', 6, 1, '2026-03-06 17:14:27'),
	(7, 'Bem-estar Animal', 'Etologia e bem-estar', '#7c3aed', 'fa-heart', 7, 1, '2026-03-06 17:14:27'),
	(8, 'Palestras Científicas', 'Palestras e conferências', '#0d2137', 'fa-microphone', 8, 1, '2026-03-06 17:14:27');

-- Copiando dados para a tabela crmv_cursos.tbl_certificados: ~3 rows (aproximadamente)
INSERT IGNORE INTO `tbl_certificados` (`cert_id`, `matricula_id`, `codigo`, `emitido_em`, `qr_path`, `pdf_path`, `valido`) VALUES
	(1, 1, 'QA5P-E5TN-GSZY', '2026-03-06 23:01:57', NULL, NULL, 1),
	(2, 2, 'CWW6-JCD6-SNND', '2026-03-07 00:07:30', NULL, NULL, 1),
	(3, 3, 'DPYU-W87C-J49J', '2026-03-09 16:36:04', NULL, NULL, 1),
	(4, 4, 'C69N-7JJG-FS67', '2026-03-31 16:12:24', NULL, NULL, 1);

-- Copiando dados para a tabela crmv_cursos.tbl_configuracoes: ~9 rows (aproximadamente)
INSERT IGNORE INTO `tbl_configuracoes` (`config_id`, `chave`, `valor`, `descricao`, `tipo`, `atualizado_em`, `atualizado_por`) VALUES
	(1, 'site_nome', 'CRMV/TO — Educação Continuada', 'Nome do sistema', 'texto', NULL, NULL),
	(2, 'site_email', 'educacao@crmvto.gov.br', 'E-mail oficial', 'texto', NULL, NULL),
	(3, 'cert_validade_anos', '5', 'Validade dos certificados', 'numero', NULL, NULL),
	(4, 'sla_alerta_dias', '30', 'Dias para alerta de prazo', 'numero', NULL, NULL),
	(5, 'upload_max_mb', '10', 'Tamanho máximo de upload', 'numero', NULL, NULL),
	(6, 'cert_rodape', 'Conselho Regional de Medicina Veterinária do Estado do Tocantins', 'Texto do rodapé do certificado', 'texto', NULL, NULL),
	(7, 'presidente_nome', 'Presidente do CRMV-TO', 'Nome do presidente para o certificado', 'texto', NULL, NULL),
	(8, 'presidente_titulo', 'Médico(a) Veterinário(a)', 'Título do presidente', 'texto', NULL, NULL),
	(9, 'cfmv_numero', '0000', 'Número de inscrição no CFMV', 'texto', NULL, NULL);

-- Copiando dados para a tabela crmv_cursos.tbl_cursos: ~8 rows (aproximadamente)
INSERT IGNORE INTO `tbl_cursos` (`curso_id`, `categoria_id`, `titulo`, `descricao`, `capa`, `observacoes`, `tipo`, `modalidade`, `carga_horaria`, `vagas`, `data_inicio`, `data_fim`, `horario`, `local_nome`, `local_cidade`, `local_uf`, `local_endereco`, `link_ead`, `youtube_id`, `valor`, `status`, `cert_modelo`, `cert_conteudo_programatico`, `cert_validade`, `cert_obs`, `ativo`, `criado_em`, `atualizado_em`, `criado_por`, `instrutor_id`, `requer_avaliacao`, `avaliacao_com_nota`, `nota_minima`, `tentativas_maximas`, `cert_frente_html`, `cert_verso_html`) VALUES
	(1, 1, 'dsadasdasdasdasd', 'sdasdsadsadsad', 'capa_1772848884_5e30e1af.jpg', '', 'CURSO', 'PRESENCIAL', 12.0, NULL, '2026-03-09', '2026-03-10', '', 'Auditório da adapec', 'Palmas', 'TO', 'Quadra ARSE 51 Alameda 9', '', '', 0.00, 'ENCERRADO', NULL, NULL, NULL, NULL, 1, '2026-03-06 23:01:24', '2026-03-07 00:11:28', 2, NULL, 0, 0, 70.00, 3, NULL, NULL),
	(2, NULL, 'Curso de Bombeiros', 'asdasdsadasdasdasdasd', 'capa_1773109651_bdee0497.jpeg', 'asdasdasd', 'CURSO', 'EAD', 10.0, NULL, '2026-03-10', '2026-03-17', '', '', '', 'TO', '', '', '', 0.00, 'PUBLICADO', NULL, 'asdsadasdasdasdasdasdasdassad', NULL, '', 1, '2026-03-07 00:06:45', '2026-03-09 23:28:09', 2, NULL, 0, 0, 70.00, 3, NULL, NULL),
	(3, 2, 'testet testando', 'asdasdsadasdasd', 'capa_1772853053_d7586b18.jpeg', '', 'PALESTRA', 'PRESENCIAL', 4.0, 14, '2026-03-09', '2026-03-09', '', 'Auditório da adapec', 'Palmas', 'TO', 'Quadra ARSE 51 Alameda 9', '', '', 0.00, 'PUBLICADO', NULL, NULL, NULL, NULL, 1, '2026-03-07 00:10:53', '2026-03-07 00:11:17', 1, NULL, 0, 0, 70.00, 3, NULL, NULL),
	(4, 7, 'Curso Febre Aftosa', 'sadasdsadsadsada', 'capa_1773026231_e46de8b9.jpeg', 'asdasdasdasdsad', 'CURSO', 'EAD', 12.0, 51, '2026-03-09', '2026-03-11', '', '', '', 'TO', '', '', 'c4XeTP11EI8', 0.00, 'ENCERRADO', NULL, '\r\n                        <div><b>Conteúdo Programático:</b></div><div><ul><li>Panorama atual da piscicultura tocantinense (ascendência da atividade, municípios / região mais produtivos e identificação das espécies de importância</li><li>econômica no Tocantins);</li><li>Ascendência da atividade, municípios / região mais produtivos e identificação das espécies de importância econômica no Tocantins;</li><li>Câmara Setorial da Piscicultura;</li><li>Qualidade da água na piscicultura (Parâmetros Físico-químicos);</li><li>Abordagem sobre o programa Estadual de Sanidade dos Animais Aquáticos;</li><li>Boas práticas manejo sanitário e medidas de biosseguridade em pisciculturas;</li><li>Cadastramento de pisciculturas (POP PESAA n° 01);</li><li>Vigilâncias em estabelecimentos aquícolas (POP PESAA n° 02);</li><li>Coleta de material, acondicionamento e envio de amostras pelo serviço veterinário oficial estadual;</li><li>Preenchimento de formulários de coleta de amostra e ou atendimentos oficiais;</li><li>Abordagem sobre o Epicolletct5 (vigilância PESAA);</li><li>Protocolos sanitários em estabelecimento quarentenário;</li><li>Principais enfermidades que acometem os peixes, identificação das doenças, diagnóstico clínico, prevenção;</li><li>Avaliações</li></ul></div><div><b>INSTRUTORES</b></div><div><ul><li>Thiago Fontolan Tardivo – Zootenista/Diretor de Desenvolvimento da AQUICULTURA - SEPEA/TO, Secretário executivo da Câmara Setorial da</li><li>Piscicultura-CSP/TO, e docente do na UniCatólica.</li><li>Marina Karina de Veiga Cabral Delphino – Médica Vet./ Gerente de Soluções de Saúde e Qualidade dos Peixes Grupo GenoMar Docente do Curso de</li><li>Medicina Veterinária do UniCatólica</li><li>Patrícia Oliveira Maciel - Médica Vet./ Pesquisadora na área temática de sanidade de organismos aquáticos da Embrapa Pesca e aquicultura Tocantins.</li><li>Andrey Chama da Costa - Eng. de Pesca/ Gerente de piscicultura do Ruraltins.</li><li>Elias Mendes – Médico Veterinário/Responsável Técnico do PESAA/TO.</li><li>César Romero - Médico Veterinário Responsável Técnico do Núcleo de Vigilância</li></ul></div>                    ', 0, '', 1, '2026-03-09 00:14:56', '2026-03-09 17:03:26', 2, NULL, 0, 0, 70.00, 3, NULL, NULL),
	(5, NULL, '2° TURMA CURSO CAPACITAÇÃO DE EVENTOS PECUÁRIOS', '2° TURMA CURSO CAPACITAÇÃO DE EVENTOS PECUÁRIOS', 'capa_1773084586_9f439565.jpeg', '', 'CURSO', 'EAD', 12.0, NULL, '2026-03-10', '2026-03-11', '', '', '', 'TO', '', 'https://www.youtube.com/watch?v=ZOXcPHh6ZqU&list=RDZOXcPHh6ZqU&start_radio=1', 'c4XeTP11EI8', 0.00, 'PUBLICADO', NULL, '<font size="3"><b>Conteúdo Programático:&nbsp;</b></font><div><ul><li>Importância e o Papel do Médico Veterinário Privado/Autônomo na Defesa Sanitária Animal&nbsp;</li><li>Legislações do Programa de Eventos&nbsp;</li><li>Critérios para Cadastramento de Empresas Leiloeiras, Recintos e Eventos Agropecuários em Geral, no Estado do Tocantins&nbsp;</li><li>Habilitação no Mapa e Cadastro na ADAPEC para RT de Eventos Pecuários&nbsp;</li><li>Trânsito Animal para Eventos Agropecuários, Entrada e Saída&nbsp;</li><li>Responsabilidade Técnica e Ética&nbsp;</li><li>Bem Estar Animal&nbsp;</li><li>Avaliações&nbsp;</li></ul></div><div><b>INSTRUTORES</b></div><div><ul><li>Márcio de Oliveira Rezende/Diretor de Inspeção e Defesa Agropecuária ADAPEC-TO&nbsp;</li><li>Frederico Borba Diniz/Responsável Técnico do Programa de Eventos Pecuários ADAPEC-TO&nbsp;</li><li>Joyce Camilla P Santos/Responsável Técnica do Programa de Cadastros Agropecuários ADAPEC-TO&nbsp;</li><li>Filipe Carrilho/Médico Veterinário - CRMV\r\nRegina Barbosa/ Inspetora de Defesa Agropecuária ADAPEC-TO&nbsp;</li><li>Ana Lucia Rodrigues/Inspetora de Defesa Agropecuária ADAPEC-TO\r\n                                            </li></ul></div>', 12, '', 1, '2026-03-09 16:29:46', NULL, 2, NULL, 0, 0, 70.00, 3, NULL, NULL),
	(6, NULL, 'Treinamento de morcegos', '', NULL, '', 'CURSO', 'PRESENCIAL', 12.0, NULL, NULL, NULL, '', '', '', 'TO', '', '', '', 0.00, 'PUBLICADO', NULL, '<div><b>Conteúdo Programático:</b></div><div><ul><li>Panorama atual da piscicultura tocantinense (ascendência da atividade, municípios / região mais produtivos e identificação das espécies de importância</li><li>econômica no Tocantins);</li><li>Ascendência da atividade, municípios / região mais produtivos e identificação das espécies de importância econômica no Tocantins;</li><li>Câmara Setorial da Piscicultura;</li><li>Qualidade da água na piscicultura (Parâmetros Físico-químicos);</li><li>Abordagem sobre o programa Estadual de Sanidade dos Animais Aquáticos;</li><li>Boas práticas manejo sanitário e medidas de biosseguridade em pisciculturas;</li><li>Cadastramento de pisciculturas (POP PESAA n° 01);</li><li>Vigilâncias em estabelecimentos aquícolas (POP PESAA n° 02);</li><li>Coleta de material, acondicionamento e envio de amostras pelo serviço veterinário oficial estadual;</li><li>Preenchimento de formulários de coleta de amostra e ou atendimentos oficiais;</li><li>Abordagem sobre o Epicolletct5 (vigilância PESAA);</li><li>Protocolos sanitários em estabelecimento quarentenário;</li><li>Principais enfermidades que acometem os peixes, identificação das doenças, diagnóstico clínico, prevenção;</li><li>Avaliações</li></ul></div><div><b>INSTRUTORES</b></div><div><ul><li>Thiago Fontolan Tardivo – Zootenista/Diretor de Desenvolvimento da AQUICULTURA - SEPEA/TO, Secretário executivo da Câmara Setorial da</li><li>Piscicultura-CSP/TO, e docente do na UniCatólica.</li><li>Marina Karina de Veiga Cabral Delphino – Médica Vet./ Gerente de Soluções de Saúde e Qualidade dos Peixes Grupo GenoMar Docente do Curso de</li><li>Medicina Veterinária do UniCatólica</li><li>Patrícia Oliveira Maciel - Médica Vet./ Pesquisadora na área temática de sanidade de organismos aquáticos da Embrapa Pesca e aquicultura Tocantins.</li><li>Andrey Chama da Costa - Eng. de Pesca/ Gerente de piscicultura do Ruraltins.</li><li>Elias Mendes – Médico Veterinário/Responsável Técnico do PESAA/TO.</li><li>César Romero - Médico Veterinário Responsável Técnico do Núcleo de Vigilância</li></ul></div>\r\n            ', 0, '', 1, '2026-03-09 22:21:46', '2026-03-09 22:26:15', 2, NULL, 0, 0, 70.00, 3, NULL, NULL),
	(7, NULL, 'Treinamento teste do teste para teste', 'sadasdasdasdasda', 'capa_1773108104_303ab60e.jpeg', 'asdsadasd', 'CURSO', 'PRESENCIAL', 10.0, NULL, '2026-03-10', '2026-03-11', '', 'Auditório da adapec', 'Palmas', 'TO', 'Quadra ARSE 51 Alameda 9', '', '', 0.00, 'PUBLICADO', NULL, '', NULL, '', 1, '2026-03-09 23:01:44', '2026-03-09 23:04:00', 2, NULL, 1, 1, 70.00, 3, NULL, NULL),
	(8, NULL, 'Curso de Bombeiros', 'asdasdsadasdasdasdasd', 'capa_1773109651_bdee0497.jpeg', 'asdasdasd', 'CURSO', 'EAD', 10.0, NULL, '2026-03-10', '2026-03-17', '', '', '', 'TO', '', '', '', 0.00, 'PUBLICADO', NULL, '', NULL, '', 1, '2026-03-09 23:27:31', '2026-03-09 23:27:59', 2, NULL, 1, 1, 70.00, 3, NULL, NULL);

-- Copiando dados para a tabela crmv_cursos.tbl_curso_instrutores: ~0 rows (aproximadamente)

-- Copiando dados para a tabela crmv_cursos.tbl_curso_materiais: ~0 rows (aproximadamente)

-- Copiando dados para a tabela crmv_cursos.tbl_instrutores: ~0 rows (aproximadamente)

-- Copiando dados para a tabela crmv_cursos.tbl_log_atividades: ~116 rows (aproximadamente)
INSERT IGNORE INTO `tbl_log_atividades` (`log_id`, `usuario_id`, `acao`, `descricao`, `tabela_ref`, `registro_id`, `ip_address`, `user_agent`, `criado_em`) VALUES
	(1, 1, 'LOGIN', 'Login realizado', 'tbl_usuarios', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-06 17:20:31'),
	(2, 1, 'LOGOUT', 'Logout realizado', 'tbl_usuarios', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-06 21:41:24'),
	(3, 1, 'LOGIN', 'Login realizado', 'tbl_usuarios', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-06 21:41:33'),
	(4, 1, 'CRIAR_USUARIO', 'Criou veterinário: Ian Leandro Cardoso Formiga', 'tbl_usuarios', 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-06 22:02:32'),
	(5, 1, 'LOGOUT', 'Logout realizado', 'tbl_usuarios', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-06 22:17:07'),
	(6, 2, 'LOGIN', 'Login realizado', 'tbl_usuarios', 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-06 22:17:13'),
	(7, 2, 'LOGOUT', 'Logout realizado', 'tbl_usuarios', 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-06 22:41:02'),
	(8, 2, 'LOGIN', 'Login realizado', 'tbl_usuarios', 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-06 22:41:09'),
	(9, 2, 'LOGOUT', 'Logout realizado', 'tbl_usuarios', 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-06 22:43:32'),
	(10, 2, 'LOGIN', 'Login realizado', 'tbl_usuarios', 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-06 22:43:54'),
	(11, 2, 'CRIAR_USUARIO', 'Criou veterinário: Laura Regina da Silva Morais', 'tbl_usuarios', 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-06 22:45:13'),
	(12, 2, 'LOGOUT', 'Logout realizado', 'tbl_usuarios', 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-06 22:45:29'),
	(13, 3, 'LOGIN', 'Login realizado', 'tbl_usuarios', 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-06 22:45:36'),
	(14, 3, 'LOGOUT', 'Logout realizado', 'tbl_usuarios', 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-06 22:45:48'),
	(15, 2, 'LOGIN', 'Login realizado', 'tbl_usuarios', 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-06 22:47:05'),
	(16, 2, 'CRIAR_CURSO', 'Criou curso: dsadasdasdasdasd', 'tbl_cursos', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-06 23:01:24'),
	(17, 2, 'EMITIR_CERT', 'Certificado emitido: QA5P-E5TN-GSZY para Laura Regina da Silva Morais', 'tbl_certificados', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-06 23:01:57'),
	(18, 2, 'LOGOUT', 'Logout realizado', 'tbl_usuarios', 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-06 23:52:16'),
	(19, 2, 'LOGIN', 'Login realizado', 'tbl_usuarios', 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-06 23:52:43'),
	(20, 2, 'CRIAR_CURSO', 'Criou curso: Curso de Bovinos', 'tbl_cursos', 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 00:06:45'),
	(21, 2, 'EMITIR_CERT', 'Certificado emitido: CWW6-JCD6-SNND para Laura Regina da Silva Morais', 'tbl_certificados', 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 00:07:30'),
	(22, 2, 'LOGOUT', 'Logout realizado', 'tbl_usuarios', 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 00:08:31'),
	(23, 1, 'LOGIN', 'Login realizado', 'tbl_usuarios', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 00:08:41'),
	(24, 1, 'CRIAR_CURSO', 'Criou curso: testet testando', 'tbl_cursos', 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 00:10:53'),
	(25, 1, 'LOGOUT', 'Logout realizado', 'tbl_usuarios', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 00:12:19'),
	(26, 2, 'LOGIN', 'Login realizado', 'tbl_usuarios', 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 10:42:23'),
	(27, 2, 'LOGOUT', 'Logout realizado', 'tbl_usuarios', 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 10:42:33'),
	(28, 3, 'LOGIN', 'Login realizado', 'tbl_usuarios', 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 10:42:39'),
	(29, 3, 'LOGOUT', 'Logout realizado', 'tbl_usuarios', 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 11:05:11'),
	(30, 3, 'LOGIN', 'Login realizado', 'tbl_usuarios', 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 11:05:20'),
	(31, 3, 'LOGOUT', 'Logout realizado', 'tbl_usuarios', 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-08 23:39:22'),
	(32, 3, 'LOGIN', 'Login realizado', 'tbl_usuarios', 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-08 23:39:40'),
	(33, 3, 'LOGOUT', 'Logout realizado', 'tbl_usuarios', 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-08 23:40:45'),
	(34, 3, 'LOGIN', 'Login realizado', 'tbl_usuarios', 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-08 23:40:53'),
	(35, 3, 'LOGOUT', 'Logout realizado', 'tbl_usuarios', 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-08 23:41:56'),
	(36, 2, 'LOGIN', 'Login realizado', 'tbl_usuarios', 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-08 23:42:01'),
	(37, 2, 'LOGOUT', 'Logout realizado', 'tbl_usuarios', 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-08 23:55:09'),
	(38, 3, 'LOGIN', 'Login realizado', 'tbl_usuarios', 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-08 23:55:16'),
	(39, 3, 'LOGOUT', 'Logout realizado', 'tbl_usuarios', 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-09 00:10:53'),
	(40, 3, 'LOGIN', 'Login realizado', 'tbl_usuarios', 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-09 00:11:02'),
	(41, 3, 'LOGOUT', 'Logout realizado', 'tbl_usuarios', 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-09 00:13:18'),
	(42, 2, 'LOGIN', 'Login realizado', 'tbl_usuarios', 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-09 00:13:23'),
	(43, 2, 'CRIAR_CURSO', 'Criou curso: Curso Febre Aftosa', 'tbl_cursos', 4, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-09 00:14:56'),
	(44, 2, 'EDITAR_CURSO', 'Editou curso: Curso Febre Aftosa', 'tbl_cursos', 4, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-09 00:17:11'),
	(45, 2, 'LOGOUT', 'Logout realizado', 'tbl_usuarios', 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-09 00:20:07'),
	(46, 3, 'LOGIN', 'Login realizado', 'tbl_usuarios', 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-09 00:20:14'),
	(47, 3, 'LOGOUT', 'Logout realizado', 'tbl_usuarios', 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-09 00:23:10'),
	(48, 3, 'LOGIN', 'Login realizado', 'tbl_usuarios', 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-09 00:23:19'),
	(49, 3, 'LOGOUT', 'Logout realizado', 'tbl_usuarios', 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-09 00:28:10'),
	(50, 2, 'LOGIN', 'Login realizado', 'tbl_usuarios', 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-09 00:28:24'),
	(51, 2, 'MATRICULAR', 'Matriculou Laura Regina da Silva Morais no curso #4 (status: ATIVA)', 'tbl_matriculas', 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-09 00:29:42'),
	(52, 2, 'LOGOUT', 'Logout realizado', 'tbl_usuarios', 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-09 00:29:59'),
	(53, 3, 'LOGIN', 'Login realizado', 'tbl_usuarios', 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-09 00:30:06'),
	(54, 2, 'LOGIN', 'Login realizado', 'tbl_usuarios', 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-09 15:45:35'),
	(55, 2, 'LOGOUT', 'Logout realizado', 'tbl_usuarios', 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-09 15:58:49'),
	(56, 3, 'LOGIN', 'Login realizado', 'tbl_usuarios', 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-09 15:58:57'),
	(57, 3, 'LOGOUT', 'Logout realizado', 'tbl_usuarios', 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-09 16:19:57'),
	(58, 3, 'LOGIN', 'Login realizado', 'tbl_usuarios', 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-09 16:20:05'),
	(59, 3, 'LOGOUT', 'Logout realizado', 'tbl_usuarios', 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-09 16:26:11'),
	(60, 2, 'LOGIN', 'Login realizado', 'tbl_usuarios', 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-09 16:26:16'),
	(61, 2, 'CRIAR_CURSO', 'Criou curso: 2° TURMA CURSO CAPACITAÇÃO DE EVENTOS PECUÁRIOS', 'tbl_cursos', 5, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-09 16:29:46'),
	(62, 2, 'LOGOUT', 'Logout realizado', 'tbl_usuarios', 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-09 16:31:40'),
	(63, 3, 'LOGIN', 'Login realizado', 'tbl_usuarios', 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-09 16:31:56'),
	(64, 3, 'LOGOUT', 'Logout realizado', 'tbl_usuarios', 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-09 16:32:51'),
	(65, 2, 'LOGIN', 'Login realizado', 'tbl_usuarios', 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-09 16:32:55'),
	(66, 2, 'LOGOUT', 'Logout realizado', 'tbl_usuarios', 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-09 16:33:58'),
	(67, 3, 'LOGIN', 'Login realizado', 'tbl_usuarios', 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-09 16:34:07'),
	(68, 3, 'LOGOUT', 'Logout realizado', 'tbl_usuarios', 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-09 16:35:23'),
	(69, 2, 'LOGIN', 'Login realizado', 'tbl_usuarios', 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-09 16:35:27'),
	(70, 2, 'EMITIR_CERT_LOTE', 'Emitidos 1 certificados para curso #4', 'tbl_cursos', 4, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-09 16:36:04'),
	(71, 2, 'LOGOUT', 'Logout realizado', 'tbl_usuarios', 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-09 16:36:08'),
	(72, 3, 'LOGIN', 'Login realizado', 'tbl_usuarios', 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-09 16:36:15'),
	(73, 3, 'LOGOUT', 'Logout realizado', 'tbl_usuarios', 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-09 16:37:12'),
	(74, 3, 'LOGIN', 'Login realizado', 'tbl_usuarios', 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-09 16:37:27'),
	(75, 3, 'LOGOUT', 'Logout realizado', 'tbl_usuarios', 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-09 16:38:21'),
	(76, 2, 'LOGIN', 'Login realizado', 'tbl_usuarios', 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-09 16:38:26'),
	(77, 2, 'EDITAR_CURSO', 'Editou curso: Curso de Bovinos', 'tbl_cursos', 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-09 16:44:32'),
	(78, 2, 'LOGOUT', 'Logout realizado', 'tbl_usuarios', 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-09 16:44:56'),
	(79, 3, 'LOGIN', 'Login realizado', 'tbl_usuarios', 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-09 16:45:03'),
	(80, 3, 'LOGOUT', 'Logout realizado', 'tbl_usuarios', 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-09 17:03:13'),
	(81, 2, 'LOGIN', 'Login realizado', 'tbl_usuarios', 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-09 17:03:19'),
	(82, 2, 'EDITAR_CURSO', 'Editou curso: Curso Febre Aftosa', 'tbl_cursos', 4, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-09 17:03:26'),
	(83, 2, 'LOGOUT', 'Logout realizado', 'tbl_usuarios', 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-09 17:03:31'),
	(84, 3, 'LOGIN', 'Login realizado', 'tbl_usuarios', 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-09 17:03:37'),
	(85, 3, 'LOGOUT', 'Logout realizado', 'tbl_usuarios', 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-09 22:03:46'),
	(86, 2, 'LOGIN', 'Login realizado', 'tbl_usuarios', 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-09 22:03:55'),
	(87, 2, 'CRIAR_CURSO', 'Criou curso: Treinamento de morcegos', 'tbl_cursos', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-09 22:21:46'),
	(88, 2, 'EDITAR_CURSO', 'Editou curso: Treinamento de morcegos', 'tbl_cursos', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-09 22:26:15'),
	(89, 2, 'EDITAR_AULAS', 'Atualizou aulas do curso 6', 'tbl_cursos', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-09 22:46:30'),
	(90, 2, 'CRIAR_CURSO', 'Criou curso: Treinamento teste do teste para teste', 'tbl_cursos', 7, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-09 23:01:44'),
	(91, 2, 'EDITAR_AULAS', 'Atualizou aulas do curso 7', 'tbl_cursos', 7, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-09 23:03:25'),
	(92, 2, 'EDITAR_CURSO', 'Editou curso: Treinamento teste do teste para teste', 'tbl_cursos', 7, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-09 23:03:34'),
	(93, 2, 'EDITAR_AVALIACAO', 'Salvou avaliação curso 7', 'tbl_cursos', 7, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-09 23:04:00'),
	(94, 2, 'EDITAR_CURSO', 'Editou curso: Treinamento teste do teste para teste', 'tbl_cursos', 7, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-09 23:04:29'),
	(95, 2, 'EDITAR_AULAS', 'Atualizou aulas do curso 7', 'tbl_cursos', 7, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-09 23:04:47'),
	(96, 2, 'EDITAR_AULAS', 'Atualizou aulas do curso 7', 'tbl_cursos', 7, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-09 23:12:09'),
	(97, 2, 'EDITAR_AULAS', 'Atualizou aulas do curso 7', 'tbl_cursos', 7, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-09 23:12:19'),
	(98, 2, 'EDITAR_CURSO', 'Editou curso: Treinamento teste do teste para teste', 'tbl_cursos', 7, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-09 23:12:30'),
	(99, 2, 'EDITAR_CURSO', 'Editou curso: Treinamento de morcegos', 'tbl_cursos', 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-09 23:25:09'),
	(100, 2, 'CRIAR_CURSO', 'Criou curso: Curso de Bombeiros', 'tbl_cursos', 8, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-09 23:27:31'),
	(101, 2, 'EDITAR_CURSO', 'Editou curso: Curso de Bombeiros', 'tbl_cursos', 8, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-09 23:27:51'),
	(102, 2, 'EDITAR_AVALIACAO', 'Salvou avaliação curso 8', 'tbl_cursos', 8, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-09 23:27:59'),
	(103, 2, 'EDITAR_CURSO', 'Editou curso: Curso de Bombeiros', 'tbl_cursos', 8, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-09 23:28:09'),
	(104, 2, 'LOGIN', 'Login realizado', 'tbl_usuarios', 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-30 23:15:00'),
	(105, 2, 'LOGOUT', 'Logout realizado', 'tbl_usuarios', 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-30 23:23:57'),
	(106, 3, 'LOGIN', 'Login realizado', 'tbl_usuarios', 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-30 23:24:03'),
	(107, 3, 'LOGOUT', 'Logout realizado', 'tbl_usuarios', 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-30 23:33:29'),
	(108, 2, 'LOGIN', 'Login realizado', 'tbl_usuarios', 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-30 23:33:41'),
	(109, 2, 'CRIAR_USUARIO', 'Criou veterinário: IDEMAR FORMIGA', 'tbl_usuarios', 4, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-30 23:35:02'),
	(110, 2, 'LOGOUT', 'Logout realizado', 'tbl_usuarios', 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-30 23:42:35'),
	(111, 2, 'LOGIN', 'Login realizado', 'tbl_usuarios', 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-30 23:43:28'),
	(112, 2, 'LOGOUT', 'Logout realizado', 'tbl_usuarios', 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-31 00:01:23'),
	(113, 2, 'LOGIN', 'Login realizado', 'tbl_usuarios', 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-31 16:10:17'),
	(114, 2, 'EMITIR_CERT', 'Certificado emitido: C69N-7JJG-FS67 para IDEMAR FORMIGA', 'tbl_certificados', 4, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-31 16:12:24'),
	(115, 2, 'LOGOUT', 'Logout realizado', 'tbl_usuarios', 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-31 16:13:16'),
	(116, 2, 'LOGIN', 'Login realizado', 'tbl_usuarios', 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-31 16:13:24'),
	(117, 2, 'LOGOUT', 'Logout realizado', 'tbl_usuarios', 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-31 16:13:30'),
	(118, 3, 'LOGIN', 'Login realizado', 'tbl_usuarios', 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-31 16:13:36'),
	(119, 3, 'LOGOUT', 'Logout realizado', 'tbl_usuarios', 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-31 16:14:31'),
	(120, 2, 'LOGIN', 'Login realizado', 'tbl_usuarios', 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-02 18:14:36'),
	(121, 2, 'LOGOUT', 'Logout realizado', 'tbl_usuarios', 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-03 19:13:37');

-- Copiando dados para a tabela crmv_cursos.tbl_materiais: ~17 rows (aproximadamente)
INSERT IGNORE INTO `tbl_materiais` (`material_id`, `curso_id`, `modulo_id`, `nome_arquivo`, `nome_original`, `tamanho`, `tipo_mime`, `criado_em`, `criado_por`) VALUES
	(1, 1, NULL, 'mat_1_1772848884_06e56d3f.pdf', 'Proposta sistema Projeto Social.pdf', 217596, 'application/pdf', '2026-03-06 23:01:24', 2),
	(2, 2, NULL, 'mat_2_1772852805_a6b2daf0.pdf', 'Turma_02___Treinamento_em_Vigilância_e_Coleta_para_envio_de_Amostras_para_Diagnóstico_de_Doenças_em_Peixes_de_Cultivo-Certificado_Turma_02___Treinamento_em_Vigilância_e_Coleta_para_envio_de_Amostras_para_Diagn.pdf', 540526, 'application/pdf', '2026-03-07 00:06:45', 2),
	(3, 2, NULL, 'mat_2_1772852805_9f003dc5.docx', 'proposta_estilizada_projeto_social.docx', 31524, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', '2026-03-07 00:06:45', 2),
	(4, 2, NULL, 'mat_2_1772852805_560e67ac.pdf', 'Proposta sistema Projeto Social.pdf', 217596, 'application/pdf', '2026-03-07 00:06:45', 2),
	(5, 2, NULL, 'mat_2_1772852805_64102f37.docx', 'proposta_sistema_projeto_social.docx', 27336, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', '2026-03-07 00:06:45', 2),
	(6, 3, NULL, 'mat_3_1772853053_5f272bdd.pdf', 'Proposta sistema Projeto Social.pdf', 217596, 'application/pdf', '2026-03-07 00:10:53', 1),
	(7, 4, NULL, 'mat_4_1773026231_4009c658.pdf', 'mat_2_1772852805_a6b2daf0.pdf', 540526, 'application/pdf', '2026-03-09 00:17:11', 2),
	(8, 4, NULL, 'mat_4_1773026231_49391c9c.pdf', 'Turma_02___Treinamento_em_Vigilância_e_Coleta_para_envio_de_Amostras_para_Diagnóstico_de_Doenças_em_Peixes_de_Cultivo-Certificado_Turma_02___Treinamento_em_Vigilância_e_Coleta_para_envio_de_Amostras_para_Diagn.pdf', 540526, 'application/pdf', '2026-03-09 00:17:11', 2),
	(9, 4, NULL, 'mat_4_1773026231_de974e1c.docx', 'proposta_estilizada_projeto_social.docx', 31524, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', '2026-03-09 00:17:11', 2),
	(10, 4, NULL, 'mat_4_1773026231_7924f433.docx', 'proposta_sistema_projeto_social.docx', 27336, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', '2026-03-09 00:17:11', 2),
	(11, 5, NULL, 'mat_5_1773084586_c73a585b.docx', 'mat_4_1773026231_7924f433.docx', 27336, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', '2026-03-09 16:29:46', 2),
	(12, 5, NULL, 'mat_5_1773084586_28531bae.docx', 'mat_4_1773026231_de974e1c.docx', 31524, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', '2026-03-09 16:29:46', 2),
	(13, 7, NULL, 'mat_7_1773108214_1588c4e2.docx', 'mat_4_1773026231_7924f433.docx', 27336, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', '2026-03-09 23:03:34', 2),
	(14, 7, NULL, 'mat_7_1773108214_04ca3551.docx', 'mat_4_1773026231_de974e1c.docx', 31524, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', '2026-03-09 23:03:34', 2),
	(15, 7, NULL, 'mat_7_1773108214_b5c96d32.pdf', 'mat_4_1773026231_49391c9c.pdf', 540526, 'application/pdf', '2026-03-09 23:03:34', 2),
	(16, 7, NULL, 'mat_7_1773108214_cca48dff.pdf', 'mat_4_1773026231_4009c658.pdf', 540526, 'application/pdf', '2026-03-09 23:03:34', 2),
	(17, 8, NULL, 'mat_8_1773109671_de66283b.docx', 'mat_4_1773026231_7924f433.docx', 27336, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', '2026-03-09 23:27:51', 2);

-- Copiando dados para a tabela crmv_cursos.tbl_matriculas: ~4 rows (aproximadamente)
INSERT IGNORE INTO `tbl_matriculas` (`matricula_id`, `usuario_id`, `curso_id`, `status`, `nota_final`, `presenca_percent`, `certificado_gerado`, `certificado_codigo`, `certificado_emitido_em`, `progresso_ead`, `matriculado_em`, `atualizado_em`) VALUES
	(1, 3, 1, 'CONCLUIDA', NULL, NULL, 1, 'QA5P-E5TN-GSZY', '2026-03-06 23:01:57', 0, '2026-03-06 23:01:57', '2026-03-06 23:01:57'),
	(2, 3, 2, 'CONCLUIDA', NULL, NULL, 1, 'CWW6-JCD6-SNND', '2026-03-07 00:07:30', 0, '2026-03-07 00:07:30', '2026-03-07 00:07:30'),
	(3, 3, 4, 'ATIVA', NULL, NULL, 1, 'DPYU-W87C-J49J', '2026-03-09 16:36:04', 100, '2026-03-09 00:29:42', '2026-03-09 16:36:04'),
	(4, 4, 5, 'CONCLUIDA', NULL, NULL, 1, 'C69N-7JJG-FS67', '2026-03-31 16:12:24', 0, '2026-03-31 16:12:24', '2026-03-31 16:12:24');

-- Copiando dados para a tabela crmv_cursos.tbl_modulos: ~3 rows (aproximadamente)
INSERT IGNORE INTO `tbl_modulos` (`modulo_id`, `curso_id`, `titulo`, `descricao`, `ordem`, `criado_em`) VALUES
	(1, 6, 'Aulas do Curso', NULL, 1, '2026-03-09 22:46:30'),
	(2, 7, 'Aulas do Curso', NULL, 1, '2026-03-09 23:03:25'),
	(3, 8, 'Aulas do Curso', NULL, 1, '2026-03-09 23:27:44');

-- Copiando dados para a tabela crmv_cursos.tbl_perfis: ~2 rows (aproximadamente)
INSERT IGNORE INTO `tbl_perfis` (`perfil_id`, `perfil_nome`, `perfil_descricao`, `ativo`, `criado_em`) VALUES
	(1, 'Administrador', 'Acesso total ao sistema', 1, '2026-03-06 17:14:27'),
	(2, 'Veterinário', 'Acesso à área do participante', 1, '2026-03-06 17:14:27');

-- Copiando dados para a tabela crmv_cursos.tbl_progresso_aulas: ~0 rows (aproximadamente)

-- Copiando dados para a tabela crmv_cursos.tbl_progresso_materiais: ~4 rows (aproximadamente)
INSERT IGNORE INTO `tbl_progresso_materiais` (`id`, `matricula_id`, `material_id`, `baixado_em`) VALUES
	(1, 3, 7, '2026-03-09 15:59:35'),
	(2, 3, 8, '2026-03-09 15:59:39'),
	(3, 3, 9, '2026-03-09 15:59:42'),
	(4, 3, 10, '2026-03-09 15:59:46');

-- Copiando dados para a tabela crmv_cursos.tbl_questoes: ~4 rows (aproximadamente)
INSERT IGNORE INTO `tbl_questoes` (`questao_id`, `avaliacao_id`, `enunciado`, `tipo`, `pontos`, `ordem`, `ativo`) VALUES
	(1, 1, 'Como você avalia a organização geral do curso?', 'MULTIPLA', 1.00, 1, 1),
	(2, 1, 'Como você avalia a organização geral do curso?', 'MULTIPLA', 1.00, 2, 1),
	(3, 1, 'Como você avalia a organização geral do curso?', 'MULTIPLA', 1.00, 3, 1),
	(4, 2, 'asdasdasdasdas', 'MULTIPLA', 1.00, 1, 1);

-- Copiando dados para a tabela crmv_cursos.tbl_respostas_avaliacao: ~0 rows (aproximadamente)

-- Copiando dados para a tabela crmv_cursos.tbl_tentativas_avaliacao: ~0 rows (aproximadamente)

-- Copiando dados para a tabela crmv_cursos.tbl_usuarios: ~4 rows (aproximadamente)
INSERT IGNORE INTO `tbl_usuarios` (`usuario_id`, `perfil_id`, `nome_completo`, `cpf`, `rg`, `data_nascimento`, `sexo`, `email`, `telefone`, `celular`, `crmv_numero`, `crmv_uf`, `especialidade`, `instituicao`, `cep`, `logradouro`, `numero`, `complemento`, `bairro`, `cidade`, `uf`, `senha_hash`, `senha_salt`, `token_reset`, `token_expira`, `ultimo_acesso`, `tentativas_login`, `bloqueado_ate`, `ativo`, `foto_perfil`, `criado_em`, `atualizado_em`, `criado_por`) VALUES
	(1, 1, 'Administrador CRMV/TO', '000.000.000-00', NULL, NULL, NULL, 'admin@crmvto.gov.br', NULL, NULL, NULL, 'TO', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '$2y$12$iFfCICMMbcmQaVbxV0aZ..U.nSNb0X6Ybu2Ge8.MgEInJ6Y4idaAG', '1c003c3db25d4845573f3552ed9b7229', NULL, NULL, '2026-03-07 00:08:41', 0, NULL, 1, NULL, '2026-03-06 17:19:47', '2026-03-07 00:08:41', NULL),
	(2, 1, 'Ian Leandro Cardoso Formiga', '04426330731', '1140811', '1997-12-06', 'M', 'formigaian@gmail.com', '63992863557', '63992863557', '123456852', 'TO', 'Pets', 'Sant Cane', '77021668', 'Quadra ARSE 51 Alameda 9', '9', 'Casa', 'Plano Diretor Sul', 'Palmas', 'TO', '$2y$12$aDB2vaPhI5i9FeS7TbZPueTIjJbAW29oCIRWs3iWOkw.gFBZTRKWS', 'f4194a06cf6d890409e15afe9d20e8fd', NULL, NULL, '2026-04-02 18:14:36', 0, NULL, 1, NULL, '2026-03-06 22:02:32', '2026-04-02 18:14:36', 1),
	(3, 2, 'Laura Regina da Silva Morais', '95814844000', '1140811', '2000-12-06', 'F', 'lrmorais29@gmail.com', '63992863557', '63992863557', '123546', 'TO', 'Pets', 'Sant Cane', '77021668', 'Quadra ARSE 51 Alameda 9', '9', NULL, 'Plano Diretor Sul', 'Palmas', 'TO', '$2y$12$oHa3CxhwokYKnJS.ucNVMusohr0JtGetyk31pJEnYFuLGy4V32bzG', 'd866be9306040c764ccca87bb54a50e8', NULL, NULL, '2026-03-31 16:13:36', 0, NULL, 1, NULL, '2026-03-06 22:45:13', '2026-03-31 16:13:36', 2),
	(4, 2, 'IDEMAR FORMIGA', '08679442003', '1140811', '1997-12-06', 'M', 'iformiga06@gmail.com', '63992863557', '63992863557', '121334', 'TO', 'Pets', NULL, '77021668', 'Quadra ARSE 51 Alameda 9', '9', 'asdasd', 'Plano Diretor Sul', 'Palmas', 'TO', '$2y$12$pcobpZYKOo0caz0XCZ75CupNOw./So7dEhZrfClwwAHYXyKm2shtC', 'ab2d47586b023f92107c9b62e3f4cc83', NULL, NULL, NULL, 0, NULL, 1, NULL, '2026-03-30 23:35:02', NULL, 2);

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;

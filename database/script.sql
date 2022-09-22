CREATE TABLE `migrations` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `migration` varchar(191) NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `password_resets` (
  `email` varchar(191) NOT NULL,
  `token` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `users` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `email` varchar(64) NOT NULL UNIQUE,
  `password` varchar(191) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `permissao` enum('ADMINISTRADOR','USUARIO','INATIVO') NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `status` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `chave` varchar(15) NOT NULL UNIQUE,
  `descricao` varchar(63) NOT NULL,
  `cor` char(7) NOT NULL DEFAULT '#000000',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `periodo_letivos` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `nome` varchar(31) NOT NULL UNIQUE,
  `id_sigecad` int NOT NULL UNIQUE,
  `descricao` varchar(64) NULL,
  `sufixo` varchar(31) NULL,
  `inicio_auto_increment` int DEFAULT NULL,
  `ativo` BOOLEAN NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `macros` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `nome` varchar(31) NOT NULL,
  `arquivo` varchar(63) DEFAULT NULL,
  `periodo_letivo_id` int NOT NULL,
  `link_servidor_moodle` varchar(63) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
   FOREIGN KEY (`periodo_letivo_id`) REFERENCES `periodo_letivos` (`id`) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `super_macros` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `descricao` varchar(31) NOT NULL,
  `macro_padrao_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
   FOREIGN KEY (`macro_padrao_id`) REFERENCES `macros` (`id`) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `macros_super_macros` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `macro_id` int NOT NULL,
  `super_macro_id` int NOT NULL,
  `ordem` int NOT NULL,
  `campo` varchar(31) NOT NULL,
  `operador` varchar(15) NOT NULL,
  `valor` varchar(255) NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
   FOREIGN KEY (`macro_id`) REFERENCES `macros` (`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
   FOREIGN KEY (`super_macro_id`) REFERENCES `super_macros` (`id`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `faculdades` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `sigla` varchar(15) NOT NULL,
  `nome` varchar(63) NOT NULL,
  `auto_increment_ref` int DEFAULT NULL,
  `ativo` boolean NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `cursos` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `nome` varchar(127) NOT NULL,
  `auto_increment_ref` int DEFAULT NULL,
  `faculdade_id` int NOT NULL,
  `curso_key` varchar(15) DEFAULT NULL,
  `ativo` boolean NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  FOREIGN KEY (`faculdade_id`) REFERENCES `faculdades` (`id`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `periodo_letivos_categorias` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `curso_id`  int NOT NULL,
  `periodo_letivo_id` int NOT NULL,
  `categoria_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  UNIQUE (`curso_id`, `periodo_letivo_id`),
  FOREIGN KEY (`curso_id`) REFERENCES `cursos` (`id`) ON UPDATE CASCADE ON DELETE CASCADE,
  FOREIGN KEY (`periodo_letivo_id`) REFERENCES `periodo_letivos` (`id`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `pl_disciplinas_academicos` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `curso_id`  int NOT NULL,
  `periodo_letivo_id` int NOT NULL,
  `disciplina` varchar(255) NOT NULL,
  `estudantes` TEXT DEFAULT NULL,
  `disciplina_key` bigint,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  FOREIGN KEY (`curso_id`) REFERENCES `cursos` (`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
  FOREIGN KEY (`periodo_letivo_id`) REFERENCES `periodo_letivos` (`id`) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `modalidades` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `sigla` varchar(31) NOT NULL UNIQUE,
  `descricao` varchar(63) NOT NULL,
  `visivel` boolean NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `objetivo_salas` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `sigla` varchar(31) NOT NULL UNIQUE,
  `descricao` varchar(63) NOT NULL,
  `visivel` boolean NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `lote_salas` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `descricao` varchar(63) NOT NULL UNIQUE,
  `periodo_letivo_id` int NOT NULL,
  `faculdade_id` int NOT NULL,
  `curso_id` int DEFAULT NULL,
  `is_salas_criadas` boolean NOT NULL,
  `is_estudantes_inseridos` boolean NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  FOREIGN KEY (`periodo_letivo_id`) REFERENCES `periodo_letivos` (`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
  FOREIGN KEY (`faculdade_id`) REFERENCES `faculdades` (`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
  FOREIGN KEY (`curso_id`) REFERENCES `cursos` (`id`) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `grupo_lotes_simplificados` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `descricao` varchar(63) NOT NULL,
  `auto_export_estudantes` boolean DEFAULT 0,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `lote_salas_simplificados` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `grupo_id` int NOT NULL,
  `descricao` varchar(63) NOT NULL UNIQUE,
  `sala_provao_id` int DEFAULT NULL,
  `servidor_moodle_id` int NOT NULL,
  `super_macro_id` int DEFAULT NULL,
  `sufixo` varchar(63) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  FOREIGN KEY (`grupo_id`) REFERENCES `grupo_lotes_simplificados` (`id`) ON UPDATE CASCADE ON DELETE CASCADE,
  FOREIGN KEY (`servidor_moodle_id`) REFERENCES `servidores_moodle` (`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
  FOREIGN KEY (`super_macro_id`) REFERENCES `super_macros` (`id`) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `salas_simplificadas` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `nome_sala` varchar(255) NOT NULL,
  `professor_id` int DEFAULT NULL,
  `periodo_letivo_id` int NOT NULL,
  `curso_id` int DEFAULT NULL,
  `disciplina_key` varchar(31) DEFAULT NULL,
  `periodo_letivo_key` int DEFAULT NULL,
  `turma_id` int NOT NULL,
  `turma_nome` varchar(15) NOT NULL,
  `carga_horaria_total_disciplina` decimal(5,2) DEFAULT NULL,
  `avaliacao` enum('nota','conceito') DEFAULT NULL,
  `sala_moodle_id` int DEFAULT NULL,
  `link_moodle` varchar(255) DEFAULT NULL,
  `lote_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  FOREIGN KEY (`professor_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
  FOREIGN KEY (`periodo_letivo_id`) REFERENCES `periodo_letivos` (`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
  FOREIGN KEY (`curso_id`) REFERENCES `cursos` (`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
  FOREIGN KEY (`lote_id`) REFERENCES `lote_salas_simplificados` (`id`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `salas` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `solicitante_id` int NOT NULL,
  `email` varchar (63) NOT NULL,
  `curso_id` int NOT NULL,
  `nome_sala` varchar(255) NOT NULL,
  `modalidade` varchar(31) NULL,
  `objetivo_sala` varchar(31) NULL,
  `senha_aluno` varchar(255) DEFAULT NULL,
  `observacao` TEXT DEFAULT NULL,
  `status_id` int NOT NULL DEFAULT 1,
  `estudantes` TEXT DEFAULT NULL,
  `mensagem` varchar(255) DEFAULT NULL,
  `periodo_letivo_id` int NOT NULL,
  `carga_horaria_total_disciplina` decimal(5,2) DEFAULT NULL,
  `turma_nome` varchar(15) DEFAULT NULL,
  `avaliacao` enum('nota','conceito') DEFAULT NULL,
  `turma_id` int DEFAULT NULL,
  `periodo_letivo_key` int DEFAULT NULL,
  `disciplina_key` varchar(31) DEFAULT NULL,
  `sala_moodle_id` int DEFAULT NULL,
  `macro_id` int NOT NULL,
  `lote_salas_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  FOREIGN KEY (`status_id`) REFERENCES `status` (`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
  FOREIGN KEY (`solicitante_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
  FOREIGN KEY (`curso_id`) REFERENCES `cursos` (`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
  FOREIGN KEY (`periodo_letivo_id`) REFERENCES `periodo_letivos` (`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
  FOREIGN KEY (`macro_id`) REFERENCES `macros` (`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
  FOREIGN KEY (`lote_salas_id`) REFERENCES `lote_salas` (`id`) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `salas_old` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `nome_professor` varchar(255) NOT NULL,
  `email` varchar (63) NOT NULL,
  `faculdade` varchar(255) NOT NULL,
  `curso` varchar(255) NOT NULL,
  `nome_sala` varchar(255) NOT NULL,
  `modalidade` varchar(31) NULL,
  `objetivo_sala` varchar(31) NULL,
  `senha_aluno` varchar(255) DEFAULT NULL,
  `senha_professor` varchar(255) NOT NULL,
  `observacao` TEXT DEFAULT NULL,
  `status_id` int NOT NULL DEFAULT 1,
  `mensagem` varchar(255) DEFAULT NULL,
  `macro_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  FOREIGN KEY (`status_id`) REFERENCES `status` (`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
  FOREIGN KEY (`macro_id`) REFERENCES `macros` (`id`) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `configuracoes` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `nome` varchar(31) NOT NULL,
  `valor` varchar(63) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


CREATE TABLE `buscadores` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `chave` varchar(31) NOT NULL,
  `entrada` varchar(31) NOT NULL,
  `macro_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  FOREIGN KEY (`macro_id`) REFERENCES `macros` (`id`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `servidores_moodle` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `nome` varchar(63) NOT NULL,
  `url` varchar(63) NOT NULL,
  `ativo` boolean NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `agenda` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `start` timestamp NOT NULL,
  `end` timestamp NULL DEFAULT NULL,
  `allDay` boolean NOT NULL,
  `maisDay` boolean NOT NULL,
  `backgroundColor` char(7) DEFAULT NULL,
  `ref_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  FOREIGN KEY (`ref_id`) REFERENCES `agenda` (`id`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `recursos` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `nome` varchar(63) NOT NULL,
  `descricao` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `gestores_recursos` (
  `recurso_id` int NOT NULL,
  `user_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`recurso_id`, `user_id`),
  FOREIGN KEY (`recurso_id`) REFERENCES `recursos` (`id`) ON UPDATE CASCADE ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `reservas` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `start` timestamp NOT NULL,
  `end` timestamp NULL DEFAULT NULL,
  `allDay` boolean NOT NULL,
  `maisDay` boolean NOT NULL,
  `backgroundColor` char(7) DEFAULT NULL,
  `ref_id` int DEFAULT NULL,
  `recurso_id` int NOT NULL,
  `usuario_id` int NOT NULL,
  `status_id` int NOT NULL DEFAULT 1,
  `observacao` TEXT DEFAULT NULL,
  `justificativa` TEXT DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  FOREIGN KEY (`ref_id`) REFERENCES `reservas` (`id`) ON UPDATE CASCADE ON DELETE CASCADE,
  FOREIGN KEY (`recurso_id`) REFERENCES `recursos` (`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
  FOREIGN KEY (`status_id`) REFERENCES `status` (`id`) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `unidades_organizacionais` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `nome` varchar(63) NOT NULL,
  `valor` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `status`(`chave`,`descricao`,`cor`) VALUES 
('ANALISE', 'Em Análise','#ffd700'),
('CONCLUIDO', 'Concluído','#00b900'),
('REJEITADO', 'Rejeitado','#ff5050'),
('DEFERIDO', 'Deferido','#00b900'),
('INDEFERIDO', 'Indeferido','#ff5050'),
('CANCELADO', 'Cancelado','#ff5050'),
('PROCESSO', 'Em Processo','#ffd700');

INSERT INTO `configuracoes`(`nome`,`valor`) VALUES 
('ARQUIVO_SAIDA', 'backup.mbz'),
('ARQUIVO_SALA_PADRAO', 'algumhashaq.mbz'),
('EMAIL_SUPORTE', 'alterar@mude.isso.com'),
('SEPARADOR_EMAIL',','),
('SUPER_MACRO_PADRAO','1'),
('SUFIXO_NOME_SALA',''),
('PERIODO_LETIVO_PADRAO','1'),
('REGEX_EMAILS_LIBERADOS','/.+@ufgd.edu.br/i'),
('TIMEZONE','-4'),
('OU_ROOT_DIR','OU=EAD,OU=ACADEMICOS,DC=ufgd,DC=edu,DC=br'),
('AD_COMPANY','UFGD - Universidade Federal da Grande Dourados'),
('AD_DEPARTMENT','EaD - Faculdede de Educação a Distância'),
('AD_USER_PRINCIPAL_NAME_SUFIXO','ufgd.edu.br'),
('AD_EMAIL_PADRAO_SUFIXO','academico.ufgd.edu.br');

INSERT INTO `modalidades`(`sigla`,`descricao`,`visivel`) VALUES 
('PRESENCIAL','Presencial',1),
('SEMIPRESENCIAL','Semipresencial',1),
('DISTANCIA','A Distância',1);

INSERT INTO `objetivo_salas`(`sigla`,`descricao`,`visivel`) VALUES 
('ENSINO','Ensino',1),
('PESQUISA','Pesquisa',1),
('EXTENSAO','Extensão',1),
('GESTAO','Gestão',1),
('OUTROS','Outros',1);

INSERT INTO `users`(`name`, `email`, `password`, `remember_token`, `permissao`) 
VALUES ('Tecnologia da Informação - EaD UFGD', 'ti.ead', '', '', 'ADMINISTRADOR');

DELIMITER $
 
CREATE TRIGGER Tgr_cursos_Insert AFTER INSERT
ON `cursos`
FOR EACH ROW
BEGIN
    INSERT INTO `periodo_letivos_categorias`(`periodo_letivo_id`,`curso_id`,`categoria_id`) 
		(SELECT periodo_letivos.id, NEW.id, NEW.auto_increment_ref+periodo_letivos.inicio_auto_increment FROM periodo_letivos);
END$
 
CREATE TRIGGER Tgr_periodo_letivos_Insert AFTER INSERT
ON `periodo_letivos`
FOR EACH ROW
BEGIN
    INSERT INTO `periodo_letivos_categorias`(`periodo_letivo_id`,`curso_id`,`categoria_id`) 
		(SELECT NEW.id, cursos.id, NEW.inicio_auto_increment+cursos.auto_increment_ref FROM cursos);
END$
 
DELIMITER ;


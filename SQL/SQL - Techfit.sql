CREATE DATABASE Techfit;
USE Techfit;

-- 1️⃣ Perfis (precisa existir antes de usuários)
CREATE TABLE perfis_acesso (
    id_perfil INT AUTO_INCREMENT PRIMARY KEY,
    nome_perfil VARCHAR(100),
    permissoes TEXT
);

-- 2️⃣ Aulas, Professor e Funcionário dependem entre si, então primeiro criamos Aulas sem FKs
CREATE TABLE Aulas (
    id_aula INT AUTO_INCREMENT PRIMARY KEY,
    local_ VARCHAR(100),
    modalidade VARCHAR(100),
    lotacao_maxima INT
);

-- 3️⃣ Professores
CREATE TABLE professor (
    id_professor INT AUTO_INCREMENT PRIMARY KEY,
    especializacao VARCHAR(100),
    nome_professor VARCHAR(100),
    id_aula INT,
    FOREIGN KEY(id_aula) REFERENCES Aulas (id_aula)
);

-- 4️⃣ Funcionários
CREATE TABLE funcionario (
    id_funcionario INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100),
    cpf_funcionario VARCHAR(15),
    cargo VARCHAR(100),
    telefone VARCHAR(50),
    id_aula INT,
    FOREIGN KEY(id_aula) REFERENCES Aulas (id_aula)
);

-- 5️⃣ Agora atualizamos Aulas para incluir os relacionamentos com professor e funcionário
ALTER TABLE Aulas
ADD COLUMN id_professor INT,
ADD COLUMN id_funcionario INT,
ADD FOREIGN KEY (id_professor) REFERENCES professor(id_professor),
ADD FOREIGN KEY (id_funcionario) REFERENCES funcionario(id_funcionario);

-- 6️⃣ Usuários (agora perfis_acesso já existe)
CREATE TABLE usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    senha VARCHAR(255),
    perfil ENUM("aluno", "professor", "admin"),
    foto BLOB,
    id_perfil INT,
    FOREIGN KEY (id_perfil) REFERENCES perfis_acesso(id_perfil)
);

-- 7️⃣ Mensagens (precisa que usuarios já exista)
CREATE TABLE mensagens (
    id_mensagem INT AUTO_INCREMENT PRIMARY KEY,
    conteudo TEXT,
    data_envio DATETIME,
    id_usuario_remetente INT,
    id_usuario_destinatario INT,
    FOREIGN KEY (id_usuario_remetente) REFERENCES usuarios(id_usuario),
    FOREIGN KEY (id_usuario_destinatario) REFERENCES usuarios(id_usuario)
);

-- 8️⃣ Avaliações Físicas
CREATE TABLE avaliacoes_fisicas (
    id_avaliacao INT AUTO_INCREMENT PRIMARY KEY,
    data_avaliacao DATE,
    peso DECIMAL,
    altura DECIMAL,
    percentual_gordura DECIMAL,
    medidas TEXT,
    id_usuario INT,
    FOREIGN KEY(id_usuario) REFERENCES usuarios (id_usuario)
);

-- 9️⃣ Acessos
CREATE TABLE acessos (
    id_acesso INT AUTO_INCREMENT PRIMARY KEY,
    tempo_permanencia INT,
    data_hora DATETIME,
    id_usuario INT,
    FOREIGN KEY(id_usuario) REFERENCES usuarios (id_usuario)
);

-- 🔟 Agendamentos
CREATE TABLE agendamentos (
    id_agendamento INT AUTO_INCREMENT PRIMARY KEY,
    data_hora DATETIME,
    objetivo ENUM("Perda de peso", "Ganho de Massa", "Hipertrofia", "Saúde"),
    modalidade VARCHAR(100) DEFAULT NULL,
    status_ ENUM("Confirmado"),
    id_aula INT,
    id_usuario INT,
    FOREIGN KEY(id_aula) REFERENCES Aulas (id_aula),
    FOREIGN KEY(id_usuario) REFERENCES usuarios (id_usuario)
);


CREATE TABLE pagamentos (
    id_pagamento INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    plano ENUM('FIT', 'TECH', 'BLACK') NOT NULL,
    valor DECIMAL(8,2) NOT NULL,
    data_pagamento DATETIME DEFAULT CURRENT_TIMESTAMP,
    status ENUM('Pago', 'Pendente') DEFAULT 'Pendente',
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
);

-- Dados de Usuários --
INSERT INTO usuarios (nome, email, senha, perfil, foto) VALUES
('Carlos Mendes', 'carlos@gmail.com', '1234', 'aluno', NULL),
('Fernanda Lima', 'fernanda@gmail.com', 'abcd', 'aluno', NULL),
('João Pereira', 'joao.p@gmail.com', 'senha1', 'aluno', NULL),
('Marina Costa', 'marina@gmail.com', 'pass123', 'professor', NULL),
('Roberto Silva', 'roberto@gmail.com', 'admin123', 'admin', NULL),
('Beatriz Souza', 'bia@gmail.com', 'xyz789', 'aluno', NULL),
('Lucas Andrade', 'lucas.a@gmail.com', 'senha99', 'professor', NULL),
('Juliana Rocha', 'juliana@gmail.com', 'abcd1234', 'aluno', NULL),
('Renato Alves', 'renato@gmail.com', 'r123', 'aluno', NULL),
('Camila Torres', 'camila@gmail.com', 'cami2025', 'aluno', NULL);

SELECT * FROM usuarios;


-- Dados de Avaliações Físicas --
INSERT INTO avaliacoes_fisicas (data_avaliacao, peso, altura, percentual_gordura, medidas, id_usuario) VALUES
('2025-01-10', 72.5, 1.75, 18.3, 'Braço: 33cm, Cintura: 85cm', 1),
('2025-02-15', 60.2, 1.65, 22.1, 'Braço: 28cm, Cintura: 70cm', 2),
('2025-03-12', 82.4, 1.80, 20.4, 'Braço: 36cm, Cintura: 88cm', 3),
('2025-01-20', 70.0, 1.70, 19.0, 'Braço: 32cm, Cintura: 83cm', 6),
('2025-04-05', 55.8, 1.60, 24.2, 'Braço: 27cm, Cintura: 72cm', 8),
('2025-03-28', 68.3, 1.73, 17.5, 'Braço: 31cm, Cintura: 80cm', 9),
('2025-05-02', 77.9, 1.82, 16.9, 'Braço: 35cm, Cintura: 87cm', 1),
('2025-06-10', 59.0, 1.62, 25.5, 'Braço: 26cm, Cintura: 71cm', 2),
('2025-06-22', 84.1, 1.85, 21.2, 'Braço: 37cm, Cintura: 89cm', 3),
('2025-07-01', 62.7, 1.68, 23.3, 'Braço: 29cm, Cintura: 76cm', 6);

SELECT * FROM avaliacoes_fisicas;


-- Dados de professores --
INSERT INTO professor (especializacao, nome_professor) VALUES
('Musculação', 'Marina Costa'),
('Yoga', 'Lucas Andrade'),
('Crossfit', 'Felipe Ramos'),
('Pilates', 'Sônia Lopes'),
('Spinning', 'Eduardo Nunes'),
('Treinamento Funcional', 'Paula Freitas'),
('Alongamento', 'Rafaela Silva'),
('Zumba', 'Carla Oliveira'),
('Natação', 'Ricardo Borges'),
('Dança', 'Priscila Mota');

SELECT * FROM professor;


-- Dados de funcionários --
INSERT INTO funcionario (nome, cpf_funcionario, cargo, telefone) VALUES
('Ana Souza', '123.456.789-00', 'Recepcionista', '(11)91234-5678'),
('Bruno Castro', '234.567.890-11', 'Limpeza', '(11)99876-5432'),
('Clara Mendes', '345.678.901-22', 'Gerente', '(11)98765-4321'),
('Diego Rocha', '456.789.012-33', 'Segurança', '(11)97654-3210'),
('Elisa Ramos', '567.890.123-44', 'Nutricionista', '(11)96543-2109'),
('Fabio Oliveira', '678.901.234-55', 'Financeiro', '(11)95432-1098'),
('Gustavo Lima', '789.012.345-66', 'Limpeza', '(11)94321-0987'),
('Helena Tavares', '890.123.456-77', 'Atendente', '(11)93210-9876'),
('Igor Campos', '901.234.567-88', 'Treinador', '(11)92109-8765'),
('Joana Melo', '012.345.678-99', 'Limpeza', '(11)91098-7654');

SELECT * FROM funcionario;


-- Dados de aulas --
INSERT INTO Aulas (local_, modalidade, lotacao_maxima) VALUES
('Sala 1', 'Musculação', 15),
('Sala 2', 'Yoga', 12),
('Sala 3', 'Crossfit', 10),
('Piscina', 'Natação', 8),
('Sala 4', 'Pilates', 10),
('Sala 5', 'Spinning', 14),
('Sala 6', 'Zumba', 18),
('Sala 7', 'Dança', 20),
('Sala 8', 'Funcional', 12),
('Área Aberta', 'Alongamento', 25);

SELECT * FROM Aulas;


-- Dados de mensagens --
INSERT INTO mensagens (conteudo, data_envio) VALUES
('Bom dia! Quando será a próxima aula de yoga?', '2025-05-01 09:00:00'),
('Lembrete: avaliação física marcada para amanhã.', '2025-05-02 08:00:00'),
('Seu plano vence em 5 dias.', '2025-05-03 10:30:00'),
('A academia estará fechada no feriado.', '2025-05-05 11:00:00'),
('Nova turma de spinning disponível!', '2025-05-06 13:00:00'),
('Alteração de horário da aula de pilates.', '2025-05-07 15:00:00'),
('Agendamento confirmado.', '2025-05-08 16:30:00'),
('Boas-vindas!', '2025-05-09 12:00:00'),
('Relatório de desempenho disponível.', '2025-05-10 18:00:00'),
('Dúvida sobre treino resolvida.', '2025-05-11 17:00:00');

SELECT * FROM mensagens;


-- Dados de acessos --
INSERT INTO acessos (tempo_permanencia, data_hora, id_usuario) VALUES
(45, '2025-05-01 07:30:00', 1),
(60, '2025-05-01 08:30:00', 2),
(30, '2025-05-02 09:00:00', 3),
(50, '2025-05-02 10:15:00', 6),
(55, '2025-05-03 11:00:00', 8),
(35, '2025-05-03 12:30:00', 9),
(70, '2025-05-04 13:45:00', 1),
(40, '2025-05-04 15:00:00', 2),
(65, '2025-05-05 16:10:00', 3),
(90, '2025-05-05 17:30:00', 6);


-- Dados de agendamentos --
INSERT INTO agendamentos (data_hora, objetivo, status_, id_aula, id_usuario) VALUES
('2025-05-10 08:00:00', 'Perda de peso', 'Confirmado', 2, 1),
('2025-05-11 09:30:00', 'Ganho de Massa', 'Confirmado', 1, 3),
('2025-05-12 10:00:00', 'Hipertrofia', 'Confirmado', 3, 6),
('2025-05-13 07:00:00', 'Saúde', 'Confirmado', 4, 2),
('2025-05-14 08:30:00', 'Ganho de Massa', 'Confirmado', 5, 8),
('2025-05-15 09:00:00', 'Perda de peso', 'Confirmado', 6, 9),
('2025-05-16 10:30:00', 'Hipertrofia', 'Confirmado', 7, 1),
('2025-05-17 11:00:00', 'Saúde', 'Confirmado', 8, 2),
('2025-05-18 12:00:00', 'Ganho de Massa', 'Confirmado', 9, 3),
('2025-05-19 13:00:00', 'Perda de peso', 'Confirmado', 10, 6);

SELECT * FROM agendamentos;


-- Dados de perfis --
INSERT INTO perfis_acesso (nome_perfil, permissoes) VALUES
('Carlos Mendes', 'Acessar aulas, visualizar desempenho'),
('Fernanda Lima', 'Acessar aulas, visualizar desempenho'),
('João Pereira', 'Acessar aulas, visualizar desempenho'),
('Marina Costa', 'Criar aulas, enviar mensagens'),
('Roberto Silva', 'Gerenciar usuários e dados'),
('Beatriz Souza', 'Acessar aulas, visualizar desempenho'),
('Lucas Andrade', 'Criar aulas, enviar mensagens'),
('Juliana Rocha', 'Acessar aulas, visualizar desempenho'),
('Renato Alves', 'Acessar aulas, visualizar desempenho'),
('Camila Torres', 'Acessar aulas, visualizar desempenho');

SELECT * FROM perfis_acesso;

INSERT INTO pagamentos (id_usuario, plano, valor, status) VALUES
(1, 'FIT', 99.90, 'Pago'),
(2, 'TECH', 119.90, 'Pago'),
(3, 'BLACK', 149.90, 'Pendente'),
(6, 'FIT', 99.90, 'Pago'),
(8, 'TECH', 119.90, 'Pago'),
(9, 'BLACK', 149.90, 'Pendente');
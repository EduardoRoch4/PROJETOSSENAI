-- Geração de Modelo físico
-- Sql ANSI 2003 - brModelo.


CREATE TABLE usuarios (
nome VARCHAR(100) NOT NULL,
email VARCHAR(100) NOT NULL,
senha VARCHAR(255),
perfil ENUM("aluno", "professor", "admin"),
foto BLOB,
id_usuario INT AUTO_INCREMENT PRIMARY KEY
);

CREATE TABLE avaliacoes_fisicas (
data_avaliacao DATE,
peso DECIMAL,
altura DECIMAL,
percentual_gordura DECIMAL,
medidas TEXT,
id_avaliacao INT AUTO_INCREMENT PRIMARY KEY,
id_usuario INT,
FOREIGN KEY(id_usuario) REFERENCES usuarios (id_usuario)
);

CREATE TABLE professor (
id_professor INT AUTO_INCREMENT PRIMARY KEY,
especialização VARCHAR(100),
nome_professor VARCHAR(100)
);

CREATE TABLE funcionario (
nome VARCHAR(100),
cpf_funcionario VARCHAR(15),
cargo VARCHAR(100),
telefone VARCHAR(50),
id_funcionario INT AUTO_INCREMENT PRIMARY KEY
);

CREATE TABLE Aulas (
local_ VARCHAR(100),
modalidade VARCHAR(100),
lotacao_maxima INT,
id_aula INT AUTO_INCREMENT PRIMARY KEY
);

CREATE TABLE mensagens (
conteudo TEXT,
data_envio DATETIME,
id_mensagem INT AUTO_INCREMENT PRIMARY KEY
);

CREATE TABLE acessos (
id_acesso INT AUTO_INCREMENT PRIMARY KEY,
tempo_permanencia INT,
data_hora DATETIME,
id_usuario INT,
FOREIGN KEY(id_usuario) REFERENCES usuarios (id_usuario)
);

CREATE TABLE agendamentos (
id_agendamento INT AUTO_INCREMENT PRIMARY KEY,
data_hora DATETIME,
objetivo ENUM("Perda de peso", "Ganho de Massa", "Hipertrofia", "Saúde"),
status_ ENUM("Confirmado"),
id_aula INT,
id_usuario INT,
FOREIGN KEY(id_aula) REFERENCES Aulas (id_aula),
FOREIGN KEY(id_usuario) REFERENCES usuarios (id_usuario)
);

CREATE TABLE perfis_acesso (
nome_perfil VARCHAR(100),
permissoes TEXT,
id_perfil INT AUTO_INCREMENT PRIMARY KEY
);

CREATE TABLE       Envia_Mensagem	 (
id_envia_mensagem INT AUTO_INCREMENT PRIMARY KEY,
id_usuario INT,
id_mensagem INT,
FOREIGN KEY(id_usuario) REFERENCES usuarios (id_usuario),
FOREIGN KEY(id_mensagem) REFERENCES mensagens (id_mensagem)
);

CREATE TABLE      Recebe_Mensagem	 (
id_recebe_mensagem INT AUTO_INCREMENT PRIMARY KEY,
id_usuario INT,
id_mensagem INT,
FOREIGN KEY(id_usuario) REFERENCES usuarios (id_usuario),
FOREIGN KEY(id_mensagem) REFERENCES mensagens (id_mensagem)
);

CREATE TABLE Faz (
id_faz INT AUTO_INCREMENT PRIMARY KEY,
id_aula INT,
id_professor INT,
FOREIGN KEY(id_aula) REFERENCES Aulas (id_aula),
FOREIGN KEY(id_professor) REFERENCES professor (id_professor)
);

CREATE TABLE Agenda (
id_agenda INT AUTO_INCREMENT PRIMARY KEY,
id_aula INT,
id_funcionario INT,
FOREIGN KEY(id_aula) REFERENCES Aulas (id_aula),
FOREIGN KEY(id_funcionario) REFERENCES funcionario (id_funcionario)
);

CREATE TABLE    Possui_Perfil	 (
id_possui_perfil INT AUTO_INCREMENT PRIMARY KEY,
id_perfil INT,
id_usuario INT,
FOREIGN KEY(id_perfil) REFERENCES perfis_acesso (id_perfil),
FOREIGN KEY(id_usuario) REFERENCES usuarios (id_usuario)
);

CREATE TABLE    Ministra_Aula	 (
id_ministra INT AUTO_INCREMENT PRIMARY KEY,
id_aula INT,
id_usuario INT,
FOREIGN KEY(id_aula) REFERENCES Aulas (id_aula),
FOREIGN KEY(id_usuario) REFERENCES usuarios (id_usuario)
);

-- ================================================
-- SCRIPT DE CRIAÇÃO DO BANCO DE DADOS: simulado_saep_db
-- Sistema: Controle de Estoque Serjão Materiais
-- Tecnologias: PHP, MySQL, HTML, CSS
-- ================================================

-- Remove o banco existente (para recriar do zero)
DROP DATABASE IF EXISTS simulado_saep_db;

-- Cria o banco
CREATE DATABASE simulado_saep_db CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE simulado_saep_db;

-- ================================================
-- TABELA DE USUÁRIOS
-- ================================================
CREATE TABLE usuarios (
  id_usuario INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(100) NOT NULL,
  email VARCHAR(100) UNIQUE NOT NULL,
  senha VARCHAR(255) NOT NULL
);

-- Usuários iniciais
INSERT INTO usuarios (nome, email, senha) VALUES
('Administrador', 'admin@serjao.com', MD5('12345')),
('João Silva', 'joao@serjao.com', MD5('12345')),
('Maria Souza', 'maria@serjao.com', MD5('12345')),
('Aluno', 'aluno@serjao.com', MD5('123')),
('Sergio Luiz', 'sergio@serjao.com', MD5('123'));

-- ================================================
-- TABELA DE PRODUTOS
-- ================================================
CREATE TABLE produtos (
  id_produto INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(100) NOT NULL,
  descricao TEXT,
  categoria VARCHAR(50),
  unidade_medida VARCHAR(20),
  quantidade_minima INT DEFAULT 0,
  quantidade_atual INT DEFAULT 0
);

-- Produtos iniciais
INSERT INTO produtos (nome, descricao, categoria, unidade_medida, quantidade_minima, quantidade_atual) VALUES
('Cimento CP-II', 'Saco de 50kg para fundações e alvenaria.', 'Fundação', 'saco', 10, 25),
('Tinta Branca Premium', 'Lata 18L para acabamento de paredes.', 'Acabamento', 'lata', 5, 8),
('Argamassa AC-II', 'Saco de 20kg para revestimentos cerâmicos.', 'Revestimento', 'saco', 8, 10);

-- ================================================
-- TABELA DE MOVIMENTAÇÕES
-- ================================================
CREATE TABLE movimentacoes (
  id_movimentacao INT AUTO_INCREMENT PRIMARY KEY,
  id_produto INT NOT NULL,
  tipo ENUM('entrada', 'saida') NOT NULL,
  quantidade INT NOT NULL,
  data_movimentacao DATE NOT NULL,
  id_usuario INT NOT NULL,
  FOREIGN KEY (id_produto) REFERENCES produtos(id_produto),
  FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
);

-- Movimentações iniciais
INSERT INTO movimentacoes (id_produto, tipo, quantidade, data_movimentacao, id_usuario) VALUES
(1, 'entrada', 10, '2024-09-01', 1),
(1, 'saida', 5, '2024-09-05', 2),
(2, 'entrada', 3, '2024-09-03', 3),
(2, 'saida', 1, '2024-09-06', 2),
(3, 'entrada', 4, '2024-09-04', 1),
(3, 'saida', 2, '2024-09-07', 3);

-- ================================================
-- CONSULTAS DE TESTE (opcional)
-- ================================================
-- Listar todos os produtos
SELECT * FROM produtos;

-- Listar histórico de movimentações com nomes
SELECT m.id_movimentacao, p.nome AS produto, m.tipo, m.quantidade, 
       m.data_movimentacao, u.nome AS usuario
FROM movimentacoes m
INNER JOIN produtos p ON m.id_produto = p.id_produto
INNER JOIN usuarios u ON m.id_usuario = u.id_usuario
ORDER BY m.data_movimentacao DESC;

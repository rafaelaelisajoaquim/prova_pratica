<?php
// --- 1. Controle de Sessão e Inclusão de Conexão ---

// Inicia ou retoma a sessão PHP para verificar se o usuário está logado.
session_start();

// Verifica se a variável de sessão 'usuario' está definida (se o usuário está logado).
if (!isset($_SESSION['usuario'])) {
    // Se não estiver logado, redireciona para a página de login e encerra o script.
    header("Location: login.php");
    exit;
}

// Inclui o arquivo de conexão com o banco de dados ($conn).
include('conexao.php');

// Inicializa variáveis para mensagens de feedback na tela.
$msg = "";
$tipoMsg = "";

// ===============================================
// 2. INSERIR OU ATUALIZAR PRODUTO (Processamento do Formulário)
// ===============================================

// Verifica se o formulário de cadastro/edição foi submetido (pelo botão 'salvar').
if (isset($_POST['salvar'])) {
    
    // Captura os dados do formulário
    $id = $_POST['id_produto']; // Se estiver vazio, é um novo cadastro. Se tiver valor, é uma edição.
    
    // Captura os dados, usando 'trim' para remover espaços em branco no início/fim.
    $nome = trim($_POST['nome']);
    $descricao = trim($_POST['descricao']);
    $categoria = trim($_POST['categoria']);
    $unidade = trim($_POST['unidade']);
    
    // Converte os valores numéricos para inteiro para garantir integridade.
    $minimo = (int)$_POST['minimo'];
    $quantidade = (int)$_POST['quantidade'];

    // --- Lógica de Decisão: UPDATE (Edição) ou INSERT (Cadastro) ---

    // Verifica se o ID do produto não está vazio (ou seja, está editando um produto existente).
    if (!empty($id)) {
        // SQL para ATUALIZAR (UPDATE) os dados do produto existente.
        // Importante: A quantidade atual (quantidade_atual) NÃO é alterada aqui.
        // A alteração de estoque é feita apenas no estoque.php.
        $sql = "UPDATE produtos SET 
                        nome='$nome', 
                        descricao='$descricao', 
                        categoria='$categoria',
                        unidade_medida='$unidade', 
                        quantidade_minima='$minimo'
                    WHERE id_produto=$id";
        $acao = "atualizado"; // Mensagem de feedback
    } else {
        // SQL para INSERIR (INSERT INTO) um novo produto.
        // Aqui é onde a quantidade inicial é definida.
        $sql = "INSERT INTO produtos 
                (nome, descricao, categoria, unidade_medida, quantidade_minima, quantidade_atual)
                VALUES ('$nome','$descricao','$categoria','$unidade','$minimo','$quantidade')";
        $acao = "cadastrado"; // Mensagem de feedback
    }

    // --- Execução da Query e Feedback ---

    if ($conn->query($sql)) {
        // Se a query for executada com sucesso.
        $msg = "Produto $acao com sucesso!";
        $tipoMsg = "sucesso";
    } else {
        // Se houver um erro na execução da query SQL.
        $msg = "Erro ao salvar o produto.";
        $tipoMsg = "erro";
    }

    // Limpa os dados do array de edição após o salvamento para evitar que o formulário
    // continue preenchido com os dados do produto salvo/editado.
    $produtoEdit = [
        'id_produto' => '',
        'nome' => '',
        'descricao' => '',
        'categoria' => '',
        'unidade_medida' => '',
        'quantidade_minima' => '',
        'quantidade_atual' => ''
    ];
}

// ===============================================
// 3. EXCLUSÃO DE PRODUTO (DELETE)
// ===============================================

// Verifica se o parâmetro 'excluir' foi passado na URL (via método GET).
if (isset($_GET['excluir'])) {
    $id = $_GET['excluir'];
    // Executa a query para DELETAR o produto com o ID especificado.
    if ($conn->query("DELETE FROM produtos WHERE id_produto=$id")) {
        $msg = "Produto excluído com sucesso!";
        $tipoMsg = "sucesso";
    } else {
        $msg = "Erro ao excluir produto.";
        $tipoMsg = "erro";
    }
}

// ===============================================
// 4. BUSCA E LISTAGEM DE PRODUTOS (READ)
// ===============================================

// Captura o termo de busca enviado via GET (se existir), ou define como vazio.
$busca = isset($_GET['busca']) ? $_GET['busca'] : '';

// SQL para selecionar todos os produtos. O "LIKE '%$busca%'" implementa a funcionalidade de busca.
// Se $busca for vazia, ele retornará todos os produtos.
$sql = "SELECT * FROM produtos WHERE nome LIKE '%$busca%'";
$result = $conn->query($sql); // Executa a consulta.

// ===============================================
// 5. EDIÇÃO – CARREGAR DADOS NO FORMULÁRIO
// ===============================================

// Inicializa o array $produtoEdit com campos vazios.
// Este array será usado para preencher os campos do formulário (para novo cadastro ou edição).
$produtoEdit = [
    'id_produto' => '',
    'nome' => '',
    'descricao' => '',
    'categoria' => '',
    'unidade_medida' => '',
    'quantidade_minima' => '',
    'quantidade_atual' => ''
];

// Verifica se o parâmetro 'editar' foi passado na URL (via método GET).
if (isset($_GET['editar'])) {
    $idEditar = $_GET['editar'];
    
    // Busca os dados do produto específico.
    $query = $conn->query("SELECT * FROM produtos WHERE id_produto=$idEditar");
    
    // Se o produto foi encontrado (pelo menos 1 linha retornada).
    if ($query->num_rows > 0) {
        // Armazena os dados do produto no array $produtoEdit.
        // O formulário HTML usará esses valores para preencher os campos automaticamente.
        $produtoEdit = $query->fetch_assoc();
    }
}
?>




<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Cadastro de Produtos</title>
<link rel="stylesheet" href="style.css">
<style>
.msg {
  width: 100%;
  padding: 10px;
  margin-bottom: 15px;
  border-radius: 5px;
  text-align: center;
  font-weight: bold;
}
.msg.sucesso {
  background-color: #d4edda;
  color: #155724;
  border: 1px solid #c3e6cb;
}
.msg.erro {
  background-color: #f8d7da;
  color: #721c24;
  border: 1px solid #f5c6cb;
}
input[readonly] {
  background-color: #e9ecef;
  color: #6c757d;
}

body {
  background:#96d3eb;
  
}

  h2 {
    color:#68ccf3;
    margin-bottom: center;
  }

  .container {
  margin-bottom: center;
  width: 900px;
  }

input {
  padding: 10px 2px;
  border: 1.5px solid #e0e0e0;
  border-radius: 18px;
  background: #f7fafd;
  color: #333;
  transition: border 0.2s;
  width: 400px;
  display: block;
  margin: 0 auto;
}

.btn {
  background-color:rgba(0, 123, 255, 0.82);
  padding: 8px 15px;
  color: white !important;
  font-size: 14px;
  border-radius: 4px;
  text-decoration: none !important;
  width: 80px; 
  display: block;
  margin: 0 auto;
}

table {
            background-color: white;
            border-radius: 10px;
            margin: 0 auto;
            margin: 20px auto;
            border-collapse: collapse;
            overflow: hidden;
            width: 70%;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            border: 1px solid #ddd;
        }

        th, td, tr {
            text-align: center;
            padding: 15px;
            border-bottom: 1px solid #e0e0e0;
        }

        th {
            background-color:rgba(0, 123, 255, 0.82);
            color: white;
            font-size: 14px;
            text-transform: uppercase;
        }

        td {
            background-color: white;
            color: #333;
            font-family: Arial, sans-serif;
        }
</style>

</head>
<body>
<div class="container">
<h2>Cadastro de Produtos</h2>

<!-- Mensagem de feedback -->
<?php if (!empty($msg)): ?>
  <div class="msg <?= $tipoMsg ?>"><?= $msg ?></div>
<?php endif; ?>


<!-- Campo de busca -->
<form method="get" style="margin-bottom:10px;">
  <input type="text" name="busca" placeholder="Buscar produto..." value="<?= htmlspecialchars($busca) ?>"><br>
  <button type="submit" class="btn">Buscar</button>
</form>

<!-- Tabela de produtos -->
<table border="1">
<tr><th>ID</th><th>Nome</th><th>Categoria</th><th>Qtd Atual</th><th>Ações</th></tr>
<?php if ($result->num_rows > 0): ?>
<?php while($p = $result->fetch_assoc()): ?>
<tr>
<td><?= $p['id_produto'] ?></td>
<td><?= htmlspecialchars($p['nome']) ?></td>
<td><?= htmlspecialchars($p['categoria']) ?></td>
<td><?= $p['quantidade_atual'] ?></td>
<td>
  <a href="?editar=<?= $p['id_produto'] ?>">Alterar</a>
  |
  <a href="?excluir=<?= $p['id_produto'] ?>" onclick="return confirm('Deseja realmente excluir este produto?')">Excluir</a>
</td>
</tr>
<?php endwhile; ?>
<?php else: ?>
<tr><td colspan="5">Nenhum produto encontrado.</td></tr>
<?php endif; ?>
</table>

<hr>
<h3><?= $produtoEdit['id_produto'] ? "Editar Produto" : "Adicionar Novo Produto" ?></h3>

<!-- Formulário de cadastro/edição -->
<form method="post">
  <input type="hidden" name="id_produto" value="<?= $produtoEdit['id_produto'] ?>">
  <input type="text" name="nome" placeholder="Nome" value="<?= htmlspecialchars($produtoEdit['nome']) ?>" required><br>
  <input type="text" name="descricao" placeholder="Descrição" value="<?= htmlspecialchars($produtoEdit['descricao']) ?>"><br>
  <input type="text" name="categoria" placeholder="Categoria" value="<?= htmlspecialchars($produtoEdit['categoria']) ?>"><br>
  <input type="text" name="unidade" placeholder="Unidade (ex: saco, lata...)" value="<?= htmlspecialchars($produtoEdit['unidade_medida']) ?>"><br>
  <input type="number" name="minimo" placeholder="Qtd Mínima" value="<?= htmlspecialchars($produtoEdit['quantidade_minima']) ?>" required><br>

  <!-- Campo bloqueado ao editar -->
  <input type="number" 
         name="quantidade" 
         placeholder="Qtd Atual" 
         value="<?= htmlspecialchars($produtoEdit['quantidade_atual']) ?>" 
         <?= $produtoEdit['id_produto'] ? 'readonly' : '' ?> 
         required><br>
         <button type="submit" class="btn">Cadastrar</button>
</form>

<br>

  <a href="index.php">Voltar</a>
</div>
</body>
</html>

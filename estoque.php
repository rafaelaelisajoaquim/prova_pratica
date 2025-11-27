<?php
// --- 1. Controle de Sessão e Acesso ---
// Inicia ou retoma a sessão para usar as variáveis de usuário.
session_start();

// Garante que o usuário esteja logado (verificando 'usuario' E 'id_usuario' na sessão).
// Se não estiver logado, redireciona para a página de login e encerra o script.
if (!isset($_SESSION['usuario']) || !isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit;
}
// Inclui o arquivo de conexão para acesso ao banco de dados ($conn).
include('conexao.php');
// Carrega as informações do usuário logado para uso em logs de movimentação.
$usuario = $_SESSION['usuario'];
$id_usuario = $_SESSION['id_usuario'];

// Inicializa variáveis para mensagens de feedback na tela.
$msg = "";
$tipoMsg = "";

// ===============================================
// REGISTRO DE MOVIMENTAÇÃO (entrada / saída)
// ===============================================
// Verifica se o formulário de movimentação foi submetido (pelo botão 'mover').
if (isset($_POST['mover'])) {
	// Captura os dados do formulário
    $id_produto = $_POST['produto'];
    $tipo = $_POST['tipo'];
    $quantidade = (int)$_POST['quantidade']; // Converte para inteiro (segurança)
    $data = $_POST['data'];

// --- Lógica de Atualização do Estoque Atual (Tabela 'produtos') ---
     // Se for uma 'entrada', adiciona a quantidade atual.
    if ($tipo == 'entrada') {
        $conn->query("UPDATE produtos SET quantidade_atual = quantidade_atual + $quantidade WHERE id_produto=$id_produto");
    } else {
        $conn->query("UPDATE produtos SET quantidade_atual = quantidade_atual - $quantidade WHERE id_produto=$id_produto");
    }

// --- Lógica de Registro no Histórico (Tabela 'movimentacoes') ---
    // Tenta inserir o registro da movimentação na tabela de histórico.
    // Registrar movimentação
    if ($conn->query("INSERT INTO movimentacoes (id_produto, tipo, quantidade, data_movimentacao, id_usuario)
                      VALUES ($id_produto, '$tipo', $quantidade, '$data', $id_usuario)")) {

        // Verificar estoque mínimo
		// Consulta os dados atualizados do produto (nome, qtd atual, qtd mínima).
        $p = $conn->query("SELECT nome, quantidade_atual, quantidade_minima FROM produtos WHERE id_produto=$id_produto")->fetch_assoc();
		
		// Compara a quantidade atual com o mínimo configurado.
        if ($p['quantidade_atual'] < $p['quantidade_minima']) {
            $msg = "⚠️ Estoque de {$p['nome']} abaixo do mínimo configurado!";
            $tipoMsg = "alerta";
        } else {
			// Define uma mensagem de SUCESSO.
            $msg = "Movimentação registrada com sucesso!";
            $tipoMsg = "sucesso";
        }
    } else {
		// Define uma mensagem de ERRO na inserção do histórico.
        $msg = "Erro ao registrar movimentação!";
        $tipoMsg = "erro";
    }
}

// ===============================================
// CARREGAR PRODUTOS E ORDENAR COM usort()
// ===============================================

// Seleciona todos os produtos do banco de dados.
$result = $conn->query("SELECT * FROM produtos");
$produtos = [];

// Transfere os resultados da consulta para um array PHP ($produtos).
while ($row = $result->fetch_assoc()) {
    $produtos[] = $row;
}
// Função nativa do PHP para ordenar arrays (User-Sort).
usort($produtos, function($a, $b) {
	// A função de comparação: usa 'strcasecmp' para comparar nomes (strings) sem diferenciar maiúsculas/minúsculas.
    return strcasecmp($a['nome'], $b['nome']); // ordena A–Z
});

// ===============================================
// CONSULTAR HISTÓRICO DE MOVIMENTAÇÕES
// ===============================================
// Consulta complexa (JOINs) para buscar o histórico de movimentações,
// juntando informações do produto e do usuário responsável.
$historico = $conn->query("
    SELECT m.*, p.nome AS produto, u.nome AS usuario
    FROM movimentacoes m
    INNER JOIN produtos p ON m.id_produto = p.id_produto
    INNER JOIN usuarios u ON m.id_usuario = u.id_usuario
    ORDER BY m.data_movimentacao DESC, m.id_movimentacao DESC
");
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Gestão de Estoque - ConstruMais</title>
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
.msg.alerta {
  background-color: #fff3cd;
  color: #856404;
  border: 1px solid #ffeeba;
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
}

select  {
  width: 500px;
}

.btn {
  background-color:rgba(0, 123, 255, 0.82);
  padding: 8px 20px;
  color: white !important;
  font-size: 14px;
  border-radius: 4px;
  text-decoration: none !important;
  width: 200px; 
  height: 30px;
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
<h2>Gestão de Estoque</h2>

<!-- Mensagem de feedback -->
<?php if (!empty($msg)): ?>
  <div class="msg <?= $tipoMsg ?>"><?= $msg ?></div>
<?php endif; ?>

<!-- Formulário de movimentação -->
<form method="post">
  <label>Produto:</label>
  <select name="produto" required>
    <?php foreach ($produtos as $p): ?>
      <option value="<?= $p['id_produto'] ?>"><?= htmlspecialchars($p['nome']) ?></option>
    <?php endforeach; ?>
  </select><br>

  <label>Tipo de Movimentação:</label>
  <select name="tipo" required>
    <option value="entrada">Entrada</option>
    <option value="saida">Saída</option>
  </select><br>

  <label>Quantidade:</label>
  <input type="number" name="quantidade" min="1" required><br>

  <label>Data:</label>
  <input type="date" name="data" required><br>

  <button type="submit" class="btn">Registrar Movimentação</button>
</form>

<hr>
<h3>Produtos Cadastrados (Ordenados Alfabeticamente)</h3>
<table border="1">
<tr><th>ID</th><th>Nome</th><th>Categoria</th><th>Qtd Atual</th><th>Qtd Mínima</th></tr>
<?php foreach ($produtos as $p): ?>
<tr>
  <td><?= $p['id_produto'] ?></td>
  <td><?= htmlspecialchars($p['nome']) ?></td>
  <td><?= htmlspecialchars($p['categoria']) ?></td>
  <td><?= $p['quantidade_atual'] ?></td>
  <td><?= $p['quantidade_minima'] ?></td>
</tr>
<?php endforeach; ?>
</table>

<hr>
<h3>Histórico de Movimentações</h3>
<table border="1">
<tr><th>Data</th><th>Produto</th><th>Tipo</th><th>Quantidade</th><th>Usuário Responsável</th></tr>
<?php if ($historico->num_rows > 0): ?>
  <?php while ($mov = $historico->fetch_assoc()): ?>
    <tr>
      <td><?= date("d/m/Y", strtotime($mov['data_movimentacao'])) ?></td>
      <td><?= htmlspecialchars($mov['produto']) ?></td>
      <td><?= ucfirst($mov['tipo']) ?></td>
      <td><?= $mov['quantidade'] ?></td>
      <td><?= htmlspecialchars($mov['usuario']) ?></td>
    </tr>
  <?php endwhile; ?>
<?php else: ?>
  <tr><td colspan="5">Nenhuma movimentação registrada ainda.</td></tr>
<?php endif; ?>
</table>

<br>
  <a href="index.php">Voltar</a>
</div>
</body>
</html>

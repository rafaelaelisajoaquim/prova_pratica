<?php
// --- Bloco de Verificação de Sessão (Controle de Acesso) ---

// Inicia ou resume uma sessão PHP existente.
// Isso é crucial para acessar as variáveis de sessão ($_SESSION).
session_start();

// Verifica se a variável de sessão 'usuario' NÃO está definida (ou seja, o usuário não está logado).
if (!isset($_SESSION['usuario'])) {
    // Se a sessão 'usuario' não existe, redireciona o usuário (força-o) para a página de login.
    header("Location: login.php");
    // Interrompe a execução do script para garantir que nada mais seja processado ou exibido.
    exit;
}

// Se o usuário está logado (o código continuou a execução), armazena o nome de usuário
// da sessão em uma variável local para uso mais fácil no HTML.
$usuario = $_SESSION['usuario'];
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Painel</title>
<link rel="stylesheet" href="style.css">
</head>
<style>
  body {
  background:#96d3eb;
  
}

  h2 {
    color:#68ccf3;
    margin-bottom: center;
  }

  .container {
  margin-bottom: center;
  width: 500px;

  }

input {
  padding: 10px 2px;
  border: 1.5px solid #e0e0e0;
  border-radius: 18px;
  background: #f7fafd;
  color: #333;
  transition: border 0.2s;
}

.btn {
  background:#68ccf3;
  color: #000000;
  padding: 12px 30px;
  border: none;
  border-radius: 18px;
}

.btn:hover {
  background:rgb(48, 164, 209);
}

.sair {
  position: fixed;
  top: 20px;
  left: 20px;
  padding: 10px 20px;
  background-color: red;
  border-radius: 12px;
  text-decoration: none;
  font-weight: 600;
  font-size: 13px;
  color: white !important;
}

 a{
  color:black;
 }

</style>
<body>
<div class="container">
  <h2>Bem-vindo, <?php echo $usuario; ?>!</h2>
  <a href="cadastro_produto.php">📦 Cadastro de Produtos</a><br><br>
  <a href="estoque.php">📊 Gestão de Estoque</a><br>
  <a href="logout.php" class="sair">Sair</a>
</div>
</body>
</html>

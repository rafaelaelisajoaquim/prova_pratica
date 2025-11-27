<?php
// --- Bloco de Processamento de Login ---

// 1. Inicia ou retoma uma sessão PHP existente.
// É essencial para poder armazenar dados do usuário após o login (como nome e ID).
session_start();

// 2. Inclui o arquivo de conexão com o banco de dados.
// Isso torna a variável $conn (objeto de conexão) disponível neste script.
include('conexao.php');

// 3. Verifica se o formulário de login foi submetido.
// A verificação é feita checando se o campo 'email' (enviado via método POST) está definido.
if (isset($_POST['email'])) {
    
    // 4. Captura e sanitiza os dados de entrada.
    // Captura o email digitado pelo usuário.
    $email = $_POST['email'];
    
    // Captura a senha digitada e a criptografa usando o algoritmo MD5.
    // ATENÇÃO: Embora funcione, MD5 é um algoritmo de hash fraco e não recomendado para senhas em produção.
    $senha = md5($_POST['senha']);

    // 5. Constrói a consulta SQL para verificar as credenciais.
    // Seleciona todos os campos de um usuário onde o 'email' e a 'senha' (já criptografada) coincidem.
    $sql = "SELECT * FROM usuarios WHERE email='$email' AND senha='$senha'";
    
    // 6. Executa a consulta SQL no banco de dados.
    // $conn é o objeto de conexão vindo de 'conexao.php'.
    $result = $conn->query($sql);

    // 7. Avalia o resultado da consulta.
    // 'num_rows' retorna o número de linhas encontradas. Se for 1, as credenciais estão corretas.
    if ($result->num_rows == 1) {
        
        // Se a busca retornar 1 linha: Login bem-sucedido.
        
        // Pega todos os dados da linha encontrada no banco de dados como um array associativo.
        $user = $result->fetch_assoc();
        
        // Cria variáveis de sessão para manter o usuário logado e identificado.
        $_SESSION['usuario'] = $user['nome'];           // Armazena o nome do usuário para exibição (ex: no index.php).
        $_SESSION['id_usuario'] = $user['id_usuario'];  // Armazena o ID do usuário para uso em outras operações (cadastro/estoque).
        
        // Redireciona o usuário para a página principal (Painel).
        header("Location: index.php");
        
        // Encerra o script para evitar processamento desnecessário após o redirecionamento.
        exit;
    } else {
        // Se a busca retornar 0 linhas: Login falhou.
        // Define uma mensagem de erro que será exibida no HTML.
        $erro = "Usuário ou senha incorretos.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Login</title>
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

</style>
<body>
<div class="container">
  <h2>Login</h2>
  <form method="post">
    <input type="email" name="email" placeholder="E-mail" required><br>
    <input type="password" name="senha" placeholder="Senha" required><br>
    <button type="submit" class="btn"> Acessar </button>
  </form>
  
  <?php if (isset($erro)) echo "<p class='erro'>$erro</p>"; ?>
</div>
</body>
</html>


<?php

// --- Configurações de Conexão com o Banco de Dados ---

// Define o endereço do servidor do banco de dados (Host).
// 'localhost' geralmente é usado quando o servidor web e o banco de dados estão na mesma máquina.
$host = "localhost";

// Define o nome de usuário para acessar o banco de dados.
// 'root' é o padrão para instalações locais (ambiente de desenvolvimento).
$user = "root";

// Define a senha para o usuário do banco de dados.
// Senha vazia ("") é comum em ambientes de desenvolvimento local (como XAMPP/WAMP).
// ATENÇÃO: Em produção, esta senha deve ser forte e segura.
$pass = "";

// Define o nome do banco de dados ao qual a aplicação irá se conectar.
// O banco de dados para o projeto SAEP Material de Construção.
$db = "simulado_saep_db";

// --- Estabelece a Conexão ---

// Cria uma nova instância da classe 'mysqli' (MySQL Improved Extension).
// Esta linha tenta estabelecer a conexão com o banco de dados usando as variáveis definidas acima.
$conn = new mysqli($host, $user, $pass, $db);

// --- Verificação de Erros na Conexão ---

// O 'if' verifica se ocorreu algum erro durante a tentativa de conexão.
// $conn->connect_error retorna uma string de erro se a conexão falhou.
if ($conn->connect_error) {
    // Se houver um erro, a função 'die()' é chamada.
    // 'die()' encerra a execução do script imediatamente.
    // Exibe uma mensagem de erro clara, concatenando a mensagem estática
    // com o erro específico retornado pelo servidor.
    die("Falha na conexão: " . $conn->connect_error);
}

// Se o script chegou até aqui, significa que a conexão ($conn) foi bem-sucedida
// e o objeto de conexão está pronto para ser usado em outras partes do seu sistema
// (por exemplo, para realizar consultas SQL).

?>

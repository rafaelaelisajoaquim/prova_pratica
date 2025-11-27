<?php
// --- Script de Logout (Encerramento de Sessão) ---

// 1. Inicia ou retoma a sessão PHP atual.
// É obrigatório chamar esta função antes de manipular (destruir) a sessão.
session_start();

// 2. Destrói todos os dados registrados em uma sessão.
// Isso efetivamente "desloga" o usuário, removendo todas as variáveis de sessão (como $_SESSION['usuario'] e $_SESSION['id_usuario']).
session_destroy();

// 3. Redireciona o navegador do usuário para a página de login.
// Após destruir a sessão, o usuário é enviado de volta ao ponto de entrada do sistema.
header("Location: login.php");

// 4. Interrompe a execução do script.
// Garante que o redirecionamento HTTP seja executado imediatamente e que nenhum conteúdo da página (mesmo que vazio) seja enviado ao navegador.
exit;
?>

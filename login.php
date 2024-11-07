<?php
// Configuração do banco de dados
$host = 'localhost'; 
$dbname = 'tarefas';
$username = 'root';  
$password = '';      

// Conexão ao banco de dados usando PDO
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Erro na conexão com o banco de dados: ' . $e->getMessage();
}

// Se o formulário de login for enviado, processa a autenticação
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];

    // Consulta para verificar se o usuário existe no banco de dados
    $stmt = $pdo->prepare("SELECT * FROM tbl_usuarios WHERE usu_nome = ? AND usu_email = ?");
    $stmt->execute([$nome, $email]);

    // Se um usuário for encontrado
    if ($stmt->rowCount() > 0) {
        // O usuário foi encontrado, redireciona para a página de tarefas
        header("Location: tarefas.php?usuario=" . urlencode($nome)); // Passa o nome como parâmetro
        exit(); // Impede que o código continue executando após o redirecionamento
    } else {
        // Caso contrário, exibe uma mensagem de erro
        $error_message = "Usuário não encontrado. Verifique o nome e email.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PÁGINA DE LOGIN</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #e3f2fd; /* Azul claro para o fundo */
            margin: 0;
            padding: 0;
        }

        .header {
            background-color: #1e88e5; /* Azul forte para o cabeçalho */
            color: white;
            padding: 20px 0;
            text-align: center;
            font-size: 32px;
            font-weight: bold;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 10;
        }

        .content {
            max-width: 450px;
            margin: 120px auto 40px;
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        }

        form input[type="text"], form input[type="email"], form input[type="submit"] {
            width: 100%;
            padding: 12px;
            margin: 12px 0;
            border-radius: 5px;
            border: 1px solid #1e88e5; /* Bordas azuis */
            box-sizing: border-box;
            font-size: 16px;
        }

        form input[type="submit"] {
            background-color: #1e88e5; /* Azul forte para o botão */
            color: white;
            cursor: pointer;
            font-size: 18px;
            font-weight: bold;
            transition: background-color 0.3s;
        }

        form input[type="submit"]:hover {
            background-color: #1565c0; /* Azul mais escuro para o hover */
        }

        p.error {
            color: #e74c3c;
            text-align: center;
            font-weight: bold;
        }

        @media screen and (max-width: 600px) {
            .content {
                margin: 90px 20px 40px;
                padding: 20px;
            }

            .header {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>

    <div class="header">
        PÁGINA DE LOGIN
    </div>

    <div class="content">
        <!-- Formulário para login -->
        <form method="POST">
            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" required>

            <label for="email">E-mail:</label>
            <input type="email" id="email" name="email" required>

            <input type="submit" value="FAZER LOGIN">
        </form>

        <!-- Exibir mensagem de erro, caso haja -->
        <?php if (isset($error_message)): ?>
            <p class="error"><?php echo htmlspecialchars($error_message); ?></p>
        <?php endif; ?>
    </div>

</body>
</html>

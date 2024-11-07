<?php
// Configuração do banco de dados
$host = 'localhost'; // Ou o IP do seu servidor de banco de dados
$dbname = 'tarefas';
$username = 'root';  // Usuário do banco de dados
$password = '';      // Senha do banco (se houver)

// Conexão ao banco de dados usando PDO
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Erro na conexão com o banco de dados: ' . $e->getMessage();
}

// Variáveis para armazenar dados do formulário
$tasks = [];

// Se o formulário for enviado, processa o cadastro de usuário e tarefa
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Pegando os dados do formulário
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    
    // Inserindo o usuário na tabela tbl_usuarios
    $stmt = $pdo->prepare("INSERT INTO tbl_usuarios (usu_nome, usu_email) VALUES (?, ?)");
    $stmt->execute([$nome, $email]);
    
    // Pegando o código do usuário recém-inserido
    $usu_codigo = $pdo->lastInsertId();

    // Inserindo uma tarefa na tabela tbl_tarefas associada ao usuário
    $setor = 'Setor Exemplo';  // Defina o valor ou colete de um campo do formulário
    $propriedade = 'Propriedade Exemplo';  // Defina o valor ou colete de um campo do formulário
    $descricao = 'Descrição Exemplo';  // Defina o valor ou colete de um campo do formulário
    $status = 'Ativa';  // Defina o valor ou colete de um campo do formulário

    $stmt = $pdo->prepare("INSERT INTO tbl_tarefas (tar_setor, tar_propriedade, tar_descricao, tar_status, usu_codigo) 
                           VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$setor, $propriedade, $descricao, $status, $usu_codigo]);

    echo "Usuário e tarefa cadastrados com sucesso!";
}

// Processando a exclusão de um usuário
if (isset($_GET['delete'])) {
    $deleteId = $_GET['delete'];

    // Excluindo o usuário da tabela tbl_usuarios
    $stmt = $pdo->prepare("DELETE FROM tbl_usuarios WHERE usu_codigo = ?");
    $stmt->execute([$deleteId]);

    // Excluindo todas as tarefas associadas a esse usuário
    $stmt = $pdo->prepare("DELETE FROM tbl_tarefas WHERE usu_codigo = ?");
    $stmt->execute([$deleteId]);

    echo "Usuário e suas tarefas foram excluídos com sucesso!";
}

// Recuperando os dados da tabela de usuários para exibição
$stmt = $pdo->query("SELECT * FROM tbl_usuarios");
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PÁGINA DE TAREFAS</title>
    <style>
        /* Resetando alguns estilos padrões */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Cor do fundo da página */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #e3f2fd; /* Azul claro */
            color: #333;
        }

        /* Estilo do cabeçalho */
        .header {
            background-color: #1e88e5; /* Azul forte */
            color: white;
            text-align: center;
            padding: 20px;
            border-bottom: 5px solid #1565c0; /* Azul escuro */
        }

        /* Estilo da área de conteúdo */
        .content {
            width: 90%;
            max-width: 800px;
            margin: 40px auto;
            padding: 30px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        /* Estilo do formulário */
        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        form input[type="text"],
        form input[type="email"],
        form input[type="submit"] {
            padding: 12px;
            font-size: 16px;
            border: 2px solid #1e88e5; /* Bordas azuis */
            border-radius: 5px;
            background-color: #fff;
            color: #333;
        }

        form input[type="submit"] {
            background-color: #1e88e5;
            color: white;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        form input[type="submit"]:hover {
            background-color: #1565c0; /* Azul mais escuro no hover */
        }

        /* Estilo da tabela de usuários */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
        }

        table th, table td {
            padding: 12px;
            text-align: left;
            border: 1px solid #1e88e5; /* Bordas azuis */
        }

        table th {
            background-color: #bbdefb; /* Azul claro */
            color: white;
        }

        table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        /* Estilo do link de exclusão */
        .delete-btn {
            color: #1e88e5;
            cursor: pointer;
            font-weight: bold;
        }

        .delete-btn:hover {
            text-decoration: underline;
        }

        /* Mensagens de erro ou sucesso */
        p {
            font-size: 1.2em;
            text-align: center;
            color: #333;
        }

        /* Responsividade */
        @media (max-width: 768px) {
            .content {
                margin: 20px;
                padding: 20px;
            }

            table th, table td {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>PÁGINA PRINCIPAL DE TAREFAS</h1>
    </div>

    <div class="content">
        <!-- Tabela de Usuários -->
        <?php if (count($tasks) > 0): ?>
            <h2>USUÁRIOS CADASTRADOS</h2>
            <table>
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>E-mail</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tasks as $task): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($task['usu_nome']); ?></td>
                            <td><?php echo htmlspecialchars($task['usu_email']); ?></td>
                            <td>
                                <a href="?delete=<?php echo $task['usu_codigo']; ?>" class="delete-btn" onclick="return confirm('Você tem certeza que deseja excluir este usuário?');">Excluir</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Nenhum usuário cadastrado ainda.</p>
        <?php endif; ?>

        <!-- Formulário para adicionar usuários -->
        <form method="POST">
            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" required>

            <label for="email">E-mail:</label>
            <input type="email" id="email" name="email" required>

            <input type="submit" value="ADICIONAR USUÁRIO">
        </form>
    </div>

</body>
</html>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jogo de Damas</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            background-color: #4169E1;
            font-family: 'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif;

        }

        h1 {
            font-family: 'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif;
            color: #000000;
        }
        button {
            font-family: 'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif;
            font-size: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Bem-vindo ao Jogo de Damas</h1>
        <button onclick="iniciarJogo()">Iniciar Jogo</button>
    </div>

    <script>
        function iniciarJogo() {
            window.location.href = "dama.php"; // Redireciona para a página do jogo
        }
    </script>
</body>
</html>

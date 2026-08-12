<?php session_start(); ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 | Organiza+</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="auth-page">
<div class="error-container">
    <div class="error-code">404</div>
    <h2>Página não encontrada</h2>
    <p>A página que você está procurando não existe ou foi movida para outro endereço.</p>
    <a href="<?= isset($_SESSION['user_id']) ? 'pages/dashboard.php' : 'index.php' ?>" class="btn-primary">← Voltar ao início</a>
</div>
</body>
</html>
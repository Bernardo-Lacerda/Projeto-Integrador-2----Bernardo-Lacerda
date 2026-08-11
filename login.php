<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: pages/dashboard.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'config/database.php';
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email = '$email' AND active = 1";
    $result = mysqli_query($conn, $sql);
    $user = mysqli_fetch_assoc($result);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_role'] = $user['role'];
        header('Location: pages/dashboard.php');
        exit;
    } else {
        $error = 'Email ou senha incorretos.';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Organiza+</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="auth-page">

<div class="auth-container">
    <div class="auth-card">
        <div class="auth-logo">
            <span class="logo-icon">✓</span>
            <span class="logo-text">Organiza+</span>
        </div>
        <h2>Bem-vindo de volta!</h2>
        <p class="auth-subtitle">Faça login para acessar sua conta</p>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" placeholder="seu@email.com" required>
            </div>
            <div class="form-group">
                <label>Senha</label>
                <input type="password" name="password" placeholder="••••••••" required>
                <a href="recuperar-senha.php" class="forgot-link">Esqueceu sua senha?</a>
            </div>
            <button type="submit" class="btn-primary btn-full">Entrar</button>
        </form>

        <p class="auth-footer">Não tem conta? <a href="cadastro.php">Criar conta</a></p>
    </div>
</div>

</body>
</html>
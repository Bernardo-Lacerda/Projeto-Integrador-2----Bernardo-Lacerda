<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: pages/dashboard.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'config/database.php';

    $name = mysqli_real_escape_string($conn, trim($_POST['name']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    if (strlen($name) < 3) {
        $error = 'O nome deve ter pelo menos 3 caracteres.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Email inválido.';
    } elseif (strlen($password) < 6) {
        $error = 'A senha deve ter pelo menos 6 caracteres.';
    } elseif ($password !== $confirm) {
        $error = 'As senhas não coincidem.';
    } else {
        $check = mysqli_query($conn, "SELECT id FROM users WHERE email = '$email'");
        if (mysqli_num_rows($check) > 0) {
            $error = 'Este email já está cadastrado.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO users (name, email, password) VALUES ('$name', '$email', '$hash')";
            if (mysqli_query($conn, $sql)) {
                $user_id = mysqli_insert_id($conn);
                $msg = "Bem-vindo ao Organiza+! Sua conta foi criada com sucesso.";
                mysqli_query($conn, "INSERT INTO notifications (user_id, title, message) VALUES ($user_id, 'Bem-vindo ao Organiza+!', '$msg')");
                $success = 'Conta criada com sucesso! Redirecionando...';
                header('Refresh: 2; url=login.php');
            } else {
                $error = 'Erro ao criar conta. Tente novamente.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro | Organiza+</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="auth-page">

<div class="auth-container">
    <div class="auth-card">
        <div class="auth-logo">
            <span class="logo-icon">✓</span>
            <span class="logo-text">Organiza+</span>
        </div>
        <h2>Criar nova conta</h2>
        <p class="auth-subtitle">Preencha os dados para se cadastrar</p>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= $error ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Nome completo</label>
                <input type="text" name="name" placeholder="Seu nome" required
                       value="<?= isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '' ?>">
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" placeholder="seu@email.com" required
                       value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
            </div>
            <div class="form-group">
                <label>Senha</label>
                <input type="password" name="password" placeholder="Crie uma senha" required>
            </div>
            <div class="form-group">
                <label>Confirmar senha</label>
                <input type="password" name="confirm_password" placeholder="Repita a senha" required>
            </div>
            <button type="submit" class="btn-primary btn-full">Criar Conta</button>
        </form>

        <p class="auth-footer">Já tem conta? <a href="login.php">Entrar</a></p>
    </div>
</div>

</body>
</html>
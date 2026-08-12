<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: pages/dashboard.php');
    exit;
}

require_once 'config/database.php';
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id FROM users WHERE email='$email' AND active=1"));
    if ($user) {
        $success = 'Se este email estiver cadastrado, você receberá as instruções em breve.';
    } else {
        $success = 'Se este email estiver cadastrado, você receberá as instruções em breve.';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Senha | Organiza+</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="auth-page">
<div class="auth-container">
    <div class="auth-card">
        <div class="auth-logo">
            <span class="logo-icon">🔑</span>
            <span class="logo-text" style="color:#7F77DD">Recuperar Senha</span>
        </div>
        <h2>Esqueceu sua senha?</h2>
        <p class="auth-subtitle">Informe seu email e enviaremos um link de recuperação</p>

        <?php if ($error): ?><div class="alert alert-error"><?= $error ?></div><?php endif; ?>
        <?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>

        <?php if (!$success): ?>
        <form method="POST">
            <div class="form-group">
                <label>Email cadastrado</label>
                <input type="email" name="email" placeholder="seu@email.com" required>
            </div>
            <button type="submit" class="btn-primary btn-full">Enviar Link de Recuperação</button>
        </form>
        <?php endif; ?>

        <p class="auth-footer"><a href="login.php">← Voltar para o login</a></p>
    </div>
</div>
</body>
</html>
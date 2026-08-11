<?php
$page_title = 'Meu Perfil';
require_once '../includes/header.php';

$uid = $_SESSION['user_id'];
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = mysqli_real_escape_string($conn, trim($_POST['name']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $new_pass = $_POST['new_password'];
    $confirm  = $_POST['confirm_password'];

    if (empty($name) || empty($email)) {
        $error = 'Nome e email são obrigatórios.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Email inválido.';
    } elseif (!empty($new_pass) && $new_pass !== $confirm) {
        $error = 'As senhas não coincidem.';
    } elseif (!empty($new_pass) && strlen($new_pass) < 6) {
        $error = 'A senha deve ter pelo menos 6 caracteres.';
    } else {
        $check = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id FROM users WHERE email='$email' AND id != $uid"));
        if ($check) {
            $error = 'Este email já está em uso.';
        } else {
            if (!empty($new_pass)) {
                $hash = password_hash($new_pass, PASSWORD_DEFAULT);
                mysqli_query($conn, "UPDATE users SET name='$name', email='$email', password='$hash' WHERE id=$uid");
            } else {
                mysqli_query($conn, "UPDATE users SET name='$name', email='$email' WHERE id=$uid");
            }
            $_SESSION['user_name'] = $name;
            $success = 'Perfil atualizado com sucesso!';
        }
    }
}

if (isset($_GET['deletar_conta'])) {
    mysqli_query($conn, "DELETE FROM users WHERE id=$uid");
    session_destroy();
    header('Location: ../index.php');
    exit;
}

$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id=$uid"));
?>

<div class="form-card">
    <div class="profile-header">
        <div class="profile-avatar">
            <?= strtoupper(substr($user['name'], 0, 2)) ?>
        </div>
        <div>
            <div style="font-size:18px;font-weight:700;color:#222"><?= htmlspecialchars($user['name']) ?></div>
            <div style="font-size:14px;color:#888"><?= htmlspecialchars($user['email']) ?></div>
            <div style="font-size:12px;color:#43A047;margin-top:4px">● Conta ativa</div>
        </div>
    </div>

    <?php if ($error): ?><div class="alert alert-error"><?= $error ?></div><?php endif; ?>
    <?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label>Nome completo</label>
            <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
        </div>
        <div class="form-group">
            <label>Nova senha</label>
            <input type="password" name="new_password" placeholder="Deixe em branco para não alterar">
        </div>
        <div class="form-group">
            <label>Confirmar nova senha</label>
            <input type="password" name="confirm_password" placeholder="Repita a nova senha">
        </div>
        <div class="form-actions">
            <button type="submit" class="btn-primary">Salvar Alterações</button>
        </div>
    </form>

    <div class="danger-zone">
        <div class="danger-title">⚠ Zona de Perigo</div>
        <p>Excluir sua conta é uma ação permanente e não pode ser desfeita.</p>
        <a href="perfil.php?deletar_conta=1" class="btn-danger"
           onclick="return confirm('Tem certeza? Esta ação não pode ser desfeita.')">
            🗑 Excluir minha conta
        </a>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
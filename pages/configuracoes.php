<?php
$page_title = 'Configurações';
require_once '../includes/header.php';

$uid = $_SESSION['user_id'];
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['notif'])) {
    $nd = isset($_POST['notify_deadline']) ? 1 : 0;
    $nd2 = isset($_POST['notify_daily']) ? 1 : 0;
    mysqli_query($conn, "UPDATE users SET notify_deadline=$nd, notify_daily=$nd2 WHERE id=$uid");
    $success = 'Preferências salvas!';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['senha'])) {
    $current  = $_POST['current_password'];
    $new_pass = $_POST['new_password'];
    $confirm  = $_POST['confirm_password'];
    $user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id=$uid"));

    if (!password_verify($current, $user['password'])) {
        $error = 'Senha atual incorreta.';
    } elseif (strlen($new_pass) < 6) {
        $error = 'A nova senha deve ter pelo menos 6 caracteres.';
    } elseif ($new_pass !== $confirm) {
        $error = 'As senhas não coincidem.';
    } else {
        $hash = password_hash($new_pass, PASSWORD_DEFAULT);
        mysqli_query($conn, "UPDATE users SET password='$hash' WHERE id=$uid");
        $success = 'Senha alterada com sucesso!';
    }
}

$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id=$uid"));
?>

<?php if ($error): ?><div class="alert alert-error"><?= $error ?></div><?php endif; ?>
<?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>

<div class="form-card" style="margin-bottom:24px">
    <h3 class="settings-section-title">Preferências de Notificação</h3>
    <p style="font-size:13px;color:#888;margin-bottom:20px">Controle como e quando receber avisos</p>
    <form method="POST">
        <input type="hidden" name="notif" value="1">
        <div class="toggle-row">
            <div>
                <div style="font-weight:600;font-size:14px">Notificações de prazo</div>
                <div style="font-size:13px;color:#888">Receber avisos quando uma tarefa estiver vencendo</div>
            </div>
            <label class="toggle-switch">
                <input type="checkbox" name="notify_deadline" <?= $user['notify_deadline'] ? 'checked' : '' ?>>
                <span class="toggle-slider"></span>
            </label>
        </div>
        <div class="toggle-row">
            <div>
                <div style="font-weight:600;font-size:14px">Resumo diário</div>
                <div style="font-size:13px;color:#888">Receber um resumo das tarefas do dia</div>
            </div>
            <label class="toggle-switch">
                <input type="checkbox" name="notify_daily" <?= $user['notify_daily'] ? 'checked' : '' ?>>
                <span class="toggle-slider"></span>
            </label>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn-primary">Salvar preferências</button>
        </div>
    </form>
</div>

<div class="form-card" style="margin-bottom:24px">
    <h3 class="settings-section-title">Segurança</h3>
    <p style="font-size:13px;color:#888;margin-bottom:20px">Gerencie sua senha de acesso</p>
    <form method="POST">
        <input type="hidden" name="senha" value="1">
        <div class="form-group">
            <label>Senha atual</label>
            <input type="password" name="current_password" placeholder="••••••••" required>
        </div>
        <div class="form-group">
            <label>Nova senha</label>
            <input type="password" name="new_password" placeholder="Nova senha" required>
        </div>
        <div class="form-group">
            <label>Confirmar nova senha</label>
            <input type="password" name="confirm_password" placeholder="Repita a nova senha" required>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn-primary">Alterar Senha</button>
        </div>
    </form>
</div>

<div class="form-card">
    <h3 class="settings-section-title">Sessão</h3>
    <p style="font-size:13px;color:#888;margin-bottom:16px">Encerrar acesso à sua conta</p>
    <a href="../logout.php" class="btn-outline">→ Fazer Logout</a>
</div>

<?php require_once '../includes/footer.php'; ?>
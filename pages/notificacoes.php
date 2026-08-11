<?php
$page_title = 'Notificações';
require_once '../includes/header.php';

$uid = $_SESSION['user_id'];

if (isset($_GET['marcar_todas'])) {
    mysqli_query($conn, "UPDATE notifications SET is_read=1 WHERE user_id=$uid");
    header('Location: notificacoes.php');
    exit;
}

if (isset($_GET['marcar']) && is_numeric($_GET['marcar'])) {
    $nid = (int)$_GET['marcar'];
    mysqli_query($conn, "UPDATE notifications SET is_read=1 WHERE id=$nid AND user_id=$uid");
    header('Location: notificacoes.php');
    exit;
}

$unread_count = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT COUNT(*) as t FROM notifications WHERE user_id=$uid AND is_read=0"))['t'];

$notifs = mysqli_query($conn,
    "SELECT * FROM notifications WHERE user_id=$uid ORDER BY created_at DESC");
?>

<div class="section-header">
    <div style="font-size:14px;color:#888"><?= $unread_count ?> notificação(ões) não lida(s)</div>
    <?php if ($unread_count > 0): ?>
        <a href="notificacoes.php?marcar_todas=1" class="btn-outline">Marcar todas como lidas</a>
    <?php endif; ?>
</div>

<div class="table-container">
    <?php if (mysqli_num_rows($notifs) == 0): ?>
        <div class="empty-state"><p>Nenhuma notificação ainda.</p></div>
    <?php else: ?>
        <?php while ($n = mysqli_fetch_assoc($notifs)): ?>
        <div class="notif-item <?= !$n['is_read'] ? 'notif-unread' : '' ?>">
            <div class="notif-icon">
                <?= !$n['is_read'] ? '🔔' : '✓' ?>
            </div>
            <div class="notif-content">
                <div class="notif-title"><?= htmlspecialchars($n['title']) ?></div>
                <div class="notif-message"><?= htmlspecialchars($n['message']) ?></div>
                <div class="notif-time"><?= date('d/m/Y H:i', strtotime($n['created_at'])) ?></div>
            </div>
            <?php if (!$n['is_read']): ?>
                <a href="notificacoes.php?marcar=<?= $n['id'] ?>" class="btn-outline" style="font-size:12px;padding:6px 12px">Marcar lida</a>
            <?php endif; ?>
        </div>
        <?php endwhile; ?>
    <?php endif; ?>
</div>

<?php require_once '../includes/footer.php'; ?>
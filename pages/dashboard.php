<?php
$page_title = 'Dashboard';
require_once '../includes/header.php';

$uid = $_SESSION['user_id'];
$total    = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM tasks WHERE user_id=$uid"))['t'];
$pending  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM tasks WHERE user_id=$uid AND status='pendente'"))['t'];
$progress = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM tasks WHERE user_id=$uid AND status='em_progresso'"))['t'];
$done     = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM tasks WHERE user_id=$uid AND status='concluida'"))['t'];
$recent   = mysqli_query($conn, "SELECT t.*, l.name as list_name FROM tasks t LEFT JOIN lists l ON t.list_id=l.id WHERE t.user_id=$uid ORDER BY t.created_at DESC LIMIT 5");
?>

<div class="dashboard-cards">
    <div class="dash-card">
        <div class="dash-label">Total de Tarefas</div>
        <div class="dash-number"><?= $total ?></div>
    </div>
    <div class="dash-card">
        <div class="dash-label">Pendentes</div>
        <div class="dash-number"><?= $pending ?></div>
    </div>
    <div class="dash-card">
        <div class="dash-label">Em Progresso</div>
        <div class="dash-number"><?= $progress ?></div>
    </div>
    <div class="dash-card">
        <div class="dash-label">Concluídas</div>
        <div class="dash-number"><?= $done ?></div>
    </div>
</div>

<div class="section-header">
    <h2>Tarefas Recentes</h2>
    <a href="tarefas.php?action=criar" class="btn-primary">+ Nova Tarefa</a>
</div>

<div class="table-container">
    <?php if (mysqli_num_rows($recent) == 0): ?>
        <div class="empty-state">
            <p>Nenhuma tarefa ainda. <a href="tarefas.php?action=criar">Crie sua primeira tarefa!</a></p>
        </div>
    <?php else: ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Tarefa</th>
                    <th>Lista</th>
                    <th>Prazo</th>
                    <th>Prioridade</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($t = mysqli_fetch_assoc($recent)): ?>
                <tr>
                    <td><?= htmlspecialchars($t['title']) ?></td>
                    <td><?= $t['list_name'] ?? '—' ?></td>
                    <td><?= $t['deadline'] ? date('d/m/Y', strtotime($t['deadline'])) : '—' ?></td>
                    <td><span class="badge badge-<?= $t['priority'] ?>"><?= ucfirst($t['priority']) ?></span></td>
                    <td><span class="badge badge-<?= $t['status'] ?>"><?= ucfirst(str_replace('_', ' ', $t['status'])) ?></span></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php require_once '../includes/footer.php'; ?>
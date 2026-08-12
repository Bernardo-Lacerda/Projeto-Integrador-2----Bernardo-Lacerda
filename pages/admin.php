<?php
$page_title = 'Administração';
require_once '../includes/header.php';

if ($_SESSION['user_role'] !== 'admin') {
    header('Location: ../acesso-negado.php');
    exit;
}

$search = isset($_GET['q']) ? mysqli_real_escape_string($conn, $_GET['q']) : '';

if (isset($_GET['remover']) && is_numeric($_GET['remover'])) {
    $rid = (int)$_GET['remover'];
    if ($rid !== $_SESSION['user_id']) {
        mysqli_query($conn, "DELETE FROM users WHERE id=$rid");
        header('Location: admin.php');
        exit;
    }
}

$total_users  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM users"))['t'];
$active_users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM users WHERE active=1"))['t'];
$total_tasks  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM tasks"))['t'];

$where = $search ? "WHERE name LIKE '%$search%' OR email LIKE '%$search%'" : '';
$users = mysqli_query($conn, "SELECT u.*, COUNT(t.id) as task_count FROM users u LEFT JOIN tasks t ON t.user_id=u.id $where GROUP BY u.id ORDER BY u.created_at DESC");
?>

<div class="dashboard-cards" style="margin-bottom:32px">
    <div class="dash-card">
        <div class="dash-label">Total de Usuários</div>
        <div class="dash-number"><?= $total_users ?></div>
    </div>
    <div class="dash-card">
        <div class="dash-label">Usuários Ativos</div>
        <div class="dash-number"><?= $active_users ?></div>
    </div>
    <div class="dash-card">
        <div class="dash-label">Total de Tarefas</div>
        <div class="dash-number"><?= $total_tasks ?></div>
    </div>
</div>

<div class="section-header">
    <h2 style="font-size:18px;font-weight:700;color:#222">Gerenciamento de Usuários</h2>
    <form method="GET" style="display:flex;gap:8px">
        <input type="text" name="q" value="<?= htmlspecialchars($search) ?>"
               placeholder="Buscar usuário..." class="filter-select" style="width:220px">
        <button type="submit" class="btn-primary">Buscar</button>
        <?php if ($search): ?>
            <a href="admin.php" class="btn-outline">Limpar</a>
        <?php endif; ?>
    </form>
</div>

<div class="table-container">
    <?php if (mysqli_num_rows($users) == 0): ?>
        <div class="empty-state"><p>Nenhum usuário encontrado.</p></div>
    <?php else: ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Usuário</th>
                    <th>Email</th>
                    <th>Tarefas</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($u = mysqli_fetch_assoc($users)): ?>
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px">
                            <div class="user-avatar" style="width:32px;height:32px;font-size:11px">
                                <?= strtoupper(substr($u['name'], 0, 2)) ?>
                            </div>
                            <div>
                                <div style="font-weight:600"><?= htmlspecialchars($u['name']) ?></div>
                                <?php if ($u['role'] === 'admin'): ?>
                                    <div style="font-size:11px;color:#7F77DD;font-weight:600">Admin</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </td>
                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td><?= $u['task_count'] ?></td>
                    <td>
                        <span class="badge <?= $u['active'] ? 'badge-concluida' : 'badge-alta' ?>">
                            <?= $u['active'] ? 'Ativo' : 'Inativo' ?>
                        </span>
                    </td>
                    <td>
                        <?php if ($u['id'] !== $_SESSION['user_id']): ?>
                            <a href="admin.php?remover=<?= $u['id'] ?>"
                               class="btn-danger" style="font-size:12px;padding:6px 12px"
                               onclick="return confirm('Remover este usuário?')">Remover</a>
                        <?php else: ?>
                            <span style="font-size:12px;color:#aaa">Você</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
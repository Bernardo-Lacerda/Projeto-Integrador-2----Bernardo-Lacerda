<?php
$page_title = 'Tarefas';
require_once '../includes/header.php';

$uid = $_SESSION['user_id'];
$action = $_GET['action'] ?? 'listar';
$task_id = $_GET['id'] ?? null;
$error = '';
$success = '';

// DELETAR
if ($action === 'deletar' && $task_id) {
    mysqli_query($conn, "DELETE FROM tasks WHERE id=$task_id AND user_id=$uid");
    header('Location: tarefas.php');
    exit;
}

// SALVAR (criar ou editar)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title    = mysqli_real_escape_string($conn, trim($_POST['title']));
    $desc     = mysqli_real_escape_string($conn, trim($_POST['description']));
    $list_id  = $_POST['list_id'] ?: 'NULL';
    $priority = $_POST['priority'];
    $status   = $_POST['status'];
    $deadline = $_POST['deadline'] ?: 'NULL';
    $deadline_val = $deadline !== 'NULL' ? "'$deadline'" : 'NULL';
    $list_val = $list_id !== 'NULL' ? $list_id : 'NULL';
    $completed = $status === 'concluida' ? "NOW()" : "NULL";

    if (empty($title)) {
        $error = 'O título é obrigatório.';
    } elseif (isset($_POST['task_id']) && $_POST['task_id']) {
        $tid = $_POST['task_id'];
        mysqli_query($conn, "UPDATE tasks SET title='$title', description='$desc',
            list_id=$list_val, priority='$priority', status='$status',
            deadline=$deadline_val, completed_at=$completed
            WHERE id=$tid AND user_id=$uid");
        $success = 'Tarefa atualizada com sucesso!';
        $action = 'listar';
        header('Location: tarefas.php');
        exit;
    } else {
        mysqli_query($conn, "INSERT INTO tasks (user_id, title, description, list_id, priority, status, deadline, completed_at)
            VALUES ($uid, '$title', '$desc', $list_val, '$priority', '$status', $deadline_val, $completed)");
        $success = 'Tarefa criada com sucesso!';
        $action = 'listar';
        header('Location: tarefas.php');
        exit;
    }
}

// BUSCAR LISTAS DO USUÁRIO
$lists = mysqli_query($conn, "SELECT * FROM lists WHERE user_id=$uid ORDER BY name");

// BUSCAR TAREFA PARA EDITAR/VISUALIZAR
$task = null;
if ($task_id && in_array($action, ['editar', 'ver'])) {
    $task = mysqli_fetch_assoc(mysqli_query($conn,
        "SELECT t.*, l.name as list_name FROM tasks t
         LEFT JOIN lists l ON t.list_id=l.id
         WHERE t.id=$task_id AND t.user_id=$uid"));
    if (!$task) { header('Location: tarefas.php'); exit; }
}
?>

<?php if ($action === 'listar'): ?>

<?php
$filter_list   = $_GET['lista'] ?? '';
$filter_status = $_GET['status'] ?? '';
$filter_prio   = $_GET['prioridade'] ?? '';
$where = "WHERE t.user_id=$uid";
if ($filter_list)   $where .= " AND t.list_id='$filter_list'";
if ($filter_status) $where .= " AND t.status='$filter_status'";
if ($filter_prio)   $where .= " AND t.priority='$filter_prio'";
$tasks = mysqli_query($conn, "SELECT t.*, l.name as list_name FROM tasks t
    LEFT JOIN lists l ON t.list_id=l.id $where ORDER BY t.created_at DESC");
?>

<div class="section-header">
    <div class="filters">
        <form method="GET" style="display:flex; gap:10px; flex-wrap:wrap;">
            <select name="lista" onchange="this.form.submit()" class="filter-select">
                <option value="">Todas as listas</option>
                <?php mysqli_data_seek($lists, 0); while ($l = mysqli_fetch_assoc($lists)): ?>
                    <option value="<?= $l['id'] ?>" <?= $filter_list == $l['id'] ? 'selected' : '' ?>><?= htmlspecialchars($l['name']) ?></option>
                <?php endwhile; ?>
            </select>
            <select name="status" onchange="this.form.submit()" class="filter-select">
                <option value="">Todos os status</option>
                <option value="pendente" <?= $filter_status=='pendente'?'selected':'' ?>>Pendente</option>
                <option value="em_progresso" <?= $filter_status=='em_progresso'?'selected':'' ?>>Em Progresso</option>
                <option value="concluida" <?= $filter_status=='concluida'?'selected':'' ?>>Concluída</option>
            </select>
            <select name="prioridade" onchange="this.form.submit()" class="filter-select">
                <option value="">Todas prioridades</option>
                <option value="alta" <?= $filter_prio=='alta'?'selected':'' ?>>Alta</option>
                <option value="media" <?= $filter_prio=='media'?'selected':'' ?>>Média</option>
                <option value="baixa" <?= $filter_prio=='baixa'?'selected':'' ?>>Baixa</option>
            </select>
        </form>
    </div>
    <a href="tarefas.php?action=criar" class="btn-primary">+ Nova Tarefa</a>
</div>

<div class="table-container">
    <?php if (mysqli_num_rows($tasks) == 0): ?>
        <div class="empty-state">
            <p>Nenhuma tarefa encontrada. <a href="tarefas.php?action=criar">Criar tarefa</a></p>
        </div>
    <?php else: ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Tarefa</th><th>Lista</th><th>Prazo</th>
                    <th>Prioridade</th><th>Status</th><th>Ações</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($t = mysqli_fetch_assoc($tasks)): ?>
                <tr class="<?= $t['status']==='concluida' ? 'row-done' : '' ?>">
                    <td><?= htmlspecialchars($t['title']) ?></td>
                    <td><?= $t['list_name'] ?? '—' ?></td>
                    <td class="<?= ($t['deadline'] && $t['deadline'] < date('Y-m-d') && $t['status']!='concluida') ? 'text-danger' : '' ?>">
                        <?= $t['deadline'] ? date('d/m/Y', strtotime($t['deadline'])) : '—' ?>
                    </td>
                    <td><span class="badge badge-<?= $t['priority'] ?>"><?= ucfirst($t['priority']) ?></span></td>
                    <td><span class="badge badge-<?= $t['status'] ?>"><?= ucfirst(str_replace('_',' ',$t['status'])) ?></span></td>
                    <td>
                        <a href="tarefas.php?action=ver&id=<?= $t['id'] ?>" class="btn-icon" title="Ver">👁</a>
                        <a href="tarefas.php?action=editar&id=<?= $t['id'] ?>" class="btn-icon" title="Editar">✏️</a>
                        <a href="tarefas.php?action=deletar&id=<?= $t['id'] ?>" class="btn-icon text-danger"
                           onclick="return confirm('Excluir esta tarefa?')" title="Excluir">🗑</a>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php elseif ($action === 'criar' || $action === 'editar'): ?>

<div class="form-card">
    <h2><?= $action === 'criar' ? 'Criar Tarefa' : 'Editar Tarefa' ?></h2>
    <p class="form-subtitle"><?= $action === 'criar' ? 'Preencha as informações para criar uma nova tarefa.' : 'Altere as informações da tarefa e salve as modificações.' ?></p>

    <?php if ($error): ?><div class="alert alert-error"><?= $error ?></div><?php endif; ?>

    <form method="POST">
        <input type="hidden" name="task_id" value="<?= $task['id'] ?? '' ?>">
        <div class="form-group">
            <label>Título *</label>
            <input type="text" name="title" placeholder="Ex.: Estudar para a prova de Matemática"
                   value="<?= htmlspecialchars($task['title'] ?? '') ?>" required>
        </div>
        <div class="form-group">
            <label>Descrição</label>
            <textarea name="description" rows="4" placeholder="Digite uma descrição para sua tarefa..."><?= htmlspecialchars($task['description'] ?? '') ?></textarea>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Lista</label>
                <select name="list_id">
                    <option value="">Selecione uma lista</option>
                    <?php mysqli_data_seek($lists, 0); while ($l = mysqli_fetch_assoc($lists)): ?>
                        <option value="<?= $l['id'] ?>" <?= ($task['list_id'] ?? '') == $l['id'] ? 'selected' : '' ?>><?= htmlspecialchars($l['name']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Prioridade</label>
                <select name="priority">
                    <option value="media" <?= ($task['priority'] ?? 'media')==='media'?'selected':'' ?>>Média</option>
                    <option value="alta"  <?= ($task['priority'] ?? '')==='alta' ?'selected':'' ?>>Alta</option>
                    <option value="baixa" <?= ($task['priority'] ?? '')==='baixa'?'selected':'' ?>>Baixa</option>
                </select>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Prazo</label>
                <input type="date" name="deadline" value="<?= $task['deadline'] ?? '' ?>">
            </div>
            <div class="form-group">
                <label>Status</label>
                <select name="status">
                    <option value="pendente"    <?= ($task['status'] ?? 'pendente')==='pendente'   ?'selected':'' ?>>Pendente</option>
                    <option value="em_progresso"<?= ($task['status'] ?? '')==='em_progresso'       ?'selected':'' ?>>Em Progresso</option>
                    <option value="concluida"   <?= ($task['status'] ?? '')==='concluida'          ?'selected':'' ?>>Concluída</option>
                </select>
            </div>
        </div>
        <div class="form-actions">
            <a href="tarefas.php" class="btn-outline">Cancelar</a>
            <?php if ($action === 'editar'): ?>
                <a href="tarefas.php?action=deletar&id=<?= $task['id'] ?>"
                   class="btn-danger" onclick="return confirm('Excluir esta tarefa?')">🗑 Excluir Tarefa</a>
            <?php endif; ?>
            <button type="submit" class="btn-primary"><?= $action === 'criar' ? 'Criar Tarefa' : 'Salvar Alterações' ?></button>
        </div>
    </form>
</div>

<?php elseif ($action === 'ver' && $task): ?>

<div class="form-card">
    <div class="task-view-header">
        <div>
            <h2><?= htmlspecialchars($task['title']) ?></h2>
            <div style="margin-top:8px; display:flex; gap:8px;">
                <span class="badge badge-<?= $task['priority'] ?>"><?= ucfirst($task['priority']) ?> Prioridade</span>
                <span class="badge badge-<?= $task['status'] ?>"><?= ucfirst(str_replace('_',' ',$task['status'])) ?></span>
            </div>
        </div>
        <a href="tarefas.php?action=editar&id=<?= $task['id'] ?>" class="btn-primary">✏ Editar</a>
    </div>

    <div class="task-view-info">
        <div class="info-item"><span class="info-label">Lista</span><span><?= $task['list_name'] ?? '—' ?></span></div>
        <div class="info-item"><span class="info-label">Prazo</span>
            <span class="<?= ($task['deadline'] && $task['deadline'] < date('Y-m-d')) ? 'text-danger' : '' ?>">
                <?= $task['deadline'] ? date('d/m/Y', strtotime($task['deadline'])) : '—' ?>
            </span>
        </div>
        <div class="info-item"><span class="info-label">Prioridade</span><span><?= ucfirst($task['priority']) ?></span></div>
        <div class="info-item"><span class="info-label">Status</span><span><?= ucfirst(str_replace('_',' ',$task['status'])) ?></span></div>
    </div>

    <?php if ($task['description']): ?>
    <div class="task-description">
        <div class="info-label">Descrição</div>
        <p><?= nl2br(htmlspecialchars($task['description'])) ?></p>
    </div>
    <?php endif; ?>

    <div class="form-actions">
        <a href="tarefas.php" class="btn-outline">← Voltar</a>
        <a href="tarefas.php?action=deletar&id=<?= $task['id'] ?>"
           class="btn-danger" onclick="return confirm('Excluir esta tarefa?')">🗑 Excluir</a>
        <a href="tarefas.php?action=editar&id=<?= $task['id'] ?>" class="btn-primary">✏ Editar Tarefa</a>
    </div>
</div>

<?php endif; ?>

<?php require_once '../includes/footer.php'; ?>
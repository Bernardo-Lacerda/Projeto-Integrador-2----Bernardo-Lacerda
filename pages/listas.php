<?php
$page_title = 'Minhas Listas';
require_once '../includes/header.php';

$uid = $_SESSION['user_id'];
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = mysqli_real_escape_string($conn, trim($_POST['name']));
    $color = $_POST['color'];

    if (empty($name)) {
        $error = 'O nome da lista é obrigatório.';
    } elseif (isset($_POST['list_id']) && $_POST['list_id']) {
        $lid = $_POST['list_id'];
        mysqli_query($conn, "UPDATE lists SET name='$name', color='$color' WHERE id=$lid AND user_id=$uid");
        $success = 'Lista atualizada com sucesso!';
    } else {
        mysqli_query($conn, "INSERT INTO lists (user_id, name, color) VALUES ($uid, '$name', '$color')");
        $success = 'Lista criada com sucesso!';
    }
}

if (isset($_GET['deletar'])) {
    $lid = (int)$_GET['deletar'];
    mysqli_query($conn, "UPDATE tasks SET list_id=NULL WHERE list_id=$lid AND user_id=$uid");
    mysqli_query($conn, "DELETE FROM lists WHERE id=$lid AND user_id=$uid");
    header('Location: listas.php');
    exit;
}

$lists = mysqli_query($conn, "
    SELECT l.*, 
        COUNT(t.id) as total_tasks,
        SUM(t.status='concluida') as done_tasks
    FROM lists l
    LEFT JOIN tasks t ON t.list_id=l.id
    WHERE l.user_id=$uid
    GROUP BY l.id
    ORDER BY l.created_at DESC
");

$edit_list = null;
if (isset($_GET['editar'])) {
    $lid = (int)$_GET['editar'];
    $edit_list = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM lists WHERE id=$lid AND user_id=$uid"));
}
?>

<div class="section-header">
    <h2 style="font-size:18px;font-weight:700;color:#222;">
        <?= $edit_list ? 'Editar Lista' : 'Minhas Listas' ?>
    </h2>
    <?php if (!$edit_list): ?>
        <button onclick="toggleForm()" class="btn-primary" id="btn-nova">+ Nova Lista</button>
    <?php endif; ?>
</div>

<?php if ($error): ?><div class="alert alert-error"><?= $error ?></div><?php endif; ?>
<?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>

<div class="form-card" id="form-nova" style="<?= $edit_list ? '' : 'display:none' ?> margin-bottom:24px; max-width:500px;">
    <h3 style="font-size:16px;font-weight:700;margin-bottom:16px;">
        <?= $edit_list ? 'Editar Lista' : 'Nova Lista' ?>
    </h3>
    <form method="POST">
        <input type="hidden" name="list_id" value="<?= $edit_list['id'] ?? '' ?>">
        <div class="form-group">
            <label>Nome da lista</label>
            <input type="text" name="name" placeholder="Ex.: Trabalho, Estudos, Pessoal"
                   value="<?= htmlspecialchars($edit_list['name'] ?? '') ?>" required>
        </div>
        <div class="form-group">
            <label>Cor</label>
    <div class="color-picker">
    <?php
    $colors = ['#7F77DD','#E53935','#43A047','#FB8C00','#1E88E5','#8E24AA','#00ACC1','#F4511E'];
    $selected = $edit_list['color'] ?? '#7F77DD';
    foreach ($colors as $i => $c):
    ?>
    <label class="color-option">
        <input type="radio" name="color" value="<?= $c ?>" <?= $selected===$c?'checked':'' ?>>
        <span class="color-dot color-<?= $i ?>"></span>
    </label>
    <?php endforeach; ?>
</div>
        </div>
        <div class="form-actions">
            <a href="listas.php" class="btn-outline">Cancelar</a>
            <button type="submit" class="btn-primary"><?= $edit_list ? 'Salvar Alterações' : 'Criar Lista' ?></button>
        </div>
    </form>
</div>

<?php if (mysqli_num_rows($lists) == 0): ?>
    <div class="empty-state">
        <p>Nenhuma lista criada ainda. Clique em <strong>+ Nova Lista</strong> para começar.</p>
    </div>
<?php else: ?>
    <div class="lists-grid">
        <?php while ($l = mysqli_fetch_assoc($lists)):
            $pct = $l['total_tasks'] > 0 ? round(($l['done_tasks'] / $l['total_tasks']) * 100) : 0;
        ?>
        <div class="list-card">
            <div class="list-card-header">
                <div class="list-icon" style="background:<?= $l['color'] ?>">
                    ☰
                </div>
                <div class="list-info">
                    <div class="list-name"><?= htmlspecialchars($l['name']) ?></div>
                    <div class="list-count"><?= $l['total_tasks'] ?> tarefa<?= $l['total_tasks'] != 1 ? 's' : '' ?></div>
                </div>
            </div>
            <div class="progress-bar">
                <div class="progress-fill" style="width:<?= $pct ?>%; background:<?= $l['color'] ?>"></div>
            </div>
            <div class="list-card-footer">
                <span class="list-pct"><?= $pct ?>% concluído</span>
                <div class="list-actions">
                    <a href="listas.php?editar=<?= $l['id'] ?>" class="btn-icon" title="Editar">✏️</a>
                    <a href="listas.php?deletar=<?= $l['id'] ?>"
                       class="btn-icon text-danger"
                       onclick="return confirm('Excluir esta lista? As tarefas não serão deletadas.')"
                       title="Excluir">🗑</a>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
<?php endif; ?>

<script>
function toggleForm() {
    var f = document.getElementById('form-nova');
    var b = document.getElementById('btn-nova');
    if (f.style.display === 'none') {
        f.style.display = 'block';
        b.textContent = '✕ Fechar';
    } else {
        f.style.display = 'none';
        b.textContent = '+ Nova Lista';
    }
}
</script>

<?php require_once '../includes/footer.php'; ?>
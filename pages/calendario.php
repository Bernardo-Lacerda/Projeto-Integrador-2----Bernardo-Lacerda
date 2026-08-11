<?php
$page_title = 'Calendário';
require_once '../includes/header.php';

$uid = $_SESSION['user_id'];

$month = isset($_GET['mes']) ? (int)$_GET['mes'] : (int)date('m');
$year  = isset($_GET['ano']) ? (int)$_GET['ano'] : (int)date('Y');

if ($month < 1) { $month = 12; $year--; }
if ($month > 12) { $month = 1; $year++; }

$prev_month = $month - 1;
$prev_year  = $year;
if ($prev_month < 1) { $prev_month = 12; $prev_year--; }

$next_month = $month + 1;
$next_year  = $year;
if ($next_month > 12) { $next_month = 1; $next_year++; }

$first_day   = mktime(0, 0, 0, $month, 1, $year);
$days_month  = (int)date('t', $first_day);
$start_week  = (int)date('w', $first_day);

$tasks_result = mysqli_query($conn,
    "SELECT t.*, l.color as list_color FROM tasks t
     LEFT JOIN lists l ON t.list_id = l.id
     WHERE t.user_id = $uid
     AND MONTH(t.deadline) = $month
     AND YEAR(t.deadline) = $year"
);

$tasks_by_day = [];
while ($t = mysqli_fetch_assoc($tasks_result)) {
    $day = (int)date('j', strtotime($t['deadline']));
    $tasks_by_day[$day][] = $t;
}

$month_names = [
    1=>'Janeiro',2=>'Fevereiro',3=>'Março',4=>'Abril',
    5=>'Maio',6=>'Junho',7=>'Julho',8=>'Agosto',
    9=>'Setembro',10=>'Outubro',11=>'Novembro',12=>'Dezembro'
];
?>

<div class="calendar-header">
    <a href="calendario.php?mes=<?= $prev_month ?>&ano=<?= $prev_year ?>" class="cal-nav">&#8249;</a>
    <h2><?= $month_names[$month] ?> <?= $year ?></h2>
    <a href="calendario.php?mes=<?= $next_month ?>&ano=<?= $next_year ?>" class="cal-nav">&#8250;</a>
    <a href="tarefas.php?action=criar" class="btn-primary" style="margin-left:auto">+ Nova Tarefa</a>
</div>

<div class="calendar-grid">
    <?php foreach (['Dom','Seg','Ter','Qua','Qui','Sex','Sáb'] as $d): ?>
        <div class="cal-weekday"><?= $d ?></div>
    <?php endforeach; ?>

    <?php for ($i = 0; $i < $start_week; $i++): ?>
        <div class="cal-day cal-empty"></div>
    <?php endfor; ?>

    <?php for ($day = 1; $day <= $days_month; $day++):
        $is_today = ($day == date('j') && $month == date('n') && $year == date('Y'));
    ?>
        <div class="cal-day <?= $is_today ? 'cal-today' : '' ?>">
            <div class="cal-day-number"><?= $day ?></div>
            <?php if (isset($tasks_by_day[$day])): ?>
                <?php foreach ($tasks_by_day[$day] as $t): ?>
                    <a href="tarefas.php?action=ver&id=<?= $t['id'] ?>" class="cal-task cal-task-<?= $t['priority'] ?>"
                       style="<?= $t['list_color'] ? 'background:'.$t['list_color'].';' : '' ?>"
                       title="<?= htmlspecialchars($t['title']) ?>">
                        <?= htmlspecialchars(mb_strimwidth($t['title'], 0, 18, '...')) ?>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    <?php endfor; ?>
</div>

<?php require_once '../includes/footer.php'; ?>
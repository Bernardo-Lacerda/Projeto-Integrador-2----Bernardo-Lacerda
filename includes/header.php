<?php
ob_start();
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}
require_once '../config/database.php';

$unread = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT COUNT(*) as total FROM notifications 
     WHERE user_id = {$_SESSION['user_id']} AND is_read = 0"
))['total'];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?? 'Organiza+' ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="app-body">

<div class="app-layout">
    <aside class="sidebar">
        <div class="sidebar-logo">
            <span class="logo-icon">✓</span>
            <span class="logo-text">Organiza+</span>
        </div>
        <nav class="sidebar-nav">
            <a href="dashboard.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>">
                <span class="nav-icon">⊞</span> Painel
            </a>
            <a href="tarefas.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'tarefas.php' ? 'active' : '' ?>">
                <span class="nav-icon">☑</span> Tarefas
            </a>
            <a href="listas.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'listas.php' ? 'active' : '' ?>">
                <span class="nav-icon">☰</span> Minhas Listas
            </a>
            <a href="calendario.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'calendario.php' ? 'active' : '' ?>">
                <span class="nav-icon">📅</span> Calendário
            </a>
            <a href="perfil.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'perfil.php' ? 'active' : '' ?>">
                <span class="nav-icon">👤</span> Perfil
            </a>
            <a href="configuracoes.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'configuracoes.php' ? 'active' : '' ?>">
                <span class="nav-icon">⚙</span> Configurações
            </a>
            <?php if ($_SESSION['user_role'] === 'admin'): ?>
<a href="admin.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'admin.php' ? 'active' : '' ?>">
    <span class="nav-icon">⚙</span> Administração
</a>
<?php endif; ?>
        </nav>
        <a href="../logout.php" class="nav-item nav-logout">
            <span class="nav-icon">→</span> Sair
        </a>
    </aside>

    <div class="app-content">
        <header class="app-header">
            <h1 class="page-title"><?= $page_title ?? '' ?></h1>
            <div class="header-right">
                <a href="notificacoes.php" class="notif-btn">
                    🔔 <?php if ($unread > 0): ?>
                        <span class="notif-badge"><?= $unread ?></span>
                    <?php endif; ?>
                </a>
                <div class="user-avatar">
                    <?= strtoupper(substr($_SESSION['user_name'], 0, 2)) ?>
                </div>
                <span class="user-name">Olá, <?= htmlspecialchars(explode(' ', $_SESSION['user_name'])[0]) ?></span>
            </div>
        </header>
        <main class="app-main">
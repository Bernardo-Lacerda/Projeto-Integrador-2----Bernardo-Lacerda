<?php session_start(); ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Organiza+ | Organize sua vida</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<nav class="navbar">
    <div class="nav-container">
        <div class="logo">
            <span class="logo-icon">✓</span>
            <span class="logo-text">Organiza+</span>
        </div>
        <div class="nav-buttons">
            <a href="login.php" class="btn-outline">Entrar</a>
            <a href="cadastro.php" class="btn-primary">Criar Conta</a>
        </div>
    </div>
</nav>

<section class="hero">
    <div class="hero-container">
        <h1>Organize sua vida,<br>
            <span class="highlight">aumente sua produtividade</span>
        </h1>
        <p>Gerencie tarefas, listas e prazos em um único lugar.<br>
           Simples, rápido e eficiente.</p>
        <div class="hero-buttons">
            <a href="cadastro.php" class="btn-primary btn-large">Começar Grátis</a>
            <a href="#features" class="btn-ghost btn-large">Saiba mais</a>
        </div>
    </div>
</section>

<section class="features" id="features">
    <div class="features-container">
        <div class="feature-card">
            <div class="feature-icon">✓</div>
            <h3>Gestão de Tarefas</h3>
            <p>Crie, edite e organize suas tarefas com prioridades e prazos definidos.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">📅</div>
            <h3>Calendário Integrado</h3>
            <p>Visualize todas as suas tarefas e compromissos em uma visão de calendário.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">📋</div>
            <h3>Listas Personalizadas</h3>
            <p>Organize tarefas em categorias customizadas para melhor controle.</p>
        </div>
    </div>
</section>

<footer class="footer">
    <p>&copy; 2026 Organiza+ — Todos os direitos reservados.</p>
</footer>

</body>
</html>
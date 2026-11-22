<?php
/** Layout principal: estructura HTML común a todas las vistas */
$cfg   = require dirname(__DIR__, 2) . '/config.php';
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e(($title ?? '') ? $title . ' · ' . $cfg['name'] : $cfg['name']) ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>:root{--accent:<?= e($cfg['color']) ?>;}</style>
</head>
<body>
<header class="topbar">
    <a class="brand" href="index.php">
        <span class="brand-icon"><?= $cfg['icon'] ?></span>
        <span><?= e($cfg['name']) ?></span>
    </a>
    <nav>
        <?php foreach ($cfg['menu'] ?? [] as $label => $action): ?>
            <a href="index.php?action=<?= e($action) ?>"><?= e($label) ?></a>
        <?php endforeach; ?>
    </nav>
</header>
<main class="container">
    <?php if ($flash): ?>
        <div class="alert alert-<?= e($flash['type']) ?>"><?= e($flash['msg']) ?></div>
    <?php endif; ?>
    <?= $content ?>
</main>
<footer class="footer">Ejercicio <?= e($cfg['number']) ?> · PHP <?= PHP_VERSION ?> · Arquitectura MVC</footer>
</body>
</html>

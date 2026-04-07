<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>TubeMod</title>
    <link rel="stylesheet" href="<?= BASEURL ?>/assets/style.css">
    <script defer src="<?= BASEURL ?>/assets/script.js"></script>
    <link rel="icon" type="image/png" href="<?= BASEURL; ?>/assets/logo.png">
</head>
<body>

<?php
$current = explode('/', $_GET['url'] ?? '')[0] ?? '';
?>

<nav class="navbar">
    <a href="<?= BASEURL ?>/home" class="logo">
        <img src="<?= BASEURL ?>/assets/logo.png" alt="TubeMod Logo">
    </a>

    <div class="nav-links">
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="<?= BASEURL ?>/home" class="<?= $current == 'home' ? 'active' : '' ?>">Home</a>
            <a href="<?= BASEURL ?>/history" class="<?= $current == 'history' ? 'active' : '' ?>">History</a>
            <a href="<?= BASEURL ?>/keyword" class="<?= $current == 'keyword' ? 'active' : '' ?>">Keywords</a>
            <a href="<?= BASEURL ?>/auth/logout" onclick="return confirm('Are you sure you want to logout?')">Logout</a>
        <?php else: ?>
            <a href="<?= BASEURL ?>/home">Home</a>
            <a href="<?= BASEURL ?>/auth/login" class="btn-nav">Login</a>
        <?php endif; ?>
    </div>
</nav>
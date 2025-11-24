<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mi Blog Personal</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>

<body>
    <header>
        <nav>
            <div class="nav-brand">
                <a href="/" class="brand">Mi Blog</a>
            </div>
            <button class="nav-toggle" aria-label="Abrir menú" aria-expanded="false" aria-controls="nav-links">
                <span class="hamburger"></span>
            </button>
            <div class="nav-links" id="nav-links">
                <a href="/">Inicio</a>
                <a href="/posts">Posts</a>
                <form action="/" method="get" class="nav-search" style="display:inline-block; margin-left:0.5rem;">
                    <input type="search" name="q" placeholder="Buscar posts..." aria-label="Buscar" />
                </form>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php
                    require_once __DIR__ . '/../../Models/User.php';
                    $userModel = new User();
                    $currentUser = $userModel->getById($_SESSION['user_id']);
                    $profileImg = !empty($currentUser['profile_image']) ? $currentUser['profile_image'] : 'assets/images/default-avatar.svg';
                    ?>
                    <a href="/profile" class="profile-link" style="display:inline-flex; align-items:center; gap:0.5rem;">
                        <img src="/<?= htmlspecialchars($profileImg) ?>" alt="Perfil" class="avatar header-avatar" style="width:34px;height:34px; object-fit:cover;" />
                        <span>Perfil</span>
                    </a>
                    <button id="dark-toggle" class="btn btn-secondary" type="button" title="Alternar modo oscuro">🌙</button>
                    <a href="/logout" class="btn btn-danger">Salir</a>
                <?php else: ?>
                    <a href="/login">Login</a>
                    <a href="/register" class="btn btn-secondary">Registrar</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>
    <main>
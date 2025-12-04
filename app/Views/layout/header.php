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
            <button class="sidebar-menu-toggle" title="Abrir/Cerrar filtros">
                <span></span>
                <span></span>
                <span></span>
            </button>
            <div class="nav-brand">
                <a href="/" class="brand shiny">Mi Blog</a>
            </div>
            <button class="nav-toggle" aria-label="Abrir menú" aria-expanded="false" aria-controls="nav-links">
                <span class="hamburger"></span>
            </button>
            <div class="nav-links" id="nav-links">
                <a href="/">Inicio</a>
                <a href="/posts">Mis posts</a>
                <form action="/" method="get" class="nav-search" style="display:inline-block; margin-left:0.5rem;">
                    <div class="search-wrapper shine">
                        <input type="search" name="q" placeholder="Buscar posts..." aria-label="Buscar" />
                    </div>
                </form>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php
                    require_once __DIR__ . '/../../Models/User.php';
                    require_once __DIR__ . '/../../Models/Notification.php';
                    require_once __DIR__ . '/../../Models/Database.php';
                    $userModel = new User();
                    $currentUser = $userModel->getById($_SESSION['user_id']);
                    $profileImg = !empty($currentUser['profile_image']) ? $currentUser['profile_image'] : 'assets/images/default-avatar.svg';
                    
                    // Obtener notificaciones no leídas
                    $db = new Database();
                    $notificationModel = new Notification($db->getConnection());
                    $unreadCount = $notificationModel->countUnread($_SESSION['user_id']);
                    ?>
                    <a href="/notifications" class="notifications-link" title="Notificaciones">
                        📬 
                        <?php if ($unreadCount > 0): ?>
                            <span class="notification-badge"><?php echo (int)$unreadCount; ?></span>
                        <?php endif; ?>
                    </a>
                    <a href="/profile" class="profile-link" style="display:inline-flex; align-items:center; gap:0.5rem;">
                        <img src="/<?= htmlspecialchars($profileImg) ?>" alt="Perfil" class="avatar header-avatar" style="width:34px;height:34px; object-fit:cover;" />
                        <span>Perfil</span>
                    </a>
                    <button id="dark-toggle" class="btn btn-secondary" type="button" title="Alternar modo oscuro">🌙</button>
                    <button id="logout-btn" class="btn btn-danger" type="button">Salir</button>
                <?php else: ?>
                    <a href="/login" class="btn btn-login">Login</a>
                    <a href="/register" class="btn btn-register">Registrar</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>
    <main>
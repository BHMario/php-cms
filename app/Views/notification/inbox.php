<?php require __DIR__ . '/../layout/header.php'; ?>

<div class="container page-top">
    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h1 class="shiny" style="margin: 0;">📬 Notificaciones</h1>
            <?php
            $ref = 
                isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])
                ? htmlspecialchars($_SERVER['HTTP_REFERER'])
                : '/posts';
            ?>
            <a href="<?php echo $ref; ?>" class="btn" onclick="history.back(); return false;">&larr; Volver</a>
        </div>
        
        <?php if (empty($notifications)): ?>
            <p class="muted">No tienes notificaciones</p>
        <?php else: ?>
            <div class="notifications-list">
                <?php foreach ($notifications as $notif): ?>
                    <div class="notification-item">
                        <div class="notification-content">
                            <?php if ($notif['type'] === 'follow'): ?>
                                <div class="notification-avatar">
                                    <?php if ($notif['actor_profile_image']): ?>
                                        <img src="/<?php echo htmlspecialchars($notif['actor_profile_image']); ?>" alt="<?php echo htmlspecialchars($notif['actor_username']); ?>">
                                    <?php else: ?>
                                        <img src="/assets/images/default-avatar.svg" alt="Default avatar">
                                    <?php endif; ?>
                                </div>
                                <div class="notification-text">
                                    <strong><?php echo htmlspecialchars($notif['actor_username']); ?></strong> te ha seguido
                                    <div class="notification-time"><?php echo date('d/m/Y H:i', strtotime($notif['created_at'])); ?></div>
                                </div>
                            <?php elseif ($notif['type'] === 'post'): ?>
                                <div class="notification-avatar">
                                    <?php if ($notif['actor_profile_image']): ?>
                                        <img src="/<?php echo htmlspecialchars($notif['actor_profile_image']); ?>" alt="<?php echo htmlspecialchars($notif['actor_username']); ?>">
                                    <?php else: ?>
                                        <img src="/assets/images/default-avatar.svg" alt="Default avatar">
                                    <?php endif; ?>
                                </div>
                                <div class="notification-text">
                                    <strong><?php echo htmlspecialchars($notif['actor_username']); ?></strong> publicó: 
                                    <a href="/posts/<?= htmlspecialchars($notif['slug'] ?? $notif['post_id']) ?>">
                                        <?php echo htmlspecialchars(substr($notif['post_title'], 0, 50)); ?>...
                                    </a>
                                    <div class="notification-time"><?php echo date('d/m/Y H:i', strtotime($notif['created_at'])); ?></div>
                                </div>
                            <?php elseif ($notif['type'] === 'like'): ?>
                                <div class="notification-avatar">
                                    <?php if ($notif['actor_profile_image']): ?>
                                        <img src="/<?php echo htmlspecialchars($notif['actor_profile_image']); ?>" alt="<?php echo htmlspecialchars($notif['actor_username']); ?>">
                                    <?php else: ?>
                                        <img src="/assets/images/default-avatar.svg" alt="Default avatar">
                                    <?php endif; ?>
                                </div>
                                <div class="notification-text">
                                    <strong><?php echo htmlspecialchars($notif['actor_username']); ?></strong> le dio like a tu publicación
                                    <div class="notification-time"><?php echo date('d/m/Y H:i', strtotime($notif['created_at'])); ?></div>
                                    <div><a href="/posts/<?php echo htmlspecialchars($notif['slug'] ?? $notif['post_id']); ?>">Ver publicación</a></div>
                                </div>
                            <?php elseif ($notif['type'] === 'comment'): ?>
                                <div class="notification-avatar">
                                    <?php if ($notif['actor_profile_image']): ?>
                                        <img src="/<?php echo htmlspecialchars($notif['actor_profile_image']); ?>" alt="<?php echo htmlspecialchars($notif['actor_username']); ?>">
                                    <?php else: ?>
                                        <img src="/assets/images/default-avatar.svg" alt="Default avatar">
                                    <?php endif; ?>
                                </div>
                                <div class="notification-text">
                                    <strong><?php echo htmlspecialchars($notif['actor_username']); ?></strong> comentó en tu publicación
                                    <div class="notification-time"><?php echo date('d/m/Y H:i', strtotime($notif['created_at'])); ?></div>
                                    <div><a href="/posts/<?php echo htmlspecialchars($notif['slug'] ?? $notif['post_id']); ?>">Ver publicación</a></div>
                                </div>
                            <?php endif; ?>
                        </div>
                        <a href="/notifications/<?php echo (int)$notif['id']; ?>/delete" class="notification-delete" onclick="return confirm('¿Eliminar notificación?')">✕</a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
    .notifications-list {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .notification-item {
        display: flex;
        gap: 1rem;
        padding: 1rem;
        border: 1px solid var(--border-color);
        border-radius: 0.5rem;
        background: var(--card-bg);
        transition: all 0.2s ease;
    }

    .notification-item:hover {
        background: var(--input-bg);
        border-color: var(--primary-color);
    }

    .notification-content {
        flex: 1;
        display: flex;
        gap: 1rem;
        align-items: flex-start;
    }

    .notification-avatar {
        flex-shrink: 0;
    }

    .notification-avatar img {
        width: 3rem;
        height: 3rem;
        border-radius: 50%;
        object-fit: cover;
    }

    .notification-text {
        flex: 1;
        color: var(--text-color);
    }

    .notification-text strong {
        color: var(--primary-color);
    }

    .notification-text a {
        color: var(--primary-color);
        text-decoration: none;
    }

    .notification-text a:hover {
        text-decoration: underline;
    }

    .notification-time {
        font-size: 0.875rem;
        color: var(--muted-color);
        margin-top: 0.25rem;
    }

    .notification-delete {
        flex-shrink: 0;
        padding: 0.5rem;
        background: transparent;
        color: var(--accent-color);
        border: none;
        cursor: pointer;
        font-size: 1.25rem;
        transition: all 0.2s ease;
    }

    .notification-delete:hover {
        color: var(--primary-color);
        transform: scale(1.2);
    }

    @media (max-width: 640px) {
        .notification-content {
            flex-direction: column;
        }

        .notification-avatar img {
            width: 2.5rem;
            height: 2.5rem;
        }
    }
</style>

<?php require __DIR__ . '/../layout/footer.php'; ?>

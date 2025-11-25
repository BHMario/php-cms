<?php require __DIR__ . '/../layout/header.php'; ?>

<div class="container auth-container">
    <div class="card">
        <h1 class="text-center shiny">Perfil público</h1>
        <div style="display:flex; gap:1rem; align-items:center;">
            <div>
                <?php if (!empty($other['profile_image'])): ?>
                    <img src="/<?= htmlspecialchars($other['profile_image']) ?>" alt="Foto de perfil" style="width:96px; height:96px; object-fit:cover; border-radius:8px;">
                <?php else: ?>
                    <img src="/assets/images/default-avatar.svg" alt="Perfil" style="width:96px; height:96px;">
                <?php endif; ?>
            </div>
            <div>
                <p><strong>Usuario:</strong> <?= htmlspecialchars($other['username']) ?></p>
                <?php if (!empty($other['bio'])): ?><p><?= nl2br(htmlspecialchars($other['bio'])) ?></p><?php endif; ?>
                <p class="muted">Seguidores: <?= $followersCount ?> — Siguiendo: <?= $followingCount ?></p>
            </div>
        </div>

        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] !== $other['id']): ?>
            <div style="margin-top:1rem;">
                <?php if ($isFollowing): ?>
                    <form action="/users/<?= $other['id'] ?>/unfollow" method="post">
                        <button class="btn btn-secondary" type="submit">Dejar de seguir</button>
                    </form>
                <?php else: ?>
                    <form action="/users/<?= $other['id'] ?>/follow" method="post">
                        <button class="btn" type="submit">Seguir</button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endif; ?>

    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>

<?php require __DIR__ . '/../layout/header.php'; ?>

<!-- Sidebar con filtros -->
<?php require __DIR__ . '/../layout/sidebar.php'; ?>

<div class="container page-top">
    <h1 class="text-center mt-4 shiny">Bienvenido a Mi Blog Personal</h1>

    <!-- Posts grid -->
    <div class="posts-container">
        <div>
            <?php if (!empty($posts)): ?>
                <section class="posts-grid mt-4">
                    <?php foreach ($posts as $post): ?>
                        <article class="card animate-in">
                            <?php if (!empty($post['image'])): ?>
                                <img src="/<?= htmlspecialchars($post['image']) ?>" alt="Imagen del post" class="post-image">
                            <?php endif; ?>
                            <h2 class="card-title"><a class="card-link" href="/posts/<?= $post['id'] ?>"><?= htmlspecialchars($post['title']) ?></a></h2>
                            <p><?= nl2br(htmlspecialchars(substr($post['content'], 0, 200))) ?>...</p>
                            <div class="card-meta" style="display:flex; align-items:center; gap:0.6rem;">
                                <?php $authorImg = !empty($post['profile_image']) ? $post['profile_image'] : 'assets/images/default-avatar.svg'; ?>
                                <img src="/<?= htmlspecialchars($authorImg) ?>" alt="Avatar" class="avatar" style="width:36px;height:36px;" />
                                <small>Publicado por <strong><a href="/users/<?= $post['user_id'] ?>" class="card-link"><?= htmlspecialchars($post['username']) ?></a></strong> el <?= $post['created_at'] ?></small>
                            </div>
                            <div class="muted" style="margin-top:0.6rem; font-size:0.95rem; display:flex; gap:1rem; align-items:center;">
                                <span>❤️ <?= $post['like_count'] ?? 0 ?></span>
                                <span>💬 <?= $post['comment_count'] ?? 0 ?></span>
                            </div>
                            <div class="card-cta">
                                <a href="/posts/<?= $post['id'] ?>" class="btn">Leer más</a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </section>
            <?php else: ?>
                <div class="card mt-4 text-center">
                    <p>No hay posts publicados aún.</p>
                    <?php if (!empty($_GET['tags']) || !empty($_GET['categories']) || isset($_GET['sort'])): ?>
                        <p style="font-size: 0.9rem; color: var(--muted-color);">Intenta cambiar los filtros o buscar otras etiquetas.</p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
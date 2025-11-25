<?php require __DIR__ . '/../layout/header.php'; ?>

<div class="container page-top">
    <?php if ($post): ?>
        <article class="card">
            <h1 class="shiny"><?= htmlspecialchars($post['title']) ?></h1>
            <div class="card-meta" style="display:flex; align-items:center; gap:0.6rem;">
                <?php $authorImg = !empty($post['profile_image']) ? $post['profile_image'] : 'assets/images/default-avatar.svg'; ?>
                <img src="/<?= htmlspecialchars($authorImg) ?>" alt="Avatar" class="avatar" style="width:44px;height:44px;" />
                <small>Publicado por <strong><a href="/users/<?= $post['user_id'] ?>" class="card-link"><?= htmlspecialchars($post['username']) ?></a></strong> el <?= $post['created_at'] ?></small>
                <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] != $post['user_id']):
                    require_once __DIR__ . '/../../Models/Follower.php';
                    $f = new Follower();
                    $isFollowing = $f->isFollowing($_SESSION['user_id'], $post['user_id']);
                ?>
                    <div style="margin-left:auto;">
                        <?php if ($isFollowing): ?>
                            <form action="/users/<?= $post['user_id'] ?>/unfollow" method="post"><button class="btn btn-secondary" type="submit">Dejar de seguir</button></form>
                        <?php else: ?>
                            <form action="/users/<?= $post['user_id'] ?>/follow" method="post"><button class="btn" type="submit">Seguir</button></form>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <?php if (!empty($post['image'])): ?>
                <img src="/<?= htmlspecialchars($post['image']) ?>" alt="Imagen del post" class="post-image-full">
            <?php endif; ?>

            <div class="post-content">
                <?= nl2br(htmlspecialchars($post['content'])) ?>
            </div>

            <div class="row-between divider-top" style="align-items:center;">
                <div>
                    <form action="/posts/<?= $post['id'] ?>/like" method="post" style="display:inline;">
                        <button type="submit" class="btn" aria-pressed="<?= isset($userLiked) && $userLiked ? 'true' : 'false' ?>">
                            <?= isset($userLiked) && $userLiked ? '❤️' : '🤍' ?> Me gusta
                        </button>
                    </form>
                        <span class="ml-1 muted">Likes: <?= $likeCount ?? 0 ?></span>
                        <span class="ml-1 muted">Comentarios: <?= count($comments) ?></span>
                </div>

                <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $post['user_id']): ?>
                    <div>
                        <a href="/posts/<?= $post['id'] ?>/edit" class="btn">Editar</a>
                        <a href="/posts/<?= $post['id'] ?>/delete" class="btn btn-danger ml-1" onclick="return confirm('¿Estás seguro de eliminar este post?')">Eliminar</a>
                    </div>
                <?php endif; ?>
            </div>
        </article>
        <section class="card mt-4">
            <h3>Comentarios</h3>
            <?php if (!empty($comments)): ?>
                <?php foreach ($comments as $c): ?>
                    <div style="border-bottom:1px solid #eee; padding:0.6rem 0; display:flex; gap:0.75rem;">
                        <?php $cImg = !empty($c['profile_image']) ? $c['profile_image'] : 'assets/images/default-avatar.svg'; ?>
                        <img src="/<?= htmlspecialchars($cImg) ?>" alt="Avatar" class="avatar" style="width:40px;height:40px; object-fit:cover;" />
                        <div style="flex:1;">
                            <strong><?= htmlspecialchars($c['username']) ?></strong>
                            <div class="muted" style="font-size:0.9rem;"><?= $c['created_at'] ?></div>
                            <p style="margin:0.5rem 0;"><?= nl2br(htmlspecialchars($c['content'])) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="muted">Sé el primero en comentar este post.</p>
            <?php endif; ?>

            <?php if (isset($_SESSION['user_id'])): ?>
                <form action="/posts/<?= $post['id'] ?>/comment" method="post" style="margin-top:1rem;">
                    <div class="form-group">
                        <label for="comment_content">Agregar comentario</label>
                        <textarea name="content" id="comment_content" rows="3" required></textarea>
                    </div>
                    <button type="submit" class="btn">Comentar</button>
                </form>
            <?php else: ?>
                <p class="muted">Debes <a href="/login" class="card-link">iniciar sesión</a> para comentar.</p>
            <?php endif; ?>
        </section>

        <div class="mt-4">
            <a href="/posts" class="back-link">&larr; Volver a Posts</a>
        </div>
    <?php else: ?>
        <div class="card text-center">
            <p>Post no encontrado.</p>
            <a href="/posts" class="btn">Volver a Posts</a>
        </div>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
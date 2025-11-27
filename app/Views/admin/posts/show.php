<?php require __DIR__ . '/../../layout/admin_header.php'; ?>

<div class="container page-top">
    <?php if ($post): ?>
        <article class="card">
            <h1 class="shiny"><?= htmlspecialchars($post['title']) ?></h1>
            <div class="card-meta" style="display:flex; align-items:center; gap:0.6rem;">
                <?php $authorImg = !empty($post['profile_image']) ? $post['profile_image'] : 'assets/images/default-avatar.svg'; ?>
                <img src="/<?= htmlspecialchars($authorImg) ?>" alt="Avatar" class="avatar" style="width:44px;height:44px;" />
                <small>Publicado por <strong><?= htmlspecialchars($post['username']) ?></strong> el <?= $post['created_at'] ?></small>
            </div>

            <?php if (!empty($post['image'])): ?>
                <img src="/<?= htmlspecialchars($post['image']) ?>" alt="Imagen del post" class="post-image-full">
            <?php endif; ?>

            <div class="post-content">
                <?= nl2br(htmlspecialchars($post['content'])) ?>
            </div>

            <div class="row-between divider-top" style="align-items:center;">
                <div>
                    <span class="ml-1 muted">Categoría: <?= !empty($post['category_name']) ? htmlspecialchars($post['category_name']) : 'Sin categoría' ?></span>
                </div>
                <div>
                    <a href="/admin/posts/<?= $post['id'] ?>/edit" class="btn">Editar</a>
                    <a href="/admin/posts/<?= $post['id'] ?>/delete" class="btn btn-danger ml-1" onclick="return confirm('¿Estás seguro de eliminar este post?')">Eliminar</a>
                </div>
            </div>
        </article>

        <div class="mt-4">
            <a href="/admin/posts" class="btn btn-back">Volver</a>
        </div>
    <?php else: ?>
        <div class="card text-center">
            <p>Post no encontrado.</p>
            <a href="/admin/posts" class="btn">Volver a Posts</a>
        </div>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/../../layout/admin_footer.php'; ?>

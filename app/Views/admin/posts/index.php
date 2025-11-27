<?php require __DIR__ . '/../../layout/admin_header.php'; ?>

<div class="container page-top">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h1 style="margin: 0;">Posts</h1>
        <a href="/admin" class="btn btn-back">&larr; Volver</a>
    </div>
    <a href="/admin/posts/create" class="btn">Crear nuevo post</a>
    <div class="mt-4">
        <?php if (!empty($posts)): ?>
            <?php foreach ($posts as $p): ?>
                <div class="card">
                    <h3><?php echo htmlspecialchars($p['title']); ?></h3>
                    <p class="muted">Por <?php echo htmlspecialchars($p['username']); ?> | <?php echo htmlspecialchars($p['created_at'] ?? ''); ?></p>
                    <div class="card-cta">
                        <a href="/admin/posts/<?php echo $p['id']; ?>/edit" class="btn btn-secondary">Editar</a>
                        <a href="/admin/posts/<?php echo $p['id']; ?>/delete" class="btn btn-danger" onclick="return confirm('Eliminar post?')">Eliminar</a>
                        <a href="/admin/posts/<?php echo $p['id']; ?>" class="btn btn-secondary">Ver</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No hay posts aún.</p>
        <?php endif; ?>
    </div>
</div>

<?php require __DIR__ . '/../../layout/admin_footer.php'; ?>

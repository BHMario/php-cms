<?php require __DIR__ . '/../../layout/admin_header.php'; ?>

<div class="container page-top">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h1 style="margin: 0;">Categorías</h1>
        <a href="/admin" class="btn" onclick="history.back(); return false;">&larr; Volver</a>
    </div>
    <a href="/admin/categories/create" class="btn">Crear categoría</a>
    <div class="mt-4">
        <?php if (!empty($categories)): ?>
            <?php foreach ($categories as $c): ?>
                <div class="card">
                    <h3><?php echo htmlspecialchars($c['name']); ?></h3>
                    <div class="card-cta">
                        <a href="/admin/categories/<?php echo $c['id']; ?>/edit" class="btn btn-secondary">Editar</a>
                        <a href="/admin/categories/<?php echo $c['id']; ?>/delete" class="btn btn-danger" onclick="return confirm('Eliminar categoría?')">Eliminar</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No hay categorías.</p>
        <?php endif; ?>
    </div>
</div>

<?php require __DIR__ . '/../../layout/admin_footer.php'; ?>

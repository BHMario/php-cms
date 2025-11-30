<?php require __DIR__ . '/../../layout/admin_header.php'; ?>

<div class="container page-top">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h1 style="margin: 0;">Usuarios</h1>
        <a href="/admin" class="btn" onclick="history.back(); return false;">&larr; Volver</a>
    </div>
    <a href="/admin/users/create" class="btn">Crear usuario</a>
    <div class="mt-4">
        <?php if (!empty($users)): ?>
            <?php foreach ($users as $u): ?>
                <div class="card">
                    <h3><?php echo htmlspecialchars($u['username']); ?></h3>
                    <p class="muted">Rol: <?php echo htmlspecialchars($u['role']); ?></p>
                    <div class="card-cta">
                        <a href="/admin/users/<?php echo $u['id']; ?>/edit" class="btn btn-secondary">Editar</a>
                        <a href="/admin/users/<?php echo $u['id']; ?>/delete" class="btn btn-danger" onclick="return confirm('Eliminar usuario?')">Eliminar</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No hay usuarios.</p>
        <?php endif; ?>
    </div>
</div>

<?php require __DIR__ . '/../../layout/admin_footer.php'; ?>

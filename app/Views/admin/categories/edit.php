<?php require __DIR__ . '/../../layout/admin_header.php'; ?>

<div class="container page-top">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h1 style="margin:0;">Editar Categoría</h1>
        <a href="/admin/categories" class="btn" onclick="history.back(); return false;">&larr; Volver</a>
    </div>
    <form action="/admin/categories/<?php echo $category['id']; ?>/update" method="post">
        <div class="form-group">
            <label>Nombre</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($category['name']); ?>" required />
        </div>
        <button class="btn" type="submit">Actualizar</button>
    </form>
</div>

<?php require __DIR__ . '/../../layout/admin_footer.php'; ?>

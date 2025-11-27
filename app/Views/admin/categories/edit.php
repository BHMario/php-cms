<?php require __DIR__ . '/../../layout/admin_header.php'; ?>

<div class="container page-top">
    <h1>Editar Categoría</h1>
    <form action="/admin/categories/<?php echo $category['id']; ?>/update" method="post">
        <div class="form-group">
            <label>Nombre</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($category['name']); ?>" required />
        </div>
        <button class="btn" type="submit">Actualizar</button>
        <a href="/admin/categories" class="btn btn-back">Volver</a>
    </form>
</div>

<?php require __DIR__ . '/../../layout/admin_footer.php'; ?>

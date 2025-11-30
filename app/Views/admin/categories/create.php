<?php require __DIR__ . '/../../layout/admin_header.php'; ?>

<div class="container page-top">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h1 style="margin:0;">Crear Categoría</h1>
        <a href="/admin/categories" class="btn" onclick="history.back(); return false;">&larr; Volver</a>
    </div>
    <form action="/admin/categories/store" method="post">
        <div class="form-group">
            <label>Nombre</label>
            <input type="text" name="name" required />
        </div>
        <button class="btn" type="submit">Guardar</button>
    </form>
</div>

<?php require __DIR__ . '/../../layout/admin_footer.php'; ?>

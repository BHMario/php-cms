<?php require __DIR__ . '/../../layout/admin_header.php'; ?>

<div class="container page-top">
    <h1>Crear Categoría</h1>
    <form action="/admin/categories/store" method="post">
        <div class="form-group">
            <label>Nombre</label>
            <input type="text" name="name" required />
        </div>
        <button class="btn" type="submit">Guardar</button>
        <a href="/admin/categories" class="btn btn-back">Volver</a>
    </form>
</div>

<?php require __DIR__ . '/../../layout/admin_footer.php'; ?>

<?php require __DIR__ . '/../../layout/admin_header.php'; ?>

<div class="container page-top">
    <h1>Crear Usuario</h1>
    <form action="/admin/users/store" method="post">
        <div class="form-group">
            <label>Usuario</label>
            <input type="text" name="username" required />
        </div>
        <div class="form-group">
            <label>Contraseña</label>
            <input type="password" name="password" required />
        </div>
        <div class="form-group">
            <label>Rol</label>
            <select name="role">
                <option value="user">Usuario</option>
                <option value="admin">Administrador</option>
            </select>
        </div>
        <button class="btn" type="submit">Crear</button>
        <a href="/admin/users" class="btn btn-back">Volver</a>
    </form>
</div>

<?php require __DIR__ . '/../../layout/admin_footer.php'; ?>

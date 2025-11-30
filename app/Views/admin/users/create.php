<?php require __DIR__ . '/../../layout/admin_header.php'; ?>

<div class="container page-top">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h1 style="margin:0;">Crear Usuario</h1>
        <a href="/admin/users" class="btn" onclick="history.back(); return false;">&larr; Volver</a>
    </div>
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
    </form>
</div>

<?php require __DIR__ . '/../../layout/admin_footer.php'; ?>

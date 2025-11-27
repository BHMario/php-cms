<?php require __DIR__ . '/../../layout/admin_header.php'; ?>

<div class="container page-top">
    <h1>Editar Usuario</h1>
    <form action="/admin/users/<?php echo $u['id']; ?>/update" method="post">
        <div class="form-group">
            <label>Usuario</label>
            <input type="text" value="<?php echo htmlspecialchars($u['username']); ?>" disabled />
        </div>
        <div class="form-group">
            <label>Nueva contraseña (dejar en blanco para no cambiar)</label>
            <input type="password" name="password" />
        </div>
        <div class="form-group">
            <label>Rol</label>
            <select name="role">
                <option value="user" <?php echo ($u['role'] === 'user') ? 'selected' : ''; ?>>Usuario</option>
                <option value="admin" <?php echo ($u['role'] === 'admin') ? 'selected' : ''; ?>>Administrador</option>
            </select>
        </div>
        <button class="btn" type="submit">Actualizar</button>
        <a href="/admin/users" class="btn btn-back">Volver</a>
    </form>
</div>

<?php require __DIR__ . '/../../layout/admin_footer.php'; ?>

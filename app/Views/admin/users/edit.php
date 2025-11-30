<?php require __DIR__ . '/../../layout/admin_header.php'; ?>

<div class="container page-top">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h1 style="margin:0;">Editar Usuario</h1>
        <a href="/admin/users" class="btn" onclick="history.back(); return false;">&larr; Volver</a>
    </div>
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
            <div class="role-buttons">
                <input type="hidden" name="role" id="role_id" value="<?php echo htmlspecialchars($u['role']); ?>">
                <button type="button" class="role-btn <?= ($u['role'] ?? '') === 'user' ? 'active' : '' ?>" data-role="user" onclick="selectRole(event, 'user')">Usuario</button>
                <button type="button" class="role-btn <?= ($u['role'] ?? '') === 'admin' ? 'active' : '' ?>" data-role="admin" onclick="selectRole(event, 'admin')">Administrador</button>
            </div>
        </div>
        <script>
        function selectRole(event, role) {
            event.preventDefault();
            document.querySelectorAll('.role-btn').forEach(btn => btn.classList.remove('active'));
            document.getElementById('role_id').value = role;
            event.target.classList.add('active');
        }
        </script>
        <button class="btn" type="submit">Actualizar</button>
    </form>
</div>

<?php require __DIR__ . '/../../layout/admin_footer.php'; ?>

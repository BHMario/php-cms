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
            <div class="role-buttons">
                <input type="hidden" name="role" id="role_id" value="user">
                <button type="button" class="role-btn active" data-role="user" onclick="selectRole(event, 'user')">Usuario</button>
                <button type="button" class="role-btn" data-role="admin" onclick="selectRole(event, 'admin')">Administrador</button>
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
        <button class="btn" type="submit">Crear</button>
    </form>
</div>

<?php require __DIR__ . '/../../layout/admin_footer.php'; ?>

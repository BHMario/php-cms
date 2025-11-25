<?php require __DIR__ . '/../layout/header.php'; ?>

<div class="container auth-container">
    <div class="card">
        <h1 class="text-center shiny">Perfil de Usuario</h1>

        <?php if (!empty($success)): ?>
            <p class="text-center success"><?= htmlspecialchars($success) ?></p>
        <?php endif; ?>
        <?php if (!empty($error)): ?>
            <p class="text-center error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <?php if ($user): ?>
            <div style="display:flex; gap:1rem; align-items:center;">
                <div>
                    <?php if (!empty($user['profile_image'])): ?>
                        <img src="/<?= htmlspecialchars($user['profile_image']) ?>" alt="Foto de perfil" style="width:96px; height:96px; object-fit:cover; border-radius:8px;">
                    <?php else: ?>
                        <img src="/assets/images/default-avatar.svg" alt="Perfil" style="width:96px; height:96px;">
                    <?php endif; ?>
                </div>
                <div>
                    <p><strong>Usuario:</strong> <?= htmlspecialchars($user['username']) ?></p>
                </div>
            </div>

            <hr>

            <h3>Actualizar Perfil</h3>
            <form action="/profile" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="profile_image">Foto de perfil (opcional)</label>
                    <input type="file" name="profile_image" id="profile_image" accept="image/*">
                </div>

                <div class="form-group">
                    <label for="bio">Biografía</label>
                    <textarea name="bio" id="bio" rows="4"><?= htmlspecialchars($user['bio'] ?? '') ?></textarea>
                </div>

                <div class="form-group">
                    <label for="new_password">Nueva Contraseña (dejar vacío para mantener)</label>
                    <input type="password" name="new_password" id="new_password">
                </div>

                <div class="row-between">
                    <a href="/" class="muted-link">Cancelar</a>
                    <button type="submit" class="btn">Actualizar Perfil</button>
                </div>
            </form>

        <?php else: ?>
            <p>Usuario no encontrado.</p>
        <?php endif; ?>
    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
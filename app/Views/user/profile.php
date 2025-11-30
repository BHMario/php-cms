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

                <div class="row-between">
                    <a href="/" class="muted-link">Cancelar</a>
                    <button type="submit" class="btn">Actualizar Perfil</button>
                </div>
            </form>

            <hr>

            <h3 style="margin-bottom: 1rem;">Seguridad</h3>
            <button id="change-password-btn" class="btn btn-secondary" style="width: 100%;">Cambiar Contraseña</button>

            <!-- Modal de cambio de contraseña -->
            <div id="password-modal" class="modal" style="display:none;">
                <div class="modal-content">
                    <h2>Cambiar Contraseña</h2>
                    <form action="/change-password" method="post" id="password-form">
                        <div class="form-group">
                            <label for="current_password">Contraseña Actual</label>
                            <input type="password" name="current_password" id="current_password" required>
                            <small id="current-pwd-error" class="error" style="display:none;"></small>
                        </div>

                        <div class="form-group">
                            <label for="new_password">Nueva Contraseña</label>
                            <input type="password" name="new_password" id="new_password" required>
                            <small id="new-pwd-error" class="error" style="display:none;"></small>
                        </div>

                        <div class="form-group">
                            <label for="confirm_password">Repetir Contraseña</label>
                            <input type="password" name="confirm_password" id="confirm_password" required>
                            <small id="confirm-pwd-error" class="error" style="display:none;"></small>
                        </div>

                        <div id="password-success" class="success" style="display:none; margin-bottom: 1rem;"></div>

                        <div class="modal-buttons">
                            <button type="button" id="password-cancel" class="btn btn-secondary">Cancelar</button>
                            <button type="submit" class="btn">Cambiar</button>
                        </div>
                    </form>
                </div>
            </div>

        <?php else: ?>
            <p>Usuario no encontrado.</p>
        <?php endif; ?>
    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
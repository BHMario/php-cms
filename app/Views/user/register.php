<?php require __DIR__ . '/../layout/header.php'; ?>

<div class="container auth-container">
    <div class="card">
        <h1 class="text-center shiny">Registrar Usuario</h1>

        <?php if (!empty($error)): ?>
            <p class="error text-center"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form action="/register/process" method="post">
            <div class="form-group">
                <label for="username">Usuario:</label>
                <input type="text" name="username" id="username" required>
            </div>

            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" name="password" id="password" required>
            </div>

            <button type="submit" class="btn btn-block">Registrar</button>
        </form>

        <p class="text-center mt-4">¿Ya tienes cuenta? <a href="/login" class="card-link">Inicia sesión aquí</a></p>
    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
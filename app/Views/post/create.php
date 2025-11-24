<?php require __DIR__ . '/../layout/header.php'; ?>

<div class="container form-container">
    <div class="card">
        <h1 class="text-center">Crear Nuevo Post</h1>

        <form action="/posts/store" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">Título:</label>
                <input type="text" name="title" id="title" required>
            </div>

            <div class="form-group">
                <label for="content">Contenido:</label>
                <textarea name="content" id="content" rows="10" required></textarea>
            </div>

            <div class="form-group">
                <label for="image">Imagen destacada (opcional)</label>
                <input type="file" name="image" id="image" accept="image/*">
            </div>

            <div class="form-group">
                <label for="category">Categoría (opcional)</label>
                <select name="category_id" id="category">
                    <option value="">-- Sin categoría --</option>
                    <?php require_once __DIR__ . '/../../Models/Category.php'; $cats = (new Category())->getAll(); foreach ($cats as $c): ?>
                        <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="tags">Etiquetas (separadas por coma)</label>
                <input type="text" name="tags" id="tags" placeholder="ej: php, tutorial, frontend">
            </div>

            <div class="row-between">
                <a href="/posts" class="muted-link">Cancelar</a>
                <button type="submit" class="btn">Publicar Post</button>
            </div>
        </form>
    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
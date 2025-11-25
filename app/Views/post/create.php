<?php require __DIR__ . '/../layout/header.php'; ?>

<div class="container form-container">
    <div class="card">
        <h1 class="text-center shiny">Crear Nuevo Post</h1>

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
                <label>Categoría (opcional)</label>
                <div class="category-buttons">
                    <input type="hidden" name="category_id" id="category_id" value="">
                    <?php require_once __DIR__ . '/../../Models/Category.php'; $cats = (new Category())->getAll(); foreach ($cats as $c): ?>
                        <button type="button" class="category-btn" data-id="<?= $c['id'] ?>" onclick="selectCategory(event, <?= $c['id'] ?>)"><?= htmlspecialchars($c['name']) ?></button>
                    <?php endforeach; ?>
                </div>
            </div>
            <script>
            function selectCategory(event, id) {
                event.preventDefault();
                document.querySelectorAll('.category-btn').forEach(btn => btn.classList.remove('active'));
                if (document.getElementById('category_id').value === id.toString()) {
                    document.getElementById('category_id').value = '';
                } else {
                    document.getElementById('category_id').value = id;
                    event.target.classList.add('active');
                }
            }
            </script>

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
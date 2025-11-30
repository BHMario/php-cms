<?php require __DIR__ . '/../layout/header.php'; ?>

<div class="container form-container">
    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h1 class="text-center shiny" style="margin:0;">Editar Post</h1>
            <a href="/posts" class="btn" onclick="history.back(); return false;">&larr; Volver</a>
        </div>

        <?php if ($post): ?>
            <form action="/posts/<?= $post['id'] ?>/update" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="title">Título:</label>
                    <input type="text" name="title" id="title" value="<?= htmlspecialchars($post['title']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="content">Contenido:</label>
                    <textarea name="content" id="content" rows="10" required><?= htmlspecialchars($post['content']) ?></textarea>
                </div>

                <div class="form-group">
                    <label for="image">Cambiar imagen destacada (opcional)</label>
                    <input type="file" name="image" id="image" accept="image/*">
                </div>

            <div class="form-group">
                <label>Categoría (opcional)</label>
                <div class="category-buttons">
                    <input type="hidden" name="category_id" id="category_id" value="<?= $post['category_id'] ?? '' ?>">
                    <?php require_once __DIR__ . '/../../Models/Category.php'; $cats = (new Category())->getAll(); foreach ($cats as $c): ?>
                        <button type="button" class="category-btn <?= ($post['category_id'] ?? '') == $c['id'] ? 'active' : '' ?>" data-id="<?= $c['id'] ?>" onclick="selectCategory(event, <?= $c['id'] ?>)"><?= htmlspecialchars($c['name']) ?></button>
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
            </script>                <div class="form-group">
                    <label for="tags">Etiquetas (separadas por coma)</label>
                    <?php $existingTags = isset($post['id']) ? (new Post())->getTags($post['id']) : []; $tagLine = implode(', ', array_map(function($t){return $t['name'];}, $existingTags)); ?>
                    <input type="text" name="tags" id="tags" value="<?= htmlspecialchars($tagLine) ?>">
                </div>

                <div class="row-between">
                    <button type="submit" class="btn">Actualizar Post</button>
                </div>
            </form>
        <?php else: ?>
            <p>Post no encontrado.</p>
            <a href="/posts" class="btn" onclick="history.back(); return false;">&larr; Volver a Posts</a>
        <?php endif; ?>
    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
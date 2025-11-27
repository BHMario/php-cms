<?php require __DIR__ . '/../../layout/admin_header.php'; ?>

<div class="container form-container">
    <div class="card">
        <h1 class="text-center shiny">Editar Post</h1>

        <?php if ($post): ?>
            <form action="/admin/posts/<?= $post['id'] ?>/update" method="post" enctype="multipart/form-data">
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

                <?php if (!empty($post['image'])): ?>
                    <div class="form-group">
                        <p class="muted">Imagen actual:</p>
                        <img src="/<?= htmlspecialchars($post['image']) ?>" alt="Imagen actual" class="post-image">
                    </div>
                <?php endif; ?>

                <div class="form-group">
                    <label>Categoría (opcional)</label>
                    <div class="category-buttons">
                        <input type="hidden" name="category_id" id="category_id" value="<?= $post['category_id'] ?? '' ?>">
                        <?php foreach ($categories as $c): ?>
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
                </script>
                <div class="form-group">
                    <label for="tags">Etiquetas (separadas por coma)</label>
                    <?php $existingTags = isset($post['id']) ? (new Post())->getTags($post['id']) : []; $tagLine = implode(', ', array_map(function($t){return $t['name'];}, $existingTags)); ?>
                    <input type="text" name="tags" id="tags" value="<?= htmlspecialchars($tagLine) ?>">
                </div>

                <div class="row-between">
                    <a href="/admin/posts" class="muted-link">Cancelar</a>
                    <button type="submit" class="btn">Actualizar Post</button>
                </div>
            </form>
        <?php else: ?>
            <p>Post no encontrado.</p>
            <a href="/admin/posts" class="btn">Volver a Posts</a>
        <?php endif; ?>
    </div>
</div>

<?php require __DIR__ . '/../../layout/admin_footer.php'; ?>

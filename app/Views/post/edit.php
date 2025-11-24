<?php require __DIR__ . '/../layout/header.php'; ?>

<div class="container form-container">
    <div class="card">
        <h1 class="text-center">Editar Post</h1>

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
                    <label for="category">Categoría (opcional)</label>
                    <select name="category_id" id="category">
                        <option value="">-- Sin categoría --</option>
                        <?php require_once __DIR__ . '/../../Models/Category.php'; $cats = (new Category())->getAll(); foreach ($cats as $c): ?>
                            <option value="<?= $c['id'] ?>" <?= ($post['category_id'] ?? '') == $c['id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="tags">Etiquetas (separadas por coma)</label>
                    <?php $existingTags = isset($post['id']) ? (new Post())->getTags($post['id']) : []; $tagLine = implode(', ', array_map(function($t){return $t['name'];}, $existingTags)); ?>
                    <input type="text" name="tags" id="tags" value="<?= htmlspecialchars($tagLine) ?>">
                </div>

                <div class="row-between">
                    <a href="/posts/<?= $post['id'] ?>" class="muted-link">Cancelar</a>
                    <button type="submit" class="btn">Actualizar Post</button>
                </div>
            </form>
        <?php else: ?>
            <p>Post no encontrado.</p>
            <a href="/posts" class="btn">Volver a Posts</a>
        <?php endif; ?>
    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
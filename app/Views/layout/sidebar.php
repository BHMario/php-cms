<?php
require_once __DIR__ . '/../../Models/Category.php';
require_once __DIR__ . '/../../Models/Tag.php';

$categories = (new Category())->getAll();
$selectedCategories = isset($_GET['categories']) ? (is_array($_GET['categories']) ? $_GET['categories'] : [$_GET['categories']]) : [];
$selectedTags = isset($_GET['tags']) ? (is_array($_GET['tags']) ? $_GET['tags'] : [$_GET['tags']]) : [];
$sort = $_GET['sort'] ?? 'recent';
$tagSearch = trim($_GET['tag_search'] ?? '');
?>

<div id="sidebar-content" class="sidebar-content">
    <h3>🔍 Filtros</h3>
    
    <form method="get" action="/" id="filter-form">
        <!-- Categorías -->
        <div class="sidebar-group">
            <h4>Categorías</h4>
            <?php if (!empty($categories)): ?>
                <?php foreach ($categories as $cat): ?>
                    <label>
                        <input type="checkbox" name="categories[]" value="<?= $cat['id'] ?>" <?= in_array($cat['id'], $selectedCategories) ? 'checked' : '' ?>>
                        <span><?= htmlspecialchars($cat['name']) ?></span>
                    </label>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="font-size: 0.85rem; color: var(--muted-color); margin: 0.5rem 0;">No hay categorías disponibles</p>
            <?php endif; ?>
        </div>

        <!-- Búsqueda de Etiquetas -->
        <div class="sidebar-group">
            <h4>Buscar Etiquetas</h4>
            <input type="text" name="tag_search" placeholder="Buscar etiquetas..." value="<?= htmlspecialchars($tagSearch) ?>" style="width: 100%; padding: 0.5rem; border: 1px solid var(--input-border); border-radius: 6px; font-size: 0.9rem;">
            <?php if ($tagSearch !== ''): ?>
                <p style="font-size: 0.8rem; color: var(--muted-color); margin: 0.5rem 0;">
                    Buscando: <strong><?= htmlspecialchars($tagSearch) ?></strong>
                </p>
            <?php endif; ?>
        </div>

        <!-- Ordenamiento -->
        <div class="sidebar-group">
            <h4>Ordenar por</h4>
            <label>
                <input type="radio" name="sort" value="recent" <?= $sort === 'recent' ? 'checked' : '' ?> onchange="document.getElementById('filter-form').submit();">
                <span>Más recientes</span>
            </label>
            <label>
                <input type="radio" name="sort" value="likes" <?= $sort === 'likes' ? 'checked' : '' ?> onchange="document.getElementById('filter-form').submit();">
                <span>Más likes</span>
            </label>
        </div>

        <!-- Botón de aplicar (solo para filtros y etiquetas) -->
        <button type="submit" class="btn" style="width: 100%; justify-content: center; margin-top: 1rem;">Aplicar Filtros</button>
        
        <!-- Botón limpiar si hay filtros activos -->
        <?php if (!empty($selectedCategories) || $tagSearch !== '' || $sort !== 'recent'): ?>
            <a href="/" class="btn" style="width: 100%; justify-content: center; margin-top: 0.5rem; text-align: center; text-decoration: none; background-color: var(--muted-color);">Limpiar</a>
        <?php endif; ?>
    </form>
</div>

    <script>
        const sidebarToggle = document.querySelector('.sidebar-menu-toggle');
        const sidebarContent = document.getElementById('sidebar-content');
    
        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', function() {
                sidebarContent.classList.toggle('open');
                document.body.classList.toggle('sidebar-open');
            });
        }
    
        // Cerrar sidebar al hacer clic fuera de él
        document.addEventListener('click', function(event) {
            if (!sidebarContent.contains(event.target) && !sidebarToggle.contains(event.target)) {
                sidebarContent.classList.remove('open');
                document.body.classList.remove('sidebar-open');
            }
        });
    </script>

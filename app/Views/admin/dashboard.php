<?php require __DIR__ . '/../layout/admin_header.php'; ?>

<div class="container page-top">
    <h1 class="shiny">Panel de Administración</h1>

    <div class="admin-cards mt-4">
        <a class="admin-card" href="/admin/posts">
            <h3>Gestionar Posts</h3>
            <p>Crear, editar, publicar y borrar entradas del blog.</p>
        </a>

        <a class="admin-card" href="/admin/categories">
            <h3>Gestionar Categorías</h3>
            <p>Añadir, editar y eliminar categorías del sitio.</p>
        </a>

        <a class="admin-card" href="/admin/users">
            <h3>Gestionar Usuarios</h3>
            <p>Ver usuarios, cambiar roles y eliminar cuentas.</p>
        </a>
    </div>
</div>

<?php require __DIR__ . '/../layout/admin_footer.php'; ?>

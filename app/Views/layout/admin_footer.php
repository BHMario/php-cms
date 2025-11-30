    </main>

<!-- Lightbox imagen -->
<div id="image-lightbox" class="lightbox">
    <span id="lightbox-close" style="position:absolute;top:30px;right:40px;font-size:40px;color:#fff;cursor:pointer;font-weight:bold;">×</span>
    <img id="lightbox-image" src="" alt="Imagen ampliada" style="max-width:90vw;max-height:90vh;box-shadow:0 0 40px #000;border-radius:8px;">
</div>

<!-- Modal logout -->
<div id="logout-modal" class="modal">
    <div class="modal-content">
        <h2>Cerrar Sesión</h2>
        <p>¿Estás seguro de que deseas cerrar sesión?</p>
        <div class="modal-buttons">
            <button id="logout-cancel" class="btn btn-secondary" type="button">Cancelar</button>
            <a href="/logout" class="btn btn-danger">Salir</a>
        </div>
    </div>
</div>

    <footer class="admin-footer">
        <div class="container">&copy; <?= date('Y') ?> Mi Blog — Admin</div>
    </footer>
    <script src="/assets/js/scripts.js"></script>
</body>
</html>

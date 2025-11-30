</main>
<footer>
    <div class="container" style="display:flex; flex-wrap:wrap; justify-content:space-between; gap:1rem; align-items:center; padding:2rem 0;">
        <div>
            <h4 style="margin:0 0 0.5rem 0;">Mi Blog Personal</h4>
            <p style="margin:0;">Pasión por compartir conocimiento sobre desarrollo web.</p>
        </div>
        <div>
            <strong>Enlaces</strong>
            <div style="display:flex; gap:0.75rem; margin-top:0.5rem;">
                <a href="/" class="muted-link">Inicio</a>
                <a href="/posts" class="muted-link">Posts</a>
                <a href="/login" class="muted-link">Login</a>
            </div>
<!-- Lightbox para ver imagen completa -->
<div id="image-lightbox" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(0,0,0,0.9); z-index:3000; align-items:center; justify-content:center;">
    <span id="lightbox-close" style="position:absolute;top:30px;right:40px;font-size:40px;color:#fff;cursor:pointer;font-weight:bold;">×</span>
    <img id="lightbox-image" src="" alt="Imagen ampliada" style="max-width:90vw;max-height:90vh;box-shadow:0 0 40px #000;border-radius:8px;">
</div>
        </div>
        <div>
            <strong>Contacto</strong>
            <p style="margin:0.25rem 0 0 0;">mariosanrui1612@gmail.com</p>
            <div style="margin-top:0.5rem; display:flex; gap:0.5rem;">
                <a href="#" class="muted-link">Twitter</a>
                <a href="#" class="muted-link">GitHub</a>
            </div>
        </div>
    </div>
    <div style="text-align:center; padding:1rem 0 2rem 0;">&copy; <?= date('Y') ?> Mi Blog Personal. Todos los derechos reservados.</div>
</footer>

<!-- Modal de confirmación logout -->
<div id="logout-modal" class="modal" style="display:none;">
    <div class="modal-content">
        <h2>Cerrar Sesión</h2>
        <p>¿Estás seguro de que deseas cerrar sesión?</p>
        <div class="modal-buttons">
            <button id="logout-cancel" class="btn btn-secondary" type="button">Cancelar</button>
            <a href="/logout" class="btn btn-danger">Salir</a>
        </div>
    </div>
</div>

<script src="/assets/js/scripts.js"></script>
</body>

</html>
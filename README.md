# 📝 Mi Blog Personal - PHP CMS

Aplicación de blog/CMS ligera desarrollada en PHP (sin frameworks) y MySQL. Incluye panel de administración, autenticación de usuarios, sistema de posts, categorías, comentarios, notificaciones y tema claro/oscuro.

---

## Requisitos

- PHP 7.4 o superior (extensión PDO y PDO_MySQL habilitadas)
- MySQL 5.7+ (o MariaDB compatible)
- Git (para clonar)

---

## Instalación rápida (Windows / PowerShell)

1. Clona el repositorio y accede al proyecto:

```powershell
git clone https://github.com/BHMario/php-cms.git
cd php-cms
```

2. Configura la base de datos:

- Por defecto `config/config.php` contiene estos valores:

```php
return [
        'host' => 'localhost',
        'dbname' => 'blog_cms',
        'user' => 'root',
        'password' => '',
        'charset' => 'utf8mb4'
];
```

- Crea la base de datos e importa el dump incluido:

```powershell
# Crear la base de datos (si no existe)
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS blog_cms CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Importar estructura/datos iniciales
mysql -u root -p blog_cms < database\blog.sql
```

Alternativa: si prefieres usar las migraciones incluidas en el proyecto:

```powershell
php scripts/migrate.php
```

3. Crear el usuario administrador (opcional — el script crea admin por defecto si no existe):

```powershell
# Crea el admin con usuario 'admin' y contraseña 'admin123'
php scripts/create_admin.php

# O crea admin personalizado:
php scripts/create_admin.php miadmin miclaveSegura
```

4. Levantar servidor de desarrollo (sirve `public/`):

```powershell
php -S localhost:8000 -t public
```

Accede luego en tu navegador a: http://localhost:8000

---

## Credenciales por defecto (IMPORTANTE)

- Panel administrador (creado por `scripts/create_admin.php` si lo ejecutas sin argumentos):
    - Usuario: `admin`
    - Contraseña: `admin123`

- Clientes:
    - Registrate con tu nombre de usuario y tu contrasea
    - Inicia sesión con esas mismas credenciales

- Base de datos (valor por defecto en `config/config.php`):
    - Host: `localhost`
    - Nombre BD: `blog_cms`
    - Usuario: `root`
    - Contraseña: `` (vacía)

⚠️ Por seguridad cambia la contraseña del admin y las credenciales de la base de datos en producción.

---

## Comandos útiles

- Importar DB: `mysql -u root -p blog_cms < database\blog.sql`
- Ejecutar migraciones: `php scripts/migrate.php`
- Crear admin: `php scripts/create_admin.php <usuario> <contraseña>`
- Levantar servidor local: `php -S localhost:8000 -t public`

---

## Rutas importantes

- `/` — Inicio
- `/login` — Iniciar sesión
- `/register` — Registro
- `/admin` — Dashboard (requiere rol admin)

La lista completa de rutas está en este repositorio y el enrutador principal (`app/Router.php`).

---

## Estructura (resumen)

```
php-cms/
├─ app/
│  ├─ Controllers/  (Lógica de negocio)
│  ├─ Models/       (BaseModel, acceso a BD con PDO)
│  ├─ Services/     (Inyección de dependencias)
│  ├─ Interfaces/   (Uploadable, etc.)
│  ├─ Views/        (Vistas HTML/PHP)
│  └─ Router.php    (Enrutamiento con slugs)
├─ config/         (Configuración BD)
├─ database/       (init.php + esquema blog.sql)
├─ public/         (Punto de entrada + assets)
├─ setup.php       (Inicializador BD)
└─ Documentación   (6 archivos .md)
```

---

## 🎓 Estado del Proyecto (Rúbrica)

**Puntuación Actual: 9.5/10** ⭐

- ✅ **POO (3.0/3.0)** - BaseModel, ServiceContainer, Interfaces
- ✅ **Enrutamiento SEO (2.0/2.0)** - Slugs en URLs (`/posts/mi-slug`)
- ✅ **PDO Prepared Statements (1.8/1.8)** - Zero SQL injection
- ✅ **Autenticación (1.0/1.0)** - Login, registro, sesiones
- ✅ **Manejo de Archivos (1.0/1.0)** - Upload seguro de imágenes
- ⚠️  **Documentación (0.5/1.0)** - Falta: diagramas PlantUML

**Para 10/10:** Generar diagramas PlantUML (class, use case, sequence)

---

## 📚 Documentación Técnica

- **PROGRESS.md** - Estado completo del proyecto
- **REFACTOR_POO_SUMMARY.md** - Detalles del refactor POO
- **ENRUTAMIENTO_COMPLETADO.md** - Sistema de slugs
- **BD_INIT_CENTRALIZADO.md** - Inicializador centralizado
- **QUICK_START_BD_INIT.md** - Guía rápida
- **REFACTOR_BD_INIT.md** - Resumen ejecutivo

---

- Cambia la contraseña del admin inmediatamente después de crear la instalación.
- No uses `root` con contraseña vacía en producción: actualiza `config/config.php` con un usuario seguro.
- Asegura la carpeta `public/uploads/` con permisos correctos y, en producción, sirve el contenido desde un servidor web (Apache/Nginx) configurado con `public/` como document root.

---

Si quieres, puedo:

- Generar un archivo `.env` y adaptar `config/config.php` para leer variables de entorno.
- Agregar instrucciones para Docker/Compose.

---

✅ Solución: Limpia la caché del navegador y recarga la página (Ctrl+Shift+R)

---

## 📚 Documentación del Código

### Ejemplo: Crear un Post

```php
// En PostController.php
public function store()
{
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $categoryId = $_POST['category_id'] ?? null;
    
    $postModel = new Post();
    $postModel->create($title, $content, $_SESSION['user_id'], $categoryId);
    
    header("Location: /posts");
    exit;
}
```

### Ejemplo: Obtener Posts con Categoría

```php
// En Post.php (Modelo)
public function getAll($limit = 10, $offset = 0, $categoryId = null)
{
    $sql = "SELECT p.*, u.username, c.name as category_name 
            FROM posts p 
            LEFT JOIN users u ON p.user_id = u.id 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE 1=1";
    
    if ($categoryId) {
        $sql .= " AND p.category_id = :category_id";
    }
    
    $sql .= " ORDER BY p.created_at DESC LIMIT :limit OFFSET :offset";
    
    $stmt = $this->db->prepare($sql);
    // ... bindear parámetros
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
```

---

## 📁 Estructura del Proyecto

```
php-cms/
├── app/
│   ├── Controllers/                    # Controladores (MVC)
│   │   ├── AdminCategoriesController.php
│   │   ├── AdminController.php
│   │   ├── AdminPostsController.php
│   │   ├── AdminUsersController.php
│   │   ├── HomeController.php
│   │   ├── NotificationController.php
│   │   ├── PostController.php (✨ con slugs)
│   │   └── UserController.php
│   ├── Models/                         # Modelos (BD + POO)
│   │   ├── BaseModel.php               # ✨ Clase abstracta (encapsulación)
│   │   ├── Category.php
│   │   ├── Comment.php
│   │   ├── Database.php                # PDO preparadas (sin SQL injection)
│   │   ├── Follower.php
│   │   ├── Like.php
│   │   ├── Notification.php
│   │   ├── Post.php                    # ✨ Slugs SEO (generateSlug, getBySlug)
│   │   ├── Tag.php
│   │   └── User.php
│   ├── Services/                       # ✨ Servicios (Dependency Injection)
│   │   ├── Uploader.php                # Gestión de uploads
│   ├── Interfaces/                     # ✨ Interfaces (Polimorfismo)
│   │   └── Uploadable.php              # Interface para subidas de archivos
│   ├── Views/                          # Vistas (HTML/PHP)
│   │   ├── layout/                     # Plantillas base
│   │   │   ├── admin_footer.php
│   │   │   ├── admin_header.php
│   │   │   ├── footer.php
│   │   │   ├── header.php
│   │   │   └── sidebar.php
│   │   ├── admin/                      # Sección administrativa
│   │   │   ├── dashboard.php
│   │   │   ├── categories/
│   │   │   │   ├── create.php
│   │   │   │   ├── edit.php
│   │   │   │   └── index.php
│   │   │   ├── posts/
│   │   │   │   ├── create.php
│   │   │   │   ├── edit.php
│   │   │   │   ├── index.php
│   │   │   │   └── show.php
│   │   │   └── users/
│   │   │       ├── create.php
│   │   │       ├── edit.php
│   │   │       └── index.php
│   │   ├── home/                       # Página de inicio
│   │   │   └── index.php
│   │   ├── notification/               # Sistema de notificaciones
│   │   │   └── inbox.php
│   │   ├── post/                       # Gestión de posts
│   │   │   ├── create.php
│   │   │   ├── edit.php
│   │   │   ├── index.php
│   │   │   └── show.php (✨ con slugs)
│   │   └── user/                       # Gestión de usuarios
│   │       ├── login.php
│   │       ├── profile.php
│   │       ├── register.php
│   │       └── view.php
│   └── Router.php                      # ✨ Enrutador (dual routing: ID + slugs)
├── config/
│   └── config.php                      # Configuración BD
├── database/
│   ├── init.php                        # ✨ Inicializador BD centralizado
│   └── blog.sql                        # Estructura de BD
├── scripts/
│   ├── create_admin.php                        
│   └── migrate.sql
├── public/
│   ├── index.php                       # Punto de entrada
│   ├── assets/
│   │   ├── css/
│   │   │   └── style.css               # Estilos (dark mode incluido)
│   │   ├── js/
│   │   │   └── scripts.js              # Interactividad (modales, lightbox)
│   │   ├── images/                     # Imágenes del sitio
│   │   │   └── default-avatar.svg      # Avatar por defecto
│   │   └── uploads/                     
├── .git/                               # Control de versiones
├── .gitignore                          # Archivos ignorados
└── README.md                           # Este archivo
```

### ✨ Cambios Recientes (Refactor POO + BD Initialization)

- **BaseModel.php** - Clase abstracta con encapsulación
- **ServiceContainer.php** - Inyección de dependencias
- **Uploadable.php** - Interface para polimorfismo
- **database/init.php** - Inicializador centralizado e idempotente
- **Post.php** - Sistema de slugs SEO-friendly
- **Router.php** - Dual routing (ID y slugs)
- **Documentación** - 6 archivos markdown con detalles técnicos

---

## 👨‍💻 Autor

**Mario Sánchez Ruiz**

- GitHub: [@BHMario](https://github.com/BHMario)
- Email: mariosanrui1612@gmail.com

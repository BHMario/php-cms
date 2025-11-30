# 📝 Mi Blog Personal - PHP CMS

Una aplicación web moderna de blog personal construida con **PHP puro** y **MySQL**, con un sistema de administración completo, autenticación de usuarios, y características avanzadas de UX/UI.

---

## ✨ Características Principales

### 👥 Para Usuarios
- **Autenticación segura** - Registro, login y gestión de sesiones
- **Perfil de usuario** - Editar biografía, cambiar foto de perfil, cambiar contraseña
- **Sistema de posts** - Leer, buscar y filtrar posts por categorías
- **Interacción social** - Dar like a posts, dejar comentarios, seguir otros usuarios
- **Notificaciones** - Recibir notificaciones de seguidores, likes y comentarios
- **Búsqueda avanzada** - Buscar posts por texto, filtrar por categorías
- **Modo oscuro** - Tema personalizable que se guarda en el navegador
- **Lightbox** - Ver imágenes de posts ampliadas con modal

### 🔧 Para Administradores
- **Dashboard completo** - Panel de control para gestión total
- **Gestión de posts** - Crear, editar, eliminar y publicar posts
- **Gestión de categorías** - Crear y administrar categorías de posts
- **Gestión de usuarios** - Crear, editar, eliminar usuarios y asignar roles
- **Control de roles** - Sistema de permisos (Usuario/Administrador)
- **Interfaz admin separada** - Diseño dedicado para administradores

---

## 🛠️ Stack Tecnológico

| Tecnología | Versión | Propósito |
|-----------|---------|----------|
| **PHP** | 7.4+ | Backend |
| **MySQL** | 5.7+ | Base de datos |
| **HTML5** | - | Estructura |
| **CSS3** | - | Estilos y temas |
| **JavaScript (Vanilla)** | ES6+ | Interactividad |
| **PDO** | - | Acceso a base de datos |

---

## 📋 Requisitos Previos

- PHP 7.4 o superior
- MySQL 5.7 o superior
- Servidor web (Apache, Nginx)
- Composer (opcional, no se usa en este proyecto)

---

## ⚙️ Instalación

### 1. Clonar el repositorio

```bash
git clone https://github.com/BHMario/php-cms.git
cd php-cms
```

### 2. Crear la base de datos

```bash
# Importar el archivo SQL
mysql -u root -p < database/blog.sql
```

O si prefieres crear manualmente:

```sql
CREATE DATABASE blog_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE blog_db;

-- Tablas (consulta database/blog.sql para la estructura completa)
```

### 3. Configurar la conexión a la base de datos

Edita `config/config.php`:

```php
<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'tu_contraseña');
define('DB_NAME', 'blog_db');
?>
```

### 4. Crear usuario administrador

```bash
php scripts/create_admin.php
# O con usuario personalizado:
php scripts/create_admin.php username password
```

### 5. Iniciar el servidor

```bash
# Usando PHP built-in server
php -S localhost:8000 -t public/

# O configura tu servidor web para servir desde /public
```

Accede a: **http://localhost:8000**

---

## 📁 Estructura del Proyecto

```
php-cms/
├── app/
│   ├── Controllers/                    # Controladores
│   │   ├── AdminCategoriesController.php
│   │   ├── AdminController.php
│   │   ├── AdminPostsController.php
│   │   ├── AdminUsersController.php
│   │   ├── HomeController.php
│   │   ├── NotificationController.php
│   │   ├── PostController.php
│   │   └── UserController.php
│   ├── Models/                         # Modelos (BD)
│   │   ├── Category.php
│   │   ├── Comment.php
│   │   ├── Database.php
│   │   ├── Follower.php
│   │   ├── Like.php
│   │   ├── Notification.php
│   │   ├── Post.php
│   │   ├── Tag.php
│   │   └── User.php
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
│   │   │   └── show.php
│   │   └── user/                       # Gestión de usuarios
│   │       ├── login.php
│   │       ├── profile.php
│   │       ├── register.php
│   │       └── view.php
│   └── Router.php                      # Enrutador principal
├── config/
│   └── config.php                      # Configuración BD
├── database/
│   └── blog.sql                        # Estructura de BD
├── public/
│   ├── index.php                       # Punto de entrada
│   ├── assets/
│   │   ├── css/
│   │   │   └── style.css               # Estilos (dark mode incluido)
│   │   ├── js/
│   │   │   └── scripts.js              # Interactividad (modales, lightbox)
│   │   ├── images/                     # Imágenes del sitio
│   │   │   └── default-avatar.svg      # Avatar por defecto
│   │   └── uploads/                    # Uploads de usuarios
├── scripts/
│   ├── create_admin.php                # Crear usuario admin
│   └── migrate.php                     # Migración de tablas
├── .git/                               # Control de versiones
├── .gitignore                          # Archivos ignorados
└── README.md                           # Este archivo
```

---

## 🚀 Uso de la Aplicación

### 🔐 Para Usuarios

#### Registro
1. Haz clic en **"Registrar"** en la página de inicio
2. Crea tu usuario y contraseña
3. Serás redirigido a login

#### Iniciar Sesión
1. Ingresa tus credenciales en **Login**
2. Accede a tu perfil, notificaciones y posts

#### Perfil
- **Editar biografía**: Actualiza tu información personal
- **Cambiar foto**: Sube una foto de perfil
- **Cambiar contraseña**: Acceso seguro a cambio de contraseña

#### Posts
- **Crear**: Crea nuevo post con título, contenido, imagen y categoría
- **Editar**: Modifica tus posts publicados
- **Eliminar**: Elimina posts (con confirmación)
- **Ver ampliado**: Haz clic en la imagen para verla en tamaño completo

#### Social
- **Dar Like**: ❤️ Marca posts que te gusten
- **Comentar**: Deja comentarios en posts
- **Seguir**: Sigue a otros usuarios para ver sus posts

#### Notificaciones
- 📬 Recibe notificaciones de:
  - Nuevos seguidores
  - Posts de usuarios seguidos
  - Likes en tus posts
  - Comentarios en tus posts

### 👨‍💼 Para Administradores

#### Dashboard
- Vista general del sistema
- Acceso rápido a gestión de posts, categorías y usuarios

#### Gestión de Posts
- **Crear**: Nuevo post con todas las características
- **Editar**: Modificar posts existentes
- **Ver**: Previsualizar posts antes de publicar
- **Eliminar**: Remover posts con confirmación

#### Gestión de Categorías
- **Crear**: Nueva categoría para organizar posts
- **Editar**: Modificar nombre de categoría
- **Eliminar**: Remover categoría

#### Gestión de Usuarios
- **Crear**: Nuevo usuario con rol asignado
- **Editar**: Cambiar rol o contraseña de usuarios
- **Ver**: Información de usuario
- **Eliminar**: Remover usuario del sistema

#### Roles
- **Usuario**: Puede crear posts y comentar
- **Administrador**: Acceso completo al sistema

---

## 🎨 Características de Diseño

### Modo Oscuro / Claro
- Cambia el tema con el botón 🌙/☀️
- Tu preferencia se guarda automáticamente
- Transiciones suaves entre temas

### Interfaz Responsiva
- Diseño adaptable a todos los tamaños de pantalla
- Mobile-first approach
- Menú hamburguesa en dispositivos pequeños

### Modales y Confirmaciones
- Confirmación para logout
- Confirmación para eliminar recursos
- Modales para cambio de contraseña
- Lightbox para ver imágenes

### Mensajes y Notificaciones
- Notificaciones en tiempo real
- Mensajes de éxito/error
- Validaciones de formularios en tiempo real

---

## 🔒 Seguridad

### Protecciones Implementadas
- **Hashing de contraseñas**: Usa `password_hash()` y `password_verify()`
- **Prepared Statements**: PDO para prevenir inyecciones SQL
- **Sessions seguras**: Validación de sesiones en cada solicitud
- **Validación de entrada**: Sanitización de datos con `htmlspecialchars()`
- **Protección de admin**: Solo administradores acceden a `/admin`
- **CSRF implícito**: Mediante sesiones y estructura del formulario

---

## 📝 Rutas Principales

| Ruta | Método | Descripción |
|------|--------|-------------|
| `/` | GET | Página de inicio |
| `/posts` | GET | Listado de posts |
| `/posts/create` | GET/POST | Crear post |
| `/posts/:id` | GET | Ver post detallado |
| `/posts/:id/edit` | GET/POST | Editar post |
| `/posts/:id/delete` | GET | Eliminar post |
| `/posts/:id/like` | POST | Dar like |
| `/posts/:id/comment` | POST | Comentar |
| `/login` | GET/POST | Iniciar sesión |
| `/register` | GET/POST | Registro |
| `/logout` | GET | Cerrar sesión |
| `/profile` | GET/POST | Perfil de usuario |
| `/change-password` | POST | Cambiar contraseña |
| `/notifications` | GET | Ver notificaciones |
| `/admin` | GET | Dashboard admin |
| `/admin/posts` | GET | Gestión de posts |
| `/admin/categories` | GET | Gestión de categorías |
| `/admin/users` | GET | Gestión de usuarios |
| `/users/:id` | GET | Perfil público de usuario |
| `/users/:id/follow` | POST | Seguir usuario |
| `/users/:id/unfollow` | POST | Dejar de seguir |

---

## 💾 Base de Datos

### Tablas Principales

#### `users`
- `id`: ID único
- `username`: Nombre de usuario único
- `password`: Hash de contraseña
- `bio`: Biografía del usuario
- `profile_image`: Ruta de foto de perfil
- `role`: Rol (user/admin)
- `created_at`: Fecha de creación

#### `posts`
- `id`: ID único
- `user_id`: ID del autor
- `title`: Título del post
- `content`: Contenido del post
- `image`: Imagen destacada
- `category_id`: Categoría del post
- `created_at`: Fecha de creación
- `updated_at`: Fecha de actualización

#### `categories`
- `id`: ID único
- `name`: Nombre de categoría
- `created_at`: Fecha de creación

#### `comments`
- `id`: ID único
- `post_id`: ID del post
- `user_id`: ID del comentador
- `content`: Contenido del comentario
- `created_at`: Fecha de creación

#### `notifications`
- `id`: ID único
- `user_id`: ID del receptor
- `type`: Tipo de notificación
- `actor_id`: ID del usuario que causó la notificación
- `post_id`: ID del post (si aplica)
- `read`: Estado de lectura
- `created_at`: Fecha de creación

#### `followers`
- `id`: ID único
- `follower_id`: ID del seguidor
- `following_id`: ID del seguido
- `created_at`: Fecha de creación

---

## 🐛 Solución de Problemas

### Error de conexión a BD
```
Error: "Call to a member function query() on null"
```
✅ Solución: Verifica las credenciales en `config/config.php`

### La página de admin no se abre
```
Error: "Acceso denegado"
```
✅ Solución: Asegúrate de estar logueado como administrador

### Las imágenes no se cargan
```
Error: 404 en uploads
```
✅ Solución: Asegúrate de que la carpeta `public/uploads/` existe y tiene permisos de escritura

### Modal de logout no funciona
```
El modal aparece pero no cierra
```
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

## 🎯 Próximas Mejoras Sugeridas

- [ ] Sistema de tags adicionales
- [ ] Búsqueda fulltext en MySQL
- [ ] Exportar posts a PDF
- [ ] Integración con redes sociales
- [ ] API REST para acceso externo
- [ ] Sistema de caché
- [ ] Analytics de posts
- [ ] Emails automáticos
- [ ] Two-factor authentication
- [ ] Backup automático de BD

---

## 📄 Licencia

Este proyecto está licenciado bajo la MIT License - consulta el archivo LICENSE para más detalles.

---

## 👨‍💻 Autor

**Mario Sánchez Ruiz**

- GitHub: [@BHMario](https://github.com/BHMario)
- Email: mariosanrui1612@gmail.com

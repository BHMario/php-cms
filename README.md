# PHP CMS - Blog Personal

## Características Principales

- ✅ **Autenticación**: Login y registro de usuarios
- ✅ **Posts**: Crear, editar, eliminar posts con imágenes
- ✅ **Comentarios**: Agregar comentarios a los posts
- ✅ **Likes**: Sistema de "me gusta" en posts
- ✅ **Perfiles**: Foto de perfil, biografía, gestión de datos
- ✅ **Seguidores**: Seguir a otros usuarios y ver sus posts
- ✅ **📬 Notificaciones**: Sistema completo de notificaciones
- ✅ **Búsqueda**: Buscar posts por título o contenido
- ✅ **Categorías y Tags**: Organizar posts por categoría y etiquetas
- ✅ **Modo Oscuro/Claro**: Toggle de tema con persistencia
- ✅ **Responsive**: Diseño adaptable a móviles

## Requisitos

- PHP 7.4 o superior
- MySQL 5.7 o superior
- Servidor web (Apache, Nginx, etc.) o usar PHP built-in server

## Instalación Rápida

```bash
# 1. Clonar y entrar al directorio
git clone <url-del-repo>
cd php-cms

# 2. Crear base de datos
mysql -u root -p < database/blog.sql

# 3. Ejecutar migraciones
php scripts/migrate.php

# 4. Iniciar servidor
php -S localhost:8000 -t public
```

Acceder a: `http://localhost:8000`

## Sistema de Notificaciones (NUEVO) 📬

### Características
- Notificaciones automáticas cuando alguien te sigue
- Notificaciones automáticas cuando alguien a quien sigues publica
- Badge con contador de notificaciones sin leer en el header
- Página de inbox para ver todas las notificaciones
- Marcar como leído automáticamente

### Uso
1. Haz clic en el icono 📬 en la navegación superior
2. Verás todas tus notificaciones con avatares y fechas
3. El badge muestra cuántas sin leer tienes
4. Elimina notificaciones individuales con la ✕

## Estructura del Proyecto

```
php-cms/
├── app/
│   ├── Controllers/        # Lógica de negocio
│   ├── Models/            # Modelos de datos
│   ├── Views/             # Vistas (HTML/PHP)
│   └── Router.php         # Enrutador
├── config/config.php      # Configuración
├── database/blog.sql      # Schema SQL
├── public/                # Punto de entrada (index.php)
├── scripts/               # Scripts de utilidad
└── NOTIFICATIONS.md       # Documentación de notificaciones
```

## Endpoints Principales

### Notificaciones (NUEVO)
- `GET /notifications` - Ver todas las notificaciones
- `GET /notifications/{id}/delete` - Eliminar una notificación
- `GET /notifications/unread-count` - Contar sin leer (JSON)

### Posts
- `GET /` - Home con todos los posts
- `GET /posts` - Tus posts
- `POST /posts/store` - Crear post
- `GET /posts/{id}` - Ver post

### Usuarios
- `POST /login` - Login
- `POST /register` - Registro
- `GET /users/{id}` - Perfil de usuario
- `GET /users/{id}/follow` - Seguir usuario
- `GET /users/{id}/unfollow` - Dejar de seguir

## Configuración

Editar `config/config.php`:

```php
return [
    'host' => 'localhost',
    'dbname' => 'blog_cms',
    'user' => 'root',
    'password' => '',
    'charset' => 'utf8mb4'
];
```

## Documentación Completa

- Ver `NOTIFICATIONS.md` para documentación del sistema de notificaciones
- API de modelos, estructura de DB, ejemplos de uso
<?php

class Router
{
    public function route($uri)
    {
        // Limpiar la URL y separar segmentos
        $uri = trim($uri, '/');
        $segments = explode('/', $uri);

        // Ruta principal
        if ($uri === '') {
            require_once __DIR__ . '/Controllers/HomeController.php';
            $controller = new HomeController();
            $controller->index();
            return;
        }

        // Rutas de posts
        if ($segments[0] === 'posts') {
            require_once __DIR__ . '/Controllers/PostController.php';
            $controller = new PostController();

            if (!isset($segments[1])) {
                $controller->index();
                return;
            }

            if ($segments[1] === 'create') {
                $controller->create();
                return;
            }

            if ($segments[1] === 'store') {
                $controller->store();
                return;
            }

            if (is_numeric($segments[1])) {
                if (isset($segments[2]) && $segments[2] === 'edit') {
                    $controller->edit($segments[1]);
                    return;
                }
                if (isset($segments[2]) && $segments[2] === 'update') {
                    $controller->update($segments[1]);
                    return;
                }
                if (isset($segments[2]) && $segments[2] === 'delete') {
                    $controller->delete($segments[1]);
                    return;
                }
                if (isset($segments[2]) && $segments[2] === 'comment') {
                    $controller->comment($segments[1]);
                    return;
                }
                if (isset($segments[2]) && $segments[2] === 'like') {
                    $controller->like($segments[1]);
                    return;
                }
                $controller->show($segments[1]);
                return;
            }
        }

        // Rutas de usuario
        if ($segments[0] === 'login') {
            require_once __DIR__ . '/Controllers/UserController.php';
            $controller = new UserController();
            if (isset($segments[1]) && $segments[1] === 'process') {
                $controller->loginProcess();
            } else {
                $controller->login();
            }
            return;
        }

        if ($segments[0] === 'register') {
            require_once __DIR__ . '/Controllers/UserController.php';
            $controller = new UserController();
            if (isset($segments[1]) && $segments[1] === 'process') {
                $controller->registerProcess();
            } else {
                $controller->register();
            }
            return;
        }

        if ($segments[0] === 'profile') {
            require_once __DIR__ . '/Controllers/UserController.php';
            $controller = new UserController();
            $controller->profile();
            return;
        }

        if ($segments[0] === 'change-password') {
            require_once __DIR__ . '/Controllers/UserController.php';
            $controller = new UserController();
            $controller->changePassword();
            return;
        }

        // Public user profiles and follow endpoints
        if ($segments[0] === 'users') {
            require_once __DIR__ . '/Controllers/UserController.php';
            $controller = new UserController();
            if (isset($segments[1]) && is_numeric($segments[1])) {
                $id = $segments[1];
                if (isset($segments[2]) && $segments[2] === 'follow') {
                    $controller->follow($id);
                    return;
                }
                if (isset($segments[2]) && $segments[2] === 'unfollow') {
                    $controller->unfollow($id);
                    return;
                }
                $controller->view($id);
                return;
            }
        }

        if ($segments[0] === 'logout') {
            require_once __DIR__ . '/Controllers/UserController.php';
            $controller = new UserController();
            $controller->logout();
            return;
        }

        // Rutas de notificaciones
        if ($segments[0] === 'notifications') {
            require_once __DIR__ . '/Controllers/NotificationController.php';
            $controller = new NotificationController();
            
            if (!isset($segments[1])) {
                $controller->inbox();
                return;
            }

            if (is_numeric($segments[1]) && isset($segments[2]) && $segments[2] === 'delete') {
                $controller->delete($segments[1]);
                return;
            }

            if ($segments[1] === 'unread-count') {
                $controller->getUnreadCount();
                return;
            }
        }

        // Rutas de administrador
        if ($segments[0] === 'admin') {
            // /admin
            if (!isset($segments[1]) || $segments[1] === '') {
                require_once __DIR__ . '/Controllers/AdminController.php';
                $controller = new AdminController();
                $controller->index();
                return;
            }

            // Sub-secciones: posts, categories, users
            if ($segments[1] === 'posts') {
                require_once __DIR__ . '/Controllers/AdminPostsController.php';
                $controller = new AdminPostsController();

                if (!isset($segments[2])) { $controller->index(); return; }
                if ($segments[2] === 'create') { $controller->create(); return; }
                if ($segments[2] === 'store') { $controller->store(); return; }
                if (is_numeric($segments[2])) {
                    $id = $segments[2];
                    if (isset($segments[3]) && $segments[3] === 'edit') { $controller->edit($id); return; }
                    if (isset($segments[3]) && $segments[3] === 'update') { $controller->update($id); return; }
                    if (isset($segments[3]) && $segments[3] === 'delete') { $controller->delete($id); return; }
                    $controller->show($id);
                    return;
                }
            }

            if ($segments[1] === 'categories') {
                require_once __DIR__ . '/Controllers/AdminCategoriesController.php';
                $controller = new AdminCategoriesController();

                if (!isset($segments[2])) { $controller->index(); return; }
                if ($segments[2] === 'create') { $controller->create(); return; }
                if ($segments[2] === 'store') { $controller->store(); return; }
                if (is_numeric($segments[2])) {
                    $id = $segments[2];
                    if (isset($segments[3]) && $segments[3] === 'edit') { $controller->edit($id); return; }
                    if (isset($segments[3]) && $segments[3] === 'update') { $controller->update($id); return; }
                    if (isset($segments[3]) && $segments[3] === 'delete') { $controller->delete($id); return; }
                    $controller->show($id);
                    return;
                }
            }

            if ($segments[1] === 'users') {
                require_once __DIR__ . '/Controllers/AdminUsersController.php';
                $controller = new AdminUsersController();

                if (!isset($segments[2])) { $controller->index(); return; }
                if ($segments[2] === 'create') { $controller->create(); return; }
                if ($segments[2] === 'store') { $controller->store(); return; }
                if (is_numeric($segments[2])) {
                    $id = $segments[2];
                    if (isset($segments[3]) && $segments[3] === 'edit') { $controller->edit($id); return; }
                    if (isset($segments[3]) && $segments[3] === 'update') { $controller->update($id); return; }
                    if (isset($segments[3]) && $segments[3] === 'delete') { $controller->delete($id); return; }
                    $controller->show($id);
                    return;
                }
            }
        }

        // Ruta no encontrada
        http_response_code(404);
        echo "Página no encontrada";
    }
}

<?php

require_once __DIR__ . '/../Models/User.php';

class UserController
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function login()
    {
        require __DIR__ . '/../Views/user/login.php';
    }

    public function loginProcess()
    {
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);

        $user = $this->userModel->getByUsername($username);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            // Si es administrador, redirigir al dashboard
            if (isset($user['role']) && $user['role'] === 'admin') {
                header("Location: /admin");
            } else {
                header("Location: /");
            }
            exit;
        }

        $error = "Credenciales incorrectas";
        require __DIR__ . '/../Views/user/login.php';
    }

    public function register()
    {
        require __DIR__ . '/../Views/user/register.php';
    }

    public function registerProcess()
    {
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);

        if ($username && $password) {
            // User::create / register already hashes the password internally,
            // so pass the raw password here to avoid double-hashing.
            $this->userModel->create($username, $password);
        }

        header("Location: /login");
        exit;
    }

    public function logout()
    {
        session_destroy();
        header("Location: /");
        exit;
    }

    public function profile()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /login");
            exit;
        }

        $user = $this->userModel->getById($_SESSION['user_id']);

        $success = null;
        $error = null;

        // Procesar envíos desde el formulario unificado de perfil (foto, bio y contraseña)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Bio
            $bio = trim($_POST['bio'] ?? '');
            if ($bio !== '') {
                $this->userModel->updateBioById($_SESSION['user_id'], $bio);
                $success = ($success ? $success . ' ' : '') . 'Biografía actualizada.';
            }

            // Procesar imagen de perfil (opcional)
            if (!empty($_FILES['profile_image']) && $_FILES['profile_image']['error'] !== UPLOAD_ERR_NO_FILE) {
                $file = $_FILES['profile_image'];
                $allowed = ['image/jpeg','image/png','image/gif'];
                if ($file['error'] === UPLOAD_ERR_OK && in_array(mime_content_type($file['tmp_name']), $allowed)) {
                    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                    $newName = 'profile_' . $_SESSION['user_id'] . '_' . time() . '.' . $ext;
                    $targetDir = __DIR__ . '/../../public/uploads/profiles';
                    if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);
                    $target = $targetDir . '/' . $newName;
                    if (move_uploaded_file($file['tmp_name'], $target)) {
                        $this->userModel->updateProfileImage($_SESSION['user_id'], 'uploads/profiles/' . $newName);
                        $success = ($success ? $success . ' ' : '') . 'Foto de perfil actualizada.';
                        $user = $this->userModel->getById($_SESSION['user_id']);
                    } else {
                        $error = 'Error al subir la imagen.';
                    }
                } else {
                    $error = 'Formato de imagen no válido. Usa JPG, PNG o GIF.';
                }
            }

            // Cambiar contraseña (opcional)
            if (!empty($_POST['new_password'])) {
                $new = trim($_POST['new_password']);
                $confirm = trim($_POST['confirm_password'] ?? '');
                
                if (strlen($new) < 6) {
                    $error = 'La contraseña debe tener al menos 6 caracteres.';
                } elseif ($new !== $confirm) {
                    $error = 'Las contraseñas no coinciden.';
                } else {
                    $this->userModel->updatePasswordById($_SESSION['user_id'], $new);
                    $success = ($success ? $success . ' ' : '') . 'Contraseña actualizada.';
                }
            }
        }

        require __DIR__ . '/../Views/user/profile.php';
    }

    public function changePassword()
    {
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'No autenticado']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
            exit;
        }

        $currentPassword = trim($_POST['current_password'] ?? '');
        $newPassword = trim($_POST['new_password'] ?? '');
        $confirmPassword = trim($_POST['confirm_password'] ?? '');

        // Validaciones
        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            http_response_code(400);
            echo json_encode(['error' => 'Todos los campos son requeridos']);
            exit;
        }

        // Obtener usuario actual
        $user = $this->userModel->getById($_SESSION['user_id']);
        if (!$user) {
            http_response_code(404);
            echo json_encode(['error' => 'Usuario no encontrado']);
            exit;
        }

        // Verificar contraseña actual
        if (!password_verify($currentPassword, $user['password'])) {
            http_response_code(401);
            echo json_encode(['error' => 'La contraseña actual es incorrecta']);
            exit;
        }

        // Validar longitud
        if (strlen($newPassword) < 6) {
            http_response_code(400);
            echo json_encode(['error' => 'La contraseña debe tener al menos 6 caracteres']);
            exit;
        }

        // Validar coincidencia
        if ($newPassword !== $confirmPassword) {
            http_response_code(400);
            echo json_encode(['error' => 'Las contraseñas no coinciden']);
            exit;
        }

        // Actualizar contraseña
        $this->userModel->updatePasswordById($_SESSION['user_id'], $newPassword);

        http_response_code(200);
        echo json_encode(['success' => 'Contraseña actualizada correctamente']);
        exit;
    }

    // View another user's public profile by id
    public function view($id)
    {
        require_once __DIR__ . '/../Models/Follower.php';
        $other = $this->userModel->getById($id);
        if (!$other) {
            http_response_code(404);
            echo 'Usuario no encontrado';
            return;
        }
        $fModel = new Follower();
        $isFollowing = false;
        $followersCount = $fModel->countFollowers($id);
        $followingCount = $fModel->countFollowing($id);
        if (isset($_SESSION['user_id'])) {
            $isFollowing = $fModel->isFollowing($_SESSION['user_id'], $id);
        }
        require __DIR__ . '/../Views/user/view.php';
    }

    public function follow($id)
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login'); exit;
        }
        require_once __DIR__ . '/../Models/Follower.php';
        $f = new Follower();
        $f->follow($_SESSION['user_id'], $id);
        header("Location: /users/" . (int)$id); exit;
    }

    public function unfollow($id)
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login'); exit;
        }
        require_once __DIR__ . '/../Models/Follower.php';
        $f = new Follower();
        $f->unfollow($_SESSION['user_id'], $id);
        header("Location: /users/" . (int)$id); exit;
    }
}

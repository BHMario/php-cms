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
            header("Location: /");
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
                if (strlen($new) < 6) {
                    $error = 'La contraseña debe tener al menos 6 caracteres.';
                } else {
                    $this->userModel->updatePasswordById($_SESSION['user_id'], $new);
                    $success = ($success ? $success . ' ' : '') . 'Contraseña actualizada.';
                }
            }
        }

        require __DIR__ . '/../Views/user/profile.php';
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

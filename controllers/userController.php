<?php

final class userController
{
    public function defaultAction()
    {
        $model = new userModel();
        $status = method_exists($model, 'getDbStatus') ? $model->getDbStatus() : ['available' => true, 'message' => ''];
        view::show('user/login', ['db_status' => $status]);
    }

    public function login(): void
    {
        $model = new userModel();
        $status = method_exists($model, 'getDbStatus') ? $model->getDbStatus() : ['available' => true, 'message' => ''];

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            view::show('user/login', ['db_status' => $status]);
            return;
        }

        $login = $_POST['login'] ?? '';
        $password = $_POST['password'] ?? '';

        $result = method_exists($model, 'authenticate')
            ? $model->authenticate($login, $password)
            : ['success' => false, 'message' => 'Authentification indisponible.'];

        if (!empty($result['success'])) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['user'] = $result['user'] ?? [];

            echo "<script>
                alert('Connexion réussie');
                window.location.href = '/profilepageView.php';
              </script>";
            exit;
        }

        echo "<script>
            alert('Identifiants invalides');
            window.location.href = '?controller=user&action=login';
          </script>";
        exit;
    }


    public function register(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo 'Méthode non autorisée';
            return;
        }

        $username = isset($_POST['username']) ? (string)$_POST['username'] : '';
        $email = isset($_POST['email']) ? (string)$_POST['email'] : '';
        $password = isset($_POST['password']) ? (string)$_POST['password'] : '';
        $confirm = isset($_POST['confirm_password']) ? (string)$_POST['confirm_password'] : '';

        if ($password !== $confirm) {
            $message = 'Les mots de passe ne correspondent pas.';
            $this->renderResult(false, $message);
            return;
        }

        $userModel = new userModel();
        $result = $userModel->createUser($username, $email, $password);

        $this->renderResult($result['success'], $result['message']);
    }

    private function renderResult(bool $success, string $message): void
    {
        view::show('homepageView', ['flash' => ['success' => $success, 'message' => $message]]);
    }
}

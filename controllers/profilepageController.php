<?php

class profilepageController
{
    public function index()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (empty($_SESSION['user']) || !is_array($_SESSION['user'])) {
            header('Location: ?controller=homepage&action=login');
            exit;
        }
        $status = $this->getDbStatus();
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        View::show('profilepageView', [
            'user' => $_SESSION['user'],
            'db_status' => $status,
            'flash' => $flash
        ]);
    }

    public function updateUsername()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ?controller=homepage&action=index');
            exit;
        }
        $username = trim($_POST['username'] ?? '');
        if ($username === '') {
            $_SESSION['flash'] = ['success' => false, 'message' => 'Le nom d\'utilisateur ne peut pas être vide.'];
        } else {
            userModel::updateUsername($_SESSION['user_id'], $username);
            $_SESSION['user']['username'] = $username;
            $_SESSION['flash'] = ['success' => true, 'message' => 'Nom d\'utilisateur mis à jour.'];
        }
        header('Location: ?controller=profilepage&action=index');
        exit;
    }

    public function updateEmail()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ?controller=homepage&action=index');
            exit;
        }
        $email = trim($_POST['email'] ?? '');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['flash'] = ['success' => false, 'message' => 'Email invalide.'];
        } else {
            userModel::updateEmail($_SESSION['user_id'], $email);
            $_SESSION['user']['email'] = $email;
            $_SESSION['flash'] = ['success' => true, 'message' => 'Email mis à jour.'];
        }
        header('Location: ?controller=profilepage&action=index');
        exit;
    }

    public function updatePassword()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ?controller=homepage&action=index');
            exit;
        }
        $password = trim($_POST['password'] ?? '');
        if (strlen($password) < 6) {
            $_SESSION['flash'] = ['success' => false, 'message' => 'Le mot de passe doit contenir au moins 6 caractères.'];
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            userModel::updatePassword($_SESSION['user_id'], $hashedPassword);
            $_SESSION['flash'] = ['success' => true, 'message' => 'Mot de passe mis à jour.'];
        }
        header('Location: ?controller=profilepage&action=index');
        exit;
    }

    public function deleteAccount()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            header('Location: ?controller=homepage&action=index');
            exit;
        }

        $userId = $_SESSION['user_id'];

        // Supprimer le compte de la base de données
        require_once 'models/userModel.php';
        $result = userModel::deleteUser($userId);

        if ($result) {
            // Détruire la session
            session_destroy();

            // Rediriger vers la page d'accueil avec un message
            header('Location: ?controller=homepage&action=index&deleted=1');
            exit;
        } else {
            $_SESSION['flash'] = ['success' => false, 'message' => 'Erreur lors de la suppression du compte.'];
            header('Location: ?controller=profilepage&action=index');
            exit;
        }
    }

    //recpère le status de la BDD
    private function getDbStatus()
    {
        require_once 'models/userModel.php';
        $userModel = new userModel();
        return $userModel->getDbStatus();
    }
}
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
            $_SESSION['flash'] = ['success' => true, 'message' => 'Email mis à jour.'];
        }
        header('Location: ?controller=profilepage&action=index');
        exit;
    }

    public function updateBio()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ?controller=homepage&action=index');
            exit;
        }
        $bio = trim($_POST['bio'] ?? '');
        userModel::updateBio($_SESSION['user_id'], $bio);
        $_SESSION['flash'] = ['success' => true, 'message' => 'Bio mise à jour.'];
        header('Location: ?controller=profilepage&action=index');
        exit;
    }

    //recpère le status de la BDD
    private function getDbStatus()
    {
        require_once 'Models/userModel.php';
        $userModel = new userModel();
        return $userModel->getDbStatus();
    }
}

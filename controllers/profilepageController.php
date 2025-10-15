<?php

final class profilepageController
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new userModel();
    }

    public function index(): void
    {
        if (!isset($_SESSION['user']) || !isset($_SESSION['logged_in'])) {
            header('Location: ?controller=user&action=login');
            exit;
        }

        // DEBUG : Décommenter pour voir le contenu de la session
        // echo '<pre>'; var_dump($_SESSION['user']); echo '</pre>'; exit;

        $userData = $_SESSION['user'];
        view::show('profilepageView', [
            'username' => $userData['username'] ?? 'N/A',
            'email' => $userData['email'] ?? 'N/A'
        ]);
    }

    public function updateProfile(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo 'Méthode non autorisée';
            return;
        }

        if (!isset($_SESSION['user'])) {
            header('Location: ?controller=user&action=login');
            exit;
        }

        $currentUsername = $_SESSION['user']['username'];

        // Mise à jour du nom d'utilisateur
        if (isset($_POST['new_username']) && !empty(trim($_POST['new_username']))) {
            $newUsername = trim($_POST['new_username']);
            $result = $this->userModel->updateUsername($currentUsername, $newUsername);

            if ($result['success']) {
                $_SESSION['user']['username'] = $newUsername;
                $_SESSION['flash'] = ['success' => true, 'message' => $result['message']];
                $currentUsername = $newUsername;
            } else {
                $_SESSION['flash'] = ['success' => false, 'message' => $result['message']];
            }
        }

        // Mise à jour de l'email
        if (isset($_POST['new_email']) && !empty(trim($_POST['new_email']))) {
            $newEmail = trim($_POST['new_email']);
            $result = $this->userModel->updateEmail($currentUsername, $newEmail);

            if ($result['success']) {
                $_SESSION['user']['email'] = $newEmail;
                $_SESSION['flash'] = ['success' => true, 'message' => $result['message']];
            } else {
                $_SESSION['flash'] = ['success' => false, 'message' => $result['message']];
            }
        }

        // Mise à jour du mot de passe
        if (isset($_POST['new_password']) && !empty($_POST['new_password'])) {
            $hashedPassword = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
            $result = $this->userModel->updatePassword($_SESSION['user']['id'], $hashedPassword);

            if ($result) {
                $_SESSION['flash'] = ['success' => true, 'message' => 'Mot de passe mis à jour avec succès.'];
            } else {
                $_SESSION['flash'] = ['success' => false, 'message' => 'Erreur lors de la mise à jour du mot de passe.'];
            }
        }

        header('Location: ?controller=profilepage');
        exit;
    }

    public function deleteAccount(): void
    {
        if (!isset($_SESSION['user'])) {
            header('Location: ?controller=user&action=login');
            exit;
        }

        // Récupérer l'ID via le username
        $username = $_SESSION['user']['username'];

        try {
            // Option A : Créer une méthode dans userModel pour récupérer l'ID
            $userId = $this->userModel->getUserIdByUsername($username);

            if (!$userId) {
                throw new Exception('Utilisateur introuvable.');
            }

            $result = $this->userModel->deleteUser($userId);

            if ($result) {
                session_unset();
                session_destroy();
                header('Location: ?controller=user&action=login&deleted=1');
                exit;
            } else {
                throw new Exception('Erreur lors de la suppression du compte.');
            }

        } catch (Exception $e) {
            $_SESSION['flash'] = [
                'success' => false,
                'message' => $e->getMessage()
            ];
            header('Location: ?controller=profilepage');
            exit;
        }
    }
}

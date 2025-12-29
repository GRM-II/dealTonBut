<?php

final class profilepageController
{
    private userModel $userModel;

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

        // Vérifier que l'utilisateur existe toujours en BDD
        $username = $_SESSION['user']['username'];
        $userExists = $this->userModel->getUserByUsername($username);

        if (!$userExists) {
            // L'utilisateur n'existe plus
            session_unset();
            session_destroy();
            header('Location: ?controller=user&action=login&session_expired=1');
            exit;
        }

        // Utiliser les données fraîches de la BDD
        view::show('profilepageView', [
            'username' => $userExists['username'] ?? 'N/A',
            'email' => $userExists['email'] ?? 'N/A',
            'points_maths' => $userExists['points_maths'] ?? 0,
            'points_programmation' => $userExists['points_programmation'] ?? 0,
            'points_reseaux' => $userExists['points_reseaux'] ?? 0,
            'points_BD' => $userExists['points_BD'] ?? 0,
            'points_autre' => $userExists['points_autre'] ?? 0
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
        // Mise à jour des moyennes
        $gradesUpdated = false;
        $gradesData = [];

        if (isset($_POST['new_points_maths']) && $_POST['new_points_maths'] !== '') {
            $gradesData['points_maths'] = (float)$_POST['new_points_maths'];
            $gradesUpdated = true;
        }

        if (isset($_POST['new_points_programmation']) && $_POST['new_points_programmation'] !== '') {
            $gradesData['points_programmation'] = (float)$_POST['new_points_programmation'];
            $gradesUpdated = true;
        }

        if (isset($_POST['new_points_reseaux']) && $_POST['new_points_reseaux'] !== '') {
            $gradesData['points_reseaux'] = (float)$_POST['new_points_reseaux'];
            $gradesUpdated = true;
        }

        if (isset($_POST['new_points_BD']) && $_POST['new_points_BD'] !== '') {
            $gradesData['points_BD'] = (float)$_POST['new_points_BD'];
            $gradesUpdated = true;
        }

        if (isset($_POST['new_points_autre']) && $_POST['new_points_autre'] !== '') {
            $gradesData['points_autre'] = (float)$_POST['new_points_autre'];
            $gradesUpdated = true;
        }

        if ($gradesUpdated) {
            $result = $this->userModel->updateGrades($_SESSION['user']['id'], $gradesData);

            if ($result['success']) {
                $_SESSION['flash'] = ['success' => true, 'message' => $result['message']];
            } else {
                $_SESSION['flash'] = ['success' => false, 'message' => $result['message']];
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

        $username = $_SESSION['user']['username'];

        try {
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
            error_log("Erreur deleteAccount: " . $e->getMessage());
            $_SESSION['flash'] = [
                'success' => false,
                'message' => 'Une erreur est survenue lors de la suppression du compte.'
            ];
            header('Location: ?controller=profilepage');
            exit;
        }
    }
}
<?php

final class profilepageController
{
    public function index(): void
    {
        // DEBUG: Afficher le contenu complet de la session
        echo "<!-- DEBUG SESSION COMPLETE: " . htmlspecialchars(json_encode($_SESSION)) . " -->\n";

        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user']) || !isset($_SESSION['logged_in'])) {
            echo "<!-- DEBUG: User not logged in, redirecting... -->\n";
            header('Location: ?controller=user&action=login');
            exit;
        }

        // Récupérer les données utilisateur depuis la session
        $userData = $_SESSION['user'];

        echo "<!-- DEBUG USER DATA: " . htmlspecialchars(json_encode($userData)) . " -->\n";
        echo "<!-- DEBUG Username: " . htmlspecialchars($userData['username'] ?? 'MISSING') . " -->\n";
        echo "<!-- DEBUG Email: " . htmlspecialchars($userData['email'] ?? 'MISSING') . " -->\n";

        // Passer les données à la vue
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

        $userModel = new userModel();
        $currentUsername = $_SESSION['user']['username'];

        // Mise à jour du nom d'utilisateur
        if (isset($_POST['new_username']) && !empty(trim($_POST['new_username']))) {
            $newUsername = trim($_POST['new_username']);
            $result = $userModel->updateUsername($currentUsername, $newUsername);

            if ($result['success']) {
                $_SESSION['user']['username'] = $newUsername;
                $currentUsername = $newUsername;
            }
        }

        // Mise à jour de l'email
        if (isset($_POST['new_email']) && !empty(trim($_POST['new_email']))) {
            $newEmail = trim($_POST['new_email']);
            $result = $userModel->updateEmail($currentUsername, $newEmail);

            if ($result['success']) {
                $_SESSION['user']['email'] = $newEmail;
            }
        }

        // Rediriger vers le profil
        header('Location: ?controller=profilepage&action=index');
        exit;
    }
}
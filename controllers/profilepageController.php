<?php

/**
 * Profile Page Controller
 *
 * Handles user profile-related actions including viewing profile information,
 * updating profile details (username, email, password, grades), and account deletion.
 * Manages user authentication and session validation for profile operations.
 *
 */
final class profilepageController
{
    /**
     * User model instance
     *
     * @var userModel
     */
    private userModel $userModel;

    /**
     * Constructor
     *
     * Initializes the controller by creating a new userModel instance
     * for database operations related to user profiles.
     */
    public function __construct()
    {
        $this->userModel = new userModel();
    }

    /**
     * Displays the user profile page
     *
     * Validates user authentication and verifies that the user still exists
     * in the database. If the user doesn't exist, destroys the session and
     * redirects to login. Otherwise, displays the profile page with user
     * information and grade points for various subjects.
     *
     * @return void
     */
    public function index(): void
    {
        if (!isset($_SESSION['user']) || !isset($_SESSION['logged_in'])) {
            header('Location: ?controller=user&action=login');
            exit;
        }

        // Checks that the user still exists in the database
        $username = $_SESSION['user']['username'];
        $userExists = $this->userModel->getUserByUsername($username);

        if (!$userExists) {
            // The user doesnt exist anymore
            session_unset();
            session_destroy();
            header('Location: ?controller=user&action=login&session_expired=1');
            exit;
        }
        $userPoints = $this->userModel->getUserPoints($userExists['id']);

        // Uses the data from the database
        view::show('profilepageView', [
            'username' => $userExists['username'],
            'email' => $userExists['email'],
            'maths_points' => $userPoints['maths_points'] ?? 0,
            'programmation_points' => $userPoints['programmation_points'] ?? 0,
            'network_points' => $userPoints['network_points'] ?? 0,
            'DB_points' => $userPoints['DB_points'] ?? 0,
            'other_points' => $userPoints['other_points'] ?? 0
        ]);
    }

    /**
     * Updates user profile information
     *
     * Processes POST requests to update various profile fields including username,
     * email, password, and grade points for multiple subjects (maths, programming,
     * networks, databases, other). Validates user authentication and input data,
     * then updates the database accordingly. Sets flash messages for success or
     * failure of each operation and redirects to the profile page.
     *
     * @return void
     */
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
        $userId = $_SESSION['user']['id'];

        // Updates the username
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

        // Updates the email
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

        // Updates the password
        if (isset($_POST['new_password']) && !empty($_POST['new_password'])) {
            $hashedPassword = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
            $result = $this->userModel->updatePassword($_SESSION['user']['id'], $hashedPassword);

            if ($result) {
                $_SESSION['flash'] = ['success' => true, 'message' => 'Mot de passe mis à jour avec succès.'];
            } else {
                $_SESSION['flash'] = ['success' => false, 'message' => 'Erreur lors de la mise à jour du mot de passe.'];
            }
        }

        // Updates the averages
        $gradesUpdated = false;
        $gradesData = [];

        if (isset($_POST['new_maths_points']) && $_POST['new_maths_points'] !== '') {
            $gradesData['maths_points'] = (float)$_POST['new_maths_points'];
            $gradesUpdated = true;
        }

        if (isset($_POST['new_programmation_points']) && $_POST['new_programmation_points'] !== '') {
            $gradesData['programmation_points'] = (float)$_POST['new_programmation_points'];
            $gradesUpdated = true;
        }

        if (isset($_POST['new_network_points']) && $_POST['new_network_points'] !== '') {
            $gradesData['network_points'] = (float)$_POST['new_network_points'];
            $gradesUpdated = true;
        }

        if (isset($_POST['new_DB_points']) && $_POST['new_DB_points'] !== '') {
            $gradesData['DB_points'] = (float)$_POST['new_DB_points'];
            $gradesUpdated = true;
        }

        if (isset($_POST['new_other_points']) && $_POST['new_other_points'] !== '') {
            $gradesData['other_points'] = (float)$_POST['new_other_points'];
            $gradesUpdated = true;
        }

        if ($gradesUpdated) {
            $result = $this->userModel->updateGrades($userId, $gradesData);

            if ($result['success']) {
                $_SESSION['flash'] = ['success' => true, 'message' => $result['message']];
            } else {
                $_SESSION['flash'] = ['success' => false, 'message' => $result['message']];
            }
        }

        header('Location: ?controller=profilepage&action=index');
        exit;
    }

    /**
     * Deletes the user account
     *
     * Permanently deletes the authenticated user's account from the database.
     * Validates user authentication, retrieves the user ID, and performs the deletion.
     * On success, destroys the session and redirects to login with a deletion confirmation.
     * On failure, logs the error and sets a flash message before redirecting to the profile page.
     *
     * @return void
     * @throws Exception If user is not found or deletion fails
     */
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
            header('Location: ?controller=profilepage&action=index');
            exit;
        }
    }
}
<?php

final class userController
{
    public function defaultAction()
    {
        $model = new userModel();
        $status = method_exists($model, 'getDbStatus') ? $model->getDbStatus() : ['available' => true, 'message' => ''];
        view::show('homepage', ['db_status' => $status]);
    }
    public function login(): void
    {
        // ACTIVATION AFFICHAGE ERREURS
        ini_set('display_errors', 1);
        error_reporting(E_ALL);

        // Debug visible à l'écran
        echo "<!-- DEBUG: Login method called -->\n";
        echo "<!-- REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD'] . " -->\n";

        // Si l'utilisateur est déjà connecté, le rediriger vers le profil
        if (isset($_SESSION['user'])) {
            echo "<!-- DEBUG: User already logged in -->\n";
            header('Location: ?controller=profilepage&action=index');
            exit;
        }

        // Si le formulaire est soumis
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            echo "<!-- DEBUG: POST request detected -->\n";
            echo "<!-- POST data: " . htmlspecialchars(json_encode($_POST)) . " -->\n";

            $login = $_POST['login'] ?? '';
            $password = $_POST['password'] ?? '';

            echo "<!-- DEBUG: Login='$login', Password length=" . strlen($password) . " -->\n";

            if (empty($login) || empty($password)) {
                echo "<!-- DEBUG: Empty credentials -->\n";
                view::show('user/login', ['error' => 'Veuillez remplir tous les champs.']);
                return;
            }

            try {
                $userModel = new userModel();
                echo "<!-- DEBUG: UserModel created -->\n";

                $result = $userModel->authenticate($login, $password);
                echo "<!-- DEBUG: Auth result: " . htmlspecialchars(json_encode($result)) . " -->\n";

                if ($result['success']) {
                    echo "<!-- DEBUG: Authentication SUCCESS -->\n";

                    // Stocker les informations de l'utilisateur dans la session
                    $_SESSION['user'] = $result['user'];
                    $_SESSION['logged_in'] = true;

                    echo "<!-- DEBUG: Session set -->\n";
                    echo "<!-- DEBUG: About to redirect... -->\n";

                    // Vérifier si des headers ont déjà été envoyés
                    if (headers_sent($file, $line)) {
                        die("<!-- ERROR: Headers already sent in $file:$line -->");
                    }

                    // Redirection vers la page de profil
                    header('Location: ?controller=profilepage&action=index');
                    exit;
                }

                echo "<!-- DEBUG: Authentication FAILED -->\n";
                // Si échec : afficher la page de login avec le message d'erreur
                view::show('user/login', ['error' => $result['message']]);
                return;

            } catch (Exception $e) {
                echo "<!-- ERROR: Exception caught: " . htmlspecialchars($e->getMessage()) . " -->\n";
                echo "<!-- Stack trace: " . htmlspecialchars($e->getTraceAsString()) . " -->\n";
                view::show('user/login', ['error' => 'Une erreur est survenue: ' . $e->getMessage()]);
                return;
            }
        }

        echo "<!-- DEBUG: GET request - showing login form -->\n";
        // GET request : Afficher le formulaire de login
        view::show('user/login');
    }

    public function logout(): void
    {
        // Détruire toutes les données de session
        session_unset();
        session_destroy();

        // Supprimer le cookie de session
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }

        // Rediriger vers la page de login
        header('Location: ?controller=user&action=homepage');
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
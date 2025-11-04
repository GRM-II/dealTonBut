<?php

final class userController
{
    private userModel $userModel;

    // Constructeur pour s'assurer que la session est démarrée et la connexion DB établie
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Instancier le modèle utilisateur (gestion DB centralisée dans userModel)
        $this->userModel = new userModel();
    }

    private function getDbStatus(): array
    {
        // Délègue au userModel pour connaître l'état de la base
        return $this->userModel->getDbStatus();
    }

    public function homepage(): void
    {
        $status = $this->getDbStatus();
        view::show('homepageView', ['db_status' => $status]);
    }

    public function login(): void
    {
        // Si l'utilisateur est déjà connecté, le rediriger vers le profil
        if (isset($_SESSION['user'])) {
            header('Location: ?controller=profilepage&action=index');
            exit;
        }

        // Si le formulaire est soumis
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $login = $_POST['login'] ?? '';
            $password = $_POST['password'] ?? '';

            if (empty($login) || empty($password)) {
                view::show('user/login', ['error' => 'Veuillez remplir tous les champs.']);
                return;
            }

            try {
                $result = $this->authenticate($login, $password);

                if ($result['success']) {
                    // Stocker les informations de l'utilisateur dans la session
                    $_SESSION['user'] = $result['user'];
                    $_SESSION['logged_in'] = true;

                    // Redirection vers la page de profil
                    header('Location: ?controller=profilepage&action=index');
                    exit;
                }

                // Si échec : afficher la page de login avec le message d'erreur
                view::show('user/login', ['error' => $result['message']]);
                return;

            } catch (Exception $e) {
                view::show('user/login', ['error' => 'Une erreur est survenue: ' . $e->getMessage()]);
                return;
            }
        }

        // GET request : Afficher le formulaire de login
        // Vérifier s'il y a un message flash de succès d'inscription
        $flashMessage = null;
        if (isset($_SESSION['flash_message'])) {
            $flashMessage = $_SESSION['flash_message'];
            unset($_SESSION['flash_message']);
        }

        view::show('user/login', ['flash' => $flashMessage]);
    }

    private function authenticate(string $login, string $password): array
    {
        // Délègue l'authentification au modèle pour éviter la duplication
        try {
            $result = $this->userModel->authenticate($login, $password);
            if (!$result['success']) {
                // Normalise le message d'erreur attendu côté UI/tests
                $result['message'] = 'Identifiants incorrects.';
            }
            return $result;
        } catch (Throwable $e) {
            error_log("Erreur authenticate (controller): " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Une erreur est survenue lors de la connexion.',
                'user' => null
            ];
        }
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

        // Rediriger vers la page d'accueil
        header('Location: ?controller=user&action=homepage');
        exit;
    }

    public function register(): void
    {
        // Activer l'affichage des erreurs temporairement
        ini_set('display_errors', 1);
        error_reporting(E_ALL);

        // Si GET : afficher le formulaire
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            view::show('user/register', ['db_status' => $this->getDbStatus()]);
            return;
        }

        // Si POST : traiter l'inscription
        $username = isset($_POST['username']) ? trim((string)$_POST['username']) : '';
        $email = isset($_POST['email']) ? trim((string)$_POST['email']) : '';
        $password = isset($_POST['password']) ? (string)$_POST['password'] : '';
        $confirm = isset($_POST['confirm_password']) ? (string)$_POST['confirm_password'] : '';

        // Validation des champs vides
        if (empty($username) || empty($email) || empty($password) || empty($confirm)) {
            view::show('user/register', [
                'db_status' => $this->getDbStatus(),
                'flash' => [
                    'success' => false,
                    'message' => 'Tous les champs sont obligatoires.'
                ]
            ]);
            return;
        }

        // Vérification de la correspondance des mots de passe
        if ($password !== $confirm) {
            view::show('user/register', [
                'db_status' => $this->getDbStatus(),
                'flash' => [
                    'success' => false,
                    'message' => 'Les mots de passe ne correspondent pas.'
                ]
            ]);
            return;
        }

        // Tentative de création de l'utilisateur
        try {
            $result = $this->createUser($username, $email, $password);

            if ($result['success']) {
                // Succès : stocker le message flash et rediriger vers login
                $_SESSION['flash_message'] = [
                    'success' => true,
                    'message' => $result['message']
                ];
                header('Location: ?controller=user&action=login');
                exit;
            } else {
                // Échec : réafficher le formulaire avec l'erreur
                view::show('user/register', [
                    'db_status' => $this->getDbStatus(),
                    'flash' => [
                        'success' => false,
                        'message' => $result['message']
                    ]
                ]);
            }
        } catch (Exception $e) {
            // Afficher l'erreur complète pour le debug
            echo "<pre>ERREUR DEBUG:\n";
            echo "Message: " . $e->getMessage() . "\n";
            echo "Trace: " . $e->getTraceAsString() . "\n";
            echo "</pre>";

            view::show('user/register', [
                'db_status' => $this->getDbStatus(),
                'flash' => [
                    'success' => false,
                    'message' => 'Erreur : ' . $e->getMessage()
                ]
            ]);
        }
    }

    private function createUser(string $username, string $email, string $password): array
    {
        // Valider côté contrôleur les règles simples d'UI
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return [
                'success' => false,
                'message' => "L'adresse email n'est pas valide."
            ];
        }
        if (strlen($password) < 6) {
            return [
                'success' => false,
                'message' => 'Le mot de passe doit contenir au moins 6 caractères.'
            ];
        }

        // Déléguer la création au modèle (unicité + insertion)
        try {
            $result = $this->userModel->createUser($username, $email, $password);
            // Normalisation des messages pour compatibilité avec l'existant/tests
            if (!$result['success']) {
                if (str_contains($result['message'], "déjà pris")) {
                    $result['message'] = "Ce nom d'utilisateur est déjà utilisé.";
                }
            }
            return $result;
        } catch (Throwable $e) {
            error_log('Erreur createUser (controller): ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Une erreur est survenue lors de la création du compte.'
            ];
        }
    }

    private function renderResult(bool $success, string $message): void
    {
        view::show('homepageView', ['flash' => ['success' => $success, 'message' => $message]]);
    }
}
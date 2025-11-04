<?php

final class userController
{
    private PDO $pdo;

    // Constructeur pour s'assurer que la session est démarrée et la connexion DB établie
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Initialiser la connexion à la base de données
        $this->initDatabase();
    }

    private function initDatabase(): void
    {
        try {
            // Charger envReader si ce n'est pas déjà fait
            if (!class_exists('envReader')) {
                require_once __DIR__ . '/../core/envReader.php';
            }

            $env = new envReader();
            $dsn = "mysql:host={$env->getHost()};port={$env->getPort()};dbname={$env->getBd()};charset=utf8mb4";

            $this->pdo = new PDO(
                $dsn,
                $env->getUser(),
                $env->getMdp(),
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch (PDOException $e) {
            throw new Exception("Erreur de connexion à la base de données : " . $e->getMessage());
        }
    }

    private function getDbStatus(): array
    {
        try {
            $this->pdo->query('SELECT 1');
            return ['available' => true, 'message' => 'Base de données connectée'];
        } catch (Exception $e) {
            return ['available' => false, 'message' => 'Erreur de connexion : ' . $e->getMessage()];
        }
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
        try {
            // Rechercher l'utilisateur par username ou email
            $stmt = $this->pdo->prepare("
                SELECT id, username, email, mdp 
                FROM User 
                WHERE username = ? OR email = ?
            ");
            $stmt->execute([$login, $login]);
            $user = $stmt->fetch();

            if (!$user) {
                return [
                    'success' => false,
                    'message' => 'Identifiants incorrects.',
                    'user' => null
                ];
            }

            // Vérifier le mot de passe
            if (!password_verify($password, $user['mdp'])) {
                return [
                    'success' => false,
                    'message' => 'Identifiants incorrects.',
                    'user' => null
                ];
            }

            // Ne pas retourner le mot de passe
            unset($user['mdp']);

            return [
                'success' => true,
                'message' => 'Connexion réussie.',
                'user' => $user
            ];

        } catch (PDOException $e) {
            error_log("Erreur authenticate: " . $e->getMessage());

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
        try {
            // Validation de l'email
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return [
                    'success' => false,
                    'message' => 'L\'adresse email n\'est pas valide.'
                ];
            }

            // Validation de la longueur du mot de passe
            if (strlen($password) < 6) {
                return [
                    'success' => false,
                    'message' => 'Le mot de passe doit contenir au moins 6 caractères.'
                ];
            }

            // Vérifier si le nom d'utilisateur existe déjà
            $stmt = $this->pdo->prepare("SELECT id FROM User WHERE username = ?");
            $stmt->execute([$username]);
            if ($stmt->fetch()) {
                return [
                    'success' => false,
                    'message' => 'Ce nom d\'utilisateur est déjà utilisé.'
                ];
            }

            // Vérifier si l'email existe déjà
            $stmt = $this->pdo->prepare("SELECT id FROM User WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                return [
                    'success' => false,
                    'message' => 'Cette adresse email est déjà utilisée.'
                ];
            }

            // Hasher le mot de passe
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Insérer le nouvel utilisateur
            $stmt = $this->pdo->prepare("
                INSERT INTO User (username, email, mdp) 
                VALUES (?, ?, ?)
            ");

            $stmt->execute([$username, $email, $hashedPassword]);

            return [
                'success' => true,
                'message' => 'Compte créé avec succès ! Vous pouvez maintenant vous connecter.'
            ];

        } catch (PDOException $e) {
            // Log l'erreur
            error_log("Erreur createUser: " . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Une erreur est survenue lors de la création du compte. Détails: ' . $e->getMessage()
            ];
        }
    }

    private function renderResult(bool $success, string $message): void
    {
        view::show('homepageView', ['flash' => ['success' => $success, 'message' => $message]]);
    }
}
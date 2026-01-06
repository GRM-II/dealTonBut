<?php

final class userController
{
    private userModel $userModel;
    private passwordResetModel $passwordResetModel;
    private emailService $emailService;

    // Constructeur pour s'assurer que la session est démarrée et la connexion DB établie

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Instancier le modèle utilisateur (gestion DB centralisée dans userModel)
        $this->userModel = new userModel();
        $this->passwordResetModel = new passwordResetModel();
        $this->emailService = new emailService();
    }

    /**
     * Récupère le statut de la base de données
     *
     * @return array{available: bool, message: string, details?: string}
     */
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
        // Si déjà connecté, déconnecter d'abord
        if (isset($_SESSION['user'])) {
            session_unset();
            session_destroy();
            // IMPORTANT : Redémarrer avec un nouvel ID de session
            session_start();
            session_regenerate_id(true);
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

                if ($result['success'] && isset($result['user'])) {
                    // Régénérer l'ID de session pour la sécurité
                    session_regenerate_id(true);

                    // Stocker les informations de l'utilisateur dans la session
                    $_SESSION['user'] = $result['user'];
                    $_SESSION['user_id'] = $result['user']['id'];
                    $_SESSION['logged_in'] = true;

                    // Vérifier s'il y a une redirection en attente
                    if (isset($_SESSION['redirect_after_login'])) {
                        $redirect = $_SESSION['redirect_after_login'];
                        unset($_SESSION['redirect_after_login']);
                        header("Location: $redirect");
                    } else {
                        // Redirection vers la page de profil par défaut
                        header('Location: ?controller=profilepage&action=index');
                    }
                    exit;
                }

                // Si échec : afficher la page de login avec le message d'erreur
                view::show('user/login', ['error' => $result['message']]);
                return;

            } catch (Exception $e) {
                error_log("Erreur login: " . $e->getMessage());
                view::show('user/login', ['error' => 'Une erreur est survenue lors de la connexion.']);
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

    /**
     * Authentifie un utilisateur
     *
     * @return array{success: bool, message: string, user?: array{id: int|string, username: string, email: string}}
     */
    private function authenticate(string $login, string $password): array
    {
        try {
            $result = $this->userModel->authenticate($login, $password);
            if (!$result['success']) {
                $result['message'] = 'Identifiants incorrects.';
            }
            return $result;
        } catch (Throwable $e) {
            error_log("Erreur authenticate (controller): " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Une erreur est survenue lors de la connexion.'
            ];
        }
    }

    public function logout(): void
    {
        // Détruire toutes les données de session
        session_unset();
        session_destroy();

        // Supprimer le cookie de session
        $sessionName = session_name();
        if ($sessionName !== false && isset($_COOKIE[$sessionName])) {
            setcookie($sessionName, '', time() - 3600, '/');
        }

        // Rediriger vers la page d'accueil
        header('Location: ?controller=user&action=homepage');
        exit;
    }

    public function register(): void
    {
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
            error_log("Erreur inscription : " . $e->getMessage());

            view::show('user/register', [
                'db_status' => $this->getDbStatus(),
                'flash' => [
                    'success' => false,
                    'message' => 'Une erreur est survenue lors de l\'inscription.'
                ]
            ]);
        }
    }

    /**
     * Crée un nouvel utilisateur
     *
     * @return array{success: bool, message: string}
     */
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

    public function forgotPassword(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?controller=user&action=homepage');
            exit;
        }

        $email = isset($_POST['email']) ? trim((string)$_POST['email']) : '';

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['flash_message'] = [
                'success' => false,
                'message' => 'Adresse email invalide.'
            ];
            header('Location: ?controller=user&action=homepage');
            exit;
        }

        try {
            $user = $this->passwordResetModel->getUserByEmail($email);

            $genericMessage = 'Si cette adresse email est enregistrée, vous recevrez un lien de réinitialisation dans quelques instants.';

            if (!$user) {
                $_SESSION['flash_message'] = [
                    'success' => true,
                    'message' => $genericMessage
                ];
                header('Location: ?controller=user&action=homepage');
                exit;
            }

            $tokenResult = $this->passwordResetModel->createResetToken((int)$user['id']);

            if (!$tokenResult['success'] || !isset($tokenResult['token'])) {
                error_log("Erreur création token pour user {$user['id']}");
                $_SESSION['flash_message'] = [
                    'success' => false,
                    'message' => 'Une erreur est survenue. Veuillez réessayer plus tard.'
                ];
                header('Location: ?controller=user&action=homepage');
                exit;
            }

            $emailResult = $this->emailService->sendPasswordResetEmail($email, $tokenResult['token']);

            if (!$emailResult['success']) {
                error_log("Erreur envoi email à $email : {$emailResult['message']}");
            }

            $_SESSION['flash_message'] = [
                'success' => true,
                'message' => $genericMessage
            ];
            header('Location: ?controller=user&action=homepage');
            exit;

        } catch (Exception $e) {
            error_log("Erreur forgotPassword: " . $e->getMessage());
            $_SESSION['flash_message'] = [
                'success' => false,
                'message' => 'Une erreur est survenue. Veuillez réessayer plus tard.'
            ];
            header('Location: ?controller=user&action=homepage');
            exit;
        }
    }

    public function resetPassword(): void
    {
        $token = isset($_GET['token']) ? trim((string)$_GET['token']) : '';

        if (empty($token)) {
            view::show('user/resetPasswordView', [
                'error' => 'Token manquant. Veuillez utiliser le lien reçu par email.'
            ]);
            return;
        }

        $tokenValidation = $this->passwordResetModel->validateToken($token);

        if (!$tokenValidation['valid'] || !isset($tokenValidation['userId'])) {
            view::show('user/resetPasswordView', [
                'error' => $tokenValidation['message']
            ]);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            view::show('user/resetPasswordView', []);
            return;
        }

        $password = isset($_POST['password']) ? (string)$_POST['password'] : '';
        $confirmPassword = isset($_POST['confirm_password']) ? (string)$_POST['confirm_password'] : '';

        if (empty($password) || empty($confirmPassword)) {
            view::show('user/resetPasswordView', [
                'error' => 'Veuillez remplir tous les champs.'
            ]);
            return;
        }

        if ($password !== $confirmPassword) {
            view::show('user/resetPasswordView', [
                'error' => 'Les mots de passe ne correspondent pas.'
            ]);
            return;
        }

        if (strlen($password) < 6) {
            view::show('user/resetPasswordView', [
                'error' => 'Le mot de passe doit contenir au moins 6 caractères.'
            ]);
            return;
        }

        try {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $updateResult = userModel::updatePassword($tokenValidation['userId'], $hashedPassword);

            if (!$updateResult) {
                view::show('user/resetPasswordView', [
                    'error' => 'Erreur lors de la mise à jour du mot de passe.'
                ]);
                return;
            }

            $this->passwordResetModel->deleteToken($token);

            view::show('user/resetPasswordView', [
                'success' => 'Votre mot de passe a été réinitialisé avec succès. Vous pouvez maintenant vous connecter.'
            ]);

        } catch (Exception $e) {
            error_log("Erreur resetPassword: " . $e->getMessage());
            view::show('user/resetPasswordView', [
                'error' => 'Une erreur est survenue. Veuillez réessayer.'
            ]);
        }
    }
}
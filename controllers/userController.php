<?php

/**
 * User Controller
 *
 * Handles all user-related actions including authentication (login/logout),
 * registration, password reset functionality, and session management.
 * Manages user authentication flow, password recovery, and account security.
 *
 */
final class userController
{
    /**
     * User model instance for database operations
     *
     * @var userModel
     */
    private userModel $userModel;

    /**
     * Password reset model instance for token management
     *
     * @var passwordResetModel
     */
    private passwordResetModel $passwordResetModel;

    /**
     * Email service instance for sending emails
     *
     * @var emailService
     */
    private emailService $emailService;

    /**
     * Constructor
     *
     * Ensures that the session is started and initializes required models
     * (user model, password reset model, and email service) for controller operations.
     */
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Instantiate the user model (centralized DB management in userModel)
        $this->userModel = new userModel();
        $this->passwordResetModel = new passwordResetModel();
        $this->emailService = new emailService();
    }

    /**
     * Gets the database connection status
     *
     * Delegates to the userModel to retrieve the current state of the database connection.
     *
     * @return array{available: bool, message: string, details?: string} Database status information
     */
    private function getDbStatus(): array
    {
        // Delegate to the userModel to know the state of the database
        return $this->userModel->getDbStatus();
    }

    /**
     * Displays the homepage
     *
     * Shows the main homepage view with the current database status.
     *
     * @return void
     */
    public function homepage(): void
    {
        $status = $this->getDbStatus();
        view::show('homepageView', ['db_status' => $status]);
    }

    /**
     * Handles user login functionality
     *
     * For GET requests: Displays the login form with any flash messages.
     * For POST requests: Authenticates the user credentials, regenerates session ID
     * for security, stores user information in session, and redirects to either
     * a pending redirect URL or the default profile page.
     *
     * If a user is already logged in, logs them out before showing the login form.
     *
     * @return void
     */
    public function login(): void
    {
        // If already connected, disconnect first.
        if (isset($_SESSION['user'])) {
            session_unset();
            session_destroy();
            // IMPORTANT: Restart with a new session ID
            session_start();
            session_regenerate_id(true);
        }

        // If the form is submitted
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $login = $_POST['login'] ?? '';
            $password = $_POST['password'] ?? '';

            if (empty($login) || empty($password)) {
                $_SESSION['flash_message'] = [
                    'success' => false,
                    'message' => 'Veuillez remplir tous les champs.'
                ];
                header('Location: ?controller=homepage&action=index');
                exit;
            }

            try {
                $result = $this->authenticate($login, $password);

                if ($result['success'] && isset($result['user'])) {
                    // Regenerate the session ID for security
                    session_regenerate_id(true);

                    // Store user information in the session
                    $_SESSION['user'] = $result['user'];
                    $_SESSION['user_id'] = $result['user']['id'];
                    $_SESSION['logged_in'] = true;

                    // Check if there is a pending redirection
                    if (isset($_SESSION['redirect_after_login'])) {
                        $redirect = $_SESSION['redirect_after_login'];
                        unset($_SESSION['redirect_after_login']);
                        header("Location: $redirect");
                    } else {
                        // Redirecting to the default profile page
                        header('Location: ?controller=profilepage&action=index');
                    }
                    exit;
                }

                // If unsuccessful: redirect to homepage with error message
                $_SESSION['flash_message'] = [
                    'success' => false,
                    'message' => $result['message']
                ];
                header('Location: ?controller=homepage&action=index');
                exit;

            } catch (Exception $e) {
                error_log("Erreur login: " . $e->getMessage());
                $_SESSION['flash_message'] = [
                    'success' => false,
                    'message' => 'Une erreur est survenue lors de la connexion.'
                ];
                header('Location: ?controller=homepage&action=index');
                exit;
            }
        }

        // GET request: Display the login form
        // Check if there is a registration success message
        $flashMessage = null;
        if (isset($_SESSION['flash_message'])) {
            $flashMessage = $_SESSION['flash_message'];
            unset($_SESSION['flash_message']);
        }

        view::show('user/login', ['flash' => $flashMessage]);
    }

    /**
     * Authenticates a user with provided credentials
     *
     * Validates user login credentials (username/email and password) against
     * the database. Returns authentication result with user data on success.
     *
     * @param string $login Username or email address
     * @param string $password User password (plain text, will be verified against hash)
     * @return array{success: bool, message: string, user?: array{id: int|string, username: string, email: string}} Authentication result
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

    /**
     * Handles user logout
     *
     * Destroys all session data, removes the session cookie, and redirects
     * to the homepage. Ensures complete cleanup of user authentication state.
     *
     * @return void
     */
    public function logout(): void
    {
        // destroy all session data
        session_unset();
        session_destroy();

        // Delete the session cookie
        $sessionName = session_name();
        if ($sessionName !== false && isset($_COOKIE[$sessionName])) {
            setcookie($sessionName, '', time() - 3600, '/');
        }

        // Redirects towards the homepage
        header('Location: ?controller=user&action=homepage');
        exit;
    }

    /**
     * Handles user registration
     *
     * For GET requests: Displays the registration form.
     * For POST requests: Validates input data (username, email, password match),
     * creates a new user account, and redirects to login with a success message.
     *
     * @return void
     */
    public function register(): void
    {
        // if GET : shows the form
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            view::show('user/register', ['db_status' => $this->getDbStatus()]);
            return;
        }

        // if POST : treats the form submission
        $username = isset($_POST['username']) ? trim((string)$_POST['username']) : '';
        $email = isset($_POST['email']) ? trim((string)$_POST['email']) : '';
        $password = isset($_POST['password']) ? (string)$_POST['password'] : '';
        $confirm = isset($_POST['confirm_password']) ? (string)$_POST['confirm_password'] : '';

        // Validates the empty fields
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

        // Password matching verification
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

        // Attempting to create a user
        try {
            $result = $this->createUser($username, $email, $password);

            if ($result['success']) {
                // Success: Store the flash message and redirect to login
                $_SESSION['flash_message'] = [
                    'success' => true,
                    'message' => $result['message']
                ];
                header('Location: ?controller=user&action=login');
                exit;
            } else {
                // Failure: redisplay the form with the error
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
     * Creates a new user account
     *
     * Validates email format and password length requirements before delegating
     * user creation to the model. Standardizes error messages for compatibility.
     *
     * @param string $username Desired username
     * @param string $email Email address
     * @param string $password Plain text password (will be hashed by model)
     * @return array{success: bool, message: string} Creation result with success status and message
     */
    private function createUser(string $username, string $email, string $password): array
    {
        // Validate the simple UI rules on the controller side
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

        // Delegate the creation to the model (uniqueness + insertion)
        try {
            $result = $this->userModel->createUser($username, $email, $password);
            // Message standardization for compatibility with existing systems/tests
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

    /**
     * Handles forgot password requests
     *
     * Processes password reset requests by validating the email, checking if
     * the user exists, creating a reset token, and sending a password reset
     * email. Uses a generic success message for security (to prevent email enumeration).
     *
     * Only accepts POST requests and redirects to homepage with flash messages.
     *
     * @return void
     */
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

    /**
     * Handles password reset via token
     *
     * For GET requests: Validates the reset token and displays the password reset form.
     * For POST requests: Validates new password input, updates the user's password,
     * deletes the used token, and displays a success message.
     *
     * @return void
     */
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
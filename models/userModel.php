<?php

require_once 'core/envReader.php';

/**
 * User model class for managing database operations related to users.
 *
 * This class implements the Singleton pattern for database connections
 * and provides comprehensive methods for user management including authentication,
 * registration, profile updates, and grade management. It handles all database
 * interactions for the Users table with proper error handling and security measures.
 *
 * @final
 */
final class userModel
{
    /**
     * Singleton PDO database connection instance.
     *
     * @var PDO|null
     */
    private static ?PDO $connection = null;

    /**
     * Retrieves or creates the single PDO connection (Singleton pattern).
     *
     * This method establishes a MySQL database connection using credentials
     * from the envReader configuration. The connection is created only once
     * and reused for subsequent calls. It configures PDO to throw exceptions
     * on errors and fetch results as associative arrays by default.
     *
     * @return PDO The PDO database connection instance
     * @throws RuntimeException If unable to connect to the database
     */
    public static function getConnection(): PDO
    {
        if (self::$connection === null) {
            try {
                $id = new envReader();

                $dsn = sprintf(
                    "mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4",
                    $id->getHost(),
                    $id->getPort(),
                    $id->getBd()
                );

                self::$connection = new PDO($dsn, $id->getUser(), $id->getMdp());
                self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            } catch (PDOException $e) {
                error_log("Erreur de connexion BDD : " . $e->getMessage());
                throw new RuntimeException("Impossible de se connecter à la base de données.");
            }
        }

        return self::$connection;
    }

    /**
     * Checks the database connection status and verifies the Users table exists.
     *
     * This method performs two checks:
     * 1. Verifies that a database connection can be established
     * 2. Confirms that the 'Users' table exists in the database
     *
     * @return array{available: bool, message: string, details?: string} An associative array containing:
     *         - available: Boolean indicating if the database is operational
     *         - message: Human-readable status message
     *         - details: Additional diagnostic information (optional)
     */
    public function getDbStatus(): array
    {
        try {
            $pdo = self::getConnection();

            // Checks if the User table exists
            $stmt = $pdo->query("SHOW TABLES LIKE 'Users'");
            $tableExists = $stmt !== false && $stmt->rowCount() > 0;

            if (!$tableExists) {
                return [
                    'available' => false,
                    'message' => "La table 'Users' n'existe pas.",
                    'details' => 'Connexion OK mais table manquante'
                ];
            }

            return [
                'available' => true,
                'message' => 'Connexion à la base de données opérationnelle.',
                'details' => "PDO MySQL OK | Table 'Users' existe"
            ];

        } catch (PDOException $e) {
            error_log("Erreur getDbStatus : " . $e->getMessage());
            return [
                'available' => false,
                'message' => 'Erreur de connexion à la base de données.',
                'details' => 'Vérifiez la configuration de la base de données'
            ];
        }
    }

    /**
     * Retrieves a user by their username.
     *
     * This method fetches complete user information including their grades/points
     * across different subjects (mathematics, programming, networks, databases, and other).
     *
     * @param string $username The username to search for
     * @return array{id: int|string, username: string, email: string, maths_maths: float|string|null, programmation_points: float|string|null, network_points: float|string|null, DB_points: float|string|null, other_points: float|string|null}|null
     *         An associative array containing user data with grades, or null if not found or on error
     */
    public function getUserByUsername(string $username): ?array
    {
        try {
            $pdo = self::getConnection();
            $stmt = $pdo->prepare("SELECT id, username, email FROM Users WHERE username = :username");
            $stmt->execute(['username' => $username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            return $user ?: null;
        } catch (PDOException $e) {
            error_log("Erreur getUserByUsername: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Retrieves a user's ID from their username.
     *
     * This is a lightweight method that only fetches the user ID,
     * useful when only the ID is needed without loading the complete user profile.
     *
     * @param string $username The username to look up
     * @return int|null The user's ID, or null if the user is not found or an error occurs
     */
    public function getUserIdByUsername(string $username): ?int
    {
        try {
            $pdo = self::getConnection();
            $stmt = $pdo->prepare("SELECT id FROM Users WHERE username = :username");
            $stmt->execute(['username' => $username]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return $result ? (int)$result['id'] : null;
        } catch (PDOException $e) {
            error_log("Erreur getUserIdByUsername: " . $e->getMessage());
            return null;
        }
    }

    public function getUserPoints(int $userId): array
    {
        try {
            $pdo = self::getConnection();
            $stmt = $pdo->prepare("SELECT maths_points, programmation_points, network_points, 
                               DB_points, other_points 
                               FROM points 
                               WHERE id = :user_id");
            $stmt->execute(['user_id' => $userId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$result) {
                $this->createUserPoints($userId);
                return [
                    'maths_points' => 0,
                    'programmation_points' => 0,
                    'network_points' => 0,
                    'DB_points' => 0,
                    'other_points' => 0
                ];
            }

            return $result;
        } catch (PDOException $e) {
            error_log("Erreur getUserPoints: " . $e->getMessage());
            return [
                'maths_points' => 0,
                'programmation_points' => 0,
                'network_points' => 0,
                'DB_points' => 0,
                'other_points' => 0
            ];
        }
    }

    private function createUserPoints(int $userId): bool
    {
        try {
            $pdo = self::getConnection();
            $stmt = $pdo->prepare("INSERT INTO points (id, maths_points, programmation_points, 
                              network_points, DB_points, other_points) 
                              VALUES (:user_id, 0, 0, 0, 0, 0)");
            return $stmt->execute(['user_id' => $userId]);
        } catch (PDOException $e) {
            error_log("Erreur createUserPoints: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Creates a new user in the database.
     *
     * This method performs the following validations and operations:
     * 1. Checks if the email address is already in use
     * 2. Checks if the username is already taken
     * 3. Hashes the password using PHP's password_hash with PASSWORD_DEFAULT algorithm
     * 4. Inserts the new user into the database
     *
     * @param string $username The desired username
     * @param string $email The user's email address
     * @param string $password The plain text password (will be hashed)
     * @return array{success: bool, message: string} An associative array containing:
     *         - success: Boolean indicating if the user was created successfully
     *         - message: A descriptive message about the operation result
     */
    public function createUser(string $username, string $email, string $password): array
    {
        try {
            $pdo = self::getConnection();

            // 1. Check if the email address already exists.
            $stmt = $pdo->prepare("SELECT id FROM Users WHERE email = :email LIMIT 1");
            $stmt->execute(['email' => $email]);

            if ($stmt->fetch()) {
                return [
                    'success' => false,
                    'message' => 'Cette adresse email est déjà utilisée.'
                ];
            }

            // 2. Check if the username already exists
            $stmt = $pdo->prepare("SELECT id FROM Users WHERE username = :username LIMIT 1");
            $stmt->execute(['username' => $username]);

            if ($stmt->fetch()) {
                return [
                    'success' => false,
                    'message' => 'Ce nom d\'utilisateur est déjà pris.'
                ];
            }

            // 3. Hasher the password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // 4. Insert the new user
            $stmt = $pdo->prepare(
                "INSERT INTO Users (username, email, mdp) 
                 VALUES (:username, :email, :password)"
            );

            $result = $stmt->execute([
                'username' => $username,
                'email' => $email,
                'password' => $hashedPassword
            ]);

            if ($result) {
                $userId = (int)$pdo->lastInsertId();
                $this->createUserPoints($userId);

                return [
                    'success' => true,
                    'message' => 'Compte créé avec succès ! Vous pouvez maintenant vous connecter.'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Erreur lors de la création du compte.'
                ];
            }

        } catch (PDOException $e) {
            error_log("Erreur création utilisateur : " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Une erreur est survenue lors de la création du compte.'
            ];
        }
    }

    /**
     * Searches for a user by their username OR email address.
     *
     * This method is used during login to allow users to authenticate
     * using either their username or email. It returns the complete user
     * record including the hashed password for verification.
     *
     * @param string $login The username or email to search for
     * @return array{id: int|string, username: string, email: string, mdp: string}|null
     *         An associative array containing user credentials, or null if not found or on error
     */
    public function findUserByLogin(string $login): ?array
    {
        $login = trim($login);

        if (empty($login)) {
            return null;
        }

        try {
            $pdo = self::getConnection();

            $sql = "SELECT id, username, email, mdp, role 
                FROM Users
                WHERE username = :login OR email = :login 
                LIMIT 1";

            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':login', $login, PDO::PARAM_STR);
            $stmt->execute();

            $user = $stmt->fetch();

            return $user ?: null;

        } catch (PDOException $e) {
            error_log("Erreur findUserByLogin : " . $e->getMessage());
            return null;
        }
    }

    /**
     * Authenticates a user with their login credentials.
     *
     * This method performs user authentication by:
     * 1. Validating that both login and password are provided
     * 2. Looking up the user by username or email
     * 3. Verifying the password using password_verify()
     * 4. Returning user information on successful authentication
     *
     * For security, the same error message is returned whether the user
     * doesn't exist or the password is incorrect to prevent user enumeration.
     *
     * @param string $login The username or email address
     * @param string $password The plain text password to verify
     * @return array{success: bool, message: string, user?: array{id: int|string, username: string, email: string}}
     *         An associative array containing:
     *         - success: Boolean indicating if authentication was successful
     *         - message: A status message
     *         - user: User data (id, username, email) only present if success is true
     */
    public function authenticate(string $login, string $password): array
    {
        $login = trim($login);

        if (empty($login) || empty($password)) {
            return ['success' => false, 'message' => 'Identifiants manquants.'];
        }

        // Search for user
        $user = $this->findUserByLogin($login);

        if (!$user) {
            return ['success' => false, 'message' => 'Identifiant ou mot de passe incorrect.'];
        }

        // Check the password
        $passwordMatch = password_verify($password, $user['mdp']);

        if (!$passwordMatch) {
            return ['success' => false, 'message' => 'Identifiant ou mot de passe incorrect.'];
        }

        return [
            'success' => true,
            'message' => 'Connexion réussie.',
            'user' => [
                'id' => $user['id'],
                'username' => $user['username'],
                'email' => $user['email'],
                'role' => $user['role'] ?? 'user'
            ]
        ];
    }

    /**
     * Updates a user's password.
     *
     * This method updates the password field in the database with a pre-hashed password.
     * Note: The password should be hashed before calling this method using password_hash().
     *
     * @param int $userId The ID of the user whose password should be updated
     * @param string $hashedPassword The hashed password to store
     * @return bool True if the password was successfully updated, false otherwise
     */
    public static function updatePassword(int $userId, string $hashedPassword): bool
    {
        if (empty($hashedPassword)) {
            return false;
        }

        try {
            $pdo = self::getConnection();
            $sql = "UPDATE Users SET mdp = :password WHERE id = :userId";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':password', $hashedPassword, PDO::PARAM_STR);
            $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);

            return $stmt->execute();

        } catch (PDOException $e) {
            error_log("Erreur updatePassword : " . $e->getMessage());
            return false;
        }
    }

    /**
     * Deletes a user from the database.
     *
     * This method permanently removes a user record from the Users table.
     * Warning: This operation cannot be undone. Consider implementing soft deletes
     * for production applications.
     *
     * @param int $userId The ID of the user to delete
     * @return bool True if the user was successfully deleted, false otherwise
     */
    public static function deleteUser(int $userId): bool
    {
        try {
            $pdo = self::getConnection();
            $sql = "DELETE FROM Users WHERE id = :userId";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);

            return $stmt->execute();

        } catch (PDOException $e) {
            error_log("Erreur deleteUser : " . $e->getMessage());
            return false;
        }
    }

    /**
     * Updates a user's username.
     *
     * This method changes the username for an existing user. It validates that
     * the new username is not empty and handles duplicate username errors.
     *
     * @param string $currentUsername The user's current username
     * @param string $newUsername The desired new username
     * @return array{success: bool, message: string} An associative array containing:
     *         - success: Boolean indicating if the update was successful
     *         - message: A descriptive message about the operation result
     */
    public function updateUsername(string $currentUsername, string $newUsername): array
    {
        $newUsername = trim($newUsername);

        if (empty($newUsername)) {
            return ['success' => false, 'message' => 'Le nom d\'utilisateur ne peut pas être vide.'];
        }

        try {
            $pdo = self::getConnection();
            $sql = "UPDATE Users SET username = :newUsername WHERE username = :currentUsername";
            $stmt = $pdo->prepare($sql);

            $stmt->bindValue(':newUsername', $newUsername, PDO::PARAM_STR);
            $stmt->bindValue(':currentUsername', $currentUsername, PDO::PARAM_STR);

            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                return ['success' => true, 'message' => 'Nom d\'utilisateur mis à jour.'];
            }

            return ['success' => false, 'message' => 'Aucune modification effectuée.'];

        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                return ['success' => false, 'message' => 'Ce nom d\'utilisateur est déjà pris.'];
            }
            error_log("Erreur updateUsername : " . $e->getMessage());
            return ['success' => false, 'message' => 'Erreur lors de la mise à jour.'];
        }
    }

    /**
     * Updates a user's email address.
     *
     * This method changes the email for an existing user. It validates that
     * the email format is correct and handles duplicate email errors.
     *
     * @param string $username The username of the user to update
     * @param string $newEmail The new email address
     * @return array{success: bool, message: string} An associative array containing:
     *         - success: Boolean indicating if the update was successful
     *         - message: A descriptive message about the operation result
     */
    public function updateEmail(string $username, string $newEmail): array
    {
        $newEmail = trim($newEmail);

        if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Email invalide.'];
        }

        try {
            $pdo = self::getConnection();
            $sql = "UPDATE Users SET email = :newEmail WHERE username = :username";
            $stmt = $pdo->prepare($sql);

            $stmt->bindValue(':newEmail', $newEmail, PDO::PARAM_STR);
            $stmt->bindValue(':username', $username, PDO::PARAM_STR);

            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                return ['success' => true, 'message' => 'Email mis à jour.'];
            }

            return ['success' => false, 'message' => 'Aucune modification effectuée.'];

        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                return ['success' => false, 'message' => 'Cet email est déjà utilisé.'];
            }
            error_log("Erreur updateEmail : " . $e->getMessage());
            return ['success' => false, 'message' => 'Erreur lors de la mise à jour.'];
        }
    }

    /**
     * Updates a user's grades/points across different subjects.
     *
     * This method allows updating one or more grade fields for a user.
     * It performs the following validations:
     * 1. Ensures all grade values are between 0 and 20
     * 2. Only updates fields that are in the allowed fields list
     * 3. Dynamically constructs the UPDATE query based on provided fields
     *
     * Allowed fields: network_points, network_points, network_points, network_points, other_points
     *
     * @param int $userId The ID of the user whose grades should be updated
     * @param array<string, float|int> $gradesData An associative array of field names and grade values
     * @return array{success: bool, message: string} An associative array containing:
     *         - success: Boolean indicating if the update was successful
     *         - message: A descriptive message about the operation result
     */
    public function updateGrades(int $userId, array $gradesData): array
    {
        if (empty($gradesData)) {
            return ['success' => false, 'message' => 'Aucune moyenne à mettre à jour.'];
        }

        // Checks that all the values are between 0 and 20
        foreach ($gradesData as $field => $value) {
            if ($value < 0 || $value > 20) {
                return [
                    'success' => false,
                    'message' => 'Les moyennes doivent être comprises entre 0 et 20.'
                ];
            }
        }

        try {
            $pdo = self::getConnection();

            // Dynamically construct the UPDATE query
            $fields = [];
            $params = [':userId' => $userId];

            $allowedFields = ['maths_points', 'programmation_points', 'network_points', 'DB_points', 'other_points'];

            foreach ($gradesData as $field => $value) {
                if (in_array($field, $allowedFields)) {
                    $fields[] = "$field = :$field";
                    $params[":$field"] = $value;
                }
            }

            if (empty($fields)) {
                return ['success' => false, 'message' => 'Aucune moyenne valide à mettre à jour.'];
            }

            // CHANGEMENT ICI : UPDATE points au lieu de UPDATE Users
            $sql = "UPDATE points SET " . implode(', ', $fields) . " WHERE id = :userId";
            $stmt = $pdo->prepare($sql);

            $result = $stmt->execute($params);

            if ($result && $stmt->rowCount() > 0) {
                return ['success' => true, 'message' => 'Moyenne(s) mise(s) à jour avec succès.'];
            }

            return ['success' => false, 'message' => 'Aucune modification effectuée.'];

        } catch (PDOException $e) {
            error_log("Erreur updateGrades : " . $e->getMessage());
            return ['success' => false, 'message' => 'Erreur lors de la mise à jour des moyennes.'];
        }
    }

    public function getUserWithRole(string $username): ?array
    {
        try {
            $pdo = self::getConnection();
            $stmt = $pdo->prepare("SELECT id, username, email, role FROM Users WHERE username = :username");
            $stmt->execute(['username' => $username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            return $user ?: null;
        } catch (PDOException $e) {
            error_log("Erreur getUserWithRole: " . $e->getMessage());
            return null;
        }
    }

    public function isAdmin(int $userId): bool
    {
        try {
            $pdo = self::getConnection();
            $stmt = $pdo->prepare("SELECT role FROM Users WHERE id = :userId");
            $stmt->execute(['userId' => $userId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return $result && $result['role'] === 'admin';
        } catch (PDOException $e) {
            error_log("Erreur isAdmin: " . $e->getMessage());
            return false;
        }
    }

    public function getAllUsers(int $limit = 50, int $offset = 0): array
    {
        try {
            $pdo = self::getConnection();
            $stmt = $pdo->prepare("SELECT id, username, email, role, created_at 
                               FROM Users 
                               ORDER BY created_at DESC 
                               LIMIT :limit OFFSET :offset");
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getAllUsers: " . $e->getMessage());
            return [];
        }
    }

    public function countUsers(): int
    {
        try {
            $pdo = self::getConnection();
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM Users");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return (int)($result['count'] ?? 0);
        } catch (PDOException $e) {
            error_log("Erreur countUsers: " . $e->getMessage());
            return 0;
        }
    }

    public function updateUserRole(int $userId, string $role): array
    {
        if (!in_array($role, ['user', 'admin'])) {
            return ['success' => false, 'message' => 'Rôle invalide.'];
        }

        try {
            $pdo = self::getConnection();
            $stmt = $pdo->prepare("UPDATE Users SET role = :role WHERE id = :userId");
            $stmt->execute([
                'role' => $role,
                'userId' => $userId
            ]);

            if ($stmt->rowCount() > 0) {
                return ['success' => true, 'message' => 'Rôle mis à jour avec succès.'];
            }

            return ['success' => false, 'message' => 'Aucune modification effectuée.'];
        } catch (PDOException $e) {
            error_log("Erreur updateUserRole: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erreur lors de la mise à jour du rôle.'];
        }
    }

    public function searchUsers(string $search): array
    {
        try {
            $pdo = self::getConnection();
            $stmt = $pdo->prepare("SELECT id, username, email, role, created_at 
                               FROM Users 
                               WHERE username LIKE :search OR email LIKE :search
                               ORDER BY created_at DESC 
                               LIMIT 50");
            $stmt->execute(['search' => "%$search%"]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur searchUsers: " . $e->getMessage());
            return [];
        }
    }
}
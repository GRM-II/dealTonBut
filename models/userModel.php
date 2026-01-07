<?php

require_once 'core/envReader.php';

final class userModel
{
    private static ?PDO $connection = null;

    /**
     * Retrieves or creates the single PDO connection (Singleton pattern)
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
     * Checks the database connection status
     *
     * @return array{available: bool, message: string, details?: string}
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
     * Retrieves a user by their username
     * @return array{id: int|string, username: string, email: string, points_maths: float|string|null, points_programmation: float|string|null, points_reseaux: float|string|null, points_BD: float|string|null, points_autre: float|string|null}|null
     */
    public function getUserByUsername(string $username): ?array
    {
        try {
            $pdo = self::getConnection();
            $stmt = $pdo->prepare("SELECT id, username, email, 
                       points_maths, points_programmation, points_reseaux, 
                       points_BD, points_autre FROM Users WHERE username = :username");
            $stmt->execute(['username' => $username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            return $user ?: null;
        } catch (PDOException $e) {
            error_log("Erreur getUserByUsername: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Retrieves a user's ID from their username
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

    /**
     * Create a new user in the database
     * @return array{success: bool, message: string}
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
     * Search for a user by their username OR their email
     * @return array{id: int|string, username: string, email: string, mdp: string}|null
     */
    public function findUserByLogin(string $login): ?array
    {
        $login = trim($login);

        if (empty($login)) {
            return null;
        }

        try {
            $pdo = self::getConnection();

            $sql = "SELECT id, username, email, mdp 
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
     * Authenticates a user
     * @return array{success: bool, message: string, user?: array{id: int|string, username: string, email: string}}
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
                'email' => $user['email']
            ]
        ];
    }

    /**
     * Updates the password
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
     * Deletes the user
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
     * Updates the username
     * @return array{success: bool, message: string}
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
     * Updates the email
     * @return array{success: bool, message: string}
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
     * Updates the averages
     * @param array<string, float|int> $gradesData
     * @return array{success: bool, message: string}
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

            $allowedFields = ['points_maths', 'points_programmation', 'points_reseaux', 'points_BD', 'points_autre'];

            foreach ($gradesData as $field => $value) {
                if (in_array($field, $allowedFields)) {
                    $fields[] = "$field = :$field";
                    $params[":$field"] = $value;
                }
            }

            if (empty($fields)) {
                return ['success' => false, 'message' => 'Aucune moyenne valide à mettre à jour.'];
            }

            $sql = "UPDATE Users SET " . implode(', ', $fields) . " WHERE id = :userId";
            $stmt = $pdo->prepare($sql);

            foreach ($gradesData as $field => $value) {
                if (in_array($field, $allowedFields)) {
                    $stmt->bindValue(":$field", $field, PDO::PARAM_STR);
                }
            }
            $stmt->bindValue(':userId', $userId, PDO::PARAM_STR);

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
}
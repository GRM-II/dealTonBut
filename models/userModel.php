<?php

require 'core/envReader.php';

final class userModel
{
    private static ?PDO $connection = null;

    /**
     * Récupère ou crée la connexion PDO unique (pattern Singleton)
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

                echo "<!-- DB CONNECTION OK -->\n";

            } catch (PDOException $e) {
                die("Erreur de connexion à la base de données : " . $e->getMessage());
            }
        }

        return self::$connection;
    }

    // Dans votre userModel.php
    public function getUserIdByUsername(string $username): ?int
    {
        try {
            $pdo = self::getConnection(); // ← Changement ici !
            $stmt = $pdo->prepare("SELECT ID FROM User WHERE Username = :username");
            $stmt->execute(['username' => $username]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return $result ? (int)$result['ID'] : null;
        } catch (PDOException $e) {
            error_log("Erreur getUserIdByUsername: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Crée un nouvel utilisateur dans la base de données
     */
    public function createUser(string $username, string $email, string $password): array
    {
        // Validation des données
        $username = trim($username);
        $email = trim($email);

        if (empty($username) || empty($email) || empty($password)) {
            return ['success' => false, 'message' => 'Tous les champs sont requis.'];
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Email invalide.'];
        }

        // Hash sécurisé du mot de passe
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        try {
            $pdo = self::getConnection();
            $sql = "INSERT INTO User (Username, Email, Mdp) VALUES (:username, :email, :password)";
            $stmt = $pdo->prepare($sql);

            $stmt->bindValue(':username', $username, PDO::PARAM_STR);
            $stmt->bindValue(':email', $email, PDO::PARAM_STR);
            $stmt->bindValue(':password', $passwordHash, PDO::PARAM_STR);

            $stmt->execute();
            $userId = $pdo->lastInsertId();

            return [
                'success' => true,
                'message' => 'Compte créé avec succès.',
                'id' => $userId
            ];

        } catch (PDOException $e) {
            // Gestion des doublons (code erreur 23000)
            if ($e->getCode() === '23000') {
                if (stripos($e->getMessage(), 'Username') !== false) {
                    return ['success' => false, 'message' => "Ce nom d'utilisateur est déjà pris."];
                }
                if (stripos($e->getMessage(), 'Email') !== false) {
                    return ['success' => false, 'message' => 'Cet email est déjà utilisé.'];
                }
            }

            error_log("Erreur createUser : " . $e->getMessage());
            return ['success' => false, 'message' => 'Erreur lors de la création du compte.'];
        }
    }

    /**
     * Recherche un utilisateur par son username OU son email (VERSION DEBUG)
     */
    public function findUserByLogin(string $login): ?array
    {
        echo "<!-- findUserByLogin appelé avec: '$login' -->\n";

        $login = trim($login);

        if (empty($login)) {
            echo "<!-- findUserByLogin: login vide -->\n";
            return null;
        }

        try {
            $pdo = self::getConnection();
            $sql = "SELECT Username, Email, Mdp 
                    FROM User 
                    WHERE Username = :login OR Email = :login 
                    LIMIT 1";

            echo "<!-- SQL: $sql -->\n";

            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':login', $login, PDO::PARAM_STR);
            $stmt->execute();

            $user = $stmt->fetch();

            if ($user) {
                echo "<!-- Utilisateur trouvé: Username=" . htmlspecialchars($user['Username']) . ", Email=" . htmlspecialchars($user['Email']) . " -->\n";
            } else {
                echo "<!-- AUCUN utilisateur trouvé pour '$login' -->\n";
            }

            return $user ?: null;

        } catch (PDOException $e) {
            echo "<!-- ERREUR PDO dans findUserByLogin: " . htmlspecialchars($e->getMessage()) . " -->\n";
            error_log("Erreur findUserByLogin : " . $e->getMessage());
            return null;
        }
    }

    /**
     * Authentifie un utilisateur (VERSION DEBUG MAXIMALE)
     */
    public function authenticate(string $login, string $password): array
    {
        echo "<!-- ============ AUTHENTICATE START ============ -->\n";
        echo "<!-- Login reçu: '" . htmlspecialchars($login) . "' -->\n";
        echo "<!-- Password length: " . strlen($password) . " -->\n";

        $login = trim($login);

        if (empty($login) || empty($password)) {
            echo "<!-- ERREUR: Identifiants vides -->\n";
            return ['success' => false, 'message' => 'Identifiants manquants.'];
        }

        // Recherche l'utilisateur
        $user = $this->findUserByLogin($login);

        if (!$user) {
            echo "<!-- ERREUR: Utilisateur non trouvé dans la base -->\n";
            return ['success' => false, 'message' => 'Identifiant ou mot de passe incorrect.'];
        }

        echo "<!-- Hash stocké en BDD: " . htmlspecialchars(substr($user['Mdp'], 0, 20)) . "... -->\n";

        // Vérifie le mot de passe
        $passwordMatch = password_verify($password, $user['Mdp']);
        echo "<!-- password_verify result: " . ($passwordMatch ? 'TRUE ✓' : 'FALSE ✗') . " -->\n";

        if (!$passwordMatch) {
            echo "<!-- ERREUR: Mot de passe incorrect -->\n";
            return ['success' => false, 'message' => 'Identifiant ou mot de passe incorrect.'];
        }

        echo "<!-- ✓✓✓ AUTHENTIFICATION RÉUSSIE ✓✓✓ -->\n";
        echo "<!-- ============ AUTHENTICATE END ============ -->\n";

        return [
            'success' => true,
            'message' => 'Connexion réussie.',
            'user' => [
                'username' => $user['Username'],
                'email' => $user['Email']
            ]
        ];
    }

    /**
     * Met à jour le mot de passe
     */
    public static function updatePassword(int $userId, string $hashedPassword): bool
    {
        if (empty($hashedPassword)) {
            return false;
        }

        try {
            $pdo = self::getConnection();
            $sql = "UPDATE User SET Mdp = :password WHERE id = :userId";
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
     * Supprime un utilisateur
     */
    public static function deleteUser(int $userId): bool
    {
        try {
            $pdo = self::getConnection();
            $sql = "DELETE FROM User WHERE id = :userId";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);

            return $stmt->execute();

        } catch (PDOException $e) {
            error_log("Erreur deleteUser : " . $e->getMessage());
            return false;
        }
    }
    /**
     * Supprime un utilisateur par son username
     */
    public function deleteUserByUsername(string $username): bool
    {
        try {
            $pdo = self::getConnection();
            $sql = "DELETE FROM User WHERE Username = :username";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':username', $username, PDO::PARAM_STR);

            return $stmt->execute();

        } catch (PDOException $e) {
            error_log("Erreur deleteUserByUsername : " . $e->getMessage());
            return false;
        }
    }


    /**
     * Met à jour le nom d'utilisateur
     */
    public function updateUsername(string $currentUsername, string $newUsername): array
    {
        $newUsername = trim($newUsername);

        if (empty($newUsername)) {
            return ['success' => false, 'message' => 'Le nom d\'utilisateur ne peut pas être vide.'];
        }

        try {
            $pdo = self::getConnection();
            $sql = "UPDATE User SET Username = :newUsername WHERE Username = :currentUsername";
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
     * Met à jour l'email
     */
    public function updateEmail(string $username, string $newEmail): array
    {
        $newEmail = trim($newEmail);

        if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Email invalide.'];
        }

        try {
            $pdo = self::getConnection();
            $sql = "UPDATE User SET Email = :newEmail WHERE Username = :username";
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
     * Vérifie le statut de la connexion à la base de données
     */
    public function getDbStatus(): array
    {
        try {
            $pdo = self::getConnection();

            // Vérifie si la table User existe
            $stmt = $pdo->query("SHOW TABLES LIKE 'User'");
            $tableExists = $stmt->rowCount() > 0;

            if (!$tableExists) {
                return [
                    'available' => false,
                    'message' => "La table 'User' n'existe pas.",
                    'details' => 'Connexion OK mais table manquante'
                ];
            }

            return [
                'available' => true,
                'message' => 'Connexion à la base de données opérationnelle.',
                'details' => "PDO MySQL OK | Table 'User' existe"
            ];

        } catch (PDOException $e) {
            return [
                'available' => false,
                'message' => 'Erreur de connexion à la base de données.',
                'details' => $e->getMessage()
            ];
        }
    }
}
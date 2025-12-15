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

            } catch (PDOException $e) {
                die("Erreur de connexion à la base de données : " . $e->getMessage());
            }
        }

        return self::$connection;
    }

    /**
     * Récupère l'ID d'un utilisateur par son username
     */
    public function getUserIdByUsername(string $username): ?int
    {
        try {
            $pdo = self::getConnection();
            // ✓ CORRIGÉ : Uniformisation avec minuscules
            $stmt = $pdo->prepare("SELECT id FROM User WHERE username = :username");
            $stmt->execute(['username' => $username]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return $result ? (int)$result['id'] : null;
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
        try {
            $pdo = self::getConnection();

            // 1. Vérifier si l'email existe déjà
            // ✓ CORRIGÉ : minuscules cohérentes
            $stmt = $pdo->prepare("SELECT id FROM User WHERE email = :email LIMIT 1");
            $stmt->execute(['email' => $email]);

            if ($stmt->fetch()) {
                return [
                    'success' => false,
                    'message' => 'Cette adresse email est déjà utilisée.'
                ];
            }

            // 2. Vérifier si le nom d'utilisateur existe déjà
            // ✓ CORRIGÉ : minuscules cohérentes
            $stmt = $pdo->prepare("SELECT id FROM User WHERE username = :username LIMIT 1");
            $stmt->execute(['username' => $username]);

            if ($stmt->fetch()) {
                return [
                    'success' => false,
                    'message' => 'Ce nom d\'utilisateur est déjà pris.'
                ];
            }

            // 3. Hasher le mot de passe
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // 4. Insérer le nouvel utilisateur
            // ✓ CORRIGÉ : minuscules cohérentes
            $stmt = $pdo->prepare(
                "INSERT INTO User (username, email, mdp) 
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
                'message' => 'Une erreur est survenue lors de la création du compte. ' . $e->getMessage()
            ];
        }
    }

    /**
     * Recherche un utilisateur par son username OU son email
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
                    FROM User 
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
     * Authentifie un utilisateur
     */
    public function authenticate(string $login, string $password): array
    {
        $login = trim($login);

        if (empty($login) || empty($password)) {
            return ['success' => false, 'message' => 'Identifiants manquants.'];
        }

        // Recherche l'utilisateur
        $user = $this->findUserByLogin($login);

        if (!$user) {
            return ['success' => false, 'message' => 'Identifiant ou mot de passe incorrect.'];
        }

        // Vérifie le mot de passe (clé cohérente avec SELECT)
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
     * Met à jour le mot de passe
     */
    public static function updatePassword(int $userId, string $hashedPassword): bool
    {
        if (empty($hashedPassword)) {
            return false;
        }

        try {
            $pdo = self::getConnection();
            // ✓ CORRIGÉ : minuscules cohérentes
            $sql = "UPDATE User SET mdp = :password WHERE id = :userId";
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
            // ✓ CORRIGÉ : minuscules cohérentes
            $sql = "DELETE FROM User WHERE username = :username";
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
            // ✓ CORRIGÉ : minuscules cohérentes
            $sql = "UPDATE User SET username = :newUsername WHERE username = :currentUsername";
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
            // ✓ CORRIGÉ : minuscules cohérentes
            $sql = "UPDATE User SET email = :newEmail WHERE username = :username";
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
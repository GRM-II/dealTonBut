<?php

final class User_model
{
    private static ?PDO $connection = null;

    // Paramètres de connexion à la base de données
    private const DB_HOST = 'yms-10.h.filess.io';
    private const DB_NAME = 'bdDealTonBut_triangleup';
    private const DB_USER = 'bdDealTonBut_triangleup';
    private const DB_PASS = 'a2aca2a35f059450391954de64d656284de558d1';
    private const DB_PORT = 61032;

    /**
     * Récupère ou crée la connexion PDO unique (pattern Singleton)
     */
    public static function getConnection(): PDO
    {
        if (self::$connection === null) {
            try {
                $dsn = sprintf(
                    "mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4",
                    self::DB_HOST,
                    self::DB_PORT,
                    self::DB_NAME
                );

                self::$connection = new PDO($dsn, self::DB_USER, self::DB_PASS);
                self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            } catch (PDOException $e) {
                die("Erreur de connexion à la base de données : " . $e->getMessage());
            }
        }

        return self::$connection;
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
            $sql = "SELECT Username, Email, Mdp, Bio 
                    FROM User 
                    WHERE Username = :login OR Email = :login 
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

        // Vérifie le mot de passe
        if (!password_verify($password, $user['Mdp'])) {
            return ['success' => false, 'message' => 'Identifiant ou mot de passe incorrect.'];
        }

        // Authentification réussie
        return [
            'success' => true,
            'message' => 'Connexion réussie.',
            'user' => [
                'username' => $user['Username'],
                'email' => $user['Email'],
                'bio' => $user['Bio'] ?? ''
            ]
        ];
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
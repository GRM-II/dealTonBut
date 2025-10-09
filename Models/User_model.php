<?php

final class User_model
{
    private $_S_message = "Si tu vois ça, c'est que ça marche ! :D";

    // Configuration de la base de données MySQL
    private string $host = 'yms-10.h.filess.io';
    private string $dbname = 'bdDealTonBut_triangleup';
    private string $username = 'bdDealTonBut_triangleup';
    private string $password = 'a2aca2a35f059450391954de64d656284de558d1';
    private int $port = 61032;

    public function __construct()
    {
        // Ne lance pas d'exception ici: l'absence de pilote doit être gérée proprement
        try {
            $this->verifyConnection();
        } catch (\Throwable $e) {
            // Ignoré pour éviter un crash au chargement. Les erreurs seront renvoyées lors des actions.
        }
    }

    public function donneMessage()
    {
        return $this->_S_message;
    }

    private function canUsePdoMySql(): bool
    {
        if (!class_exists('PDO')) {
            return false;
        }
        try {
            $drivers = \PDO::getAvailableDrivers();
            return in_array('mysql', $drivers, true);
        } catch (\Throwable $e) {
            return false;
        }
    }

    private function getPdo(): \PDO
    {
        if (!$this->canUsePdoMySql()) {
            throw new \RuntimeException("Le pilote PDO MySQL (pdo_mysql) n'est pas disponible sur le serveur.");
        }
        $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4', $this->host, $this->port, $this->dbname);
        try {
            $pdo = new \PDO($dsn, $this->username, $this->password, [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (\PDOException $e) {
            throw new \RuntimeException('Erreur de connexion PDO MySQL: ' . $e->getMessage());
        }
        return $pdo;
    }

    private function hasMysqli(): bool
    {
        return extension_loaded('mysqli');
    }

    private function getMysqli(): \mysqli
    {
        if (!$this->hasMysqli()) {
            throw new \RuntimeException("L'extension MySQLi n'est pas disponible sur le serveur.");
        }

        $mysqli = @new \mysqli($this->host, $this->username, $this->password, $this->dbname, $this->port);

        if ($mysqli->connect_errno) {
            throw new \RuntimeException('Erreur de connexion MySQLi: ' . $mysqli->connect_error);
        }

        if (!$mysqli->set_charset('utf8mb4')) {
            throw new \RuntimeException('Impossible de définir le charset utf8mb4: ' . $mysqli->error);
        }

        return $mysqli;
    }

    /**
     * Vérifie que la connexion à la base de données fonctionne
     * et que la table 'User' existe
     */
    private function verifyConnection(): void
    {
        if ($this->canUsePdoMySql()) {
            $pdo = $this->getPdo();
            // Vérifie que la table existe
            $stmt = $pdo->query("SHOW TABLES LIKE 'User'");
            if ($stmt->rowCount() === 0) {
                throw new \RuntimeException("La table 'User' n'existe pas dans la base de données.");
            }
            return;
        }

        if ($this->hasMysqli()) {
            $mysqli = $this->getMysqli();
            $result = $mysqli->query("SHOW TABLES LIKE 'User'");
            if ($result->num_rows === 0) {
                $mysqli->close();
                throw new \RuntimeException("La table 'User' n'existe pas dans la base de données.");
            }
            $mysqli->close();
            return;
        }
    }

    public function createUser(string $username, string $email, string $password): array
    {
        // Validation de base
        $username = trim($username);
        $email = trim($email);
        if ($username === '' || $email === '' || $password === '') {
            return ['success' => false, 'message' => 'Champs requis manquants.'];
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Email invalide.'];
        }

        // Hash du mot de passe
        $hash = password_hash($password, PASSWORD_DEFAULT);

        // DEBUG: Log les valeurs
        error_log("Tentative création user - Username: $username, Email: $email");

        // Essaye d'abord PDO MySQL si disponible
        if ($this->canUsePdoMySql()) {
            try {
                $pdo = $this->getPdo();
                error_log("PDO: Connexion OK, préparation de la requête");
                $stmt = $pdo->prepare('INSERT INTO User (Username, Email, Mdp) VALUES (:u, :e, :p)');
                $stmt->bindValue(':u', $username, \PDO::PARAM_STR);
                $stmt->bindValue(':e', $email, \PDO::PARAM_STR);
                $stmt->bindValue(':p', $hash, \PDO::PARAM_STR);
                error_log("PDO: Exécution de la requête");
                $stmt->execute();
                $lastId = $pdo->lastInsertId();
                error_log("PDO: Utilisateur créé avec ID: $lastId");
                return ['success' => true, 'message' => 'Compte créé avec succès.', 'id' => $lastId];
            } catch (\PDOException $e) {
                $code = $e->getCode();
                $msg = $e->getMessage();
                error_log("PDO Exception: Code=$code, Message=$msg");
                // 23000 = integrity constraint violation (incl. duplicates)
                if ($code === '23000' || strpos($msg, '1062') !== false) {
                    if (stripos($msg, 'Username') !== false) {
                        return ['success' => false, 'message' => "Le nom d'utilisateur est déjà utilisé."];
                    }
                    if (stripos($msg, 'Email') !== false) {
                        return ['success' => false, 'message' => 'Cet email est déjà utilisé.'];
                    }
                    return ['success' => false, 'message' => 'Donnée en double.'];
                }
                return ['success' => false, 'message' => 'Erreur: ' . $msg];
            } catch (\Throwable $e) {
                error_log("PDO Throwable: " . $e->getMessage());
                return ['success' => false, 'message' => 'Erreur: ' . $e->getMessage()];
            }
        }

        // Sinon, tente MySQLi
        if ($this->hasMysqli()) {
            try {
                $mysqli = $this->getMysqli();
                error_log("MySQLi: Connexion OK, préparation de la requête");
                $stmt = $mysqli->prepare('INSERT INTO User (Username, Email, Mdp) VALUES (?, ?, ?)');
                if ($stmt === false) {
                    $err = $mysqli->error;
                    error_log("MySQLi: Erreur préparation - $err");
                    $mysqli->close();
                    return ['success' => false, 'message' => 'Erreur préparation de requête: ' . $err];
                }

                $stmt->bind_param('sss', $username, $email, $hash);
                error_log("MySQLi: Exécution de la requête");
                $ok = $stmt->execute();

                if ($ok) {
                    $lastId = $mysqli->insert_id;
                    error_log("MySQLi: Utilisateur créé avec ID: $lastId");
                    $stmt->close();
                    $mysqli->close();
                    return ['success' => true, 'message' => 'Compte créé avec succès.', 'id' => $lastId];
                }

                $errno = $stmt->errno ?: $mysqli->errno;
                $error = $stmt->error ?: $mysqli->error;
                error_log("MySQLi: Erreur execution - errno=$errno, error=$error");
                $stmt->close();
                $mysqli->close();

                if ($errno === 1062) {
                    if (stripos($error, 'Username') !== false) {
                        return ['success' => false, 'message' => "Le nom d'utilisateur est déjà utilisé."];
                    }
                    if (stripos($error, 'Email') !== false) {
                        return ['success' => false, 'message' => 'Cet email est déjà utilisé.'];
                    }
                    return ['success' => false, 'message' => 'Donnée en double.'];
                }

                return ['success' => false, 'message' => 'Erreur lors de la création du compte.'];
            } catch (\Throwable $e) {
                error_log("MySQLi Throwable: " . $e->getMessage());
                return ['success' => false, 'message' => 'Erreur: ' . $e->getMessage()];
            }
        }

        // Aucun pilote MySQL disponible
        return ['success' => false, 'message' => "Aucun pilote MySQL n'est disponible sur le serveur. Veuillez activer pdo_mysql ou mysqli."];
    }

    public function getDbStatus(): array
    {
        $pdo = $this->canUsePdoMySql();
        $mysqli = $this->hasMysqli();
        $available = ($pdo || $mysqli);
        $details = [];
        $details[] = $pdo ? 'pdo_mysql: OK' : 'pdo_mysql: indisponible';
        $details[] = $mysqli ? 'mysqli: OK' : 'mysqli: indisponible';

        // Vérifie aussi si la table existe
        $tableExists = false;
        try {
            if ($available) {
                $this->verifyConnection();
                $tableExists = true;
            }
        } catch (\Throwable $e) {
            $details[] = "Table 'User': " . $e->getMessage();
        }

        if ($tableExists) {
            $details[] = "Table 'User': existe";
        }

        $message = $available
            ? 'La connexion à la base MySQL est possible.'
            : "Aucun pilote MySQL n'est disponible sur le serveur. Veuillez activer pdo_mysql ou mysqli.";

        return [
            'available' => $available,
            'message' => $message,
            'details' => implode(' | ', $details),
        ];
    }

    /**
     * Récupère un utilisateur par nom d'utilisateur OU email
     */
    public function findUserByLogin(string $login): array
    {
        $login = trim($login);
        if ($login === '') {
            return [];
        }

        // Essaye PDO
        if ($this->canUsePdoMySql()) {
            try {
                $pdo = $this->getPdo();
                $stmt = $pdo->prepare('SELECT Username, Email, Mdp, Bio FROM User WHERE Username = :l OR Email = :l LIMIT 1');
                $stmt->bindValue(':l', $login, \PDO::PARAM_STR);
                $stmt->execute();
                $row = $stmt->fetch();
                return is_array($row) ? $row : [];
            } catch (\Throwable $e) {
                error_log('findUserByLogin PDO: ' . $e->getMessage());
                return [];
            }
        }

        // Sinon MySQLi
        if ($this->hasMysqli()) {
            try {
                $mysqli = $this->getMysqli();
                $stmt = $mysqli->prepare('SELECT Username, Email, Mdp, Bio FROM User WHERE Username = ? OR Email = ? LIMIT 1');
                if ($stmt === false) {
                    $mysqli->close();
                    return [];
                }
                $stmt->bind_param('ss', $login, $login);
                $stmt->execute();
                $result = $stmt->get_result();
                $row = $result ? $result->fetch_assoc() : null;
                $stmt->close();
                $mysqli->close();
                return is_array($row) ? $row : [];
            } catch (\Throwable $e) {
                error_log('findUserByLogin MySQLi: ' . $e->getMessage());
                return [];
            }
        }

        return [];
    }

    /**
     * Authentifie un utilisateur avec un login (username/email) et mot de passe
     */
    public function authenticate(string $login, string $password): array
    {
        $login = trim($login);
        if ($login === '' || $password === '') {
            return ['success' => false, 'message' => 'Identifiants manquants.'];
        }

        $user = $this->findUserByLogin($login);

        // Ajoute cette ligne pour voir ce que la base retourne
        error_log("Résultat findUserByLogin : " . print_r($user, true));

        if (!$user) {
            return ['success' => false, 'message' => 'Identifiant ou mot de passe incorrect.'];
        }

        $hash = $user['Mdp'] ?? '';
        if (!is_string($hash) || $hash === '' || !password_verify($password, $hash)) {
            return ['success' => false, 'message' => 'Identifiant ou mot de passe incorrect.'];
        }

        return [
            'success' => true,
            'message' => 'Connexion réussie.',
            'user' => [
                'username' => $user['Username'] ?? '',
                'email' => $user['Email'] ?? '',
                'bio' => $user['Bio'] ?? ''
            ]
        ];
    }
}
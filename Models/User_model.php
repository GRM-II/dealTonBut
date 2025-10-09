<?php

final class User_model
{
    private $_S_message = "Si tu vois ça, c'est que ça marche ! :D";

    // Configuration de la base de données MySQL
    private string $host = 'yms-10.h.filess.io';        // ex: localhost ou mysql.votre-hebergeur.com
    private string $dbname = 'bdDealTonBut_triangleup';
    private string $username = 'bdDealTonBut_triangleup';    // ex: root
    private string $password = 'a2aca2a35f059450391954de64d656284de558d1';
    private int $port = 61032;                                // changez si différent

    public function __construct()
    {
        // Ne lance pas d'exception ici: l'absence de pilote doit être gérée proprement
        try {
            $this->initializeDatabase();
        } catch (\Throwable $e) {
            // Ignoré pour éviter un crash au chargement. Les erreurs seront renvoyées lors des actions.
        }
    }

    public function donneMessage()
    {
        return $this->_S_message ;
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

    private function initializeDatabase(): void
    {
        $sql = 'CREATE TABLE IF NOT EXISTS users (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(100) NOT NULL UNIQUE,
                email VARCHAR(255) NOT NULL UNIQUE,
                password_hash VARCHAR(255) NOT NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci';

        if ($this->canUsePdoMySql()) {
            $pdo = $this->getPdo();
            try {
                $pdo->exec($sql);
            } finally {
                // rien de spécial à fermer pour PDO
            }
            return;
        }

        if ($this->hasMysqli()) {
            $mysqli = $this->getMysqli();
            if (!$mysqli->query($sql)) {
                $err = $mysqli->error;
                $mysqli->close();
                throw new \RuntimeException('Échec lors de la création de la table: ' . $err);
            }
            $mysqli->close();
            return;
        }

        // Aucun pilote disponible: ne rien faire ici, sera géré lors de l'action
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

        $hash = password_hash($password, PASSWORD_DEFAULT);

        // Essaye d'abord PDO MySQL si disponible
        if ($this->canUsePdoMySql()) {
            try {
                $pdo = $this->getPdo();
                $stmt = $pdo->prepare('INSERT INTO users (username, email, password_hash, created_at) VALUES (:u, :e, :p, NOW())');
                $stmt->bindValue(':u', $username, \PDO::PARAM_STR);
                $stmt->bindValue(':e', $email, \PDO::PARAM_STR);
                $stmt->bindValue(':p', $hash, \PDO::PARAM_STR);
                $stmt->execute();
                return ['success' => true, 'message' => 'Compte créé avec succès.'];
            } catch (\PDOException $e) {
                $code = $e->getCode(); // SQLSTATE or driver code
                $msg = $e->getMessage();
                // 23000 = integrity constraint violation (incl. duplicates)
                if ($code === '23000' || strpos($msg, '1062') !== false) {
                    if (stripos($msg, 'username') !== false) {
                        return ['success' => false, 'message' => "Le nom d'utilisateur est déjà utilisé."];
                    }
                    if (stripos($msg, 'email') !== false) {
                        return ['success' => false, 'message' => 'Cet email est déjà utilisé.'];
                    }
                    return ['success' => false, 'message' => 'Donnée en double.'];
                }
                return ['success' => false, 'message' => 'Erreur: ' . $msg];
            } catch (\Throwable $e) {
                return ['success' => false, 'message' => 'Erreur: ' . $e->getMessage()];
            }
        }

        // Sinon, tente MySQLi
        if ($this->hasMysqli()) {
            try {
                $mysqli = $this->getMysqli();
                $stmt = $mysqli->prepare('INSERT INTO users (username, email, password_hash, created_at) VALUES (?, ?, ?, NOW())');
                if ($stmt === false) {
                    $err = $mysqli->error;
                    $mysqli->close();
                    return ['success' => false, 'message' => 'Erreur préparation de requête: ' . $err];
                }

                $stmt->bind_param('sss', $username, $email, $hash);
                $ok = $stmt->execute();

                if ($ok) {
                    $stmt->close();
                    $mysqli->close();
                    return ['success' => true, 'message' => 'Compte créé avec succès.'];
                }

                $errno = $stmt->errno ?: $mysqli->errno;
                $error = $stmt->error ?: $mysqli->error;
                $stmt->close();
                $mysqli->close();

                if ($errno === 1062) { // entrée en double
                    if (stripos($error, 'username') !== false) {
                        return ['success' => false, 'message' => "Le nom d'utilisateur est déjà utilisé."];
                    }
                    if (stripos($error, 'email') !== false) {
                        return ['success' => false, 'message' => 'Cet email est déjà utilisé.'];
                    }
                    return ['success' => false, 'message' => 'Donnée en double.'];
                }

                return ['success' => false, 'message' => 'Erreur lors de la création du compte.'];
            } catch (\Throwable $e) {
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
        $message = $available
            ? 'La connexion à la base MySQL est possible.'
            : "Aucun pilote MySQL n'est disponible sur le serveur. Veuillez activer pdo_mysql ou mysqli.";
        return [
            'available' => $available,
            'message' => $message,
            'details' => implode(' | ', $details),
        ];
    }
}

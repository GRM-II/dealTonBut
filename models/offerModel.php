<?php

require_once 'core/envReader.php';

final class offerModel
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
                error_log("Erreur de connexion BDD : " . $e->getMessage());
                throw new RuntimeException("Impossible de se connecter à la base de données.");
            }
        }

        return self::$connection;
    }

    /**
     * Vérifie le statut de la connexion à la base de données
     *
     * @return array{available: bool, message: string, details?: string}
     */
    public function getDbStatus(): array
    {
        try {
            $pdo = self::getConnection();

            // Vérifie si la table User existe
            $stmt = $pdo->query("SHOW TABLES LIKE 'Offers'");
            $tableExists = $stmt !== false && $stmt->rowCount() > 0;

            if (!$tableExists) {
                return [
                    'available' => false,
                    'message' => "La table 'Offers' n'existe pas.",
                    'details' => 'Connexion OK mais table manquante'
                ];
            }

            return [
                'available' => true,
                'message' => 'Connexion à la base de données opérationnelle.',
                'details' => "PDO MySQL OK | Table 'Offers' existe"
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

    public static function getAllOffers(): ?array {
        try {
            $pdo = self::getConnection();

            $sql = "SELECT id, user_id
                    FROM Offers";

            $stmt = $pdo->prepare($sql);
            $stmt->execute();

            $offers = $stmt->fetch();

            return explode(' ',$offers) ?: null;

        } catch (PDOException $e) {
            error_log("Erreur getAllOffers : " . $e->getMessage());
            return null;
        }
        return null;
    }
}
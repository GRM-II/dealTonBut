<?php

require_once 'core/envReader.php';

final class offerModel
{
    private static ?PDO $connection = null;

    /**
     * Retrieves or creates the unique PDO connection (Singleton pattern)
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
                error_log("Database connection error: " . $e->getMessage());
                throw new RuntimeException("Unable to connect to the database.");
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

            // Check if the Offers table exists
            $stmt = $pdo->query("SHOW TABLES LIKE 'Offers'");
            $tableExists = $stmt !== false && $stmt->rowCount() > 0;

            if (!$tableExists) {
                return [
                    'available' => false,
                    'message' => "The 'Offers' table does not exist.",
                    'details' => 'Connection OK but table missing'
                ];
            }

            return [
                'available' => true,
                'message' => 'Database connection operational.',
                'details' => "PDO MySQL OK | 'Offers' table exists"
            ];

        } catch (PDOException $e) {
            error_log("getDbStatus error: " . $e->getMessage());
            return [
                'available' => false,
                'message' => 'Database connection error.',
                'details' => 'Check database configuration'
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
            error_log("getAllOffers error: " . $e->getMessage());
            return null;
        }
        return null;
    }
}
<?php

require_once 'core/envReader.php';

/**
 * Offer model class for managing database operations related to offers.
 *
 * This class implements the Singleton pattern for database connections
 * and provides methods to interact with the Offers table. It handles
 * database connection management, status checks, and offer retrieval operations.
 *
 * @final
 */
final class offerModel
{
    /**
     * Singleton PDO database connection instance.
     *
     * @var PDO|null
     */
    private static ?PDO $connection = null;

    /**
     * Retrieves or creates the unique PDO connection (Singleton pattern).
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
                error_log("Database connection error: " . $e->getMessage());
                throw new RuntimeException("Unable to connect to the database.");
            }
        }

        return self::$connection;
    }

    /**
     * Checks the database connection status and verifies the Offers table exists.
     *
     * This method performs two checks:
     * 1. Verifies that a database connection can be established
     * 2. Confirms that the 'Offers' table exists in the database
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

    /**
     * Retrieves all offers from the database.
     *
     * This method fetches the id and user_id fields from the Offers table.
     * Note: The current implementation only fetches the first row and splits
     * it by spaces, which may not be the intended behavior for retrieving
     * all offers.
     *
     * @return array|null An array of offer data split by spaces, or null if:
     *                    - No offers are found
     *                    - A database error occurs
     *                    - The query fails to execute
     */
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
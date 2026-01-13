<?php

require_once 'core/envReader.php';

/**
 * Offer model class for managing database operations related to offers.
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
     * Gets or initiates connection to the database
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
     * Checks database connection status
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

    /**
     * Fetches all the offers from the database
     *
     * Retrieves all available marketplace offers.
     *
     * @return array<int, array<string, mixed>> Array of offers with their details
     */
    public static function getAllOffers(): array {
        try {
            $pdo = self::getConnection();

            $sql = "SELECT o.id, o.user_id, o.title, o.description, o.category, o.price, o.created_at, u.username
                    FROM Offers o
                    LEFT JOIN Users u ON o.user_id = u.id
                    ORDER BY o.created_at DESC";

            $stmt = $pdo->prepare($sql);
            $stmt->execute();

            $offers = $stmt->fetchAll();

            return $offers ?: [];

        } catch (PDOException $e) {
            error_log("Erreur getAllOffers : " . $e->getMessage());
            return [];
        }
    }

    /**
     * Retrieves an offer by its identification.
     *
     * This method fetches complete offer information.
     *
     * @param string $offer_id The offer to search for
     * @return array{id: int|string, user_id: int|string,title: string, description: string, category: string, price: int, created_at: string}|null
     *         An associative array containing offer data, or null if not found or on error
     */
    public static function getOfferById(int $offer_id): array {
        try {
            $pdo = self::getConnection();

            $sql = "SELECT id, user_id, title, description, category, price, created_at
                    FROM Offers WHERE id = :offer_id";

            $stmt = $pdo->prepare($sql);
            $stmt->execute(['offer_id' => $offer_id]);

            $offer = $stmt->fetch();

            return $offer ?: [];

        } catch (PDOException $e) {
            error_log("Erreur getOfferById : " . $e->getMessage());
            return [];
        }
    }

    /**
     * Creates an offer from arguments
     *
     * @param int $user_id
     * @param string $title
     * @param string $description
     * @param float $price
     * @param string $category
     * @return array{success: bool, message: string}
     */
    public static function createOffer(int $user_id, string $title, string $description, float $price, string $category): array {
        if ($price < 0 || $price > 20) {
            return [
                'success' => false,
                'message' => 'Le prix doit être compris entre 0 et 20.'];
        }

        try {
            $pdo = self::getConnection();

            $sql = "INSERT INTO Offers (user_id, title, description, price, category) VALUES (:user_id, :title, :description, :price, :category)";

            $stmt = $pdo->prepare($sql);
            $result = $stmt->execute(['user_id' => $user_id,
                'title' => $title,
                'description' => $description,
                'price' => $price,
                'category' => $category
            ]);

            if ($result) {
                return ['success' => true,
                    'message' => 'Offre créée avec succès !'
                ];
            } else {
                return ['success' => false,
                    'message' => 'Erreur lors de la création de l\'offre.'
                ];
            }

        } catch (PDOException $e) {
            error_log("Erreur createOffer : " . $e->getMessage());
            return [];
        }
    }

    /**
     * Creates an offer from arguments
     *
     * @param array{id: int|string, user_id: int|string,title: string, description: string, category: string, price: int}
     * @param int $user_id
     * @return array{success: bool, message: string}
     */
    public static function purchaseOffer(array $offer, int $user_id): array{
        if ($offer['user_id'] == $user_id) {
            return ['success' => false,
                'message' => "Vous ne pouvez pas acheter votre propre offre."];
        }

        try {
            $pdo = self::getConnection();

            $stmt = $pdo->prepare("SELECT ". trim($offer['category']."_points") ." FROM Users WHERE id = :user_id;");
            $stmt->execute(['user_id' => $user_id]);
            $buyerPoints = $stmt->fetch(PDO::FETCH_ASSOC);

            if ((intval($buyerPoints[$offer['category']."_points"]) - intval($offer['price']) < 0)) {
                return ['success' => false,
                    'message' => "Vous n'avez pas assez de points"
                ];
            } else {
                $newBuyerPoints = intval($buyerPoints[$offer['category']."_points"]) - intval($offer['price']);
            }

            $stmt = $pdo->prepare("SELECT ". trim($offer['category']."_points") ." FROM Users WHERE id = :user_id;");
            $stmt->execute(['user_id' => $offer['user_id']]);
            $sellerPoints = $stmt->fetch(PDO::FETCH_ASSOC);
            $newSellerPoints = min(intval($sellerPoints[$offer['category']."_points"]) + intval($offer['price']), 20);

            $sql = "UPDATE Users SET ". trim($offer['category']."_points") ." = :newBuyerPoints WHERE id = :buyer_id;
                    UPDATE Users SET ". trim($offer['category']."_points") ." = :newSellerPoints WHERE id = :seller_id;
                    DELETE FROM Offers WHERE id = :offer_id;";

            $stmt = $pdo->prepare($sql);
            $result = $stmt->execute(['newBuyerPoints' => $newBuyerPoints,
                'newSellerPoints' => $newSellerPoints,
                'buyer_id' => $user_id,
                'seller_id' => $offer['user_id'],
                'offer_id' => $offer['id']
            ]);

            if ($result) {
                return ['success' => true,
                    'message' => 'Transaction effectuée avec succès !'
                ];
            } else {
                return ['success' => false,
                    'message' => 'Erreur lors de la transaction.'
                ];
            }

        } catch (PDOException $e) {
            error_log("Erreur purchaseOffer : " . $e->getMessage());
            return ['success' => false,
                'message' => "Erreur lors de l'éxecution de la transaction."];
        }
    }

    /**
     * Deletes an offer from database using identification
     *
     * @param int $id
     * @param int $user_id
     * @return array{success: bool, message: string}
     */
    public static function deleteOffer(int $id, int $user_id): array {
        try {
            $pdo = self::getConnection();
            $sql = "DELETE FROM Offers WHERE id = :id AND  user_id = :user_id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);

            $result = $stmt->execute();

            if ($result) {
                return [
                    'success' => true,
                    'message' => 'Offre supprimée avec succès !'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Erreur lors de la suppression de l\'offre.'
                ];
            }

        } catch (PDOException $e) {
            error_log("Erreur deleteOffer : " . $e->getMessage());
            return [];
        }
    }
}
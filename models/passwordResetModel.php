<?php

require_once 'models/userModel.php';

/**
 * Password reset model class for managing password reset token operations.
 *
 * This class handles the creation, validation, and deletion of password reset tokens.
 * Tokens are stored in the password_resets table and expire after 30 minutes (1800 seconds).
 * It provides methods to manage the complete password reset workflow including token
 * generation, validation, and user lookup by email.
 *
 * @final
 */
final class passwordResetModel
{
    /**
     * Creates a new password reset token for a user.
     *
     * This method generates a secure random token and stores it in the database
     * with an expiration time of 30 minutes. Any existing tokens for the user
     * are deleted before creating the new one to ensure only one active token
     * per user exists at a time.
     *
     * @param int $userId The ID of the user requesting the password reset
     * @return array{success: bool, token?: string, message: string} An associative array containing:
     *         - success: Boolean indicating if the token was created successfully
     *         - token: The generated reset token (only present if success is true)
     *         - message: A status message describing the result
     */
    public function createResetToken(int $userId): array
    {
        try {
            $pdo = userModel::getConnection();

            $token = bin2hex(random_bytes(32));
            $expiresAt = date('Y-m-d H:i:s', time() + 1800);

            $stmt = $pdo->prepare("DELETE FROM password_resets WHERE user_id = :userId");
            $stmt->execute(['userId' => $userId]);

            $stmt = $pdo->prepare(
                "INSERT INTO password_resets (user_id, token, expires_at) 
                 VALUES (:userId, :token, :expiresAt)"
            );

            $result = $stmt->execute([
                'userId' => $userId,
                'token' => $token,
                'expiresAt' => $expiresAt
            ]);

            if ($result) {
                return ['success' => true, 'token' => $token, 'message' => 'Token créé avec succès.'];
            }

            return ['success' => false, 'message' => 'Erreur lors de la création du token.'];

        } catch (PDOException $e) {
            error_log("Erreur createResetToken: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erreur lors de la création du token.'];
        }
    }

    /**
     * Validates a password reset token.
     *
     * This method checks if a token exists in the database and verifies that it
     * has not expired. If the token is expired, it is automatically deleted from
     * the database. The token expiration time is 30 minutes from creation.
     *
     * @param string $token The reset token to validate
     * @return array{valid: bool, userId?: int, message: string} An associative array containing:
     *         - valid: Boolean indicating if the token is valid and not expired
     *         - userId: The ID of the user associated with the token (only present if valid is true)
     *         - message: A status message describing the validation result
     */
    public function validateToken(string $token): array
    {
        try {
            $pdo = userModel::getConnection();

            $stmt = $pdo->prepare(
                "SELECT user_id, expires_at 
                 FROM password_resets 
                 WHERE token = :token 
                 LIMIT 1"
            );

            $stmt->execute(['token' => $token]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$result) {
                return ['valid' => false, 'message' => 'Token invalide ou expiré.'];
            }

            $expiresAt = strtotime($result['expires_at']);
            $now = time();

            if ($now > $expiresAt) {
                $this->deleteToken($token);
                return ['valid' => false, 'message' => 'Ce lien a expiré. Veuillez faire une nouvelle demande.'];
            }

            return ['valid' => true, 'userId' => (int)$result['user_id'], 'message' => 'Token valide.'];

        } catch (PDOException $e) {
            error_log("Erreur validateToken: " . $e->getMessage());
            return ['valid' => false, 'message' => 'Erreur lors de la validation du token.'];
        }
    }

    /**
     * Deletes a password reset token from the database.
     *
     * This method is typically called after a successful password reset or when
     * a token has expired. It removes the token record from the password_resets table.
     *
     * @param string $token The reset token to delete
     * @return bool True if the token was successfully deleted, false otherwise
     */
    public function deleteToken(string $token): bool
    {
        try {
            $pdo = userModel::getConnection();
            $stmt = $pdo->prepare("DELETE FROM password_resets WHERE token = :token");
            return $stmt->execute(['token' => $token]);
        } catch (PDOException $e) {
            error_log("Erreur deleteToken: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Retrieves user information by email address.
     *
     * This method looks up a user in the Users table by their email address
     * and returns their basic information. It is typically used during the
     * password reset request process to verify the user exists and retrieve
     * their user ID for token creation.
     *
     * @param string $email The email address to search for
     * @return array{id: int|string, username: string, email: string}|null An associative array containing
     *         the user's id, username, and email, or null if no user is found or an error occurs
     */
    public function getUserByEmail(string $email): ?array
    {
        try {
            $pdo = userModel::getConnection();

            $stmt = $pdo->prepare(
                "SELECT id, username, email 
                 FROM Users 
                 WHERE email = :email 
                 LIMIT 1"
            );

            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            return $user ?: null;

        } catch (PDOException $e) {
            error_log("Erreur getUserByEmail: " . $e->getMessage());
            return null;
        }
    }
}
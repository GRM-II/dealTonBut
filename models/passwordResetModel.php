<?php

require_once 'models/userModel.php';

final class PasswordResetModel
{
    /**
     * @return array{success: bool, token?: string, message: string}
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
     * @return array{valid: bool, userId?: int, message: string}
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
     * @return array{id: int|string, username: string, email: string}|null
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


<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once 'vendor/autoload.php';
require_once 'core/envReader.php';

final class emailService
{
    private string $smtpHost;
    private string $smtpPort;
    private string $smtpUsername;
    private string $smtpPassword;
    private string $smtpEncryption;

    /**
     * Constructeur du service d'envoi d'email
     */
    public function __construct()
    {
        $envReader = new envReader();

        $this->smtpHost = $envReader->getSmtpHost();
        $this->smtpPort = $envReader->getSmtpPort();
        $this->smtpUsername = $envReader->getSmtpUsername();
        $this->smtpPassword = $envReader->getSmtpPassword();
        $this->smtpEncryption = $envReader->getSmtpEncryption();
    }

    /**
     * Envoie un email de réinitialisation de mot de passe
     *
     * @param string $toEmail L'adresse email du destinataire
     * @param string $token Le token de réinitialisation
     * @return array{success: bool, message: string} Résultat de l'envoi
     */
    public function sendPasswordResetEmail(string $toEmail, string $token): array
    {
        try {
            $mail = new PHPMailer(true);

            $mail->isSMTP();
            $mail->Host = $this->smtpHost;
            $mail->SMTPAuth = true;
            $mail->Username = $this->smtpUsername;
            $mail->Password = $this->smtpPassword;
            $mail->SMTPSecure = $this->smtpEncryption;
            $mail->Port = (int)$this->smtpPort;
            $mail->CharSet = 'UTF-8';

            $mail->setFrom($this->smtpUsername, 'DealTonBut');
            $mail->addAddress($toEmail);

            $resetLink = $this->getResetLink($token);

            $mail->isHTML(true);
            $mail->Subject = 'Réinitialisation de votre mot de passe - DealTonBut';
            $mail->Body = $this->getEmailTemplate($resetLink);
            $mail->AltBody = "Bonjour,\n\nVous avez demandé la réinitialisation de votre mot de passe.\n\nCliquez sur le lien suivant :\n$resetLink\n\nCe lien expire dans 30 minutes.\n\nCordialement,\nL'équipe DealTonBut";

            $mail->send();

            return ['success' => true, 'message' => 'Email envoyé avec succès.'];

        } catch (Exception $e) {
            error_log("Erreur envoi email : " . $mail->ErrorInfo);
            return ['success' => false, 'message' => 'Erreur lors de l\'envoi de l\'email : ' . $mail->ErrorInfo];
        }
    }

    /**
     * Génère le lien de réinitialisation de mot de passe
     *
     * @param string $token Le token de réinitialisation
     * @return string L'URL complète de réinitialisation
     */
    private function getResetLink(string $token): string
    {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        return "$protocol://$host/index.php?controller=user&action=resetPassword&token=" . urlencode($token);
    }

    /**
     * Génère le template HTML de l'email de réinitialisation
     *
     * @param string $resetLink Le lien de réinitialisation à inclure
     * @return string Le contenu HTML de l'email
     */
    private function getEmailTemplate(string $resetLink): string
    {
        return <<<HTML
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background-color: #1360AA; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0;">
        <h1 style="margin: 0;">DealTonBut</h1>
    </div>
    
    <div style="background-color: #f4f4f4; padding: 30px; border-radius: 0 0 5px 5px;">
        <h2 style="color: #1360AA;">Réinitialisation de votre mot de passe</h2>
        
        <p>Bonjour,</p>
        
        <p>Vous avez demandé la réinitialisation de votre mot de passe sur DealTonBut.</p>
        
        <p>Cliquez sur le bouton ci-dessous pour créer un nouveau mot de passe :</p>
        
        <div style="text-align: center; margin: 30px 0;">
            <a href="$resetLink" 
               style="background-color: #1360AA; 
                      color: white; 
                      padding: 15px 30px; 
                      text-decoration: none; 
                      border-radius: 5px; 
                      display: inline-block;
                      font-weight: bold;">
                Réinitialiser mon mot de passe
            </a>
        </div>
        
        <p style="color: #666; font-size: 14px;">
            <strong>Ce lien expirera dans 30 minutes.</strong>
        </p>
        
        <p style="color: #666; font-size: 14px;">
            Si le bouton ne fonctionne pas, copiez ce lien :
            <br>
            <a href="$resetLink" style="color: #1360AA; word-break: break-all;">$resetLink</a>
        </p>
        
        <hr style="border: none; border-top: 1px solid #ddd; margin: 30px 0;">
        
        <p style="color: #999; font-size: 12px;">
            Si vous n'avez pas demandé cette réinitialisation, ignorez cet email.
        </p>
    </div>
</body>
</html>
HTML;
    }
}


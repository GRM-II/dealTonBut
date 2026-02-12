<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../core/envReader.php';

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
        $originalMaxExecutionTime = ini_get('max_execution_time');
        $originalSocketTimeout = ini_get('default_socket_timeout');

        set_time_limit(300); // 5 minutes
        ini_set('default_socket_timeout', '180'); // 3 minutes

        try {
            $mail = new PHPMailer(true);

            $mail->isSMTP();
            $mail->Host = $this->smtpHost;
            $mail->SMTPAuth = true;
            $mail->Username = $this->smtpUsername;
            $mail->Password = $this->smtpPassword;

            if ($this->smtpPort === '587') {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            } elseif ($this->smtpPort === '465') {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            } else {
                $mail->SMTPSecure = $this->smtpEncryption;
            }

            $mail->Port = (int)$this->smtpPort;
            $mail->CharSet = 'UTF-8';

            // Timeout très élevé car le serveur SMTP est très lent (84s observés)
            $mail->Timeout = 180; // 3 minutes

            // Options SSL/TLS pour éviter les erreurs de certificat
            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                ]
            ];

            // Mode debug SMTP (décommenter en cas de problème)
            // $mail->SMTPDebug = 2;
            // $mail->Debugoutput = function($str, $level) {
            //     error_log("SMTP Debug level $level: $str");
            // };

            error_log("Envoi email à $toEmail via {$this->smtpHost}:{$this->smtpPort} (encryption: {$mail->SMTPSecure})");

            $mail->setFrom($this->smtpUsername, 'DealTonBut');
            $mail->addAddress($toEmail);

            $resetLink = $this->getResetLink($token);

            $mail->isHTML(true);
            $mail->Subject = 'Réinitialisation de votre mot de passe - DealTonBut';
            $mail->Body = $this->getEmailTemplate($resetLink);
            $mail->AltBody = "Bonjour,\n\nVous avez demandé la réinitialisation de votre mot de passe.\n\nCliquez sur le lien suivant :\n$resetLink\n\nCe lien expire dans 30 minutes.\n\nCordialement,\nL'équipe DealTonBut";

            $mail->send();

            error_log("Email envoyé avec succès à $toEmail");

            return ['success' => true, 'message' => 'Email envoyé avec succès.'];

        } catch (Exception $e) {
            $errorDetails = "Erreur envoi email à $toEmail : " . $e->getMessage();
            error_log($errorDetails);

            return [
                'success' => false,
                'message' => 'Erreur lors de l\'envoi de l\'email. Vérifiez la configuration SMTP.'
            ];
        } finally {
            // Restaurer les timeouts originaux
            set_time_limit((int)$originalMaxExecutionTime);
            ini_set('default_socket_timeout', $originalSocketTimeout);
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
        ob_start();
        include __DIR__ . '/../views/mail/mailView.php';
        $content = ob_get_clean();
        return $content !== false ? $content : '';
    }
}


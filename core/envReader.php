<?php

class envReader
{
    private string $host;
    private string $user;
    private string $mdp;
    private string $port;
    private string $bd;
    private string $smtpHost;
    private string $smtpPort;
    private string $smtpUsername;
    private string $smtpPassword;
    private string $smtpEncryption;

    public function __construct()
    {
        // Absolute path to the .env file
        $envPath = __DIR__ . '/.env';

        // Checks that the file exists
        if (!file_exists($envPath)) {
            throw new Exception("Le fichier .env n'existe pas à l'emplacement : " . $envPath);
        }

        // Reads the entire file
        $content = file_get_contents($envPath);

        if ($content === false) {
            throw new Exception("Impossible de lire le fichier .env");
        }

        // Remove the UTF-8 BOM if present
        $content = str_replace("\xEF\xBB\xBF", '', $content);

        // Standardize line breaks
        $normalizedContent = preg_replace('/\r\n|\r|\n/', "\n", $content);
        if ($normalizedContent === null) {
            throw new Exception("Erreur lors de la normalisation du fichier .env");
        }

        // Separate by lines
        $lines = explode("\n", $normalizedContent);

        $env = [];
        foreach ($lines as $line) {
            // Cleans the line
            $line = trim($line);

            // Ignores the empty lines and commentaries
            if (empty($line) || $line[0] === '#') {
                continue;
            }

            // Parser with: or =
            $parts = preg_split('/\s*[:=]\s*/', $line, 2);

            if ($parts !== false && count($parts) === 2) {
                $key = trim($parts[0]);
                $value = trim($parts[1]);

                // Remove the quotation marks if present
                $value = trim($value, '"\'');

                $env[$key] = $value;
            }
        }

        // Key mapping
        $this->host = $env['host'] ?? $env['DB_HOST'] ?? '';
        $this->user = $env['user'] ?? $env['DB_USER'] ?? '';
        $this->mdp = $env['mdp'] ?? $env['DB_MDP'] ?? '';
        $this->port = $env['port'] ?? $env['DB_PORT'] ?? '';
        $this->bd = $env['bd'] ?? $env['DB_NAME'] ?? '';

        $this->smtpHost = $env['SMTP_HOST'] ?? '';
        $this->smtpPort = $env['SMTP_PORT'] ?? '';
        $this->smtpUsername = $env['SMTP_USERNAME'] ?? '';
        $this->smtpPassword = $env['SMTP_PASSWORD'] ?? '';
        $this->smtpEncryption = $env['SMTP_ENCRYPTION'] ?? '';

        // Verify that all values ​​are present
        if (empty($this->host) || empty($this->user) || empty($this->mdp) ||
            empty($this->port) || empty($this->bd)) {
            // Do not expose configuration details in error messages
            error_log("Configuration .env incomplète - vérifiez les variables: host, user, mdp, port, bd");
            throw new Exception("Configuration de la base de données incomplète. Veuillez vérifier le fichier .env");
        }
    }

    public function getHost(): string { return $this->host; }
    public function getUser(): string { return $this->user; }
    public function getMdp(): string { return $this->mdp; }
    public function getPort(): string { return $this->port; }
    public function getBd(): string { return $this->bd; }
    public function getSmtpHost(): string { return $this->smtpHost; }
    public function getSmtpPort(): string { return $this->smtpPort; }
    public function getSmtpUsername(): string { return $this->smtpUsername; }
    public function getSmtpPassword(): string { return $this->smtpPassword; }
    public function getSmtpEncryption(): string { return $this->smtpEncryption; }
}
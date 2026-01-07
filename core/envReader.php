<?php

/**
 * Environment configuration reader class.
 *
 * This class reads and parses a .env configuration file to extract database
 * and SMTP configuration parameters. It handles various .env file formats,
 * removes BOM markers, and validates that required database configuration
 * values are present.
 *
 * The parser supports both colon (:) and equals (=) as key-value separators,
 * handles comments (lines starting with #), and removes surrounding quotes
 * from values.
 */
class envReader
{
    /**
     * Database host address.
     *
     * @var string
     */
    private string $host;

    /**
     * Database username.
     *
     * @var string
     */
    private string $user;

    /**
     * Database password.
     *
     * @var string
     */
    private string $mdp;

    /**
     * Database port number.
     *
     * @var string
     */
    private string $port;

    /**
     * Database name.
     *
     * @var string
     */
    private string $bd;

    /**
     * SMTP server host address.
     *
     * @var string
     */
    private string $smtpHost;

    /**
     * SMTP server port number.
     *
     * @var string
     */
    private string $smtpPort;

    /**
     * SMTP authentication username.
     *
     * @var string
     */
    private string $smtpUsername;

    /**
     * SMTP authentication password.
     *
     * @var string
     */
    private string $smtpPassword;

    /**
     * SMTP encryption type (e.g., 'tls', 'ssl').
     *
     * @var string
     */
    private string $smtpEncryption;

    /**
     * Initializes the environment reader and loads configuration from .env file.
     *
     * @throws Exception If the .env file doesn't exist
     * @throws Exception If the .env file cannot be read
     * @throws Exception If line break normalization fails
     * @throws Exception If required database configuration is missing or incomplete
     */
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

        // Verify that all values are present
        if (empty($this->host) || empty($this->user) || empty($this->mdp) ||
            empty($this->port) || empty($this->bd)) {
            // Do not expose configuration details in error messages
            error_log("Configuration .env incomplète - vérifiez les variables: host, user, mdp, port, bd");
            throw new Exception("Configuration de la base de données incomplète. Veuillez vérifier le fichier .env");
        }
    }

    /**
     * Gets the database host address.
     *
     * @return string The database host
     */
    public function getHost(): string { return $this->host; }

    /**
     * Gets the database username.
     *
     * @return string The database username
     */
    public function getUser(): string { return $this->user; }

    /**
     * Gets the database password.
     *
     * @return string The database password
     */
    public function getMdp(): string { return $this->mdp; }

    /**
     * Gets the database port number.
     *
     * @return string The database port
     */
    public function getPort(): string { return $this->port; }

    /**
     * Gets the database name.
     *
     * @return string The database name
     */
    public function getBd(): string { return $this->bd; }

    /**
     * Gets the SMTP server host address.
     *
     * @return string The SMTP host
     */
    public function getSmtpHost(): string { return $this->smtpHost; }

    /**
     * Gets the SMTP server port number.
     *
     * @return string The SMTP port
     */
    public function getSmtpPort(): string { return $this->smtpPort; }

    /**
     * Gets the SMTP authentication username.
     *
     * @return string The SMTP username
     */
    public function getSmtpUsername(): string { return $this->smtpUsername; }

    /**
     * Gets the SMTP authentication password.
     *
     * @return string The SMTP password
     */
    public function getSmtpPassword(): string { return $this->smtpPassword; }

    /**
     * Gets the SMTP encryption type.
     *
     * @return string The SMTP encryption type (e.g., 'tls', 'ssl')
     */
    public function getSmtpEncryption(): string { return $this->smtpEncryption; }
}
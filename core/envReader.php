<?php

class envReader
{
    private string $host;
    private string $user;
    private string $mdp;
    private string $port;
    private string $bd;

    public function __construct()
    {
        // Chemin absolu du fichier .env
        $envPath = __DIR__ . '/.env';

        // Vérifier si le fichier existe
        if (!file_exists($envPath)) {
            throw new Exception("Le fichier .env n'existe pas à l'emplacement : " . $envPath);
        }

        // Lecture du contenu complet
        $content = file_get_contents($envPath);

        if ($content === false) {
            throw new Exception("Impossible de lire le fichier .env");
        }

        // Supprimer le BOM UTF-8 si présent
        $content = str_replace("\xEF\xBB\xBF", '', $content);

        // Normaliser les retours à la ligne
        $content = preg_replace('/\r\n|\r|\n/', "\n", $content);

        // Séparer par lignes
        $lines = explode("\n", $content);

        $env = [];
        foreach ($lines as $line) {
            // Nettoyer la ligne
            $line = trim($line);

            // Ignorer les lignes vides et les commentaires
            if (empty($line) || $line[0] === '#') {
                continue;
            }

            // Parser avec : ou =
            $parts = preg_split('/\s*[:=]\s*/', $line, 2);

            if (count($parts) === 2) {
                $key = trim($parts[0]);
                $value = trim($parts[1]);

                // Enlever les guillemets si présents
                $value = trim($value, '"\'');

                $env[$key] = $value;
            }
        }

        // Mapping des clés
        $this->host = $env['host'] ?? $env['DB_HOST'] ?? '';
        $this->user = $env['user'] ?? $env['DB_USER'] ?? '';
        $this->mdp = $env['mdp'] ?? $env['DB_MDP'] ?? '';
        $this->port = $env['port'] ?? $env['DB_PORT'] ?? '';
        $this->bd = $env['bd'] ?? $env['DB_NAME'] ?? '';

        // Vérifier que toutes les valeurs sont présentes
        if (empty($this->host) || empty($this->user) || empty($this->mdp) ||
            empty($this->port) || empty($this->bd)) {
            throw new Exception(
                "Configuration incomplète. Valeurs trouvées: " .
                "host='{$this->host}', user='{$this->user}', " .
                "mdp='" . (empty($this->mdp) ? 'vide' : 'défini') . "', " .
                "port='{$this->port}', bd='{$this->bd}'"
            );
        }
    }

    public function getHost(): string { return $this->host; }
    public function getUser(): string { return $this->user; }
    public function getMdp(): string { return $this->mdp; }
    public function getPort(): string { return $this->port; }
    public function getBd(): string { return $this->bd; }
}
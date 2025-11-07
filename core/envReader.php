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

        // Normaliser les fins de ligne (Windows/Linux/Mac)
        $content = str_replace(["\r\n", "\r"], "\n", $content);

        // Séparer par lignes
        $lines = explode("\n", $content);

        $env = [];
        foreach ($lines as $line) {
            // Nettoyer la ligne
            $line = trim($line);

            // Ignorer les lignes vides et les commentaires
            if (empty($line) || strpos($line, '#') === 0) {
                continue;
            }

            // Parser la ligne (chercher le premier =)
            $pos = strpos($line, '=');
            if ($pos !== false) {
                $key = trim(substr($line, 0, $pos));
                $value = trim(substr($line, $pos + 1));

                // Enlever les guillemets si présents
                $value = trim($value, '"\'');

                $env[$key] = $value;
            }
        }

        // Debug : afficher ce qui a été trouvé (à retirer après tests)
        if (empty($env)) {
            throw new Exception("Aucune variable trouvée dans le fichier .env. Contenu brut : " . var_export($content, true));
        }

        // Vérifier que toutes les clés nécessaires existent
        $requiredKeys = ['DB_HOST', 'DB_USER', 'DB_MDP', 'DB_PORT', 'DB_NAME'];
        foreach ($requiredKeys as $key) {
            if (!isset($env[$key])) {
                throw new Exception("La clé '$key' est manquante dans le fichier .env. Clés trouvées : " . implode(', ', array_keys($env)));
            }
        }

        // Assigner les valeurs
        $this->host = $env['DB_HOST'];
        $this->user = $env['DB_USER'];
        $this->mdp = $env['DB_MDP'];
        $this->port = $env['DB_PORT'];
        $this->bd = $env['DB_NAME'];
    }

    public function getHost(): string { return $this->host; }
    public function getUser(): string { return $this->user; }
    public function getMdp(): string { return $this->mdp; }
    public function getPort(): string { return $this->port; }
    public function getBd(): string { return $this->bd; }
}
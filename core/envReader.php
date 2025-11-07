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

        // Lecture du fichier et parsing des variables
        $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        if ($lines === false) {
            throw new Exception("Impossible de lire le fichier .env");
        }

        $env = [];
        foreach ($lines as $line) {
            // Ignorer les commentaires
            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            // Parser la ligne
            $parts = explode('=', $line, 2);
            if (count($parts) === 2) {
                $key = trim($parts[0]);
                $value = trim($parts[1]);
                $env[$key] = $value;
            }
        }

        // Vérifier que toutes les clés nécessaires existent
        $requiredKeys = ['DB_HOST', 'DB_USER', 'DB_MDP', 'DB_PORT', 'DB_NAME'];
        foreach ($requiredKeys as $key) {
            if (!isset($env[$key])) {
                throw new Exception("La clé '$key' est manquante dans le fichier .env");
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
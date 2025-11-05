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

        // Lecture du fichier
        $env = fopen($envPath, "r");

        if (!$env) {
            throw new Exception("Impossible d'ouvrir le fichier .env");
        }

        // Lecture et nettoyage des valeurs (suppression des espaces et retours à la ligne)
        parse_str(trim(fgets($env)), $output);
        $this->host = $output['DB_HOST'];
        parse_str(trim(fgets($env)), $output);
        $this->user = $output['DB_USER'];
        parse_str(trim(fgets($env)), $output);
        $this->mdp = $output['DB_MDP'];
        parse_str(trim(fgets($env)), $output);
        $this->port = $output['DB_PORT'];
        parse_str(trim(fgets($env)), $output);
        $this->bd = $output['DB_NAME'];

        fclose($env);
    }

    public function getHost(): string { return $this->host; }
    public function getUser(): string { return $this->user; }
    public function getMdp(): string { return $this->mdp; }
    public function getPort(): string { return $this->port; }
    public function getBd(): string { return $this->bd; }
}
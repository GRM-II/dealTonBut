<?php

class envReader
{
    // création des variables
    private string $host;
    private string $user;
    private string $mdp;
    private string $port;
    private string $bd;

    public function __construct()
    {
        // ouverture d'une tête de lecture dans le fichier d'indentification
        $env = fopen(".env", "r") or die("Unable to open file");

        $this->host = fgets($env);

        $this->user = fgets($env);

        $this->mdp = fgets($env);

        $this->port = fgets($env);

        $this->bd = fgets($env);
        // fermeture du fichier
        fclose($env);
    }

    public function getHost(): string {
        return $this->host;
    }

    public function getUser(): string {
        return $this->user;
    }

    public function getMdp(): string {
        return $this->mdp;
    }

    public function getPort(): string {
        return $this->port;
    }

    public function getBd(): string {
        return $this->bd;
    }
}

?>
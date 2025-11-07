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

        // Normaliser TOUS les types de retours à la ligne et espaces
        $content = preg_replace('/\r\n|\r|\n/', "\n", $content);

        // Remplacer les espaces multiples par un seul espace
        $content = preg_replace('/[ \t]+/', ' ', $content);

        // Séparer par lignes
        $lines = explode("\n", $content);

        $env = [];
        foreach ($lines as $lineNumber => $line) {
            // Nettoyer la ligne de tous les espaces en début/fin
            $line = trim($line);

            // Ignorer les lignes vides et les commentaires
            if (empty($line) || $line[0] === '#') {
                continue;
            }

            // Parser la ligne - chercher le signe =
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);

                $key = trim($key);
                $value = trim($value);

                // Enlever les guillemets si présents
                $value = trim($value, '"\'');

                if (!empty($key)) {
                    $env[$key] = $value;
                }
            }
        }

        // Debug amélioré
        if (empty($env)) {
            // Afficher les détails du contenu pour debug
            $debugInfo = "Contenu brut (longueur: " . strlen($content) . "):\n";
            $debugInfo .= var_export($content, true) . "\n\n";
            $debugInfo .= "Lignes trouvées: " . count($lines) . "\n";
            foreach ($lines as $i => $l) {
                $debugInfo .= "Ligne $i: [" . bin2hex($l) . "] = '" . $l . "'\n";
            }
            throw new Exception("Aucune variable trouvée dans le fichier .env.\n" . $debugInfo);
        }

        // Vérifier que toutes les clés nécessaires existent
        $requiredKeys = ['DB_HOST', 'DB_USER', 'DB_MDP', 'DB_PORT', 'DB_NAME'];
        $missingKeys = [];

        foreach ($requiredKeys as $key) {
            if (!isset($env[$key]) || empty($env[$key])) {
                $missingKeys[] = $key;
            }
        }

        if (!empty($missingKeys)) {
            throw new Exception(
                "Clés manquantes: " . implode(', ', $missingKeys) .
                ". Clés trouvées: " . implode(', ', array_keys($env)) .
                ". Valeurs: " . var_export($env, true)
            );
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
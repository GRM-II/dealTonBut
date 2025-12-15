<?php

final class view
{
    /**
     * Démarre le tampon de sortie principal
     */
    public static function openBuffer(): void
    {
        ob_start();
    }

    /**
     * Récupère et nettoie le contenu du tampon principal
     */
    public static function getBufferContent(): string
    {
        return ob_get_clean() ?: '';
    }

    /**
     * Affiche une vue avec les paramètres fournis
     *
     * @param string $S_localisation Chemin relatif de la vue (sans extension .php)
     * @param array<string, mixed> $A_parameters Paramètres à passer à la vue
     * @throws RuntimeException Si le fichier de vue n'existe pas
     */
    public static function show(string $S_localisation, array $A_parameters = []): void
    {
        $S_file = constants::viewsRepository() . $S_localisation . '.php';

        if (!is_readable($S_file)) {
            error_log("Vue introuvable: " . $S_file);
            throw new RuntimeException("La vue '$S_localisation' est introuvable.");
        }

        // Extraction des paramètres pour les rendre disponibles dans la vue
        $A_view = $A_parameters;

        // Démarrage d'un sous-tampon
        ob_start();
        include $S_file;
        ob_end_flush();
    }
}
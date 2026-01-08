<?php

final class view
{
    /**
     * Starts the main output buffer
     */
    public static function openBuffer(): void
    {
        ob_start();
    }

    /**
     *  Retrieves and cleans the contents of the main buffer
     */
    public static function getBufferContent(): string
    {
        return ob_get_clean() ?: '';
    }

    /**
     * Displays a view with the provided parameters
     *
     * @param string $S_localisation Relative path to the view (without the .php extension)
     * @param array<string, mixed> $A_parameters Parameters to pass to the view
     * @throws RuntimeException if the view file does not exist

     */
    public static function show(string $S_localisation, array $A_parameters = []): void
    {
        $S_file = constants::viewsRepository() . $S_localisation . '.php';

        if (!is_readable($S_file)) {
            error_log("Vue introuvable: " . $S_file);
            throw new RuntimeException("La vue '$S_localisation' est introuvable.");
        }

        // Extracting the parameters to make them available in the view
        $A_view = $A_parameters;

        // Starting a sub-buffer
        ob_start();
        include $S_file;
        ob_end_flush();
    }
}
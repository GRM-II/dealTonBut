<?php
/**
 * Point d'entrée de l'application DealTonBut
 */

// Configuration selon l'environnement
$isProduction = getenv('APP_ENV') === 'production';

if ($isProduction) {
    ini_set('display_errors', '0');
    ini_set('display_startup_errors', '0');
    error_reporting(0);
} else {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
}

// Démarrage de la session une seule fois
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'core/autoLoader.php';
require_once 'core/view.php';
require_once 'core/controller.php';
require_once 'core/exception/controllerException.php';

try {
    // Récupérer le contrôleur et l'action depuis les paramètres GET
    $S_controller = $_GET['controller'] ?? 'homepage';
    $S_action = $_GET['action'] ?? 'login';

    view::openBuffer();

    // Exécution du contrôleur et de l'action
    $C_controller = new controller($S_controller, $S_action);
    $C_controller->execute();

    // Récupère le contenu tamponné
    $displayContent = view::getBufferContent();
    $A_params = $C_controller->getParams();

    // Utilise le layout avec le contenu
    view::show('standard/layout', ['body' => $displayContent]);

} catch (ControllerException $e) {
    // Erreur spécifique aux contrôleurs
    error_log("ControllerException: " . $e->getMessage());
    http_response_code(404);
    echo $isProduction
        ? "Page non trouvée."
        : "Erreur contrôleur: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');

} catch (RuntimeException $e) {
    // Erreur d'exécution
    error_log("RuntimeException: " . $e->getMessage());
    http_response_code(500);
    echo $isProduction
        ? "Une erreur est survenue."
        : "Erreur: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');

} catch (Throwable $e) {
    // Toute autre erreur
    error_log("Exception: " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine());
    http_response_code(500);
    echo $isProduction
        ? "Une erreur inattendue est survenue."
        : "Erreur: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
}

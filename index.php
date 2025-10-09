<?php

// Ce fichier est le point d'entrée de votre application

require 'Core/AutoLoader.php';
require 'Core/View.php';
require 'Core/Controller.php';
require 'Core/Exception/ControllerException.php';

//////////////////////////////////////////////////////////////////////////
if (session_status() === PHP_SESSION_NONE) {
    // Démarre la session uniquement si elle n'est pas déjà démarrée
    session_start([
        'use_strict_mode' => true,
        'cookie_httponly' => true,
        'cookie_secure' => true,
        'cookie_samesite' => 'None'
    ]);
}

// Récupérer l'URI demandée
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = '/' . trim($uri, '/');

$S_controller = $_GET['controller'] ?? 'homepage';
$S_action = $_GET['action'] ?? 'login';

View::openBuffer();
// Exécution du contrôleur et de l'action
$C_controller = new Controller($S_controller, $S_action);
$C_controller->execute();

// Récupère le contenu tamponné
$displayContent = View::getBufferContent();
$A_params = $C_controller->getParams();

// Utilise le layout avec le contenu
View::show('Layout', ['body' => $displayContent]);

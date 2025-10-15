<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Activation des erreurs en développement
ini_set('display_errors', 1);
error_reporting(E_ALL);
error_log("Session started in index.php");
// Ce fichier est le point d'entrée de votre application

require 'core/autoLoader.php';
require 'core/view.php';
require 'core/controller.php';
require 'core/exception/controllerException.php';

// Configuration pour afficher les erreurs (à désactiver en production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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
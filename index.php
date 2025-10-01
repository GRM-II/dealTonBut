<?php

// Ce fichier est le point d'entrée de votre application

require 'Core/AutoLoader.php';
require 'Core/View.php';
/*
 url pour notre premier test MVC Hello World,
 nous n'avons pas d'action précisée on visera celle par défaut
 /index.php?ctrl=helloworld
 /helloworld
 /controleur/nom_action/whatever/whatever2/

*/
/*
    $S_controleur = isset($_GET['ctrl']) ? $_GET['ctrl'] : null;
    $S_action = isset($_GET['action']) ? $_GET['action'] : null;

    Vue::ouvrirTampon(); //  /Noyau/Vue.php : on ouvre le tampon d'affichage, les contrôleurs qui appellent des vues les mettront dedans
    $O_controleur = new Controleur($S_controleur, $S_action);
*/

$S_splitUrl = isset($_GET['url']) ? $_GET['url'] : null;
$A_postParams = isset($_POST) ? $_POST : null;

View::openBuffer(); // on ouvre le tampon d'affichage, les contrôleurs qui appellent des vues les mettront dedans

try {
    $O_controller = new Controller($S_splitUrl, $A_postParams);
    $O_controller->execute();
} catch (ControllerException $O_exception) {
    echo('An error occured : ' . $O_exception->getMessage());
}


// Les différentes sous-vues ont été "crachées" dans le tampon d'affichage, on les récupère
$contentToPrint = View::getBufferContent();

// On affiche le contenu dans la partie body du gabarit général
View::show('Layout', array('body' => $contentToPrint));
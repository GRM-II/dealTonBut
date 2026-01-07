<?php
final class homepageController
{
    public function login(): void
    {
        $model = new userModel();
        $status = $model->getDbStatus();
        view::show("homepageView", ['db_status' => $status]);
    }

    public function index(): void  // Adds the index method
    {
        $this->login();  // Redirects towards to login or shows the  homepage
    }

    // Simple diagnostic page to help activate MySQL drivers
    public function diagnostics(): void  // Adds the return type
    {
        $pdoAvailable = class_exists('PDO');
        $pdoDrivers = $pdoAvailable ? implode(', ', \PDO::getAvailableDrivers()) : 'PDO indisponible';
        $pdoMysql = $pdoAvailable && in_array('mysql', \PDO::getAvailableDrivers(), true);
        $mysqli = extension_loaded('mysqli');
        $phpVersion = PHP_VERSION;
        $iniFile = php_ini_loaded_file();

        header('Content-Type: text/html; charset=utf-8');
        echo '<!DOCTYPE html><html lang="fr"><head><meta charset="UTF-8"><title>Diagnostics</title></head><body>';
        echo '<h1>Diagnostics MySQL</h1>';
        echo '<ul>';
        echo '<li>Version de PHP: ' . htmlspecialchars($phpVersion, ENT_QUOTES, 'UTF-8') . '</li>';
        echo '<li>Fichier php.ini chargé: ' . htmlspecialchars((string)$iniFile, ENT_QUOTES, 'UTF-8') . '</li>';
        echo '<li>Extension mysqli: ' . ($mysqli ? 'OK' : 'indisponible') . '</li>';
        echo '<li>PDO: ' . ($pdoAvailable ? 'OK' : 'indisponible') . '</li>';
        echo '<li>Pilotes PDO disponibles: ' . htmlspecialchars($pdoDrivers, ENT_QUOTES, 'UTF-8') . '</li>';
        echo '<li>pdo_mysql: ' . ($pdoMysql ? 'OK' : 'indisponible') . '</li>';
        echo '</ul>';
        echo '<p><a href="?controller=homepage&action=login">Retour</a></p>';
        echo '</body></html>';
    }
}
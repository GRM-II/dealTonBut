<?php
require_once 'core/constants.php';

final class autoLoader
{
    /**
     * Charge une classe depuis le répertoire core
     */
    public static function loadClassCore(string $S_className): void
    {
        $S_file = constants::coreRepository() . "$S_className.php";
        static::_load($S_file);
    }

    /**
     * Charge une classe depuis le répertoire des exceptions
     */
    public static function loadClassException(string $S_className): void
    {
        $S_file = constants::exceptionsRepository() . "$S_className.php";
        static::_load($S_file);
    }

    /**
     * Charge une classe depuis le répertoire des modèles
     */
    public static function loadClassModel(string $S_className): void
    {
        $S_file = constants::modelsRepository() . "$S_className.php";
        static::_load($S_file);
    }

    /**
     * Charge une classe depuis le répertoire des vues
     */
    public static function loadClassView(string $S_className): void
    {
        $S_file = constants::viewsRepository() . "$S_className.php";
        static::_load($S_file);
    }

    /**
     * Charge une classe depuis le répertoire des contrôleurs
     */
    public static function loadClassController(string $S_className): void
    {
        $S_file = constants::controllersRepository() . "$S_className.php";
        static::_load($S_file);
    }

    /**
     * Charge une classe depuis le répertoire des services
     */
    public static function loadClassService(string $S_className): void
    {
        $S_file = constants::servicesRepository() . "$S_className.php";
        static::_load($S_file);
    }

    /**
     * Charge un fichier s'il existe et est lisible
     */
    private static function _load(string $S_fileToLoad): void
    {
        if (is_readable($S_fileToLoad)) {
            require_once $S_fileToLoad;
        }
    }
}

// Enregistrement des autoloaders
spl_autoload_register('autoLoader::loadClassCore');
spl_autoload_register('autoLoader::loadClassException');
spl_autoload_register('autoLoader::loadClassModel');
spl_autoload_register('autoLoader::loadClassView');
spl_autoload_register('autoLoader::loadClassController');
spl_autoload_register('autoLoader::loadClassService');

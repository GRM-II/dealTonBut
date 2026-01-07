<?php
require_once 'core/constants.php';

final class autoLoader
{
    /**
     * Loads a class from the core directory
     */
    public static function loadClassCore(string $S_className): void
    {
        $S_file = constants::coreRepository() . "$S_className.php";
        static::_load($S_file);
    }

    /**
     * Loads a class from the exception directory
     */
    public static function loadClassException(string $S_className): void
    {
        $S_file = constants::exceptionsRepository() . "$S_className.php";
        static::_load($S_file);
    }

    /**
     * Loads a class from the model directory
     */
    public static function loadClassModel(string $S_className): void
    {
        $S_file = constants::modelsRepository() . "$S_className.php";
        static::_load($S_file);
    }

    /**
     * Loads a class from the view directory
     */
    public static function loadClassView(string $S_className): void
    {
        $S_file = constants::viewsRepository() . "$S_className.php";
        static::_load($S_file);
    }

    /**
     * Loads a class from the controller directory
     */
    public static function loadClassController(string $S_className): void
    {
        $S_file = constants::controllersRepository() . "$S_className.php";
        static::_load($S_file);
    }

    /**
     * Loads a class from the service directory
     */
    public static function loadClassService(string $S_className): void
    {
        $S_file = constants::servicesRepository() . "$S_className.php";
        static::_load($S_file);
    }

    /**
     * Loads a class if it exists and is readable
     */
    private static function _load(string $S_fileToLoad): void
    {
        if (is_readable($S_fileToLoad)) {
            require_once $S_fileToLoad;
        }
    }
}

// Autoloader registration
spl_autoload_register('autoLoader::loadClassCore');
spl_autoload_register('autoLoader::loadClassException');
spl_autoload_register('autoLoader::loadClassModel');
spl_autoload_register('autoLoader::loadClassView');
spl_autoload_register('autoLoader::loadClassController');
spl_autoload_register('autoLoader::loadClassService');

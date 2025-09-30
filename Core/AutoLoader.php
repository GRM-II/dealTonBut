<?php
require 'Core/Constants.php';

final class AutoLoader
{
    public static function loadClassCore ($S_className)
    {
        $S_file = Constants::coreRepository() . "$S_className";
        return static::_load($S_file);
    }

    public static function loadClassException ($S_className)
    {
        $S_file = Constants::exceptionsRepository() . "$S_className";

        return static::_load($S_file);
    }

    public static function loadClassModel ($S_className)
    {
        $S_file = Constants::modelsRepository() . "$S_className";

        return static::_load($S_file);
    }

    public static function loadClassView ($S_className)
    {
        $S_file = Constants::viewsRepository() . "$S_className";

        return static::_load($S_file);
    }

    public static function loadClassController ($S_className)
    {
        $S_file = Constants::controllersRepository() . "$S_className.php";

        return static::_load($S_file);
    }
    private static function _load ($S_fileToLoad)
    {
        if (is_readable($S_fileToLoad))
        {
            require $S_fileToLoad;
        }
    }
}

// J'empile tout ce beau monde comme j'ai toujours appris à le faire... >:D
spl_autoload_register('AutoLoader::LoadClassCore');
spl_autoload_register('AutoLoader::loadClassException');
spl_autoload_register('AutoLoader::loadClassModel');
spl_autoload_register('AutoLoader::loadClassView');
spl_autoload_register('AutoLoader::loadClassController');
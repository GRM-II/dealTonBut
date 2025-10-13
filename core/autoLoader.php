<?php
require 'core/constants.php';

final class autoLoader
{
    public static function loadClassCore ($S_className)
    {
        $S_file = constants::coreRepository() . "$S_className";
        return static::_load($S_file);
    }

    public static function loadClassException ($S_className)
    {
        $S_file = constants::exceptionsRepository() . "$S_className";

        return static::_load($S_file);
    }

    public static function loadClassModel ($S_className)
    {
        $S_file = constants::modelsRepository() . "$S_className.php";

        return static::_load($S_file);
    }

    public static function loadClassView ($S_className)
    {
        $S_file = constants::viewsRepository() . "$S_className";

        return static::_load($S_file);
    }

    public static function loadClassController ($S_className)
    {
        $S_file = constants::controllersRepository() . "$S_className.php";

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
spl_autoload_register('autoLoader::LoadClassCore');
spl_autoload_register('autoLoader::loadClassException');
spl_autoload_register('autoLoader::loadClassModel');
spl_autoload_register('autoLoader::loadClassView');
spl_autoload_register('autoLoader::loadClassController');
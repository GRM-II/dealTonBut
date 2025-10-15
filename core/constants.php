<?php
final class constants
{
    // Les constantes relatives aux chemins

    const VIEWS_REPOSITORY       = '/views/';

    const MODELS_REPOSITORY      = '/models/';

    const CORE_REPOSITORY        = '/core/';

    const EXCEPTIONS_REPOSITORY  = '/core/exception/';

    const CONTROLLERS_REPOSITORY = '/controllers/';

    const STANDARD_REPOSITORY    = '/views/standard/';


    public static function rootRepository(): string {
        return realpath(__DIR__ . '/../');
    }

    public static function coreRepository(): string {
        return self::rootRepository() . self::CORE_REPOSITORY;
    }

    public static function exceptionsRepository(): string {
        return self::rootRepository() . self::EXCEPTIONS_REPOSITORY;
    }

    public static function viewsRepository(): string {
        return self::rootRepository() . self::VIEWS_REPOSITORY;
    }

    public static function modelsRepository(): string {
        return self::rootRepository() . self::MODELS_REPOSITORY;
    }

    public static function controllersRepository(): string {
        return self::rootRepository() . self::CONTROLLERS_REPOSITORY;
    }

    public static function standardRepository(): string {
        return self::rootRepository() . self::STANDARD_REPOSITORY;
    }


}
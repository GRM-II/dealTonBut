<?php
final class constants
{
    // Les constantes relatives aux chemins

    const VIEWS_REPOSITORY       = '/Views/';

    const MODELS_REPOSITORY      = '/Models/';

    const CORE_REPOSITORY        = '/Core/';

    const EXCEPTIONS_REPOSITORY  = '/Core/Exceptions/';

    const CONTROLLERS_REPOSITORY = '/Controllers/';


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


}
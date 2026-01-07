<?php
final class constants
{
    // Path constants
    private const VIEWS_REPOSITORY       = '/views/';
    private const MODELS_REPOSITORY      = '/models/';
    private const CORE_REPOSITORY        = '/core/';
    private const EXCEPTIONS_REPOSITORY  = '/core/exception/';
    private const CONTROLLERS_REPOSITORY = '/controllers/';
    private const SERVICES_REPOSITORY    = '/services/';
    private const STANDARD_REPOSITORY    = '/views/standard/';

    public static function rootRepository(): string {
        return realpath(__DIR__ . '/../') ?: '';
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

    public static function servicesRepository(): string {
        return self::rootRepository() . self::SERVICES_REPOSITORY;
    }

    public static function standardRepository(): string {
        return self::rootRepository() . self::STANDARD_REPOSITORY;
    }

}
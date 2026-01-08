<?php

/**
 * Constants Class
 *
 * Provides centralized path management for the application's directory structure.
 * Contains methods to retrieve absolute paths to various application directories
 * including views, models, controllers, core files, services, and exceptions.
 *
 * All paths are calculated relative to the root directory and use realpath()
 * for consistent path resolution across different operating systems.
 */
final class constants
{
    /**
     * Views directory path relative to root
     *
     * @var string
     */
    private const VIEWS_REPOSITORY = '/views/';

    /**
     * Models directory path relative to root
     *
     * @var string
     */
    private const MODELS_REPOSITORY = '/models/';

    /**
     * Core directory path relative to root
     *
     * @var string
     */
    private const CORE_REPOSITORY = '/core/';

    /**
     * Exceptions directory path relative to root
     *
     * @var string
     */
    private const EXCEPTIONS_REPOSITORY = '/core/exception/';

    /**
     * Controllers directory path relative to root
     *
     * @var string
     */
    private const CONTROLLERS_REPOSITORY = '/controllers/';

    /**
     * Services directory path relative to root
     *
     * @var string
     */
    private const SERVICES_REPOSITORY = '/services/';

    /**
     * Standard views directory path relative to root
     *
     * @var string
     */
    private const STANDARD_REPOSITORY = '/views/standard/';

    /**
     * Gets the application root directory path
     *
     * Returns the absolute path to the application's root directory,
     * calculated from the parent directory of the current file location.
     *
     * @return string Absolute path to root directory, empty string on failure
     */
    public static function rootRepository(): string
    {
        return realpath(__DIR__ . '/../') ?: '';
    }

    /**
     * Gets the core directory path
     *
     * Returns the absolute path to the core application files directory.
     *
     * @return string Absolute path to core directory
     */
    public static function coreRepository(): string
    {
        return self::rootRepository() . self::CORE_REPOSITORY;
    }

    /**
     * Gets the exceptions directory path
     *
     * Returns the absolute path to the exceptions directory within core.
     *
     * @return string Absolute path to exceptions directory
     */
    public static function exceptionsRepository(): string
    {
        return self::rootRepository() . self::EXCEPTIONS_REPOSITORY;
    }

    /**
     * Gets the views directory path
     *
     * Returns the absolute path to the application's views directory
     * containing all view templates.
     *
     * @return string Absolute path to views directory
     */
    public static function viewsRepository(): string
    {
        return self::rootRepository() . self::VIEWS_REPOSITORY;
    }

    /**
     * Gets the models directory path
     *
     * Returns the absolute path to the application's models directory
     * containing all data model classes.
     *
     * @return string Absolute path to models directory
     */
    public static function modelsRepository(): string
    {
        return self::rootRepository() . self::MODELS_REPOSITORY;
    }

    /**
     * Gets the controllers directory path
     *
     * Returns the absolute path to the application's controllers directory
     * containing all controller classes.
     *
     * @return string Absolute path to controllers directory
     */
    public static function controllersRepository(): string
    {
        return self::rootRepository() . self::CONTROLLERS_REPOSITORY;
    }

    /**
     * Gets the services directory path
     *
     * Returns the absolute path to the application's services directory
     * containing service classes for business logic.
     *
     * @return string Absolute path to services directory
     */
    public static function servicesRepository(): string
    {
        return self::rootRepository() . self::SERVICES_REPOSITORY;
    }

    /**
     * Gets the standard views directory path
     *
     * Returns the absolute path to the standard views directory
     * containing reusable standard view components.
     *
     * @return string Absolute path to standard views directory
     */
    public static function standardRepository(): string
    {
        return self::rootRepository() . self::STANDARD_REPOSITORY;
    }
}
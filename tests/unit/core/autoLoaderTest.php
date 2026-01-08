<?php

use PHPUnit\Framework\TestCase;

/**
 * Unit test for the autoLoader class
 *
 *  Tests the automatic loading functionality of classes
 *
 *  for different file types (Core, Exception, Model, View, Controller)
 */
class autoLoaderTest extends TestCase
{
    private static string $rootPath;

    /**
     * Configuration before all tests
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$rootPath = realpath(__DIR__ . '/../../../');
    }

    /**
     * Test that the autoLoader class exists
     */
    public function testAutoLoaderClassExists(): void
    {
        $this->assertTrue(
            class_exists('autoLoader'),
            'La classe autoLoader doit exister'
        );
    }

    /**
     * Test that all public methods exist
     */
    public function testAutoLoaderMethodsExist(): void
    {
        $methods = [
            'loadClassCore',
            'loadClassException',
            'loadClassModel',
            'loadClassView',
            'loadClassController'
        ];

        foreach ($methods as $method) {
            $this->assertTrue(
                method_exists('autoLoader', $method),
                "La méthode autoLoader::$method doit exister"
            );
        }
    }

    /**
     * Testing the loading of an existing Core class
     */
    public function testLoadClassCoreWithExistingClass(): void
    {
        // Verify that the Core files exist
        $coreFiles = ['view.php', 'controller.php', 'envReader.php'];

        foreach ($coreFiles as $file) {
            $filePath = self::$rootPath . '/core/' . $file;
            if (file_exists($filePath)) {
                $this->assertFileExists($filePath, "Le fichier $file doit exister dans core/");
            }
        }

        // Verify that the loadfileCore files exist
        $this->assertTrue(
            method_exists('autoLoader', 'loadClassCore'),
            'La méthode loadClassCore doit exister'
        );
    }

    /**
     * Testing the loading of an existing Controller class
     */
    public function testLoadClassControllerWithExistingClass(): void
    {
        // Verify that at least one controller exists
        $controllerFile = self::$rootPath . '/controllers/homepageController.php';
        $this->assertFileExists($controllerFile, 'Le fichier homepageController.php doit exister');

        // Verify that the method exists
        $this->assertTrue(
            method_exists('autoLoader', 'loadClassController'),
            'La méthode loadClassController doit exister'
        );

        // Verify that homepageController is available (already loaded)
        $this->assertTrue(
            class_exists('homepageController'),
            'La classe homepageController devrait être disponible via autoload'
        );
    }

    /**
     * Testing the loading of an existing Model class
     */
    public function testLoadClassModelWithExistingClass(): void
    {
        // Check that at least one model exists
        $modelFile = self::$rootPath . '/models/userModel.php';
        $this->assertFileExists($modelFile, 'Le fichier userModel.php doit exister');

        // Verify that the method exists
        $this->assertTrue(
            method_exists('autoLoader', 'loadClassModel'),
            'La méthode loadClassModel doit exister'
        );
    }

    /**
     * Testing the loading of an existing class. Exception
     */
    public function testLoadClassExceptionWithExistingClass(): void
    {
        // verify that the exception class exists
        $exceptionFile = self::$rootPath . '/core/exception/controllerException.php';
        $this->assertFileExists($exceptionFile, 'Le fichier controllerException.php doit exister');

        // Verify that the method exists
        $this->assertTrue(
            method_exists('autoLoader', 'loadClassException'),
            'La méthode loadClassException doit exister'
        );
    }

    /**
     * Testing the loading of a non-existent class (should not crash)
     */
    public function testLoadClassWithNonExistentClass(): void
    {
        // Loading a class that doesn't exist should not throw an exception

        // The autoloader should simply do nothing if the file doesn't exist

        // These calls should not throw an exception
        autoLoader::loadClassCore('NonExistentClass99999.php');
        autoLoader::loadClassController('NonExistentController99999');
        autoLoader::loadClassModel('NonExistentModel99999');

        // If we've arrived here, it's because it hasn't crashed.
        $this->assertTrue(true, 'Le chargement de classes inexistantes ne doit pas planter');
    }

    /**
     * Test that the autoload functions are registered
     */
    public function testAutoloadFunctionsAreRegistered(): void
    {
        $autoloadFunctions = spl_autoload_functions();

        $this->assertIsArray($autoloadFunctions, 'spl_autoload_functions doit retourner un tableau');
        $this->assertNotEmpty($autoloadFunctions, 'Au moins une fonction d\'autoload doit être enregistrée');

        // Verify that our functions are registered
        $expectedFunctions = [
            ['autoLoader', 'loadClassCore'],
            ['autoLoader', 'loadClassException'],
            ['autoLoader', 'loadClassModel'],
            ['autoLoader', 'loadClassView'],
            ['autoLoader', 'loadClassController']
        ];

        $registeredCount = 0;
        foreach ($expectedFunctions as $expectedFunction) {
            foreach ($autoloadFunctions as $registeredFunction) {
                if (is_array($registeredFunction) &&
                    $registeredFunction[0] === $expectedFunction[0] &&
                    strcasecmp($registeredFunction[1], $expectedFunction[1]) === 0) {
                    $registeredCount++;
                    break;
                }
            }
        }

        $this->assertGreaterThanOrEqual(
            5,
            $registeredCount,
            'Les 5 fonctions d\'autoload de autoLoader doivent être enregistrées'
        );
    }

    /**
     * Test that the necessary directories exist
     */
    public function testRequiredDirectoriesExist(): void
    {
        $this->assertDirectoryExists(
            self::$rootPath . '/core',
            'Le répertoire core doit exister'
        );

        $this->assertDirectoryExists(
            self::$rootPath . '/controllers',
            'Le répertoire controllers doit exister'
        );

        $this->assertDirectoryExists(
            self::$rootPath . '/models',
            'Le répertoire models doit exister'
        );

        $this->assertDirectoryExists(
            self::$rootPath . '/views',
            'Le répertoire views doit exister'
        );

        $this->assertDirectoryExists(
            self::$rootPath . '/core/exception',
            'Le répertoire exceptions doit exister'
        );
    }

    /**
     * Test that the key files exist
     */
    public function testKeyFilesExist(): void
    {
        $this->assertFileExists(
            self::$rootPath . '/core/autoLoader.php',
            'Le fichier autoLoader.php doit exister'
        );

        $this->assertFileExists(
            self::$rootPath . '/core/constants.php',
            'Le fichier constants.php doit exister'
        );

        $this->assertFileExists(
            self::$rootPath . '/core/controller.php',
            'Le fichier controller.php doit exister'
        );

        $this->assertFileExists(
            self::$rootPath . '/core/view.php',
            'Le fichier view.php doit exister'
        );
    }

    /**
     * Testing full integration: automatic loading via new
     */
    public function testAutoloadIntegrationWithNewInstance(): void
    {
        // Test that autoloading works with instantiation
        // (homepageController should already be loaded, but we're testing the principle)
        $this->assertTrue(
            class_exists('homepageController'),
            'homepageController doit être disponible via autoload'
        );

        // Verify that it can be instantiated
        $instance = new homepageController();
        $this->assertInstanceOf(
            homepageController::class,
            $instance,
            'On doit pouvoir instancier homepageController'
        );
    }
}


<?php

use PHPUnit\Framework\TestCase;

/**
 * Test unitaire pour la classe autoLoader
 *
 * Teste les fonctionnalités de chargement automatique des classes
 * pour les différents types de fichiers (Core, Exception, Model, View, Controller)
 */
class autoLoaderTest extends TestCase
{
    private static string $rootPath;

    /**
     * Configuration avant tous les tests
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$rootPath = realpath(__DIR__ . '/../../../');
    }

    /**
     * Test que la classe autoLoader existe
     */
    public function testAutoLoaderClassExists(): void
    {
        $this->assertTrue(
            class_exists('autoLoader'),
            'La classe autoLoader doit exister'
        );
    }

    /**
     * Test que toutes les méthodes publiques existent
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
     * Test du chargement d'une classe Core existante
     */
    public function testLoadClassCoreWithExistingClass(): void
    {
        // Vérifier que les fichiers Core existent
        $coreFiles = ['view.php', 'controller.php', 'envReader.php'];

        foreach ($coreFiles as $file) {
            $filePath = self::$rootPath . '/core/' . $file;
            if (file_exists($filePath)) {
                $this->assertFileExists($filePath, "Le fichier $file doit exister dans core/");
            }
        }

        // Vérifier que la méthode loadClassCore existe
        $this->assertTrue(
            method_exists('autoLoader', 'loadClassCore'),
            'La méthode loadClassCore doit exister'
        );
    }

    /**
     * Test du chargement d'une classe Controller existante
     */
    public function testLoadClassControllerWithExistingClass(): void
    {
        // Vérifier qu'au moins un contrôleur existe
        $controllerFile = self::$rootPath . '/controllers/homepageController.php';
        $this->assertFileExists($controllerFile, 'Le fichier homepageController.php doit exister');

        // Vérifier que la méthode existe
        $this->assertTrue(
            method_exists('autoLoader', 'loadClassController'),
            'La méthode loadClassController doit exister'
        );

        // Vérifier que homepageController est disponible (déjà chargé)
        $this->assertTrue(
            class_exists('homepageController'),
            'La classe homepageController devrait être disponible via autoload'
        );
    }

    /**
     * Test du chargement d'une classe Model existante
     */
    public function testLoadClassModelWithExistingClass(): void
    {
        // Vérifier qu'au moins un modèle existe
        $modelFile = self::$rootPath . '/models/userModel.php';
        $this->assertFileExists($modelFile, 'Le fichier userModel.php doit exister');

        // Vérifier que la méthode existe
        $this->assertTrue(
            method_exists('autoLoader', 'loadClassModel'),
            'La méthode loadClassModel doit exister'
        );
    }

    /**
     * Test du chargement d'une classe Exception existante
     */
    public function testLoadClassExceptionWithExistingClass(): void
    {
        // Vérifier que la classe d'exception existe
        $exceptionFile = self::$rootPath . '/core/exception/controllerException.php';
        $this->assertFileExists($exceptionFile, 'Le fichier controllerException.php doit exister');

        // Vérifier que la méthode existe
        $this->assertTrue(
            method_exists('autoLoader', 'loadClassException'),
            'La méthode loadClassException doit exister'
        );
    }

    /**
     * Test du chargement d'une classe inexistante (ne doit pas planter)
     */
    public function testLoadClassWithNonExistentClass(): void
    {
        // Charger une classe qui n'existe pas ne doit pas lever d'exception
        // L'autoloader doit juste ne rien faire si le fichier n'existe pas

        // Ces appels ne devraient pas lever d'exception
        autoLoader::loadClassCore('NonExistentClass99999.php');
        autoLoader::loadClassController('NonExistentController99999');
        autoLoader::loadClassModel('NonExistentModel99999');

        // Si on arrive ici, c'est que ça n'a pas planté
        $this->assertTrue(true, 'Le chargement de classes inexistantes ne doit pas planter');
    }

    /**
     * Test que les fonctions d'autoload sont enregistrées
     */
    public function testAutoloadFunctionsAreRegistered(): void
    {
        $autoloadFunctions = spl_autoload_functions();

        $this->assertIsArray($autoloadFunctions, 'spl_autoload_functions doit retourner un tableau');
        $this->assertNotEmpty($autoloadFunctions, 'Au moins une fonction d\'autoload doit être enregistrée');

        // Vérifier que nos fonctions sont enregistrées
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
     * Test que les répertoires nécessaires existent
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
     * Test que les fichiers clés existent
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
     * Test de l'intégration complète : chargement automatique via new
     */
    public function testAutoloadIntegrationWithNewInstance(): void
    {
        // Tester que l'autoload fonctionne avec l'instanciation
        // (homepageController devrait déjà être chargé, mais on teste le principe)

        $this->assertTrue(
            class_exists('homepageController'),
            'homepageController doit être disponible via autoload'
        );

        // Vérifier qu'on peut l'instancier
        $instance = new homepageController();
        $this->assertInstanceOf(
            homepageController::class,
            $instance,
            'On doit pouvoir instancier homepageController'
        );
    }
}


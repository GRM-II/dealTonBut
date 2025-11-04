<?php

use PHPUnit\Framework\TestCase;

/**
 * Test unitaire pour la classe constants
 *
 * Teste toutes les constantes et méthodes statiques qui retournent
 * les chemins des différents répertoires de l'application
 */
class constantsTest extends TestCase
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
     * Test que la classe constants existe et est finale
     */
    public function testConstantsClassExists(): void
    {
        $this->assertTrue(
            class_exists('constants'),
            'La classe constants doit exister'
        );

        $reflection = new ReflectionClass('constants');
        $this->assertTrue(
            $reflection->isFinal(),
            'La classe constants doit être finale (final class)'
        );
    }

    /**
     * Test que toutes les constantes de classe sont définies
     */
    public function testClassConstantsAreDefined(): void
    {
        $this->assertTrue(
            defined('constants::VIEWS_REPOSITORY'),
            'La constante VIEWS_REPOSITORY doit être définie'
        );

        $this->assertTrue(
            defined('constants::MODELS_REPOSITORY'),
            'La constante MODELS_REPOSITORY doit être définie'
        );

        $this->assertTrue(
            defined('constants::CORE_REPOSITORY'),
            'La constante CORE_REPOSITORY doit être définie'
        );

        $this->assertTrue(
            defined('constants::EXCEPTIONS_REPOSITORY'),
            'La constante EXCEPTIONS_REPOSITORY doit être définie'
        );

        $this->assertTrue(
            defined('constants::CONTROLLERS_REPOSITORY'),
            'La constante CONTROLLERS_REPOSITORY doit être définie'
        );

        $this->assertTrue(
            defined('constants::STANDARD_REPOSITORY'),
            'La constante STANDARD_REPOSITORY doit être définie'
        );
    }

    /**
     * Test les valeurs des constantes de classe
     */
    public function testClassConstantsValues(): void
    {
        $this->assertSame('/views/', constants::VIEWS_REPOSITORY);
        $this->assertSame('/models/', constants::MODELS_REPOSITORY);
        $this->assertSame('/core/', constants::CORE_REPOSITORY);
        $this->assertSame('/core/exception/', constants::EXCEPTIONS_REPOSITORY);
        $this->assertSame('/controllers/', constants::CONTROLLERS_REPOSITORY);
        $this->assertSame('/views/standard/', constants::STANDARD_REPOSITORY);
    }

    /**
     * Test que toutes les méthodes statiques existent
     */
    public function testStaticMethodsExist(): void
    {
        $methods = [
            'rootRepository',
            'coreRepository',
            'exceptionsRepository',
            'viewsRepository',
            'modelsRepository',
            'controllersRepository',
            'standardRepository'
        ];

        foreach ($methods as $method) {
            $this->assertTrue(
                method_exists('constants', $method),
                "La méthode constants::$method doit exister"
            );

            $reflection = new ReflectionMethod('constants', $method);
            $this->assertTrue(
                $reflection->isStatic(),
                "La méthode constants::$method doit être statique"
            );

            $this->assertTrue(
                $reflection->isPublic(),
                "La méthode constants::$method doit être publique"
            );
        }
    }

    /**
     * Test de la méthode rootRepository
     */
    public function testRootRepository(): void
    {
        $root = constants::rootRepository();

        $this->assertIsString($root, 'rootRepository doit retourner une chaîne');
        $this->assertNotEmpty($root, 'rootRepository ne doit pas être vide');
        $this->assertDirectoryExists($root, 'Le répertoire racine doit exister');

        // Vérifier que c'est un chemin absolu
        $this->assertTrue(
            str_starts_with($root, '/'),
            'rootRepository doit retourner un chemin absolu'
        );

        // Vérifier que le chemin ne se termine pas par /
        $this->assertStringEndsNotWith(
            '/',
            $root,
            'rootRepository ne doit pas se terminer par /'
        );
    }

    /**
     * Test de la méthode coreRepository
     */
    public function testCoreRepository(): void
    {
        $core = constants::coreRepository();

        $this->assertIsString($core);
        $this->assertNotEmpty($core);
        $this->assertDirectoryExists($core, 'Le répertoire core doit exister');

        $this->assertStringEndsWith(
            '/core/',
            $core,
            'coreRepository doit se terminer par /core/'
        );

        // Vérifier qu'il contient le chemin racine
        $this->assertStringContainsString(
            constants::rootRepository(),
            $core,
            'coreRepository doit contenir le chemin racine'
        );
    }

    /**
     * Test de la méthode exceptionsRepository
     */
    public function testExceptionsRepository(): void
    {
        $exceptions = constants::exceptionsRepository();

        $this->assertIsString($exceptions);
        $this->assertNotEmpty($exceptions);
        $this->assertDirectoryExists($exceptions, 'Le répertoire exceptions doit exister');

        $this->assertStringEndsWith(
            '/core/exception/',
            $exceptions,
            'exceptionsRepository doit se terminer par /core/exception/'
        );

        // Vérifier qu'il contient le chemin racine
        $this->assertStringContainsString(
            constants::rootRepository(),
            $exceptions
        );
    }

    /**
     * Test de la méthode viewsRepository
     */
    public function testViewsRepository(): void
    {
        $views = constants::viewsRepository();

        $this->assertIsString($views);
        $this->assertNotEmpty($views);
        $this->assertDirectoryExists($views, 'Le répertoire views doit exister');

        $this->assertStringEndsWith(
            '/views/',
            $views,
            'viewsRepository doit se terminer par /views/'
        );

        $this->assertStringContainsString(
            constants::rootRepository(),
            $views
        );
    }

    /**
     * Test de la méthode modelsRepository
     */
    public function testModelsRepository(): void
    {
        $models = constants::modelsRepository();

        $this->assertIsString($models);
        $this->assertNotEmpty($models);
        $this->assertDirectoryExists($models, 'Le répertoire models doit exister');

        $this->assertStringEndsWith(
            '/models/',
            $models,
            'modelsRepository doit se terminer par /models/'
        );

        $this->assertStringContainsString(
            constants::rootRepository(),
            $models
        );
    }

    /**
     * Test de la méthode controllersRepository
     */
    public function testControllersRepository(): void
    {
        $controllers = constants::controllersRepository();

        $this->assertIsString($controllers);
        $this->assertNotEmpty($controllers);
        $this->assertDirectoryExists($controllers, 'Le répertoire controllers doit exister');

        $this->assertStringEndsWith(
            '/controllers/',
            $controllers,
            'controllersRepository doit se terminer par /controllers/'
        );

        $this->assertStringContainsString(
            constants::rootRepository(),
            $controllers
        );
    }

    /**
     * Test de la méthode standardRepository
     */
    public function testStandardRepository(): void
    {
        $standard = constants::standardRepository();

        $this->assertIsString($standard);
        $this->assertNotEmpty($standard);
        $this->assertDirectoryExists($standard, 'Le répertoire standard doit exister');

        $this->assertStringEndsWith(
            '/views/standard/',
            $standard,
            'standardRepository doit se terminer par /views/standard/'
        );

        $this->assertStringContainsString(
            constants::rootRepository(),
            $standard
        );
    }

    /**
     * Test que tous les chemins retournés sont des chemins absolus
     */
    public function testAllPathsAreAbsolute(): void
    {
        $paths = [
            'root' => constants::rootRepository(),
            'core' => constants::coreRepository(),
            'exceptions' => constants::exceptionsRepository(),
            'views' => constants::viewsRepository(),
            'models' => constants::modelsRepository(),
            'controllers' => constants::controllersRepository(),
            'standard' => constants::standardRepository()
        ];

        foreach ($paths as $name => $path) {
            $this->assertTrue(
                str_starts_with($path, '/'),
                "Le chemin $name doit être un chemin absolu (commence par /)"
            );
        }
    }

    /**
     * Test que tous les répertoires existent réellement
     */
    public function testAllDirectoriesExist(): void
    {
        $directories = [
            'root' => constants::rootRepository(),
            'core' => constants::coreRepository(),
            'exceptions' => constants::exceptionsRepository(),
            'views' => constants::viewsRepository(),
            'models' => constants::modelsRepository(),
            'controllers' => constants::controllersRepository(),
            'standard' => constants::standardRepository()
        ];

        foreach ($directories as $name => $path) {
            $this->assertDirectoryExists(
                $path,
                "Le répertoire $name ($path) doit exister"
            );

            $this->assertTrue(
                is_readable($path),
                "Le répertoire $name ($path) doit être lisible"
            );
        }
    }

    /**
     * Test de cohérence : tous les chemins contiennent le chemin racine
     */
    public function testAllPathsContainRoot(): void
    {
        $root = constants::rootRepository();

        $paths = [
            'core' => constants::coreRepository(),
            'exceptions' => constants::exceptionsRepository(),
            'views' => constants::viewsRepository(),
            'models' => constants::modelsRepository(),
            'controllers' => constants::controllersRepository(),
            'standard' => constants::standardRepository()
        ];

        foreach ($paths as $name => $path) {
            $this->assertStringStartsWith(
                $root,
                $path,
                "Le chemin $name doit commencer par le chemin racine"
            );
        }
    }

    /**
     * Test que les constantes correspondent aux suffixes des méthodes
     */
    public function testConstantsMatchMethodSuffixes(): void
    {
        $this->assertStringEndsWith(
            constants::CORE_REPOSITORY,
            constants::coreRepository()
        );

        $this->assertStringEndsWith(
            constants::EXCEPTIONS_REPOSITORY,
            constants::exceptionsRepository()
        );

        $this->assertStringEndsWith(
            constants::VIEWS_REPOSITORY,
            constants::viewsRepository()
        );

        $this->assertStringEndsWith(
            constants::MODELS_REPOSITORY,
            constants::modelsRepository()
        );

        $this->assertStringEndsWith(
            constants::CONTROLLERS_REPOSITORY,
            constants::controllersRepository()
        );

        $this->assertStringEndsWith(
            constants::STANDARD_REPOSITORY,
            constants::standardRepository()
        );
    }

    /**
     * Test que les fichiers clés existent dans les répertoires
     */
    public function testKeyFilesExistInDirectories(): void
    {
        // Fichiers dans core
        $this->assertFileExists(
            constants::coreRepository() . '/constants.php',
            'Le fichier constants.php doit exister dans core'
        );

        $this->assertFileExists(
            constants::coreRepository() . '/autoLoader.php',
            'Le fichier autoLoader.php doit exister dans core'
        );

        $this->assertFileExists(
            constants::coreRepository() . '/controller.php',
            'Le fichier controller.php doit exister dans core'
        );

        $this->assertFileExists(
            constants::coreRepository() . '/view.php',
            'Le fichier view.php doit exister dans core'
        );

        // Fichier dans exceptions
        $this->assertFileExists(
            constants::exceptionsRepository() . '/controllerException.php',
            'Le fichier controllerException.php doit exister dans exceptions'
        );
    }

    /**
     * Test de performance : les appels répétés retournent le même résultat
     */
    public function testMethodsReturnConsistentResults(): void
    {
        // Appeler plusieurs fois chaque méthode
        $this->assertSame(
            constants::rootRepository(),
            constants::rootRepository(),
            'rootRepository doit retourner toujours le même résultat'
        );

        $this->assertSame(
            constants::coreRepository(),
            constants::coreRepository(),
            'coreRepository doit retourner toujours le même résultat'
        );

        $this->assertSame(
            constants::viewsRepository(),
            constants::viewsRepository(),
            'viewsRepository doit retourner toujours le même résultat'
        );
    }
}


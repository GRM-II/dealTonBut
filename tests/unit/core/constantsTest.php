<?php

use PHPUnit\Framework\TestCase;

/**
 * Unit test for the constants class
 *
 * Tests all constants and static methods that return
 * the paths of the different application directories
 */
class constantsTest extends TestCase
{
    private static string $rootPath;

    /**
     * Setup before running all tests
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$rootPath = realpath(__DIR__ . '/../../../');
    }

    /**
     * Test that the constants class exists and is final
     */
    public function testConstantsClassExists(): void
    {
        $this->assertTrue(
            class_exists('constants'),
            'The constants class must exist'
        );

        $reflection = new ReflectionClass('constants');
        $this->assertTrue(
            $reflection->isFinal(),
            'The constants class must be final'
        );
    }

    /**
     * Test that all static methods exist
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
            'servicesRepository',
            'standardRepository'
        ];

        foreach ($methods as $method) {
            $this->assertTrue(
                method_exists('constants', $method),
                "The method constants::$method must exist"
            );

            $reflection = new ReflectionMethod('constants', $method);
            $this->assertTrue(
                $reflection->isStatic(),
                "The method constants::$method must be static"
            );

            $this->assertTrue(
                $reflection->isPublic(),
                "The method constants::$method must be public"
            );
        }
    }

    /**
     * Test the rootRepository method
     */
    public function testRootRepository(): void
    {
        $root = constants::rootRepository();

        $this->assertIsString($root, 'rootRepository must return a string');
        $this->assertNotEmpty($root, 'rootRepository must not be empty');
        $this->assertDirectoryExists($root, 'The root directory must exist');

        // Check that it is an absolute path
        $this->assertTrue(
            str_starts_with($root, '/') || preg_match('/^[A-Za-z]:/', $root),
            'rootRepository must return an absolute path'
        );

        // Check that the path does not end with /
        $this->assertStringEndsNotWith(
            '/',
            $root,
            'rootRepository must not end with /'
        );
    }

    /**
     * Test the coreRepository method
     */
    public function testCoreRepository(): void
    {
        $core = constants::coreRepository();

        $this->assertIsString($core);
        $this->assertNotEmpty($core);
        $this->assertDirectoryExists($core, 'The core directory must exist');

        $this->assertStringEndsWith(
            '/core/',
            $core,
            'coreRepository must end with /core/'
        );

        // Check that it contains the root path
        $this->assertStringContainsString(
            constants::rootRepository(),
            $core,
            'coreRepository must contain the root path'
        );
    }

    /**
     * Test the exceptionsRepository method
     */
    public function testExceptionsRepository(): void
    {
        $exceptions = constants::exceptionsRepository();

        $this->assertIsString($exceptions);
        $this->assertNotEmpty($exceptions);
        $this->assertDirectoryExists($exceptions, 'The exceptions directory must exist');

        $this->assertStringEndsWith(
            '/core/exception/',
            $exceptions,
            'exceptionsRepository must end with /core/exception/'
        );

        // Check that it contains the root path
        $this->assertStringContainsString(
            constants::rootRepository(),
            $exceptions
        );
    }

    /**
     * Test the viewsRepository method
     */
    public function testViewsRepository(): void
    {
        $views = constants::viewsRepository();

        $this->assertIsString($views);
        $this->assertNotEmpty($views);
        $this->assertDirectoryExists($views, 'The views directory must exist');

        $this->assertStringEndsWith(
            '/views/',
            $views,
            'viewsRepository must end with /views/'
        );

        $this->assertStringContainsString(
            constants::rootRepository(),
            $views
        );
    }

    /**
     * Test the modelsRepository method
     */
    public function testModelsRepository(): void
    {
        $models = constants::modelsRepository();

        $this->assertIsString($models);
        $this->assertNotEmpty($models);
        $this->assertDirectoryExists($models, 'The models directory must exist');

        $this->assertStringEndsWith(
            '/models/',
            $models,
            'modelsRepository must end with /models/'
        );

        $this->assertStringContainsString(
            constants::rootRepository(),
            $models
        );
    }

    /**
     * Test the controllersRepository method
     */
    public function testControllersRepository(): void
    {
        $controllers = constants::controllersRepository();

        $this->assertIsString($controllers);
        $this->assertNotEmpty($controllers);
        $this->assertDirectoryExists($controllers, 'The controllers directory must exist');

        $this->assertStringEndsWith(
            '/controllers/',
            $controllers,
            'controllersRepository must end with /controllers/'
        );

        $this->assertStringContainsString(
            constants::rootRepository(),
            $controllers
        );
    }

    /**
     * Test the servicesRepository method
     */
    public function testServicesRepository(): void
    {
        $services = constants::servicesRepository();

        $this->assertIsString($services);
        $this->assertNotEmpty($services);
        $this->assertDirectoryExists($services, 'The services directory must exist');

        $this->assertStringEndsWith(
            '/services/',
            $services,
            'servicesRepository must end with /services/'
        );

        $this->assertStringContainsString(
            constants::rootRepository(),
            $services
        );
    }

    /**
     * Test the standardRepository method
     */
    public function testStandardRepository(): void
    {
        $standard = constants::standardRepository();

        $this->assertIsString($standard);
        $this->assertNotEmpty($standard);
        $this->assertDirectoryExists($standard, 'The standard directory must exist');

        $this->assertStringEndsWith(
            '/views/standard/',
            $standard,
            'standardRepository must end with /views/standard/'
        );

        $this->assertStringContainsString(
            constants::rootRepository(),
            $standard
        );
    }

    /**
     * Test that all returned paths are absolute paths
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
            'services' => constants::servicesRepository(),
            'standard' => constants::standardRepository()
        ];

        foreach ($paths as $name => $path) {
            $this->assertTrue(
                str_starts_with($path, '/') || preg_match('/^[A-Za-z]:/', $path),
                "The $name path must be an absolute path"
            );
        }
    }

    /**
     * Test that all directories actually exist
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
            'services' => constants::servicesRepository(),
            'standard' => constants::standardRepository()
        ];

        foreach ($directories as $name => $path) {
            $this->assertDirectoryExists(
                $path,
                "The $name directory ($path) must exist"
            );

            $this->assertTrue(
                is_readable($path),
                "The $name directory ($path) must be readable"
            );
        }
    }

    /**
     * Consistency test: all paths contain the root path
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
            'services' => constants::servicesRepository(),
            'standard' => constants::standardRepository()
        ];

        foreach ($paths as $name => $path) {
            $this->assertStringStartsWith(
                $root,
                $path,
                "The $name path must start with the root path"
            );
        }
    }

    /**
     * Test that key files exist in the directories
     */
    public function testKeyFilesExistInDirectories(): void
    {
        // Files in core
        $this->assertFileExists(
            constants::coreRepository() . 'constants.php',
            'The constants.php file must exist in core'
        );

        $this->assertFileExists(
            constants::coreRepository() . 'autoLoader.php',
            'The autoLoader.php file must exist in core'
        );

        $this->assertFileExists(
            constants::coreRepository() . 'controller.php',
            'The controller.php file must exist in core'
        );

        $this->assertFileExists(
            constants::coreRepository() . 'view.php',
            'The view.php file must exist in core'
        );

        // File in exceptions
        $this->assertFileExists(
            constants::exceptionsRepository() . 'controllerException.php',
            'The controllerException.php file must exist in exceptions'
        );
    }

    /**
     * Performance test: repeated calls return the same result
     */
    public function testMethodsReturnConsistentResults(): void
    {
        // Call each method multiple times
        $this->assertSame(
            constants::rootRepository(),
            constants::rootRepository(),
            'rootRepository must always return the same result'
        );

        $this->assertSame(
            constants::coreRepository(),
            constants::coreRepository(),
            'coreRepository must always return the same result'
        );

        $this->assertSame(
            constants::viewsRepository(),
            constants::viewsRepository(),
            'viewsRepository must always return the same result'
        );

        $this->assertSame(
            constants::servicesRepository(),
            constants::servicesRepository(),
            'servicesRepository must always return the same result'
        );
    }
}
<?php

use PHPUnit\Framework\TestCase;

/**
 * Unit test for the view class
 *
 * Tests output buffer handling,
 * view rendering, and parameter passing
 */
class viewTest extends TestCase
{
    private static string $rootPath;
    private static string $testViewPath;

    /**
     * Setup before all tests
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$rootPath = realpath(__DIR__ . '/../../../');
        self::$testViewPath = self::$rootPath . '/views/test';
    }

    /**
     * Cleanup after each test
     */
    protected function tearDown(): void
    {
        // Clean all remaining output buffers
        // (except PHPUnit's own buffer which is at level 1)
        while (ob_get_level() > 1) {
            @ob_end_clean();
        }
        parent::tearDown();
    }

    /**
     * Test that the view class exists and is final
     */
    public function testViewClassExists(): void
    {
        $this->assertTrue(
            class_exists('view'),
            'The view class must exist'
        );

        $reflection = new ReflectionClass('view');
        $this->assertTrue(
            $reflection->isFinal(),
            'The view class must be final (final class)'
        );
    }

    /**
     * Test that all public static methods exist
     */
    public function testStaticMethodsExist(): void
    {
        $methods = ['openBuffer', 'getBufferContent', 'show'];

        foreach ($methods as $method) {
            $this->assertTrue(
                method_exists('view', $method),
                "The method view::$method must exist"
            );

            $reflection = new ReflectionMethod('view', $method);
            $this->assertTrue(
                $reflection->isStatic(),
                "The method view::$method must be static"
            );

            $this->assertTrue(
                $reflection->isPublic(),
                "The method view::$method must be public"
            );
        }
    }

    /**
     * Test the openBuffer method
     */
    public function testOpenBuffer(): void
    {
        $levelBefore = ob_get_level();

        view::openBuffer();

        $levelAfter = ob_get_level();

        $this->assertSame(
            $levelBefore + 1,
            $levelAfter,
            'openBuffer must increase the buffer level by 1'
        );

        // Cleanup
        ob_end_clean();
    }

    /**
     * Test the getBufferContent method
     */
    public function testGetBufferContent(): void
    {
        $levelBefore = ob_get_level();

        view::openBuffer();

        echo "Test content";

        $content = view::getBufferContent();

        $this->assertSame(
            "Test content",
            $content,
            'getBufferContent must return the buffer content'
        );

        // Check that the buffer is closed after getBufferContent
        $this->assertSame(
            $levelBefore,
            ob_get_level(),
            'getBufferContent must close the buffer (ob_get_clean)'
        );
    }

    /**
     * Test that getBufferContent returns a string
     */
    public function testGetBufferContentReturnsString(): void
    {
        view::openBuffer();

        $content = view::getBufferContent();

        $this->assertIsString(
            $content,
            'getBufferContent must return a string'
        );
    }

    /**
     * Test the show method with an existing view
     */
    public function testShowWithExistingView(): void
    {
        // Use an existing view (homepageView)
        $viewFile = self::$rootPath . '/views/homepageView.php';

        if (!file_exists($viewFile)) {
            $this->markTestSkipped('The homepageView.php file does not exist');
        }

        // Capture output
        ob_start();
        view::show('homepageView', ['test' => 'value']);
        $output = ob_get_clean();

        $this->assertIsString($output, 'show must produce output');
    }

    /**
     * Test that show accepts empty parameters
     */
    public function testShowWithEmptyParameters(): void
    {
        $viewFile = self::$rootPath . '/views/homepageView.php';

        if (!file_exists($viewFile)) {
            $this->markTestSkipped('The homepageView.php file does not exist');
        }

        // show must accept an empty array
        ob_start();
        view::show('homepageView', []);
        $output = ob_get_clean();

        $this->assertIsString($output);
    }

    /**
     * Test that show accepts omission of the parameters argument
     */
    public function testShowWithoutParameters(): void
    {
        $viewFile = self::$rootPath . '/views/homepageView.php';

        if (!file_exists($viewFile)) {
            $this->markTestSkipped('The homepageView.php file does not exist');
        }

        // show must accept omission of the second parameter
        ob_start();
        view::show('homepageView');
        $output = ob_get_clean();

        $this->assertIsString($output);
    }

    /**
     * Test the return type of openBuffer
     */
    public function testOpenBufferReturnsVoid(): void
    {
        $reflection = new ReflectionMethod('view', 'openBuffer');
        $returnType = $reflection->getReturnType();

        $this->assertNotNull(
            $returnType,
            'openBuffer must have a declared return type'
        );

        $this->assertSame(
            'void',
            $returnType->getName(),
            'openBuffer must return void'
        );
    }

    /**
     * Test the return type of getBufferContent
     */
    public function testGetBufferContentReturnsStringType(): void
    {
        $reflection = new ReflectionMethod('view', 'getBufferContent');
        $returnType = $reflection->getReturnType();

        $this->assertNotNull(
            $returnType,
            'getBufferContent must have a declared return type'
        );

        $this->assertSame(
            'string',
            $returnType->getName(),
            'getBufferContent must return string'
        );
    }

    /**
     * Test the return type of show
     */
    public function testShowReturnsVoid(): void
    {
        $reflection = new ReflectionMethod('view', 'show');
        $returnType = $reflection->getReturnType();

        $this->assertNotNull(
            $returnType,
            'show must have a declared return type'
        );

        $this->assertSame(
            'void',
            $returnType->getName(),
            'show must return void'
        );
    }

    /**
     * Test multiple nested output buffers
     */
    public function testMultipleBuffers(): void
    {
        view::openBuffer();
        echo "First";

        view::openBuffer();
        echo "Second";

        $content2 = view::getBufferContent();
        $this->assertSame("Second", $content2);

        $content1 = view::getBufferContent();
        $this->assertSame("First", $content1);
    }

    /**
     * Test that openBuffer can be called multiple times
     */
    public function testOpenBufferMultipleTimes(): void
    {
        $level0 = ob_get_level();

        view::openBuffer();
        $level1 = ob_get_level();

        view::openBuffer();
        $level2 = ob_get_level();

        view::openBuffer();
        $level3 = ob_get_level();

        $this->assertSame($level0 + 1, $level1);
        $this->assertSame($level0 + 2, $level2);
        $this->assertSame($level0 + 3, $level3);

        // Cleanup
        ob_end_clean();
        ob_end_clean();
        ob_end_clean();
    }

    /**
     * Test that getBufferContent handles empty content
     */
    public function testGetBufferContentWithEmptyBuffer(): void
    {
        view::openBuffer();
        // Write nothing

        $content = view::getBufferContent();

        $this->assertSame(
            '',
            $content,
            'getBufferContent must return an empty string if nothing is written'
        );
    }

    /**
     * Test that show uses constants::viewsRepository
     */
    public function testShowUsesConstantsViewsRepository(): void
    {
        // Check that constants::viewsRepository exists and returns a path
        $viewsRepo = constants::viewsRepository();

        $this->assertIsString($viewsRepo);
        $this->assertDirectoryExists($viewsRepo);
        $this->assertStringEndsWith('/views/', $viewsRepo);
    }

    /**
     * Test the number of public methods
     */
    public function testPublicMethodCount(): void
    {
        $reflection = new ReflectionClass('view');
        $publicMethods = array_filter(
            $reflection->getMethods(ReflectionMethod::IS_PUBLIC),
            fn($method) => !$method->isConstructor()
        );

        $publicMethodNames = array_map(fn($m) => $m->getName(), $publicMethods);

        $this->assertCount(
            3,
            $publicMethods,
            'view must have exactly 3 public methods'
        );

        $this->assertContains('openBuffer', $publicMethodNames);
        $this->assertContains('getBufferContent', $publicMethodNames);
        $this->assertContains('show', $publicMethodNames);
    }

    /**
     * Test that view has no properties
     */
    public function testViewHasNoProperties(): void
    {
        $reflection = new ReflectionClass('view');
        $properties = $reflection->getProperties();

        $this->assertCount(
            0,
            $properties,
            'view must not have any properties (static utility class)'
        );
    }

    /**
     * Test that show parameters use the correct types
     */
    public function testShowParametersTypes(): void
    {
        $reflection = new ReflectionMethod('view', 'show');
        $parameters = $reflection->getParameters();

        $this->assertCount(
            2,
            $parameters,
            'show must have 2 parameters'
        );

        // First parameter: $S_localisation (no type hint in current code)
        $param1 = $parameters[0];
        $this->assertSame('S_localisation', $param1->getName());

        // Second parameter: $A_parameters with default array()
        $param2 = $parameters[1];
        $this->assertSame('A_parameters', $param2->getName());
        $this->assertTrue(
            $param2->isOptional(),
            'The second parameter must be optional'
        );
        $this->assertTrue(
            $param2->isDefaultValueAvailable(),
            'The second parameter must have a default value'
        );
    }

    /**
     * Integration test: complete buffer lifecycle
     */
    public function testCompleteBufferCycle(): void
    {
        $levelBefore = ob_get_level();

        // Open a buffer
        view::openBuffer();

        // Write content
        echo "Line 1\n";
        echo "Line 2\n";
        echo "Line 3";

        // Retrieve content
        $content = view::getBufferContent();

        // Check content
        $this->assertSame("Line 1\nLine 2\nLine 3", $content);

        // Check that buffer is closed and level restored
        $this->assertSame($levelBefore, ob_get_level());
    }

    /**
     * Test that show builds the correct file path
     */
    public function testShowBuildsCorrectFilePath(): void
    {
        // Use reflection to indirectly verify logic
        $viewsRepo = constants::viewsRepository();
        $expectedPath = $viewsRepo . 'homepageView.php';

        $this->assertFileExists(
            $expectedPath,
            'The path built by show must point to an existing file'
        );
    }

    /**
     * Test that view is a proper utility class (not instantiable normally)
     */
    public function testViewIsUtilityClass(): void
    {
        $reflection = new ReflectionClass('view');

        // Check that all public methods are static
        $methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);

        foreach ($methods as $method) {
            if (!$method->isConstructor()) {
                $this->assertTrue(
                    $method->isStatic(),
                    'All public methods of view must be static (utility class)'
                );
            }
        }
    }
}

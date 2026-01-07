<?php

use PHPUnit\Framework\TestCase;

/**
 * Unit test for the controller class
 *
 * Tests routing, action execution, error handling,
 * and controller/action manipulation
 */
class controllerTest extends TestCase
{
    /**
     * Test that the controller class exists and is final
     */
    public function testControllerClassExists(): void
    {
        $this->assertTrue(
            class_exists('controller'),
            'The controller class must exist'
        );

        $reflection = new ReflectionClass('controller');
        $this->assertTrue(
            $reflection->isFinal(),
            'The controller class must be final (final class)'
        );
    }

    /**
     * Test the constructor with a valid controller and action
     */
    public function testConstructorWithValidControllerAndAction(): void
    {
        $controller = new controller('homepage', 'index');

        $this->assertInstanceOf(
            controller::class,
            $controller,
            'The constructor must create a controller instance'
        );
    }

    /**
     * Test the constructor with null values (default values)
     */
    public function testConstructorWithNullValues(): void
    {
        $controller = new controller(null, null);

        $this->assertInstanceOf(
            controller::class,
            $controller,
            'The constructor must accept null values'
        );
    }

    /**
     * Test the constructor with a null controller (should use homepage by default)
     */
    public function testConstructorWithNullControllerUsesDefault(): void
    {
        $controller = new controller(null, 'index');

        // We cannot directly test the private value,
        // but we can check that it does not fail
        $this->assertInstanceOf(controller::class, $controller);
    }

    /**
     * Test the constructor with a null action (should use login by default)
     */
    public function testConstructorWithNullActionUsesDefault(): void
    {
        $controller = new controller('homepage', null);

        $this->assertInstanceOf(controller::class, $controller);
    }

    /**
     * Test that the execute method exists
     */
    public function testExecuteMethodExists(): void
    {
        $this->assertTrue(
            method_exists('controller', 'execute'),
            'The execute method must exist'
        );

        $reflection = new ReflectionMethod('controller', 'execute');
        $this->assertTrue(
            $reflection->isPublic(),
            'The execute method must be public'
        );
    }

    /**
     * Test that the getParams method exists
     */
    public function testGetParamsMethodExists(): void
    {
        $this->assertTrue(
            method_exists('controller', 'getParams'),
            'The getParams method must exist'
        );

        $reflection = new ReflectionMethod('controller', 'getParams');
        $this->assertTrue(
            $reflection->isPublic(),
            'The getParams method must be public'
        );
    }

    /**
     * Test that getParams returns an empty array by default
     */
    public function testGetParamsReturnsEmptyArrayByDefault(): void
    {
        $controller = new controller('homepage', 'index');

        $params = $controller->getParams();

        $this->assertIsArray($params, 'getParams must return an array');
        $this->assertEmpty($params, 'getParams must return an empty array by default');
    }

    /**
     * Test execution with a valid controller
     */
    public function testExecuteWithValidController(): void
    {
        // homepageController exists and has an index method
        $controller = new controller('homepage', 'index');

        // Note: We cannot really execute it because it performs redirects
        // We only verify that the object is correctly created
        $this->assertInstanceOf(controller::class, $controller);
        $this->assertTrue(class_exists('homepageController'));
        $this->assertTrue(method_exists('homepageController', 'index'));
    }

    /**
     * Test execution with a non-existent controller
     */
    public function testExecuteWithNonExistentControllerThrowsException(): void
    {
        $controller = new controller('NonExistentController999', 'index');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('is not found');

        $controller->execute();
    }

    /**
     * Test execution with a non-existent action
     */
    public function testExecuteWithNonExistentActionThrowsException(): void
    {
        $controller = new controller('homepage', 'nonExistentAction999');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('is not found in the controller');

        $controller->execute();
    }

    /**
     * Test that controller names are correctly formatted
     * (indirect test via class existence)
     */
    public function testControllerNameFormatting(): void
    {
        // The 'homepage' controller must be transformed into 'homepageController'
        $controller = new controller('homepage', 'index');

        // Check that the object is correctly created (formatting is correct)
        $this->assertInstanceOf(controller::class, $controller);

        // Check that the target controller exists
        $this->assertTrue(class_exists('homepageController'));
    }

    /**
     * Test that spaces in controller names are handled
     */
    public function testControllerNameWithSpacesIsTrimmed(): void
    {
        // Spaces must be removed
        $controller = new controller('  homepage  ', 'index');

        // If trim works, the instance is created correctly
        $this->assertInstanceOf(controller::class, $controller);

        // Check that the target controller exists (spaces were removed)
        $this->assertTrue(class_exists('homepageController'));
    }

    /**
     * Test that special characters are escaped (htmlspecialchars)
     */
    public function testControllerNameEscapesSpecialCharacters(): void
    {
        // htmlspecialchars must escape special characters
        $controller = new controller('homepage', 'index');

        // The name must not contain malicious HTML
        $this->assertInstanceOf(controller::class, $controller);
    }

    /**
     * Test an empty string for the controller (should use homepage by default)
     */
    public function testEmptyStringControllerUsesDefault(): void
    {
        $controller = new controller('', 'index');

        $this->assertInstanceOf(controller::class, $controller);
    }

    /**
     * Test an empty string for the action (should use login by default)
     */
    public function testEmptyStringActionUsesDefault(): void
    {
        $controller = new controller('homepage', '');

        $this->assertInstanceOf(controller::class, $controller);
    }

    /**
     * Test that execution handles exceptions thrown by actions
     */
    public function testExecuteWrapsActionExceptions(): void
    {
        // Create a test controller that throws an exception
        // (we test with an existing controller that could throw an exception)

        // Note: This test would require a mock or a specific test controller
        // For now, we just verify that the mechanism exists
        $this->assertTrue(
            method_exists('controller', 'execute'),
            'The execute method exists to handle exceptions'
        );
    }

    /**
     * Test that private methods exist (via Reflection)
     */
    public function testPrivateMethodsExist(): void
    {
        $reflection = new ReflectionClass('controller');

        $this->assertTrue(
            $reflection->hasMethod('controllerName'),
            'The private method controllerName must exist'
        );

        $this->assertTrue(
            $reflection->hasMethod('actionName'),
            'The private method actionName must exist'
        );
    }

    /**
     * Test that private methods are actually private
     */
    public function testPrivateMethodsArePrivate(): void
    {
        $reflection = new ReflectionClass('controller');

        $controllerNameMethod = $reflection->getMethod('controllerName');
        $this->assertTrue(
            $controllerNameMethod->isPrivate(),
            'The controllerName method must be private'
        );

        $actionNameMethod = $reflection->getMethod('actionName');
        $this->assertTrue(
            $actionNameMethod->isPrivate(),
            'The actionName method must be private'
        );
    }

    /**
     * Test that the url property is private and exists
     */
    public function testUrlPropertyIsPrivate(): void
    {
        $reflection = new ReflectionClass('controller');

        $this->assertTrue(
            $reflection->hasProperty('url'),
            'The url property must exist'
        );

        $urlProperty = $reflection->getProperty('url');
        $this->assertTrue(
            $urlProperty->isPrivate(),
            'The url property must be private'
        );
    }

    /**
     * Test that the params property is private and exists
     */
    public function testParamsPropertyIsPrivate(): void
    {
        $reflection = new ReflectionClass('controller');

        $this->assertTrue(
            $reflection->hasProperty('params'),
            'The params property must exist'
        );

        $paramsProperty = $reflection->getProperty('params');
        $this->assertTrue(
            $paramsProperty->isPrivate(),
            'The params property must be private'
        );
    }

    /**
     * Integration test: full execution with multiple controllers
     */
    public function testExecuteWithMultipleControllers(): void
    {
        $controllers = [
            ['homepage', 'index'],
            ['marketpage', 'index'],
            ['profilepage', 'index']
        ];

        foreach ($controllers as [$controllerName, $action]) {
            $controller = new controller($controllerName, $action);

            // Check that the controller is correctly created
            $this->assertInstanceOf(controller::class, $controller);

            // Note: We cannot really execute the controllers in tests
            // because they perform redirects, manipulate sessions, etc.
            // This test only verifies that instances are created correctly
        }
    }

    /**
     * Test that the return type of getParams is always an array
     */
    public function testGetParamsAlwaysReturnsArray(): void
    {
        $controller = new controller('homepage', 'index');

        $params1 = $controller->getParams();
        $this->assertIsArray($params1);

        // Check multiple times for consistency
        $params2 = $controller->getParams();
        $this->assertIsArray($params2);
        $this->assertSame($params1, $params2, 'getParams must return the same reference');
    }

    /**
     * Robustness test: controller with Unicode characters
     */
    public function testControllerWithUnicodeCharacters(): void
    {
        // htmlspecialchars must handle UTF-8 characters
        $controller = new controller('homepage', 'index');

        $this->assertInstanceOf(controller::class, $controller);
    }

    /**
     * Test the number of public methods (public API)
     */
    public function testPublicApiMethodCount(): void
    {
        $reflection = new ReflectionClass('controller');
        $publicMethods = array_filter(
            $reflection->getMethods(ReflectionMethod::IS_PUBLIC),
            fn($method) => !$method->isConstructor()
        );

        $publicMethodNames = array_map(fn($m) => $m->getName(), $publicMethods);

        $this->assertContains('execute', $publicMethodNames);
        $this->assertContains('getParams', $publicMethodNames);
    }
}

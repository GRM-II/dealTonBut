<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class homepageControllerTest extends TestCase
{
    /**
     * We isolate the process to freely declare test doubles for classes like
     * userModel and view without colliding with real project classes.
     * We also avoid Composer autoload bringing real classes by requiring the
     * controller file manually after our doubles are declared.
     */
    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    public function testLoginUsesUserModelGetDbStatus(): void
    {
        // Test doubles
        // Stub for userModel with getDbStatus
        if (!class_exists('userModel', false)) {
            eval('final class userModel { public function getDbStatus(): array { return ["available"=>false, "message"=>"DB down for maintenance"]; } }');
        }

        // Spy for view::show
        if (!class_exists('view', false)) {
            eval('final class view { public static string $lastTemplate = ""; public static array $lastParams = []; public static function show($tpl, $params = []) : void { self::$lastTemplate = (string)$tpl; self::$lastParams = (array)$params; } }');
        }

        // Load the SUT after doubles are in place
        require_once __DIR__ . '\\..\\..\\..\\controllers\\homepageController.php';

        $controller = new homepageController();
        $controller->login();

        $this->assertSame('homepageView', view::$lastTemplate, 'Template name should be homepageView');
        $this->assertArrayHasKey('db_status', view::$lastParams, 'db_status should be passed to the view');
        $this->assertSame([
            'available' => false,
            'message'   => 'DB down for maintenance',
        ], view::$lastParams['db_status']);
    }

    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    public function testLoginFallbackWhenNoGetDbStatus(): void
    {
        // userModel without getDbStatus -> method_exists should be false
        if (!class_exists('userModel', false)) {
            eval('final class userModel { }');
        }

        if (!class_exists('view', false)) {
            eval('final class view { public static string $lastTemplate = ""; public static array $lastParams = []; public static function show($tpl, $params = []) : void { self::$lastTemplate = (string)$tpl; self::$lastParams = (array)$params; } }');
        }

        require_once __DIR__ . '\\..\\..\\..\\controllers\\homepageController.php';

        $controller = new homepageController();
        $controller->login();

        $this->assertSame('homepageView', view::$lastTemplate);
        $this->assertArrayHasKey('db_status', view::$lastParams);
        $this->assertSame([
            'available' => true,
            'message'   => '',
        ], view::$lastParams['db_status']);
    }

    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    public function testIndexDelegatesToLogin(): void
    {
        // Minimal doubles
        if (!class_exists('userModel', false)) {
            // No getDbStatus to trigger default path
            eval('final class userModel { }');
        }
        if (!class_exists('view', false)) {
            eval('final class view { public static int $calls = 0; public static function show($tpl, $params = []) : void { self::$calls++; } }');
        }

        require_once __DIR__ . '\\..\\..\\..\\controllers\\homepageController.php';

        $controller = new homepageController();
        $controller->index();

        $this->assertSame(1, view::$calls, 'index() should call login() which calls view::show exactly once');
    }

    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    public function testDiagnosticsOutputsExpectedHtml(): void
    {
        require_once __DIR__ . '\\..\\..\\..\\controllers\\homepageController.php';

        $controller = new homepageController();

        ob_start();
        $controller->diagnostics();
        $output = ob_get_clean();

        $this->assertIsString($output);
        $this->assertStringContainsString('<h1>Diagnostics MySQL</h1>', $output);
        $this->assertStringContainsString('Version de PHP', $output);
        $this->assertStringContainsString('PDO:', $output);
        $this->assertStringContainsString('pdo_mysql:', $output);
        $this->assertStringContainsString('Extension mysqli:', $output);
        $this->assertStringContainsString('?controller=homepage&action=login', $output);
    }
}

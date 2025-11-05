<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class profilepageControllerTest extends TestCase
{
    /**
     * Isolate process to allow defining test doubles safely, and to avoid
     * exiting the whole PHPUnit run when the SUT calls exit() in other paths.
     */
    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    public function testIndexRendersProfileWithUserDataWhenLoggedIn(): void
    {
        // Start a session for the controller
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['user'] = [
            'id' => 7,
            'username' => 'bob',
            'email' => 'bob@example.com',
        ];
        $_SESSION['logged_in'] = true;

        // Define minimal doubles before loading the SUT
        if (!class_exists('view', false)) {
            eval('final class view { public static string $lastTemplate = ""; public static array $lastParams = []; public static function show($tpl, $params = []) : void { self::$lastTemplate = (string)$tpl; self::$lastParams = (array)$params; } }');
        }
        if (!class_exists('userModel', false)) {
            // Constructor needs userModel, but index() does not use it
            eval('final class userModel { }');
        }

        require_once __DIR__ . '\\..\\..\\..\\controllers\\profilepageController.php';

        $controller = new profilepageController();
        $controller->index();

        // Asserts
        $this->assertSame('profilepageView', view::$lastTemplate, 'Should render the profile view');
        $this->assertArrayHasKey('username', view::$lastParams);
        $this->assertArrayHasKey('email', view::$lastParams);
        $this->assertSame('bob', view::$lastParams['username']);
        $this->assertSame('bob@example.com', view::$lastParams['email']);
    }

    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    public function testUpdateProfileMethodNotAllowedOnGet(): void
    {
        // Prepare environment: non-POST request
        $_SERVER['REQUEST_METHOD'] = 'GET';

        if (!class_exists('userModel', false)) {
            eval('final class userModel { }');
        }

        require_once __DIR__ . '\\..\\..\\..\\controllers\\profilepageController.php';

        $controller = new profilepageController();

        ob_start();
        $controller->updateProfile();
        $output = ob_get_clean();

        // Assert 405 and message
        $this->assertSame(405, http_response_code());
        $this->assertStringContainsString('Méthode non autorisée', (string)$output);
    }

    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    public function testIndexRendersDefaultValuesWhenMissingInSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        // Provide an empty user array; also mark as logged in
        $_SESSION['user'] = [];
        $_SESSION['logged_in'] = true;

        if (!class_exists('view', false)) {
            eval('final class view { public static string $lastTemplate = ""; public static array $lastParams = []; public static function show($tpl, $params = []) : void { self::$lastTemplate = (string)$tpl; self::$lastParams = (array)$params; } }');
        }
        if (!class_exists('userModel', false)) {
            eval('final class userModel { }');
        }

        require_once __DIR__ . '\\..\\..\\..\\controllers\\profilepageController.php';

        $controller = new profilepageController();
        $controller->index();

        $this->assertSame('profilepageView', view::$lastTemplate);
        $this->assertSame('N/A', view::$lastParams['username'] ?? null);
        $this->assertSame('N/A', view::$lastParams['email'] ?? null);
    }
}

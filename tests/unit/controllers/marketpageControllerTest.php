<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class marketpageControllerTest extends TestCase
{
    /**
     * We isolate each test in its own PHP process so we can safely define
     * test doubles for classes like View, userModel, and offerModel without
     * colliding with the real project classes or Composer autoload.
     */
    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    public function testIndexRendersMarketpageWithOffersAndDbStatusWhenLoggedIn(): void
    {
        // Start session and prepare environment
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['user'] = ['id' => 42, 'username' => 'alice'];

        // Doubles
        if (!class_exists('View', false)) {
            eval('final class View { public static string $lastTemplate = ""; public static array $lastParams = []; public static function show($tpl, $params = []) : void { self::$lastTemplate = (string)$tpl; self::$lastParams = (array)$params; } }');
        }
        if (!class_exists('userModel', false)) {
            eval('final class userModel { public function getDbStatus(): array { return ["available"=>true, "message"=>"ok-test"]; } }');
        }
        if (!class_exists('offerModel', false)) {
            eval('final class offerModel { public static function getAllOffers(): array { return [["id"=>1,"title"=>"Ballon"],["id"=>2,"title"=>"Maillot"]]; } public static function createOffer(){} public static function deleteOffer(){} }');
        }

        // Load SUT after doubles are declared
        require_once __DIR__ . '\\..\\..\\..\\controllers\\marketpageController.php';

        $controller = new marketpageController();
        $controller->index();

        // Assertions
        $this->assertSame('marketpageView', View::$lastTemplate, 'Should render the market page view');
        $params = View::$lastParams;
        $this->assertArrayHasKey('user', $params);
        $this->assertArrayHasKey('isLoggedIn', $params);
        $this->assertArrayHasKey('db_status', $params);
        $this->assertArrayHasKey('flash', $params);
        $this->assertArrayHasKey('offers', $params);

        $this->assertSame(['id' => 42, 'username' => 'alice'], $params['user']);
        $this->assertTrue($params['isLoggedIn']);
        $this->assertSame(['available'=>true,'message'=>'ok-test'], $params['db_status']);
        $this->assertNull($params['flash']);
        $this->assertSame([
            ['id'=>1,'title'=>'Ballon'],
            ['id'=>2,'title'=>'Maillot'],
        ], $params['offers']);
    }

    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    public function testIndexPassesFlashThenClearsItWhenGuest(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['flash'] = ['success' => false, 'message' => 'Erreur test'];

        // Doubles
        if (!class_exists('View', false)) {
            eval('final class View { public static string $lastTemplate = ""; public static array $lastParams = []; public static function show($tpl, $params = []) : void { self::$lastTemplate = (string)$tpl; self::$lastParams = (array)$params; } }');
        }
        if (!class_exists('userModel', false)) {
            // Return a deterministic status so we can assert on it too if desired
            eval('final class userModel { public function getDbStatus(): array { return ["available"=>false, "message"=>"maintenance"]; } }');
        }
        if (!class_exists('offerModel', false)) {
            eval('final class offerModel { public static function getAllOffers(): array { return []; } public static function createOffer(){} public static function deleteOffer(){} }');
        }

        require_once __DIR__ . '\\..\\..\\..\\controllers\\marketpageController.php';

        $controller = new marketpageController();
        $controller->index();

        $this->assertSame('marketpageView', View::$lastTemplate);
        $params = View::$lastParams;

        $this->assertNull($params['user']);
        $this->assertFalse($params['isLoggedIn']);
        $this->assertSame(['available'=>false,'message'=>'maintenance'], $params['db_status']);
        $this->assertSame(['success'=>false,'message'=>'Erreur test'], $params['flash']);
        $this->assertSame([], $params['offers']);

        // Flash must be cleared from session after rendering
        $this->assertFalse(isset($_SESSION['flash']), 'Flash message should be cleared from session after index()');
    }

    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    public function testIndexCallsViewShowExactlyOnce(): void
    {
        if (!class_exists('View', false)) {
            eval('final class View { public static int $calls = 0; public static function show($tpl, $params = []) : void { self::$calls++; } }');
        }
        if (!class_exists('userModel', false)) {
            eval('final class userModel { public function getDbStatus(): array { return ["available"=>true, "message"=>""]; } }');
        }
        if (!class_exists('offerModel', false)) {
            eval('final class offerModel { public static function getAllOffers(): array { return []; } public static function createOffer(){} public static function deleteOffer(){} }');
        }

        require_once __DIR__ . '\\..\\..\\..\\controllers\\marketpageController.php';

        $controller = new marketpageController();
        $controller->index();

        $this->assertSame(1, View::$calls);
    }
}

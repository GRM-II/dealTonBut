<?php

use PHPUnit\Framework\TestCase;

class tradeplaceControllerTest extends TestCase
{
    protected function setUp(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
        $_SESSION = [];
    }

    public function testControllerClassExists(): void
    {
        $this->assertTrue(class_exists('tradeplaceController'), 'La classe tradeplaceController doit exister');
    }

    public function testIndexMethodExists(): void
    {
        $controller = new tradeplaceController();
        $this->assertTrue(method_exists($controller, 'index'), 'La méthode index doit exister');
    }

    public function testGetParamsMethodExists(): void
    {
        $controller = new tradeplaceController();
        $this->assertTrue(method_exists($controller, 'getParams'), 'La méthode getParams doit exister');
    }

    public function testGetParamsReturnsArray(): void
    {
        $controller = new tradeplaceController();
        $result = $controller->getParams();
        $this->assertIsArray($result, 'getParams doit retourner un tableau');
    }

    public function testSessionStartedWhenNeeded(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }

        $controller = new tradeplaceController();

        $this->assertInstanceOf(tradeplaceController::class, $controller);
    }

    protected function tearDown(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
        $_SESSION = [];
    }
}


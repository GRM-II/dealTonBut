<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../../controllers/sitemapController.php';
require_once __DIR__ . '/../../../core/view.php';

class sitemapControllerTest extends TestCase
{
    private sitemapController $controller;

    protected function setUp(): void
    {
        $this->controller = new sitemapController();

        if (session_status() !== PHP_SESSION_ACTIVE) {
            @session_start();
        }
    }

    public function testIndexMethodExists(): void
    {
        $this->assertTrue(
            method_exists($this->controller, 'index'),
            'The index method should exist in sitemapController'
        );
    }

    public function testIndexMethodIsCallable(): void
    {
        $this->assertTrue(
            is_callable([$this->controller, 'index']),
            'The index method should be callable'
        );
    }

    public function testIndexMethodReturnsVoid(): void
    {
        $reflection = new ReflectionMethod($this->controller, 'index');
        $returnType = $reflection->getReturnType();

        $this->assertNotNull($returnType, 'The index method should have a return type');
        $this->assertEquals('void', $returnType->getName(), 'The index method should return void');
    }

    public function testControllerClassExists(): void
    {
        $this->assertTrue(
            class_exists('sitemapController'),
            'The sitemapController class should exist'
        );
    }

    public function testControllerCanBeInstantiated(): void
    {
        $controller = new sitemapController();
        $this->assertInstanceOf(
            sitemapController::class,
            $controller,
            'Should be able to instantiate sitemapController'
        );
    }

    public function testControllerIsFinal(): void
    {
        $reflection = new ReflectionClass($this->controller);
        $this->assertTrue(
            $reflection->isFinal(),
            'The sitemapController class should be declared as final'
        );
    }

    public function testIndexStartsSessionIfNeeded(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }

        ob_start();
        try {
            $this->controller->index();
            ob_end_clean();

            $this->assertEquals(
                PHP_SESSION_ACTIVE,
                session_status(),
                'Session should be started by the index method'
            );
        } catch (Exception $e) {
            ob_end_clean();
            $this->fail('Index method should not throw exceptions: ' . $e->getMessage());
        }
    }

    public function testIndexHandlesLoggedInUser(): void
    {
        $_SESSION['user_id'] = 1;
        $_SESSION['user'] = ['id' => 1, 'username' => 'testuser'];

        ob_start();
        try {
            $this->controller->index();
            ob_end_clean();
            $this->assertTrue(true);
        } catch (Exception $e) {
            ob_end_clean();
            $this->fail('Index should handle logged in user: ' . $e->getMessage());
        }
    }

    public function testIndexHandlesGuestUser(): void
    {
        unset($_SESSION['user_id']);
        unset($_SESSION['user']);

        ob_start();
        try {
            $this->controller->index();
            ob_end_clean();
            $this->assertTrue(true);
        } catch (Exception $e) {
            ob_end_clean();
            $this->fail('Index should handle guest user: ' . $e->getMessage());
        }
    }

    protected function tearDown(): void
    {
        $this->controller = null;

        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
    }
}


<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../../controllers/legalnoticeController.php';

class legalnoticeControllerTest extends TestCase
{
    private legalnoticeController $controller;

    protected function setUp(): void
    {
        $this->controller = new legalnoticeController();
    }

    public function testIndexMethodExists(): void
    {
        $this->assertTrue(
            method_exists($this->controller, 'index'),
            'The index method should exist in legalnoticeController'
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

    public function testIndexIncludesViewFile(): void
    {
        ob_start();

        try {
            $this->controller->index();
            ob_end_clean();
            $this->assertTrue(true);
        } catch (Exception $e) {
            ob_end_clean();
            $this->fail('The index method should include the view file without errors: ' . $e->getMessage());
        }
    }

    public function testControllerClassExists(): void
    {
        $this->assertTrue(
            class_exists('legalnoticeController'),
            'The legalnoticeController class should exist'
        );
    }

    public function testControllerCanBeInstantiated(): void
    {
        $controller = new legalnoticeController();
        $this->assertInstanceOf(
            legalnoticeController::class,
            $controller,
            'Should be able to instantiate legalnoticeController'
        );
    }

    protected function tearDown(): void
    {
        $this->controller = null;
    }
}


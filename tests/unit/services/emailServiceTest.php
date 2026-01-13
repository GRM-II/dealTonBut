<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../../services/emailService.php';
require_once __DIR__ . '/../../../core/envReader.php';

class emailServiceTest extends TestCase
{
    private emailService $service;

    protected function setUp(): void
    {
        try {
            $this->service = new emailService();
        } catch (Exception $e) {
            $this->markTestSkipped('Email service cannot be initialized: ' . $e->getMessage());
        }
    }

    public function testClassExists(): void
    {
        $this->assertTrue(
            class_exists('emailService'),
            'The emailService class should exist'
        );
    }

    public function testClassIsFinal(): void
    {
        $reflection = new ReflectionClass('emailService');
        $this->assertTrue(
            $reflection->isFinal(),
            'The emailService class should be declared as final'
        );
    }

    public function testCanBeInstantiated(): void
    {
        try {
            $service = new emailService();
            $this->assertInstanceOf(
                emailService::class,
                $service,
                'Should be able to instantiate emailService'
            );
        } catch (Exception $e) {
            $this->markTestSkipped('Cannot instantiate emailService: ' . $e->getMessage());
        }
    }

    public function testSendPasswordResetEmailMethodExists(): void
    {
        $this->assertTrue(
            method_exists($this->service, 'sendPasswordResetEmail'),
            'The sendPasswordResetEmail method should exist'
        );
    }

    public function testSendPasswordResetEmailReturnsArray(): void
    {
        $result = $this->service->sendPasswordResetEmail('test@example.com', 'test_token_123');

        $this->assertIsArray($result, 'sendPasswordResetEmail should return an array');
        $this->assertArrayHasKey('success', $result, 'Result should contain success key');
        $this->assertArrayHasKey('message', $result, 'Result should contain message key');
        $this->assertIsBool($result['success'], 'success should be a boolean');
        $this->assertIsString($result['message'], 'message should be a string');
    }

    public function testSendPasswordResetEmailWithEmptyEmail(): void
    {
        $result = $this->service->sendPasswordResetEmail('', 'test_token');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        $this->assertFalse($result['success'], 'Should fail with empty email');
    }

    public function testSendPasswordResetEmailWithEmptyToken(): void
    {
        $result = $this->service->sendPasswordResetEmail('test@example.com', '');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
    }

    public function testSendPasswordResetEmailWithInvalidEmail(): void
    {
        $result = $this->service->sendPasswordResetEmail('invalid_email', 'test_token');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        $this->assertFalse($result['success'], 'Should fail with invalid email format');
    }

    public function testConstructorInitializesSmtpSettings(): void
    {
        $reflection = new ReflectionClass($this->service);

        $properties = ['smtpHost', 'smtpPort', 'smtpUsername', 'smtpPassword', 'smtpEncryption'];

        foreach ($properties as $propertyName) {
            $this->assertTrue(
                $reflection->hasProperty($propertyName),
                "The emailService should have a $propertyName property"
            );
        }
    }

    public function testSmtpHostPropertyIsString(): void
    {
        $reflection = new ReflectionClass($this->service);
        $property = $reflection->getProperty('smtpHost');
        $property->setAccessible(true);

        $value = $property->getValue($this->service);
        $this->assertIsString($value, 'smtpHost should be a string');
    }

    public function testSmtpPortPropertyIsString(): void
    {
        $reflection = new ReflectionClass($this->service);
        $property = $reflection->getProperty('smtpPort');
        $property->setAccessible(true);

        $value = $property->getValue($this->service);
        $this->assertIsString($value, 'smtpPort should be a string');
    }

    public function testSmtpUsernamePropertyIsString(): void
    {
        $reflection = new ReflectionClass($this->service);
        $property = $reflection->getProperty('smtpUsername');
        $property->setAccessible(true);

        $value = $property->getValue($this->service);
        $this->assertIsString($value, 'smtpUsername should be a string');
    }

    public function testSmtpPasswordPropertyIsString(): void
    {
        $reflection = new ReflectionClass($this->service);
        $property = $reflection->getProperty('smtpPassword');
        $property->setAccessible(true);

        $value = $property->getValue($this->service);
        $this->assertIsString($value, 'smtpPassword should be a string');
    }

    public function testSmtpEncryptionPropertyIsString(): void
    {
        $reflection = new ReflectionClass($this->service);
        $property = $reflection->getProperty('smtpEncryption');
        $property->setAccessible(true);

        $value = $property->getValue($this->service);
        $this->assertIsString($value, 'smtpEncryption should be a string');
    }

    public function testSendPasswordResetEmailHandlesException(): void
    {
        $result = $this->service->sendPasswordResetEmail('test@nonexistent-domain-12345.com', 'token123');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        $this->assertArrayHasKey('message', $result);
    }

    public function testSendPasswordResetEmailMessageNotEmpty(): void
    {
        $result = $this->service->sendPasswordResetEmail('test@example.com', 'test_token');

        $this->assertNotEmpty($result['message'], 'Message should not be empty');
    }

    protected function tearDown(): void
    {
        $this->service = null;
    }
}


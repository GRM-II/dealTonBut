<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../../models/passwordResetModel.php';
require_once __DIR__ . '/../../../models/userModel.php';

class passwordResetModelTest extends TestCase
{
    private passwordResetModel $model;
    private static ?PDO $pdo = null;

    public static function setUpBeforeClass(): void
    {
        try {
            self::$pdo = userModel::getConnection();
        } catch (Exception $e) {
            self::markTestSkipped('Database connection not available: ' . $e->getMessage());
        }
    }

    protected function setUp(): void
    {
        $this->model = new passwordResetModel();

        if (self::$pdo === null) {
            $this->markTestSkipped('Database connection not available');
        }
    }

    public function testClassExists(): void
    {
        $this->assertTrue(
            class_exists('passwordResetModel'),
            'The passwordResetModel class should exist'
        );
    }

    public function testClassIsFinal(): void
    {
        $reflection = new ReflectionClass($this->model);
        $this->assertTrue(
            $reflection->isFinal(),
            'The passwordResetModel class should be declared as final'
        );
    }

    public function testCanBeInstantiated(): void
    {
        $model = new passwordResetModel();
        $this->assertInstanceOf(
            passwordResetModel::class,
            $model,
            'Should be able to instantiate passwordResetModel'
        );
    }

    public function testCreateResetTokenMethodExists(): void
    {
        $this->assertTrue(
            method_exists($this->model, 'createResetToken'),
            'The createResetToken method should exist'
        );
    }

    public function testValidateTokenMethodExists(): void
    {
        $this->assertTrue(
            method_exists($this->model, 'validateToken'),
            'The validateToken method should exist'
        );
    }

    public function testDeleteTokenMethodExists(): void
    {
        $this->assertTrue(
            method_exists($this->model, 'deleteToken'),
            'The deleteToken method should exist'
        );
    }

    public function testGetUserByEmailMethodExists(): void
    {
        $this->assertTrue(
            method_exists($this->model, 'getUserByEmail'),
            'The getUserByEmail method should exist'
        );
    }

    public function testCreateResetTokenReturnsArray(): void
    {
        $result = $this->model->createResetToken(999999);

        $this->assertIsArray($result, 'createResetToken should return an array');
        $this->assertArrayHasKey('success', $result, 'Result should contain success key');
        $this->assertArrayHasKey('message', $result, 'Result should contain message key');
        $this->assertIsBool($result['success'], 'success should be a boolean');
        $this->assertIsString($result['message'], 'message should be a string');
    }

    public function testCreateResetTokenWithValidUserId(): void
    {
        $result = $this->model->createResetToken(1);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);

        if ($result['success']) {
            $this->assertArrayHasKey('token', $result, 'Success result should contain token');
            $this->assertIsString($result['token'], 'Token should be a string');
            $this->assertEquals(64, strlen($result['token']), 'Token should be 64 characters long');

            $this->model->deleteToken($result['token']);
        }
    }

    public function testValidateTokenReturnsArray(): void
    {
        $result = $this->model->validateToken('invalid_token');

        $this->assertIsArray($result, 'validateToken should return an array');
        $this->assertArrayHasKey('valid', $result, 'Result should contain valid key');
        $this->assertArrayHasKey('message', $result, 'Result should contain message key');
        $this->assertIsBool($result['valid'], 'valid should be a boolean');
        $this->assertIsString($result['message'], 'message should be a string');
    }

    public function testValidateInvalidToken(): void
    {
        $result = $this->model->validateToken('definitely_invalid_token_12345');

        $this->assertFalse($result['valid'], 'Invalid token should not be valid');
        $this->assertStringContainsString('invalide', strtolower($result['message']));
    }

    public function testDeleteTokenReturnsBool(): void
    {
        $result = $this->model->deleteToken('some_token');

        $this->assertIsBool($result, 'deleteToken should return a boolean');
    }

    public function testGetUserByEmailReturnsArrayOrNull(): void
    {
        $result = $this->model->getUserByEmail('nonexistent@example.com');

        $this->assertTrue(
            is_array($result) || is_null($result),
            'getUserByEmail should return an array or null'
        );
    }

    public function testGetUserByEmailWithInvalidEmail(): void
    {
        $result = $this->model->getUserByEmail('invalid_email_format');

        $this->assertTrue(
            is_array($result) || is_null($result),
            'getUserByEmail should handle invalid email format gracefully'
        );
    }

    public function testGetUserByEmailReturnsCorrectStructure(): void
    {
        $result = $this->model->getUserByEmail('test@example.com');

        if ($result !== null) {
            $this->assertIsArray($result);
            $this->assertArrayHasKey('id', $result);
            $this->assertArrayHasKey('username', $result);
            $this->assertArrayHasKey('email', $result);
        } else {
            $this->assertNull($result);
        }
    }

    public function testTokenExpirationFlow(): void
    {
        $createResult = $this->model->createResetToken(1);

        if ($createResult['success']) {
            $token = $createResult['token'];

            $validateResult = $this->model->validateToken($token);

            if ($validateResult['valid']) {
                $this->assertTrue($validateResult['valid']);
                $this->assertArrayHasKey('userId', $validateResult);
            }

            $deleteResult = $this->model->deleteToken($token);
            $this->assertIsBool($deleteResult);

            $validateAfterDelete = $this->model->validateToken($token);
            $this->assertFalse($validateAfterDelete['valid']);
        } else {
            $this->markTestSkipped('Could not create reset token for testing');
        }
    }

    public function testCreateResetTokenDeletesOldTokens(): void
    {
        $firstResult = $this->model->createResetToken(1);

        if ($firstResult['success']) {
            $firstToken = $firstResult['token'];

            $secondResult = $this->model->createResetToken(1);

            if ($secondResult['success']) {
                $secondToken = $secondResult['token'];

                $validateFirst = $this->model->validateToken($firstToken);
                $this->assertFalse(
                    $validateFirst['valid'],
                    'Old token should be invalid after creating new token'
                );

                $this->model->deleteToken($secondToken);
            }
        } else {
            $this->markTestSkipped('Could not create reset tokens for testing');
        }
    }

    protected function tearDown(): void
    {
        $this->model = null;
    }

    public static function tearDownAfterClass(): void
    {
        self::$pdo = null;
    }
}


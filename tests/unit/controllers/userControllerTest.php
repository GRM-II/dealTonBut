<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

/**
 * Test doubles used by userController tests
 */
if (!class_exists('view', false)) {
    final class view
    {
        public static string $lastTemplate = '';
        public static array $lastParams = [];
        public static function show($tpl, $params = []): void
        {
            self::$lastTemplate = (string)$tpl;
            self::$lastParams = (array)$params;
        }
    }
}

/**
 * Very small PDO fake that returns programmable FakeStmt instances.
 */
final class FakePDO extends PDO
{
    /** @var array<string,bool> */
    private array $existingUsernames;
    /** @var array<string,bool> */
    private array $existingEmails;
    /** @var array<string,array<string,mixed>> */
    private array $usersByLogin; // key: username or email -> user row incl. mdp

    public function __construct(
        array $existingUsernames = [],
        array $existingEmails = [],
        array $usersByLogin = []
    ) {
        // Intentionally do not call parent constructor
        $this->existingUsernames = array_fill_keys(array_keys($existingUsernames ?: []), true) + array_fill_keys($existingUsernames, true);
        $this->existingEmails = array_fill_keys(array_keys($existingEmails ?: []), true) + array_fill_keys($existingEmails, true);
        $this->usersByLogin = $usersByLogin;
    }

    #[\ReturnTypeWillChange]
    public function query($statement, $mode = null, ...$fetch_mode_args)
    {
        // Used by getDbStatus(): a successful query means DB available
        return new FakeStmt(function (array $_params) {
            return 1; // any truthy value
        });
    }

    #[\ReturnTypeWillChange]
    public function prepare($statement, $options = null)
    {
        $sql = (string)$statement;

        if (stripos($sql, 'FROM User') !== false && stripos($sql, 'username = ? OR email = ?') !== false) {
            // authenticate() select
            return new FakeStmt(function (array $params) {
                $login = (string)($params[0] ?? '');
                if (isset($this->usersByLogin[$login])) {
                    return $this->usersByLogin[$login];
                }
                return false; // no user
            });
        }

        if (stripos($sql, 'SELECT id FROM User WHERE username = ?') !== false) {
            return new FakeStmt(function (array $params) {
                $username = (string)($params[0] ?? '');
                if (isset($this->existingUsernames[$username])) {
                    return ['id' => 1];
                }
                return false;
            });
        }

        if (stripos($sql, 'SELECT id FROM User WHERE email = ?') !== false) {
            return new FakeStmt(function (array $params) {
                $email = (string)($params[0] ?? '');
                if (isset($this->existingEmails[$email])) {
                    return ['id' => 1];
                }
                return false;
            });
        }

        if (stripos($sql, 'INSERT INTO User') !== false) {
            return new FakeStmt(function (array $params) {
                // simulate success
                return true;
            }, returnsOnFetch: false);
        }

        // Default benign statement
        return new FakeStmt(function (array $params) {
            return false;
        });
    }
}

final class FakeStmt
{
    /** @var callable */
    private $onExecute;
    /** @var mixed */
    private $result;
    private bool $returnsOnFetch;

    public function __construct(callable $onExecute, bool $returnsOnFetch = true)
    {
        $this->onExecute = $onExecute;
        $this->returnsOnFetch = $returnsOnFetch;
    }

    public function execute(?array $params = null): bool
    {
        $callable = $this->onExecute;
        $this->result = $callable($params ?? []);
        return true;
    }

    public function fetch(int $mode = 0)
    {
        if ($this->returnsOnFetch) {
            return $this->result;
        }
        return false;
    }
}

final class userControllerTest extends TestCase
{
    private function makeControllerWithPDO(PDO $pdo): userController
    {
        // Load the SUT file
        require_once __DIR__ . '\\..\\..\\..\\controllers\\userController.php';

        $ref = new ReflectionClass('userController');
        /** @var userController $controller */
        $controller = $ref->newInstanceWithoutConstructor();
        $prop = $ref->getProperty('pdo');
        $prop->setAccessible(true);
        $prop->setValue($controller, $pdo);
        return $controller;
    }

    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    public function testLoginGetShowsFormAndConsumesFlash(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['flash_message'] = ['success' => true, 'message' => 'Bienvenue !'];
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $controller = $this->makeControllerWithPDO(new FakePDO());
        $controller->login();

        $this->assertSame('user/login', view::$lastTemplate);
        $this->assertArrayHasKey('flash', view::$lastParams);
        $this->assertSame('Bienvenue !', view::$lastParams['flash']['message'] ?? null);
        $this->assertArrayNotHasKey('flash_message', $_SESSION, 'Flash message should be consumed');
    }

    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    public function testLoginPostWithInvalidCredentialsRendersError(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['login'] = 'unknown';
        $_POST['password'] = 'secret';

        // Fake PDO knows no user for 'unknown'
        $pdo = new FakePDO(usersByLogin: []);
        $controller = $this->makeControllerWithPDO($pdo);
        $controller->login();

        $this->assertSame('user/login', view::$lastTemplate);
        $this->assertStringContainsString('Identifiants incorrects', (string)(view::$lastParams['error'] ?? ''));
    }

    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    public function testHomepageRendersWithDbStatus(): void
    {
        // Any successful query => DB available
        $controller = $this->makeControllerWithPDO(new FakePDO());
        $controller->homepage();

        $this->assertSame('homepageView', view::$lastTemplate);
        $this->assertTrue((bool)(view::$lastParams['db_status']['available'] ?? false));
    }

    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    public function testRegisterGetRendersFormWithDbStatus(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $controller = $this->makeControllerWithPDO(new FakePDO());
        $controller->register();

        $this->assertSame('user/register', view::$lastTemplate);
        $this->assertArrayHasKey('db_status', view::$lastParams);
    }

    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    public function testRegisterPostValidationEmptyFields(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = ['username' => '', 'email' => '', 'password' => '', 'confirm_password' => ''];

        $controller = $this->makeControllerWithPDO(new FakePDO());
        $controller->register();

        $this->assertSame('user/register', view::$lastTemplate);
        $this->assertSame('Tous les champs sont obligatoires.', view::$lastParams['flash']['message'] ?? null);
    }

    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    public function testRegisterPostPasswordMismatch(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = ['username' => 'alice', 'email' => 'alice@example.com', 'password' => 'abcdef', 'confirm_password' => 'abcdeg'];

        $controller = $this->makeControllerWithPDO(new FakePDO());
        $controller->register();

        $this->assertSame('user/register', view::$lastTemplate);
        $this->assertSame('Les mots de passe ne correspondent pas.', view::$lastParams['flash']['message'] ?? null);
    }

    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    public function testRegisterPostInvalidEmail(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = ['username' => 'alice', 'email' => 'not-an-email', 'password' => 'abcdef', 'confirm_password' => 'abcdef'];

        $controller = $this->makeControllerWithPDO(new FakePDO());
        $controller->register();

        $this->assertSame('user/register', view::$lastTemplate);
        $this->assertSame("L'adresse email n'est pas valide.", view::$lastParams['flash']['message'] ?? null);
    }

    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    public function testRegisterPostExistingUsername(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = ['username' => 'existing', 'email' => 'new@example.com', 'password' => 'abcdef', 'confirm_password' => 'abcdef'];

        $pdo = new FakePDO(existingUsernames: ['existing' => true]);
        $controller = $this->makeControllerWithPDO($pdo);
        $controller->register();

        $this->assertSame('user/register', view::$lastTemplate);
        $this->assertSame("Ce nom d'utilisateur est déjà utilisé.", view::$lastParams['flash']['message'] ?? null);
    }
}

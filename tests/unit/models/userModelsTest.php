<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

/**
 * Minimal fakes to isolate userModel from a real database.
 */
final class UM_FakePDO extends PDO
{
    /** @var array<string,array{ID:int,Username:string,Email:string,Mdp:string}> */
    public array $users = [];
    public bool $tableUserExists = true;

    public function __construct(array $seedUsers = [], bool $tableExists = true)
    {
        // do not call parent
        foreach ($seedUsers as $u) {
            $this->users[$u['Username']] = $u;
        }
        $this->tableUserExists = $tableExists;
    }

    #[\ReturnTypeWillChange]
    public function query($statement, $mode = null, ...$fetch_mode_args)
    {
        $sql = (string)$statement;
        if (stripos($sql, "SHOW TABLES LIKE 'User'") !== false) {
            return new UM_FakeStmt(static function () {
                return null;
            }, rowCount: $this->tableUserExists ? 1 : 0);
        }
        // default benign statement
        return new UM_FakeStmt(static function () {
            return null;
        });
    }

    #[\ReturnTypeWillChange]
    public function prepare($statement, $options = null)
    {
        $sql = (string)$statement;
        return new UM_FakeStmt(function (array $params) use ($sql) {
            // Normalize named/positional
            $p = [];
            foreach ($params as $k => $v) {
                $p[ltrim((string)$k, ':')] = $v;
            }

            // SELECT ID by Username
            if (stripos($sql, 'SELECT ID FROM User WHERE Username =') !== false) {
                $username = (string)($p['username'] ?? '');
                foreach ($this->users as $u) {
                    if (strcasecmp($u['Username'], $username) === 0) {
                        return ['ID' => $u['ID']];
                    }
                }
                return false;
            }

            // SELECT ID by Email
            if (stripos($sql, 'SELECT ID FROM User WHERE Email =') !== false) {
                $email = (string)($p['email'] ?? '');
                foreach ($this->users as $u) {
                    if (strcasecmp($u['Email'], $email) === 0) {
                        return ['ID' => $u['ID']];
                    }
                }
                return false;
            }

            // SELECT Username, Email, Mdp WHERE Username = :login OR Email = :login
            if (stripos($sql, 'SELECT Username, Email, Mdp') !== false
                && stripos($sql, 'WHERE Username = :login OR Email = :login') !== false) {
                $login = (string)($p['login'] ?? '');
                foreach ($this->users as $u) {
                    if (strcasecmp($u['Username'], $login) === 0 || strcasecmp($u['Email'], $login) === 0) {
                        return ['Username' => $u['Username'], 'Email' => $u['Email'], 'Mdp' => $u['Mdp']];
                    }
                }
                return false;
            }

            // INSERT INTO User (Username, Email, Mdp, created_at)
            if (stripos($sql, 'INSERT INTO User') !== false) {
                $username = (string)($p['username'] ?? '');
                $email = (string)($p['email'] ?? '');
                $password = (string)($p['password'] ?? '');
                // uniqueness checks as in createUser are done before insert in SUT
                $this->users[$username] = [
                    'ID' => $this->generateNextId(),
                    'Username' => $username,
                    'Email' => $email,
                    'Mdp' => $password,
                ];
                return true; // fetch() should not be used on INSERT
            }

            // UPDATE password by userId
            if (stripos($sql, 'UPDATE User SET Mdp =') !== false && stripos($sql, 'WHERE id =') !== false) {
                $userId = (int)($p['userId'] ?? 0);
                $pwd = (string)($p['password'] ?? '');
                $count = 0;
                foreach ($this->users as &$u) {
                    if ($u['ID'] === $userId) {
                        $u['Mdp'] = $pwd;
                        $count = 1;
                        break;
                    }
                }
                return new UM_RowCountResult($count);
            }

            // DELETE by ID
            if (stripos($sql, 'DELETE FROM User WHERE ID =') !== false) {
                $userId = (int)($p['userId'] ?? 0);
                $count = 0;
                foreach ($this->users as $k => $u) {
                    if ($u['ID'] === $userId) {
                        unset($this->users[$k]);
                        $count = 1;
                        break;
                    }
                }
                return new UM_RowCountResult($count);
            }

            // DELETE by Username
            if (stripos($sql, 'DELETE FROM User WHERE Username =') !== false) {
                $username = (string)($p['username'] ?? '');
                $count = 0;
                if (isset($this->users[$username])) {
                    unset($this->users[$username]);
                    $count = 1;
                }
                return new UM_RowCountResult($count);
            }

            // UPDATE Username
            if (stripos($sql, 'UPDATE User SET Username =') !== false) {
                $new = (string)($p['newUsername'] ?? '');
                $cur = (string)($p['currentUsername'] ?? '');
                if ($new !== '' && isset($this->users[$new])) {
                    throw new UM_PDOException('duplicate', '23000');
                }
                $count = 0;
                if (isset($this->users[$cur])) {
                    $row = $this->users[$cur];
                    unset($this->users[$cur]);
                    $row['Username'] = $new;
                    $this->users[$new] = $row;
                    $count = 1;
                }
                return new UM_RowCountResult($count);
            }

            // UPDATE Email
            if (stripos($sql, 'UPDATE User SET Email =') !== false) {
                $new = (string)($p['newEmail'] ?? '');
                $username = (string)($p['username'] ?? '');
                foreach ($this->users as $u) {
                    if (strcasecmp($u['Email'], $new) === 0 && strcasecmp($u['Username'], $username) !== 0) {
                        throw new UM_PDOException('duplicate', '23000');
                    }
                }
                $count = 0;
                if (isset($this->users[$username])) {
                    $this->users[$username]['Email'] = $new;
                    $count = 1;
                }
                return new UM_RowCountResult($count);
            }

            return false;
        }, allowBind: true);
    }

    private function generateNextId(): int
    {
        $max = 0;
        foreach ($this->users as $u) {
            $max = max($max, $u['ID']);
        }
        return $max + 1;
    }
}

/**
 * Helper class to carry a rowCount value through the FakeStmt result pipeline
 */
final class UM_RowCountResult
{
    public function __construct(public int $count) {}
}

final class UM_PDOException extends PDOException
{
    public function __construct(string $message, string $sqlState)
    {
        parent::__construct($message, 0);
        $this->code = $sqlState; // make getCode() return SQLSTATE string like real PDOException
    }
}

final class UM_FakeStmt
{
    /** @var callable */
    private $onExecute;
    private array $bound = [];
    private mixed $result = null;
    private int $rowCount;
    private bool $allowBind;

    public function __construct(callable $onExecute, int $rowCount = 0, bool $allowBind = false)
    {
        $this->onExecute = $onExecute;
        $this->rowCount = $rowCount;
        $this->allowBind = $allowBind;
    }

    public function bindValue($param, $value, $type = null): bool
    {
        if (!$this->allowBind) {
            return true;
        }
        $key = is_int($param) ? (string)$param : ltrim((string)$param, ':');
        $this->bound[$key] = $value;
        return true;
    }

    public function execute(?array $params = null): bool
    {
        $params = $params ?? [];
        // merge with bound values (bound wins)
        $merged = [];
        foreach ($params as $k => $v) {
            $merged[ltrim((string)$k, ':')] = $v;
        }
        $merged = $merged + $this->bound;
        $call = $this->onExecute;
        $this->result = $call($merged);
        if ($this->result instanceof UM_RowCountResult) {
            $this->rowCount = $this->result->count;
        }
        return true;
    }

    public function fetch($mode = null)
    {
        if ($this->result instanceof UM_RowCountResult) {
            return false;
        }
        return $this->result ?: false;
    }

    public function rowCount(): int
    {
        return $this->rowCount;
    }
}

final class userModelsTest extends TestCase
{
    private function setConnection(PDO $pdo): void
    {
        require_once __DIR__ . '\\..\\..\\..\\models\\userModel.php';
        $ref = new ReflectionClass('userModel');
        $prop = $ref->getProperty('connection');
        $prop->setAccessible(true);
        $prop->setValue(null, $pdo); // static property
    }

    private function resetConnection(): void
    {
        $this->setConnection(new UM_FakePDO());
    }

    public function testGetUserIdByUsernameFoundAndNotFound(): void
    {
        $hash = password_hash('secret', PASSWORD_DEFAULT);
        $pdo = new UM_FakePDO([[ 'ID'=>5,'Username'=>'alice','Email'=>'alice@example.com','Mdp'=>$hash ]]);
        $this->setConnection($pdo);
        $model = new userModel();

        $this->assertSame(5, $model->getUserIdByUsername('alice'));
        $this->assertNull($model->getUserIdByUsername('bob'));
    }

    public function testCreateUserRejectsExistingEmailAndUsername(): void
    {
        $hash = password_hash('secret', PASSWORD_DEFAULT);
        $pdo = new UM_FakePDO([
            [ 'ID'=>1,'Username'=>'taken','Email'=>'used@example.com','Mdp'=>$hash ]
        ]);
        $this->setConnection($pdo);
        $model = new userModel();

        $res1 = $model->createUser('newuser', 'used@example.com', 'abcdef');
        $this->assertFalse($res1['success']);
        $this->assertStringContainsString('déjà utilisée', $res1['message']);

        $res2 = $model->createUser('taken', 'new@example.com', 'abcdef');
        $this->assertFalse($res2['success']);
        $this->assertStringContainsString('déjà pris', $res2['message']);
    }

    public function testCreateUserSucceedsOnNewUser(): void
    {
        $pdo = new UM_FakePDO([]);
        $this->setConnection($pdo);
        $model = new userModel();

        $res = $model->createUser('carol', 'carol@example.com', 'abcdef');
        $this->assertTrue($res['success']);
        $this->assertArrayHasKey('carol', $pdo->users);
    }

    public function testFindUserByLoginReturnsUserOrNull(): void
    {
        $hash = password_hash('pw', PASSWORD_DEFAULT);
        $pdo = new UM_FakePDO([
            [ 'ID'=>2,'Username'=>'bob','Email'=>'bob@example.com','Mdp'=>$hash ]
        ]);
        $this->setConnection($pdo);
        $model = new userModel();

        $u1 = $model->findUserByLogin('bob');
        $this->assertNotNull($u1);
        $this->assertSame('bob', $u1['Username']);

        $u2 = $model->findUserByLogin('bob@example.com');
        $this->assertNotNull($u2);
        $this->assertSame('bob@example.com', $u2['Email']);

        $this->assertNull($model->findUserByLogin('nope'));
    }

    public function testAuthenticateSuccessAndFailures(): void
    {
        $hash = password_hash('goodpass', PASSWORD_DEFAULT);
        $pdo = new UM_FakePDO([
            [ 'ID'=>3,'Username'=>'dave','Email'=>'dave@example.com','Mdp'=>$hash ]
        ]);
        $this->setConnection($pdo);
        $model = new userModel();

        $ok = $model->authenticate('dave', 'goodpass');
        $this->assertTrue($ok['success']);

        $badUser = $model->authenticate('unknown', 'goodpass');
        $this->assertFalse($badUser['success']);

        $badPwd = $model->authenticate('dave', 'wrong');
        $this->assertFalse($badPwd['success']);
    }

    public function testUpdatePasswordReturnsTrueOnSuccessAndFalseOnEmpty(): void
    {
        $hash = password_hash('old', PASSWORD_DEFAULT);
        $pdo = new UM_FakePDO([
            [ 'ID'=>9,'Username'=>'eve','Email'=>'eve@example.com','Mdp'=>$hash ]
        ]);
        $this->setConnection($pdo);

        $this->assertFalse(userModel::updatePassword(9, ''));
        $this->assertTrue(userModel::updatePassword(9, 'NEW'));
    }

    public function testDeleteUserAndByUsername(): void
    {
        $hash = password_hash('x', PASSWORD_DEFAULT);
        $pdo = new UM_FakePDO([
            [ 'ID'=>10,'Username'=>'zoe','Email'=>'zoe@example.com','Mdp'=>$hash ]
        ]);
        $this->setConnection($pdo);
        $model = new userModel();

        $this->assertTrue($model->deleteUser(10));
        $pdo->users['zoe'] = [ 'ID'=>11,'Username'=>'zoe','Email'=>'zoe@example.com','Mdp'=>$hash ];
        $this->assertTrue($model->deleteUserByUsername('zoe'));
    }

    public function testUpdateUsernameAndEmail(): void
    {
        $hash = password_hash('x', PASSWORD_DEFAULT);
        $pdo = new UM_FakePDO([
            [ 'ID'=>20,'Username'=>'henry','Email'=>'henry@old.com','Mdp'=>$hash ],
            [ 'ID'=>21,'Username'=>'taken','Email'=>'taken@example.com','Mdp'=>$hash ],
        ]);
        $this->setConnection($pdo);
        $model = new userModel();

        // username success
        $r1 = $model->updateUsername('henry', 'harry');
        $this->assertTrue($r1['success']);

        // username duplicate
        $r2 = $model->updateUsername('harry', 'taken');
        $this->assertFalse($r2['success']);
        $this->assertStringContainsString('déjà pris', $r2['message']);

        // email invalid
        $r3 = $model->updateEmail('harry', 'not-an-email');
        $this->assertFalse($r3['success']);

        // email duplicate
        $r4 = $model->updateEmail('harry', 'taken@example.com');
        $this->assertFalse($r4['success']);

        // email success
        $r5 = $model->updateEmail('harry', 'new@example.com');
        $this->assertTrue($r5['success']);
    }

    public function testGetDbStatus(): void
    {
        $pdo1 = new UM_FakePDO([], true);
        $this->setConnection($pdo1);
        $model = new userModel();
        $st1 = $model->getDbStatus();
        $this->assertTrue($st1['available']);

        $pdo2 = new UM_FakePDO([], false);
        $this->setConnection($pdo2);
        $st2 = $model->getDbStatus();
        $this->assertFalse($st2['available']);
    }
}

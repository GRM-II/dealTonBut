<?php

use PHPUnit\Framework\TestCase;

/**
 * Unit test for the envReader class
 *
 * Tests reading the .env file and accessing database
 * configuration values (host, user, password, port, database name)
 */
class envReaderTest extends TestCase
{
    private static string $rootPath;
    private static string $envPath;

    /**
     * Setup before all tests
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$rootPath = realpath(__DIR__ . '/../../../');
        self::$envPath = self::$rootPath . '/core/.env';
    }

    /**
     * Test that the envReader class exists
     */
    public function testEnvReaderClassExists(): void
    {
        $this->assertTrue(
            class_exists('envReader'),
            'The envReader class must exist'
        );
    }

    /**
     * Test that all getters exist and are public
     */
    public function testGettersExist(): void
    {
        $getters = ['getHost', 'getUser', 'getMdp', 'getPort', 'getBd'];

        foreach ($getters as $getter) {
            $this->assertTrue(
                method_exists('envReader', $getter),
                "The envReader::$getter method must exist"
            );

            $reflection = new ReflectionMethod('envReader', $getter);
            $this->assertTrue(
                $reflection->isPublic(),
                "The $getter method must be public"
            );
        }
    }

    /**
     * Test that all private properties exist
     */
    public function testPrivatePropertiesExist(): void
    {
        $reflection = new ReflectionClass('envReader');

        $properties = ['host', 'user', 'mdp', 'port', 'bd'];

        foreach ($properties as $property) {
            $this->assertTrue(
                $reflection->hasProperty($property),
                "The $property property must exist"
            );

            $prop = $reflection->getProperty($property);
            $this->assertTrue(
                $prop->isPrivate(),
                "The $property property must be private"
            );
        }
    }

    /**
     * Test that the constructor exists
     */
    public function testConstructorExists(): void
    {
        $reflection = new ReflectionClass('envReader');

        $this->assertTrue(
            $reflection->hasMethod('__construct'),
            'The constructor must exist'
        );

        $constructor = $reflection->getMethod('__construct');
        $this->assertTrue(
            $constructor->isPublic(),
            'The constructor must be public'
        );
    }

    /**
     * Test that the .env file exists (prerequisite)
     */
    public function testEnvFileExists(): void
    {
        $this->assertFileExists(
            self::$envPath,
            'The .env file must exist in core/ for envReader to work'
        );

        $this->assertFileIsReadable(
            self::$envPath,
            'The .env file must be readable'
        );
    }

    /**
     * Test envReader instantiation with a valid .env file
     */
    public function testInstantiationWithValidEnvFile(): void
    {
        // This test requires the .env file to exist
        if (!file_exists(self::$envPath)) {
            $this->markTestSkipped('The .env file does not exist, test skipped');
        }

        try {
            $envReader = new envReader();

            $this->assertInstanceOf(
                envReader::class,
                $envReader,
                'The constructor must create an envReader instance'
            );
        } catch (Exception $e) {
            $this->fail('The constructor should not throw an exception with a valid .env file: ' . $e->getMessage());
        }
    }

    /**
     * Test that getHost returns a string
     */
    public function testGetHostReturnsString(): void
    {
        if (!file_exists(self::$envPath)) {
            $this->markTestSkipped('The .env file does not exist, test skipped');
        }

        $envReader = new envReader();
        $host = $envReader->getHost();

        $this->assertIsString($host, 'getHost must return a string');
        $this->assertNotEmpty($host, 'getHost must not return an empty string');
    }

    /**
     * Test that getUser returns a string
     */
    public function testGetUserReturnsString(): void
    {
        if (!file_exists(self::$envPath)) {
            $this->markTestSkipped('The .env file does not exist, test skipped');
        }

        $envReader = new envReader();
        $user = $envReader->getUser();

        $this->assertIsString($user, 'getUser must return a string');
        $this->assertNotEmpty($user, 'getUser must not return an empty string');
    }

    /**
     * Test that getMdp returns a string
     */
    public function testGetMdpReturnsString(): void
    {
        if (!file_exists(self::$envPath)) {
            $this->markTestSkipped('The .env file does not exist, test skipped');
        }

        $envReader = new envReader();
        $mdp = $envReader->getMdp();

        $this->assertIsString($mdp, 'getMdp must return a string');
        // The password may be empty, so we do not test assertNotEmpty
    }

    /**
     * Test that getPort returns a string
     */
    public function testGetPortReturnsString(): void
    {
        if (!file_exists(self::$envPath)) {
            $this->markTestSkipped('The .env file does not exist, test skipped');
        }

        $envReader = new envReader();
        $port = $envReader->getPort();

        $this->assertIsString($port, 'getPort must return a string');
        $this->assertNotEmpty($port, 'getPort must not return an empty string');

        // Check that it is a valid number
        $this->assertMatchesRegularExpression(
            '/^\d+$/',
            $port,
            'The port must be a number'
        );
    }

    /**
     * Test that getBd returns a string
     */
    public function testGetBdReturnsString(): void
    {
        if (!file_exists(self::$envPath)) {
            $this->markTestSkipped('The .env file does not exist, test skipped');
        }

        $envReader = new envReader();
        $bd = $envReader->getBd();

        $this->assertIsString($bd, 'getBd must return a string');
        $this->assertNotEmpty($bd, 'getBd must not return an empty string');
    }

    /**
     * Test that all getters return consistent values
     */
    public function testAllGettersReturnConsistentValues(): void
    {
        if (!file_exists(self::$envPath)) {
            $this->markTestSkipped('The .env file does not exist, test skipped');
        }

        $envReader = new envReader();

        // Call each getter twice to check consistency
        $host1 = $envReader->getHost();
        $host2 = $envReader->getHost();
        $this->assertSame($host1, $host2, 'getHost must return the same value');

        $user1 = $envReader->getUser();
        $user2 = $envReader->getUser();
        $this->assertSame($user1, $user2, 'getUser must return the same value');

        $port1 = $envReader->getPort();
        $port2 = $envReader->getPort();
        $this->assertSame($port1, $port2, 'getPort must return the same value');

        $bd1 = $envReader->getBd();
        $bd2 = $envReader->getBd();
        $this->assertSame($bd1, $bd2, 'getBd must return the same value');
    }

    /**
     * Test that all getters return string type
     */
    public function testAllGettersReturnStringType(): void
    {
        $reflection = new ReflectionClass('envReader');
        $getters = ['getHost', 'getUser', 'getMdp', 'getPort', 'getBd'];

        foreach ($getters as $getter) {
            $method = $reflection->getMethod($getter);
            $returnType = $method->getReturnType();

            $this->assertNotNull(
                $returnType,
                "The $getter method must have a declared return type"
            );

            $this->assertSame(
                'string',
                $returnType->getName(),
                "The $getter method must return a string"
            );
        }
    }

    /**
     * Test that all properties have string type
     */
    public function testAllPropertiesHaveStringType(): void
    {
        $reflection = new ReflectionClass('envReader');
        $properties = ['host', 'user', 'mdp', 'port', 'bd'];

        foreach ($properties as $property) {
            $prop = $reflection->getProperty($property);
            $type = $prop->getType();

            $this->assertNotNull(
                $type,
                "The $property property must have a declared type"
            );

            $this->assertSame(
                'string',
                $type->getName(),
                "The $property property must be of type string"
            );
        }
    }

    /**
     * Test the structure of the .env file (expected format)
     */
    public function testEnvFileFormat(): void
    {
        if (!file_exists(self::$envPath)) {
            $this->markTestSkipped('The .env file does not exist, test skipped');
        }

        $content = file_get_contents(self::$envPath);

        // Check that the file contains the expected keys
        $expectedKeys = ['DB_HOST', 'DB_USER', 'DB_MDP', 'DB_PORT', 'DB_NAME'];

        foreach ($expectedKeys as $key) {
            $this->assertStringContainsString(
                $key,
                $content,
                "The .env file must contain the $key key"
            );
        }
    }

    /**
     * Test that common default values are recognized
     */
    public function testCommonDefaultValues(): void
    {
        if (!file_exists(self::$envPath)) {
            $this->markTestSkipped('The .env file does not exist, test skipped');
        }

        $envReader = new envReader();

        // Test that the host is either localhost or an IP
        $host = $envReader->getHost();
        $this->assertTrue(
            $host === 'localhost' ||
            $host === '127.0.0.1' ||
            filter_var($host, FILTER_VALIDATE_IP) !== false ||
            preg_match('/^[a-zA-Z0-9\.\-]+$/', $host),
            'The host must be a valid hostname or IP'
        );

        // Test that the port is a number between 1 and 65535
        $port = $envReader->getPort();
        $portNum = (int)$port;
        $this->assertGreaterThan(0, $portNum, 'The port must be greater than 0');
        $this->assertLessThanOrEqual(65535, $portNum, 'The port must be less than or equal to 65535');
    }

    /**
     * Test multiple instantiations (each instance must be independent)
     */
    public function testMultipleInstances(): void
    {
        if (!file_exists(self::$envPath)) {
            $this->markTestSkipped('The .env file does not exist, test skipped');
        }

        $envReader1 = new envReader();
        $envReader2 = new envReader();

        $this->assertNotSame(
            $envReader1,
            $envReader2,
            'Each instantiation must create a new object'
        );

        // But the values must be identical
        $this->assertSame(
            $envReader1->getHost(),
            $envReader2->getHost(),
            'Both instances must read the same values'
        );
    }

    /**
     * Test the number of public methods
     */
    public function testPublicMethodCount(): void
    {
        $reflection = new ReflectionClass('envReader');
        $publicMethods = array_filter(
            $reflection->getMethods(ReflectionMethod::IS_PUBLIC),
            fn($method) => !$method->isConstructor() && !$method->isStatic()
        );

        $this->assertCount(
            5,
            $publicMethods,
            'envReader must have exactly 5 public methods (the 5 getters)'
        );
    }

    /**
     * Test that the .env file path is correct
     */
    public function testEnvFilePathIsCorrect(): void
    {
        $expectedPath = __DIR__ . '/../../../core/.env';
        $normalizedExpectedPath = str_replace('\\', '/', realpath(dirname($expectedPath)) . '/.env');
        $normalizedActualPath = str_replace('\\', '/', self::$envPath);

        $this->assertSame(
            $normalizedExpectedPath,
            $normalizedActualPath,
            'The .env file path must be located in core/'
        );
    }
}

<?php

use PHPUnit\Framework\TestCase;

/**
 * Test unitaire pour la classe envReader
 *
 * Teste la lecture du fichier .env et l'accès aux configurations
 * de base de données (host, user, password, port, database name)
 */
class envReaderTest extends TestCase
{
    private static string $rootPath;
    private static string $envPath;

    /**
     * Configuration avant tous les tests
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$rootPath = realpath(__DIR__ . '/../../../');
        self::$envPath = self::$rootPath . '/core/.env';
    }

    /**
     * Test que la classe envReader existe
     */
    public function testEnvReaderClassExists(): void
    {
        $this->assertTrue(
            class_exists('envReader'),
            'La classe envReader doit exister'
        );
    }

    /**
     * Test que tous les getters existent et sont publics
     */
    public function testGettersExist(): void
    {
        $getters = ['getHost', 'getUser', 'getMdp', 'getPort', 'getBd'];

        foreach ($getters as $getter) {
            $this->assertTrue(
                method_exists('envReader', $getter),
                "La méthode envReader::$getter doit exister"
            );

            $reflection = new ReflectionMethod('envReader', $getter);
            $this->assertTrue(
                $reflection->isPublic(),
                "La méthode $getter doit être publique"
            );
        }
    }

    /**
     * Test que toutes les propriétés privées existent
     */
    public function testPrivatePropertiesExist(): void
    {
        $reflection = new ReflectionClass('envReader');

        $properties = ['host', 'user', 'mdp', 'port', 'bd'];

        foreach ($properties as $property) {
            $this->assertTrue(
                $reflection->hasProperty($property),
                "La propriété $property doit exister"
            );

            $prop = $reflection->getProperty($property);
            $this->assertTrue(
                $prop->isPrivate(),
                "La propriété $property doit être privée"
            );
        }
    }

    /**
     * Test que le constructeur existe
     */
    public function testConstructorExists(): void
    {
        $reflection = new ReflectionClass('envReader');

        $this->assertTrue(
            $reflection->hasMethod('__construct'),
            'Le constructeur doit exister'
        );

        $constructor = $reflection->getMethod('__construct');
        $this->assertTrue(
            $constructor->isPublic(),
            'Le constructeur doit être public'
        );
    }

    /**
     * Test que le fichier .env existe (prérequis)
     */
    public function testEnvFileExists(): void
    {
        $this->assertFileExists(
            self::$envPath,
            'Le fichier .env doit exister dans core/ pour que envReader fonctionne'
        );

        $this->assertFileIsReadable(
            self::$envPath,
            'Le fichier .env doit être lisible'
        );
    }

    /**
     * Test de l'instanciation de envReader avec un fichier .env valide
     */
    public function testInstantiationWithValidEnvFile(): void
    {
        // Ce test nécessite que le fichier .env existe
        if (!file_exists(self::$envPath)) {
            $this->markTestSkipped('Le fichier .env n\'existe pas, test ignoré');
        }

        try {
            $envReader = new envReader();

            $this->assertInstanceOf(
                envReader::class,
                $envReader,
                'Le constructeur doit créer une instance de envReader'
            );
        } catch (Exception $e) {
            $this->fail('Le constructeur ne devrait pas lever d\'exception avec un fichier .env valide: ' . $e->getMessage());
        }
    }

    /**
     * Test que getHost retourne une chaîne
     */
    public function testGetHostReturnsString(): void
    {
        if (!file_exists(self::$envPath)) {
            $this->markTestSkipped('Le fichier .env n\'existe pas, test ignoré');
        }

        $envReader = new envReader();
        $host = $envReader->getHost();

        $this->assertIsString($host, 'getHost doit retourner une chaîne');
        $this->assertNotEmpty($host, 'getHost ne doit pas retourner une chaîne vide');
    }

    /**
     * Test que getUser retourne une chaîne
     */
    public function testGetUserReturnsString(): void
    {
        if (!file_exists(self::$envPath)) {
            $this->markTestSkipped('Le fichier .env n\'existe pas, test ignoré');
        }

        $envReader = new envReader();
        $user = $envReader->getUser();

        $this->assertIsString($user, 'getUser doit retourner une chaîne');
        $this->assertNotEmpty($user, 'getUser ne doit pas retourner une chaîne vide');
    }

    /**
     * Test que getMdp retourne une chaîne
     */
    public function testGetMdpReturnsString(): void
    {
        if (!file_exists(self::$envPath)) {
            $this->markTestSkipped('Le fichier .env n\'existe pas, test ignoré');
        }

        $envReader = new envReader();
        $mdp = $envReader->getMdp();

        $this->assertIsString($mdp, 'getMdp doit retourner une chaîne');
        // Le mot de passe peut être vide, donc on ne teste pas assertNotEmpty
    }

    /**
     * Test que getPort retourne une chaîne
     */
    public function testGetPortReturnsString(): void
    {
        if (!file_exists(self::$envPath)) {
            $this->markTestSkipped('Le fichier .env n\'existe pas, test ignoré');
        }

        $envReader = new envReader();
        $port = $envReader->getPort();

        $this->assertIsString($port, 'getPort doit retourner une chaîne');
        $this->assertNotEmpty($port, 'getPort ne doit pas retourner une chaîne vide');

        // Vérifier que c'est un nombre valide
        $this->assertMatchesRegularExpression(
            '/^\d+$/',
            $port,
            'Le port doit être un nombre'
        );
    }

    /**
     * Test que getBd retourne une chaîne
     */
    public function testGetBdReturnsString(): void
    {
        if (!file_exists(self::$envPath)) {
            $this->markTestSkipped('Le fichier .env n\'existe pas, test ignoré');
        }

        $envReader = new envReader();
        $bd = $envReader->getBd();

        $this->assertIsString($bd, 'getBd doit retourner une chaîne');
        $this->assertNotEmpty($bd, 'getBd ne doit pas retourner une chaîne vide');
    }

    /**
     * Test que tous les getters retournent des valeurs cohérentes
     */
    public function testAllGettersReturnConsistentValues(): void
    {
        if (!file_exists(self::$envPath)) {
            $this->markTestSkipped('Le fichier .env n\'existe pas, test ignoré');
        }

        $envReader = new envReader();

        // Appeler chaque getter deux fois pour vérifier la cohérence
        $host1 = $envReader->getHost();
        $host2 = $envReader->getHost();
        $this->assertSame($host1, $host2, 'getHost doit retourner la même valeur');

        $user1 = $envReader->getUser();
        $user2 = $envReader->getUser();
        $this->assertSame($user1, $user2, 'getUser doit retourner la même valeur');

        $port1 = $envReader->getPort();
        $port2 = $envReader->getPort();
        $this->assertSame($port1, $port2, 'getPort doit retourner la même valeur');

        $bd1 = $envReader->getBd();
        $bd2 = $envReader->getBd();
        $this->assertSame($bd1, $bd2, 'getBd doit retourner la même valeur');
    }

    /**
     * Test que le type de retour de tous les getters est string
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
                "La méthode $getter doit avoir un type de retour déclaré"
            );

            $this->assertSame(
                'string',
                $returnType->getName(),
                "La méthode $getter doit retourner un string"
            );
        }
    }

    /**
     * Test que toutes les propriétés ont le type string
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
                "La propriété $property doit avoir un type déclaré"
            );

            $this->assertSame(
                'string',
                $type->getName(),
                "La propriété $property doit être de type string"
            );
        }
    }

    /**
     * Test de la structure du fichier .env (format attendu)
     */
    public function testEnvFileFormat(): void
    {
        if (!file_exists(self::$envPath)) {
            $this->markTestSkipped('Le fichier .env n\'existe pas, test ignoré');
        }

        $content = file_get_contents(self::$envPath);

        // Vérifier que le fichier contient les clés attendues
        $expectedKeys = ['DB_HOST', 'DB_USER', 'DB_MDP', 'DB_PORT', 'DB_NAME'];

        foreach ($expectedKeys as $key) {
            $this->assertStringContainsString(
                $key,
                $content,
                "Le fichier .env doit contenir la clé $key"
            );
        }
    }

    /**
     * Test que les valeurs par défaut communes sont reconnues
     */
    public function testCommonDefaultValues(): void
    {
        if (!file_exists(self::$envPath)) {
            $this->markTestSkipped('Le fichier .env n\'existe pas, test ignoré');
        }

        $envReader = new envReader();

        // Tester que le host est soit localhost, soit une IP
        $host = $envReader->getHost();
        $this->assertTrue(
            $host === 'localhost' ||
            $host === '127.0.0.1' ||
            filter_var($host, FILTER_VALIDATE_IP) !== false ||
            preg_match('/^[a-zA-Z0-9\.\-]+$/', $host),
            'Le host doit être un nom d\'hôte ou une IP valide'
        );

        // Tester que le port est un nombre entre 1 et 65535
        $port = $envReader->getPort();
        $portNum = (int)$port;
        $this->assertGreaterThan(0, $portNum, 'Le port doit être supérieur à 0');
        $this->assertLessThanOrEqual(65535, $portNum, 'Le port doit être inférieur ou égal à 65535');
    }

    /**
     * Test de multiple instanciations (chaque instance doit être indépendante)
     */
    public function testMultipleInstances(): void
    {
        if (!file_exists(self::$envPath)) {
            $this->markTestSkipped('Le fichier .env n\'existe pas, test ignoré');
        }

        $envReader1 = new envReader();
        $envReader2 = new envReader();

        $this->assertNotSame(
            $envReader1,
            $envReader2,
            'Chaque instanciation doit créer un nouvel objet'
        );

        // Mais les valeurs doivent être identiques
        $this->assertSame(
            $envReader1->getHost(),
            $envReader2->getHost(),
            'Les deux instances doivent lire les mêmes valeurs'
        );
    }

    /**
     * Test du nombre de méthodes publiques
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
            'envReader doit avoir exactement 5 méthodes publiques (les 5 getters)'
        );
    }

    /**
     * Test que le chemin du fichier .env est correct
     */
    public function testEnvFilePathIsCorrect(): void
    {
        $expectedPath = __DIR__ . '/../../../core/.env';
        $normalizedExpectedPath = str_replace('\\', '/', realpath(dirname($expectedPath)) . '/.env');
        $normalizedActualPath = str_replace('\\', '/', self::$envPath);

        $this->assertSame(
            $normalizedExpectedPath,
            $normalizedActualPath,
            'Le chemin du fichier .env doit être dans core/'
        );
    }
}

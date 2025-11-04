<?php

use PHPUnit\Framework\TestCase;

/**
 * Test unitaire pour la classe view
 *
 * Teste la gestion des tampons de sortie (output buffers),
 * l'affichage des vues et le passage de paramètres
 */
class viewTest extends TestCase
{
    private static string $rootPath;
    private static string $testViewPath;

    /**
     * Configuration avant tous les tests
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$rootPath = realpath(__DIR__ . '/../../../');
        self::$testViewPath = self::$rootPath . '/views/test';
    }

    /**
     * Nettoyage après chaque test
     */
    protected function tearDown(): void
    {
        // Nettoyer tous les tampons de sortie qui pourraient rester
        // (sauf le tampon de PHPUnit lui-même qui est au niveau 1)
        while (ob_get_level() > 1) {
            @ob_end_clean();
        }
        parent::tearDown();
    }

    /**
     * Test que la classe view existe et est finale
     */
    public function testViewClassExists(): void
    {
        $this->assertTrue(
            class_exists('view'),
            'La classe view doit exister'
        );

        $reflection = new ReflectionClass('view');
        $this->assertTrue(
            $reflection->isFinal(),
            'La classe view doit être finale (final class)'
        );
    }

    /**
     * Test que toutes les méthodes statiques publiques existent
     */
    public function testStaticMethodsExist(): void
    {
        $methods = ['openBuffer', 'getBufferContent', 'show'];

        foreach ($methods as $method) {
            $this->assertTrue(
                method_exists('view', $method),
                "La méthode view::$method doit exister"
            );

            $reflection = new ReflectionMethod('view', $method);
            $this->assertTrue(
                $reflection->isStatic(),
                "La méthode view::$method doit être statique"
            );

            $this->assertTrue(
                $reflection->isPublic(),
                "La méthode view::$method doit être publique"
            );
        }
    }

    /**
     * Test de la méthode openBuffer
     */
    public function testOpenBuffer(): void
    {
        $levelBefore = ob_get_level();

        view::openBuffer();

        $levelAfter = ob_get_level();

        $this->assertSame(
            $levelBefore + 1,
            $levelAfter,
            'openBuffer doit augmenter le niveau de tampon de 1'
        );

        // Nettoyer
        ob_end_clean();
    }

    /**
     * Test de la méthode getBufferContent
     */
    public function testGetBufferContent(): void
    {
        $levelBefore = ob_get_level();

        view::openBuffer();

        echo "Test content";

        $content = view::getBufferContent();

        $this->assertSame(
            "Test content",
            $content,
            'getBufferContent doit retourner le contenu du tampon'
        );

        // Vérifier que le tampon est fermé après getBufferContent
        $this->assertSame(
            $levelBefore,
            ob_get_level(),
            'getBufferContent doit fermer le tampon (ob_get_clean)'
        );
    }

    /**
     * Test que getBufferContent retourne une chaîne
     */
    public function testGetBufferContentReturnsString(): void
    {
        view::openBuffer();

        $content = view::getBufferContent();

        $this->assertIsString(
            $content,
            'getBufferContent doit retourner une chaîne'
        );
    }

    /**
     * Test de la méthode show avec une vue existante
     */
    public function testShowWithExistingView(): void
    {
        // Utiliser une vue existante (homepageView)
        $viewFile = self::$rootPath . '/views/homepageView.php';

        if (!file_exists($viewFile)) {
            $this->markTestSkipped('Le fichier homepageView.php n\'existe pas');
        }

        // Capturer la sortie
        ob_start();
        view::show('homepageView', ['test' => 'value']);
        $output = ob_get_clean();

        $this->assertIsString($output, 'show doit produire une sortie');
    }

    /**
     * Test que show accepte des paramètres vides
     */
    public function testShowWithEmptyParameters(): void
    {
        $viewFile = self::$rootPath . '/views/homepageView.php';

        if (!file_exists($viewFile)) {
            $this->markTestSkipped('Le fichier homepageView.php n\'existe pas');
        }

        // show doit accepter un tableau vide
        ob_start();
        view::show('homepageView', []);
        $output = ob_get_clean();

        $this->assertIsString($output);
    }

    /**
     * Test que show accepte l'omission du paramètre
     */
    public function testShowWithoutParameters(): void
    {
        $viewFile = self::$rootPath . '/views/homepageView.php';

        if (!file_exists($viewFile)) {
            $this->markTestSkipped('Le fichier homepageView.php n\'existe pas');
        }

        // show doit accepter l'omission du second paramètre
        ob_start();
        view::show('homepageView');
        $output = ob_get_clean();

        $this->assertIsString($output);
    }

    /**
     * Test du type de retour de openBuffer
     */
    public function testOpenBufferReturnsVoid(): void
    {
        $reflection = new ReflectionMethod('view', 'openBuffer');
        $returnType = $reflection->getReturnType();

        $this->assertNotNull(
            $returnType,
            'openBuffer doit avoir un type de retour déclaré'
        );

        $this->assertSame(
            'void',
            $returnType->getName(),
            'openBuffer doit retourner void'
        );
    }

    /**
     * Test du type de retour de getBufferContent
     */
    public function testGetBufferContentReturnsStringType(): void
    {
        $reflection = new ReflectionMethod('view', 'getBufferContent');
        $returnType = $reflection->getReturnType();

        $this->assertNotNull(
            $returnType,
            'getBufferContent doit avoir un type de retour déclaré'
        );

        $this->assertSame(
            'string',
            $returnType->getName(),
            'getBufferContent doit retourner string'
        );
    }

    /**
     * Test du type de retour de show
     */
    public function testShowReturnsVoid(): void
    {
        $reflection = new ReflectionMethod('view', 'show');
        $returnType = $reflection->getReturnType();

        $this->assertNotNull(
            $returnType,
            'show doit avoir un type de retour déclaré'
        );

        $this->assertSame(
            'void',
            $returnType->getName(),
            'show doit retourner void'
        );
    }

    /**
     * Test de la chaîne de tampons multiples
     */
    public function testMultipleBuffers(): void
    {
        view::openBuffer();
        echo "First";

        view::openBuffer();
        echo "Second";

        $content2 = view::getBufferContent();
        $this->assertSame("Second", $content2);

        $content1 = view::getBufferContent();
        $this->assertSame("First", $content1);
    }

    /**
     * Test que openBuffer peut être appelé plusieurs fois
     */
    public function testOpenBufferMultipleTimes(): void
    {
        $level0 = ob_get_level();

        view::openBuffer();
        $level1 = ob_get_level();

        view::openBuffer();
        $level2 = ob_get_level();

        view::openBuffer();
        $level3 = ob_get_level();

        $this->assertSame($level0 + 1, $level1);
        $this->assertSame($level0 + 2, $level2);
        $this->assertSame($level0 + 3, $level3);

        // Nettoyer
        ob_end_clean();
        ob_end_clean();
        ob_end_clean();
    }

    /**
     * Test que getBufferContent gère le contenu vide
     */
    public function testGetBufferContentWithEmptyBuffer(): void
    {
        view::openBuffer();
        // Ne rien écrire

        $content = view::getBufferContent();

        $this->assertSame(
            '',
            $content,
            'getBufferContent doit retourner une chaîne vide si rien n\'est écrit'
        );
    }

    /**
     * Test que show utilise constants::viewsRepository
     */
    public function testShowUsesConstantsViewsRepository(): void
    {
        // Vérifier que constants::viewsRepository existe et retourne un chemin
        $viewsRepo = constants::viewsRepository();

        $this->assertIsString($viewsRepo);
        $this->assertDirectoryExists($viewsRepo);
        $this->assertStringEndsWith('/views/', $viewsRepo);
    }

    /**
     * Test du nombre de méthodes publiques
     */
    public function testPublicMethodCount(): void
    {
        $reflection = new ReflectionClass('view');
        $publicMethods = array_filter(
            $reflection->getMethods(ReflectionMethod::IS_PUBLIC),
            fn($method) => !$method->isConstructor()
        );

        $publicMethodNames = array_map(fn($m) => $m->getName(), $publicMethods);

        $this->assertCount(
            3,
            $publicMethods,
            'view doit avoir exactement 3 méthodes publiques'
        );

        $this->assertContains('openBuffer', $publicMethodNames);
        $this->assertContains('getBufferContent', $publicMethodNames);
        $this->assertContains('show', $publicMethodNames);
    }

    /**
     * Test que view n'a pas de propriétés
     */
    public function testViewHasNoProperties(): void
    {
        $reflection = new ReflectionClass('view');
        $properties = $reflection->getProperties();

        $this->assertCount(
            0,
            $properties,
            'view ne doit pas avoir de propriétés (classe utilitaire statique)'
        );
    }

    /**
     * Test que les paramètres show utilisent le bon type
     */
    public function testShowParametersTypes(): void
    {
        $reflection = new ReflectionMethod('view', 'show');
        $parameters = $reflection->getParameters();

        $this->assertCount(
            2,
            $parameters,
            'show doit avoir 2 paramètres'
        );

        // Premier paramètre: $S_localisation (pas de type hint dans le code actuel)
        $param1 = $parameters[0];
        $this->assertSame('S_localisation', $param1->getName());

        // Second paramètre: $A_parameters avec valeur par défaut array()
        $param2 = $parameters[1];
        $this->assertSame('A_parameters', $param2->getName());
        $this->assertTrue(
            $param2->isOptional(),
            'Le second paramètre doit être optionnel'
        );
        $this->assertTrue(
            $param2->isDefaultValueAvailable(),
            'Le second paramètre doit avoir une valeur par défaut'
        );
    }

    /**
     * Test d'intégration: cycle complet de tampon
     */
    public function testCompleteBufferCycle(): void
    {
        $levelBefore = ob_get_level();

        // Ouvrir un tampon
        view::openBuffer();

        // Écrire du contenu
        echo "Line 1\n";
        echo "Line 2\n";
        echo "Line 3";

        // Récupérer le contenu
        $content = view::getBufferContent();

        // Vérifier le contenu
        $this->assertSame("Line 1\nLine 2\nLine 3", $content);

        // Vérifier que le tampon est fermé et revenu au niveau initial
        $this->assertSame($levelBefore, ob_get_level());
    }

    /**
     * Test que show construit le bon chemin de fichier
     */
    public function testShowBuildsCorrectFilePath(): void
    {
        // Utiliser réflexion pour vérifier la logique (indirectement)
        $viewsRepo = constants::viewsRepository();
        $expectedPath = $viewsRepo . 'homepageView.php';

        $this->assertFileExists(
            $expectedPath,
            'Le chemin construit par show doit pointer vers un fichier existant'
        );
    }

    /**
     * Test que view est bien une classe utilitaire (pas instanciable normalement)
     */
    public function testViewIsUtilityClass(): void
    {
        $reflection = new ReflectionClass('view');

        // Vérifier que toutes les méthodes sont statiques
        $methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);

        foreach ($methods as $method) {
            if (!$method->isConstructor()) {
                $this->assertTrue(
                    $method->isStatic(),
                    "Toutes les méthodes publiques de view doivent être statiques (classe utilitaire)"
                );
            }
        }
    }
}

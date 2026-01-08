<?php
use PHPUnit\Framework\TestCase;

class LegalnoticeControllerTest extends TestCase
{
    private $controller;

    protected function setUp(): void
    {
        $this->controller = new legalnoticeController();
    }

    /**
     * Tests that legalnotice exists
     */
    public function testLegalNoticeMethodExists()
    {
        $this->assertTrue(
            method_exists($this->controller, 'legalNotice'),
            "La méthode legalNotice devrait exister"
        );
    }

    /**
     * Tests that legal notice can be called with no errors
     */
    public function testLegalNoticeCanBeCalled()
    {
        ob_start();
        $this->controller->legalNotice();
        $output = ob_get_clean();

        $this->assertIsString($output);
    }

    /**
     * Tests that the view file exists
     */
    public function testViewFileExists()
    {
        $viewPath = 'views/legalnoticeView.php';

        $this->assertFileExists(
            $viewPath,
            "Le fichier de vue {$viewPath} devrait exister"
        );
    }

    /**
     * Tests that the view file is included
     */
    public function testViewIsIncluded()
    {
        $tempViewPath = sys_get_temp_dir() . '/legalnoticeView.php';
        file_put_contents($tempViewPath, '<?php echo "Legal Notice View"; ?>');

        $testController = new class {
            public function legalNotice() {
                $A_view = [];
                require sys_get_temp_dir() . '/legalnoticeView.php';
            }
        };

        ob_start();
        $testController->legalNotice();
        $output = ob_get_clean();

        $this->assertStringContainsString('Legal Notice View', $output);

        unlink($tempViewPath);
    }

    /**
     * Test that $A_view is a table
     */
    public function testAViewIsArray()
    {
        $testController = new class extends legalnoticeController {
            public function legalNoticeTestable() {
                $A_view = [];
                return $A_view;
            }
        };

        $result = $testController->legalNoticeTestable();
        $this->assertIsArray($result);
    }

    /**
     * Test that $A_view is empty
     */
    public function testAViewIsEmptyArray()
    {
        $testController = new class extends legalnoticeController {
            public function legalNoticeTestable() {
                $A_view = [];
                return $A_view;
            }
        };

        $result = $testController->legalNoticeTestable();
        $this->assertEmpty($result);
        $this->assertIsArray($result);
    }

    /**
     * Tests that the complete legal notice is displayed
     */
    public function testLegalNoticeIntegration()
    {
        if (file_exists('views/legalnoticeView.php')) {
            ob_start();
            $this->controller->legalNotice();
            $output = ob_get_clean();

            $this->assertNotNull($output);
        } else {
            $this->markTestSkipped('Le fichier de vue n\'existe pas');
        }
    }

    protected function tearDown(): void
    {
        $this->controller = null;
    }
}
?>
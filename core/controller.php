<?php
final class controller
{
    /** @var array{controller: string, action: string} */
    private array $url;

    /** @var array<string, mixed> */
    private array $params = [];

    public function __construct(?string $S_controller, ?string $S_action)
    {
        $this->url['controller'] = $this->controllerName($S_controller);
        $this->url['action'] = $this->actionName($S_action);
    }

    private function controllerName(?string $S_controller): string
    {
        if (empty($S_controller)) {
            $S_controller = 'homepage';
        }
        $S_name = trim($S_controller) . 'Controller';
        return htmlspecialchars($S_name, ENT_QUOTES, 'UTF-8');
    }

    private function actionName(?string $S_action): string
    {
        if (empty($S_action)) {
            return 'login';
        }
        return htmlspecialchars($S_action, ENT_QUOTES, 'UTF-8');
    }

    public function execute(): void
    {
        $controller = $this->url['controller'];
        $action = $this->url['action'];

        if (!class_exists($controller)) {
            throw new RuntimeException("'$controller' est introuvable.");
        }

        $controllerInstance = new $controller();

        if (!method_exists($controllerInstance, $action)) {
            throw new RuntimeException("L'action '$action' est introuvable dans le contrôleur '$controller'.");
        }

        try {
            $callable = [$controllerInstance, $action];
            if (is_callable($callable)) {
                call_user_func($callable);
            }
        } catch (\Throwable $e) {
            error_log("Erreur exécution action '$action': " . $e->getMessage());
            throw new RuntimeException("Erreur lors de l'exécution de l'action '$action'.");
        }

        if (method_exists($controllerInstance, 'getParams')) {
            $this->params = $controllerInstance->getParams();
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function getParams(): array
    {
        return $this->params;
    }
}
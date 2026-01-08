<?php

/**
 * Main controller class responsible for routing and executing controller actions.
 *
 * This class handles the routing mechanism by managing controller and action names,
 * instantiating the appropriate controller, and executing the requested action.
 * It also manages parameters passed between controllers.
 *
 * @final
 */
final class controller
{
    /**
     * Stores the controller and action names to be executed.
     *
     * @var array{controller: string, action: string}
     */
    private array $url;

    /**
     * Stores parameters returned by the executed controller.
     *
     * @var array<string, mixed>
     */
    private array $params = [];

    /**
     * Initializes the controller with the specified controller and action names.
     *
     * @param string|null $S_controller The controller name (without 'Controller' suffix)
     * @param string|null $S_action The action/method name to execute
     */
    public function __construct(?string $S_controller, ?string $S_action)
    {
        $this->url['controller'] = $this->controllerName($S_controller);
        $this->url['action'] = $this->actionName($S_action, $this->url['controller']);
    }

    /**
     * Processes and sanitizes the controller name.
     *
     * If no controller is provided, defaults to 'homepage'.
     * Appends 'Controller' suffix and sanitizes the name.
     *
     * @param string|null $S_controller The raw controller name
     * @return string The sanitized controller name with 'Controller' suffix
     */
    private function controllerName(?string $S_controller): string
    {
        if (empty($S_controller)) {
            $S_controller = 'homepage';
        }
        $S_name = trim($S_controller) . 'Controller';
        return htmlspecialchars($S_name, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Processes and sanitizes the action name.
     *
     * If no action is provided, defaults to 'login' for userController
     * and 'index' for all other controllers.
     *
     * @param string|null $S_action The raw action name
     * @param string $controller The controller name (used to determine default action)
     * @return string The sanitized action name
     */
    private function actionName(?string $S_action, string $controller): string
    {
        if (empty($S_action)) {
            if ($controller === 'userController') {
                return 'login';
            }
            return 'index';
        }
        return htmlspecialchars($S_action, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Executes the specified controller action.
     *
     * @throws RuntimeException If the controller class is not found
     * @throws RuntimeException If the action method is not found in the controller
     * @return void
     */
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

            echo "<pre>";
            echo "Erreur complète : " . $e->getMessage() . "\n";
            echo $e->getTraceAsString();
            echo "</pre>";
            exit;
        }

        if (method_exists($controllerInstance, 'getParams')) {
            $this->params = $controllerInstance->getParams();
        }
    }

    /**
     * Retrieves the parameters set by the executed controller.
     *
     * @return array<string, mixed> An associative array of parameters
     */
    public function getParams(): array
    {
        return $this->params;
    }
}
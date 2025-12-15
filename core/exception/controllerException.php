<?php

/**
 * Exception personnalisée pour les erreurs liées aux contrôleurs
 */
class ControllerException extends Exception
{
    private string $controllerName;
    private string $actionName;

    public function __construct(
        string $message = "",
        string $controllerName = "",
        string $actionName = "",
        int $code = 0,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->controllerName = $controllerName;
        $this->actionName = $actionName;
    }

    public function getControllerName(): string
    {
        return $this->controllerName;
    }

    public function getActionName(): string
    {
        return $this->actionName;
    }
}

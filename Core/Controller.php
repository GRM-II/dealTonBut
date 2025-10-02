<?php
final class Controller
{
    private $_A_splitUrl;

    private $_A_urlParams;

    private $_A_postParams;

    public function __construct ($S_url, $A_postParams)
    {
        // On élimine l'éventuel slash en fin d'URL sinon notre explode renverra une dernière entrée vide
        if ('/' == substr($S_url, -1, 1)) {
            $S_url = substr($S_url, 0, strlen($S_url) - 1);
        }

        // On éclate l'URL, elle va prendre place dans un tableau
        $A_splitUrl = explode('/', $S_url);

        if (empty($A_splitUrl[0])) {
            // Nous avons pris le parti de suffixer tous les controleurs par "_controleur"
            $A_splitUrl[0] = 'defaultController';
        } else {
            $A_splitUrl[0] = ucfirst($A_splitUrl[0]). '_controller';
        }

        if (empty($A_splitUrl[1])) {
            // L'action est vide ! On la valorise par défaut
            $A_splitUrl[1] = 'defaultAction';
        } else {
            // On part du principe que toutes nos actions sont suffixées par 'Action'...à nous de le rajouter
            $A_splitUrl[1] = 'M_' . $A_splitUrl[1];
        }


        // on dépile 2 fois de suite depuis le début, c'est à dire qu'on enlève de notre tableau le contrôleur et l'action
        // il ne reste donc que les éventuels parametres (si nous en avons)...
        $this->_A_splitUrl['controller'] = array_shift($A_splitUrl); // on recupere le contrôleur
        $this->_A_splitUrl['method']     = array_shift($A_splitUrl); // puis l'action

        // ...on stocke ces éventuels parametres dans la variable d'instance qui leur est réservée
        $this->_A_urlParams = $A_splitUrl;

        // On  s'occupe du tableau $A_postParams
        $this->_A_postParams = $A_postParams;


    }

    // On exécute notre triplet

    public function execute()
    {
        if (!class_exists($this->_A_splitUrl['controller'])) {
            throw new ControllerException($this->_A_splitUrl['controller'] . " doesn't exist.");
        }

        if (!method_exists($this->_A_splitUrl['controller'], $this->_A_splitUrl['method'])) {
            throw new ControllerException($this->_A_splitUrl['method'] . " of controller " .
                $this->_A_splitUrl['controller'] . " doesn't exist.");
        }

        $B_called = call_user_func_array(array(new $this->_A_splitUrl['controller'],
            $this->_A_splitUrl['method']), array($this->_A_urlParams, $this->_A_postParams ));

        if (false === $B_called) {
            throw new ControllerException("The method " . $this->_A_splitUrl['method'] .
                " of controller " . $this->_A_splitUrl['controller'] . " encountered an error.");
        }
    }
}
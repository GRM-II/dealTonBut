<?php

final class User_controller
{
    public function defaultAction()
    {
        $O_user =  new User_model();
        View::show('user/Login', array('user' =>  $O_user->donneMessage()));
    }

    public function register(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo 'Méthode non autorisée';
            return;
        }

        $username = isset($_POST['username']) ? (string)$_POST['username'] : '';
        $email = isset($_POST['email']) ? (string)$_POST['email'] : '';
        $password = isset($_POST['password']) ? (string)$_POST['password'] : '';
        $confirm = isset($_POST['confirm_password']) ? (string)$_POST['confirm_password'] : '';

        if ($password !== $confirm) {
            $message = 'Les mots de passe ne correspondent pas.';
            $this->renderResult(false, $message);
            return;
        }

        $userModel = new User_model();
        $result = $userModel->createUser($username, $email, $password);

        $this->renderResult($result['success'], $result['message']);
    }

    private function renderResult(bool $success, string $message): void
    {
        View::show('homepageView', ['flash' => ['success' => $success, 'message' => $message]]);
    }
}

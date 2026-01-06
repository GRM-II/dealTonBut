<?php

final class marketpageController
{
    public function index(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Vérifier si l'utilisateur est connecté
        $isLoggedIn = !empty($_SESSION['user']) && is_array($_SESSION['user']);

        $status = $this->getDbStatus();
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        // Récupérer les offres depuis la base de données
        $offers = $this->getOffers();

        view::show('marketpageView', [
            'user' => $_SESSION['user'] ?? null,
            'isLoggedIn' => $isLoggedIn,
            'db_status' => $status,
            'flash' => $flash,
            'offers' => $offers
        ]);
    }

    public function createOffer(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            header('Location: ?controller=homepage&action=login');
            exit;
        }

        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $price = trim($_POST['price'] ?? '');
        $category = trim($_POST['category'] ?? '');

        if (empty($title) || empty($description) || empty($price)) {
            $_SESSION['flash'] = ['success' => false, 'message' => 'Tous les champs sont requis.'];
        } elseif (!is_numeric($price) || $price <= 0) {
            $_SESSION['flash'] = ['success' => false, 'message' => 'Le prix doit être un nombre valide.'];
        } else {
            if (!class_exists('offerModel', false)) {
                require_once constants::modelsRepository() . 'offerModel.php';
            }
            $result = offerModel::createOffer($_SESSION['user_id'], $title, $description, $price, $category);

            if ($result['success']) {
                $_SESSION['flash'] = ['success' => true, 'message' => $result['message']];
            } else {
                $_SESSION['flash'] = ['success' => false, 'message' => $result['message']];
            }
        }

        header('Location: ?controller=marketpage&action=index');
        exit;
    }

    public function deleteOffer(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            header('Location: ?controller=homepage&action=login');
            exit;
        }

        $offerId = $_POST['offer_id'] ?? null;

        if ($offerId) {
            if (!class_exists('offerModel', false)) {
                require_once constants::modelsRepository() . 'offerModel.php';
            }
            /** @var bool $result */
            $result = offerModel::deleteOffer((int)$offerId, $_SESSION['user_id']);

            if ($result) {
                $_SESSION['flash'] = ['success' => true, 'message' => 'Offre supprimée avec succès.'];
            } else {
                $_SESSION['flash'] = ['success' => false, 'message' => 'Erreur lors de la suppression de l\'offre.'];
            }
        }

        header('Location: ?controller=marketpage&action=index');
        exit;
    }

    /**
     * Récupère toutes les offres
     *
     * @return array<int, array<string, mixed>>
     */
    private function getOffers(): array
    {
        if (!class_exists('offerModel', false)) {
            require_once constants::modelsRepository() . 'offerModel.php';
        }
        return offerModel::getAllOffers();
    }

    /**
     * Récupère le status de la BDD
     *
     * @return array{available: bool, message: string, details?: string}
     */
    private function getDbStatus(): array
    {
        if (!class_exists('userModel', false)) {
            require_once constants::modelsRepository() . 'userModel.php';
        }
        $userModel = new userModel();
        return $userModel->getDbStatus();
    }
}
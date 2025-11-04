<?php

class marketpageController
{
    public function index()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Vérifier si l'utilisateur est connecté
        $isLoggedIn = !empty($_SESSION['user']) && is_array($_SESSION['user']);

        $status = $this->getDbStatus();
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        // Récupérer les offres depuis la base de données - désactivé pour le moment
        $offers = []; // Tableau vide en attendant l'implémentation de offerModel

        view::show('marketpageView', [
            'user' => $_SESSION['user'] ?? null,
            'isLoggedIn' => $isLoggedIn,
            'db_status' => $status,
            'flash' => $flash,
            'offers' => $offers
        ]);
    }

    // Fonctionnalité désactivée - nécessite offerModel et table 'offers'
    // public function createOffer()
    // {
    //     if (session_status() === PHP_SESSION_NONE) {
    //         session_start();
    //     }
    //
    //     if (!isset($_SESSION['user_id'])) {
    //         header('Location: ?controller=homepage&action=login');
    //         exit;
    //     }
    //
    //     $title = trim($_POST['title'] ?? '');
    //     $description = trim($_POST['description'] ?? '');
    //     $price = trim($_POST['price'] ?? '');
    //     $category = trim($_POST['category'] ?? '');
    //
    //     if (empty($title) || empty($description) || empty($price)) {
    //         $_SESSION['flash'] = ['success' => false, 'message' => 'Tous les champs sont requis.'];
    //     } elseif (!is_numeric($price) || $price <= 0) {
    //         $_SESSION['flash'] = ['success' => false, 'message' => 'Le prix doit être un nombre valide.'];
    //     } else {
    //         require_once 'models/offerModel.php';
    //         $result = offerModel::createOffer($_SESSION['user_id'], $title, $description, $price, $category);
    //
    //         if ($result) {
    //             $_SESSION['flash'] = ['success' => true, 'message' => 'Offre créée avec succès !'];
    //         } else {
    //             $_SESSION['flash'] = ['success' => false, 'message' => 'Erreur lors de la création de l\'offre.'];
    //         }
    //     }
    //
    //     header('Location: ?controller=marketpage&action=index');
    //     exit;
    // }

    // Fonctionnalité désactivée - nécessite offerModel et table 'offers'
    // public function deleteOffer()
    // {
    //     if (session_status() === PHP_SESSION_NONE) {
    //         session_start();
    //     }
    //
    //     if (!isset($_SESSION['user_id'])) {
    //         header('Location: ?controller=homepage&action=login');
    //         exit;
    //     }
    //
    //     $offerId = $_POST['offer_id'] ?? null;
    //
    //     if ($offerId) {
    //         require_once 'models/offerModel.php';
    //         $result = offerModel::deleteOffer($offerId, $_SESSION['user_id']);
    //
    //         if ($result) {
    //             $_SESSION['flash'] = ['success' => true, 'message' => 'Offre supprimée avec succès.'];
    //         } else {
    //             $_SESSION['flash'] = ['success' => false, 'message' => 'Erreur lors de la suppression de l\'offre.'];
    //         }
    //     }
    //
    //     header('Location: ?controller=marketpage&action=index');
    //     exit;
    // }

    // Récupère toutes les offres - désactivé
    // private function getOffers()
    // {
    //     require_once 'models/offerModel.php';
    //     return offerModel::getAllOffers();
    // }

    // Récupère le status de la BDD
    private function getDbStatus()
    {
        require_once 'models/userModel.php';
        $userModel = new userModel();
        return $userModel->getDbStatus();
    }
}
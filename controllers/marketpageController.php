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

        // Si pas connecté, rediriger vers la page de login
        if (!$isLoggedIn) {
            $_SESSION['redirect_after_login'] = '?controller=marketpage&action=index';
            header('Location: ?controller=user&action=login');
            exit;
        }

        $status = $this->getDbStatus();
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        // Récupérer les offres depuis la base de données
        $offers = $this->getOffers();

        View::show('marketpageView', [
            'user' => $_SESSION['user'] ?? null,
            'isLoggedIn' => $isLoggedIn,
            'db_status' => $status,
            'flash' => $flash,
            'offers' => $offers
        ]);
    }

    public function createOffer()
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
            require_once 'models/offerModel.php';
            $result = offerModel::createOffer($_SESSION['user_id'], $title, $description, $price, $category);

            if ($result) {
                $_SESSION['flash'] = ['success' => true, 'message' => 'Offre créée avec succès !'];
            } else {
                $_SESSION['flash'] = ['success' => false, 'message' => 'Erreur lors de la création de l\'offre.'];
            }
        }

        header('Location: ?controller=marketpage&action=index');
        exit;
    }

    public function deleteOffer()
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
            require_once 'models/offerModel.php';
            $result = offerModel::deleteOffer($offerId, $_SESSION['user_id']);

            if ($result) {
                $_SESSION['flash'] = ['success' => true, 'message' => 'Offre supprimée avec succès.'];
            } else {
                $_SESSION['flash'] = ['success' => false, 'message' => 'Erreur lors de la suppression de l\'offre.'];
            }
        }

        header('Location: ?controller=marketpage&action=index');
        exit;
    }

    public function login()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Rediriger vers la page de connexion en conservant la destination
        $_SESSION['redirect_after_login'] = '?controller=marketpage&action=index';
        header('Location: ?controller=user&action=login');
        exit;
    }

    public function viewOffer()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $offerId = $_GET['id'] ?? null;

        if (!$offerId) {
            $_SESSION['flash'] = ['success' => false, 'message' => 'Identifiant d\'offre manquant.'];
            header('Location: ?controller=marketpage&action=index');
            exit;
        }

        require_once 'models/offerModel.php';
        $offer = offerModel::getOfferById($offerId);

        if (!$offer) {
            $_SESSION['flash'] = ['success' => false, 'message' => 'Offre introuvable.'];
            header('Location: ?controller=marketpage&action=index');
            exit;
        }

        $status = $this->getDbStatus();
        $isLoggedIn = !empty($_SESSION['user']) && is_array($_SESSION['user']);
        $isOwner = $isLoggedIn && isset($_SESSION['user_id']) && $offer['user_id'] == $_SESSION['user_id'];

        View::show('offerDetailsView', [
            'user' => $_SESSION['user'] ?? null,
            'isLoggedIn' => $isLoggedIn,
            'isOwner' => $isOwner,
            'offer' => $offer,
            'db_status' => $status
        ]);
    }

    public function purchaseOffer()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            $_SESSION['flash'] = ['success' => false, 'message' => 'Vous devez être connecté pour acheter une offre.'];
            header('Location: ?controller=marketpage&action=login');
            exit;
        }

        $offerId = $_POST['offer_id'] ?? null;

        if ($offerId) {
            require_once 'models/offerModel.php';
            $result = offerModel::purchaseOffer($offerId, $_SESSION['user_id']);

            if ($result['success']) {
                $_SESSION['flash'] = ['success' => true, 'message' => $result['message'] ?? 'Offre achetée avec succès !'];
            } else {
                $_SESSION['flash'] = ['success' => false, 'message' => $result['message'] ?? 'Erreur lors de l\'achat de l\'offre.'];
            }
        } else {
            $_SESSION['flash'] = ['success' => false, 'message' => 'Identifiant d\'offre manquant.'];
        }

        header('Location: ?controller=marketpage&action=index');
        exit;
    }

    // Récupère toutes les offres
    private function getOffers()
    {
        // Pour les tests, si une classe offerModel est déjà définie (double),
        // on évite d'inclure le vrai fichier pour ne pas redéclarer la classe.
        if (!class_exists('offerModel', false)) {
            require_once 'models/offerModel.php';
        }
        return offerModel::getAllOffers();
    }

    // Récupère le status de la BDD
    private function getDbStatus()
    {
        // Idem: si un double de test existe déjà, on ne charge pas le vrai fichier.
        if (!class_exists('userModel', false)) {
            require_once 'models/userModel.php';
        }
        $userModel = new userModel();
        // Rendez le contrôleur tolérant si le modèle ne propose pas getDbStatus
        if (method_exists($userModel, 'getDbStatus')) {
            return $userModel->getDbStatus();
        }
        return ['available' => true, 'message' => ''];
    }
}


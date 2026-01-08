<?php

/**
 * Marketplace Controller
 *
 * Handles marketplace-related actions including displaying offers,
 * creating new offers, and deleting existing offers. Manages user
 * authentication and flash messages for marketplace operations.
 *
 */
final class marketpageController
{
    /**
     * Displays the marketplace page with all offers
     *
     * Initializes the session if needed, checks user authentication status,
     * retrieves database status and all available offers, then displays
     * the marketplace view with flash messages if any.
     *
     * @return void
     */
    public function index(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Checks if the user is connected
        $isLoggedIn = !empty($_SESSION['user']) && is_array($_SESSION['user']);

        $status = $this->getDbStatus();
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        // Gets the offers from the database
        $offers = $this->getOffers();

        view::show('marketpageView', [
            'user' => $_SESSION['user'] ?? null,
            'isLoggedIn' => $isLoggedIn,
            'db_status' => $status,
            'flash' => $flash,
            'offers' => $offers
        ]);
    }

    /**
     * Creates a new marketplace offer
     *
     * Processes the offer creation form submission. Validates user authentication,
     * required fields (title, description, price), and price format. Creates the
     * offer in the database if validation passes. Sets appropriate flash messages
     * for success or failure and redirects to the marketplace index.
     *
     * @return void
     */
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

    /**
     * Deletes an existing marketplace offer
     *
     * Processes the offer deletion request. Validates user authentication
     * and ensures the user owns the offer before deletion. Sets appropriate
     * flash messages for success or failure and redirects to the marketplace index.
     *
     * @return void
     */
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
            $result = offerModel::deleteOffer((int)$offerId, $_SESSION['user_id']);

            if ($result['success']) {
                $_SESSION['flash'] = ['success' => true, 'message' => $result['message']];
            } else {
                $_SESSION['flash'] = ['success' => false, 'message' => $result['message']];
            }
        }

        header('Location: ?controller=marketpage&action=index');
        exit;
    }

    /**
     * Fetches all the offers from the database
     *
     * Retrieves all available marketplace offers using the offer model.
     * Automatically loads the offer model if not already loaded.
     *
     * @return array<int, array<string, mixed>> Array of offers with their details
     */
    private function getOffers(): array
    {
        if (!class_exists('offerModel', false)) {
            require_once constants::modelsRepository() . 'offerModel.php';
        }
        return offerModel::getAllOffers();
    }

    /**
     * Fetches the database connection status
     *
     * Retrieves the current database connection status using the user model.
     * Automatically loads the user model if not already loaded.
     *
     * @return array{available: bool, message: string, details?: string} Database status information
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
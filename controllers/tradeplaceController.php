<?php

/**
 * Trade Place Controller
 *
 * Handles the trade place page where users can view and interact with offers.
 * Manages offer selection, display, and provides database status information.
 *
 */
class tradeplaceController
{
    /**
     * Displays the trade place page with offers
     *
     * Initializes the session if needed, checks user authentication status,
     * retrieves all available offers and database status. Handles offer selection
     * from GET parameters or defaults to the first available offer. Displays the
     * trade place view with flash messages if any.
     *
     * @return void
     */
    public function index(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $isLoggedIn = !empty($_SESSION['user']) && is_array($_SESSION['user']);

        $status = $this->getDbStatus();
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        $offers = $this->getOffers();

        $selectedOfferId = $_GET['offer_id'] ?? null;
        $selectedOffer = null;

        if ($selectedOfferId) {
            foreach ($offers as $offer) {
                if ($offer['id'] == $selectedOfferId) {
                    $selectedOffer = $offer;
                    break;
                }
            }
        }

        View::show('tradeplaceView', [
            'user' => $_SESSION['user'] ?? null,
            'isLoggedIn' => $isLoggedIn,
            'db_status' => $status,
            'flash' => $flash,
            'offers' => $offers,
            'selectedOffer' => $selectedOffer
        ]);
    }

    /**
     * Fetches the database connection status
     *
     * Retrieves the current database connection status using the user model.
     * Automatically loads the user model if not already loaded. Returns a default
     * success status if the getDbStatus method is not available.
     *
     * @return array{available: bool, message: string, details?: string} Database status information
     */
    private function getDbStatus(): array
    {
        if (!class_exists('userModel', false)) {
            require_once 'models/userModel.php';
        }
        $userModel = new userModel();
        if (method_exists($userModel, 'getDbStatus')) {
            return $userModel->getDbStatus();
        }
        return ['available' => true, 'message' => ''];
    }

    /**
     * Fetches all available offers from the database
     *
     * Retrieves all marketplace offers using the offer model.
     * Automatically loads the offer model if not already loaded.
     *
     * @return array<int, array<string, mixed>> Array of offers with their details
     */
    private function getOffers(): array
    {
        if (!class_exists('offerModel', false)) {
            require_once 'models/offerModel.php';
        }

        $offers = [];

        $cat = ['Maths' => 'Maths', 'Programmation' => 'Programmation', 'Network' => 'Réseau', 'DB' => 'BD', 'Other' => 'Autre'];

        foreach (offerModel::getAllOffers() as $offer) {
            $offer['category'] = $cat[$offer['category']];
            $offers[] = $offer;
        }

        return $offers;
    }

    private function purchaseOffer(): array
    {
        var_dump($_GET['offer'], $_SESSION['user']);
        exit;
        return offerModel::purchaseOffer($_GET['offer'], $_SESSION['user']);
    }

    /**
     * Returns controller parameters
     *
     * Provides an empty array of parameters for controller configuration.
     * This method can be used by the routing system or for future parameter handling.
     *
     * @return array<string, mixed> Empty array of parameters
     */
    public function getParams(): array
    {
        return [];
    }
}
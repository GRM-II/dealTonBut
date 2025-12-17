<?php

class tradeplaceController
{
    public function index()
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
        } elseif (!empty($offers)) {
            $selectedOffer = $offers[0];
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

    private function getDbStatus()
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

    private function getOffers()
    {
        if (!class_exists('offerModel', false)) {
            require_once 'models/offerModel.php';
        }
        return offerModel::getAllOffers();
    }

    public function getParams(): array
    {
        return [];
    }
}


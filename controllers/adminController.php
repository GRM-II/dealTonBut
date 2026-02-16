<?php

final class adminController
{
    private userModel $userModel;
    private offerModel $offerModel;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->userModel = new userModel();

        if (class_exists('offerModel')) {
            $this->offerModel = new offerModel();
        }

        $this->checkAdminAccess();
    }

    private function checkAdminAccess(): void
    {
        if (!isset($_SESSION['user']) ||
            !isset($_SESSION['is_admin']) ||
            $_SESSION['is_admin'] !== true) {
            $_SESSION['flash_message'] = [
                'success' => false,
                'message' => 'Accès non autorisé. Vous devez être administrateur.'
            ];
            header('Location: ?controller=user&action=homepage');
            exit;
        }
    }

    public function index(): void
    {
        $stats = [
            'total_users' => $this->userModel->countUsers(),
            'total_offers' => method_exists($this->offerModel ?? null, 'countOffers')
                ? $this->offerModel->countOffers()
                : 0
        ];

        view::show('admin/dashboardView', [
            'stats' => $stats,
            'current_page' => 'dashboard'
        ]);
    }

    public function users(): void
    {
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $search = isset($_GET['search']) ? trim($_GET['search']) : '';

        if (!empty($search)) {
            $users = $this->userModel->searchUsers($search);
            $totalUsers = count($users);
        } else {
            $users = $this->userModel->getAllUsers($limit, $offset);
            $totalUsers = $this->userModel->countUsers();
        }

        $totalPages = ceil($totalUsers / $limit);

        view::show('admin/usersView', [
            'users' => $users,
            'current_page' => 'users',
            'page' => $page,
            'total_pages' => $totalPages,
            'search' => $search
        ]);
    }

    public function deleteUser(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo 'Méthode non autorisée';
            return;
        }

        $userId = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;

        if ($userId === 0) {
            $_SESSION['flash'] = [
                'success' => false,
                'message' => 'ID utilisateur invalide.'
            ];
            header('Location: ?controller=admin&action=users');
            exit;
        }

        if ($userId === $_SESSION['user']['id']) {
            $_SESSION['flash'] = [
                'success' => false,
                'message' => 'Vous ne pouvez pas supprimer votre propre compte.'
            ];
            header('Location: ?controller=admin&action=users');
            exit;
        }

        try {
            $result = $this->userModel->deleteUser($userId);

            if ($result) {
                $_SESSION['flash'] = [
                    'success' => true,
                    'message' => 'Utilisateur supprimé avec succès.'
                ];
            } else {
                $_SESSION['flash'] = [
                    'success' => false,
                    'message' => 'Erreur lors de la suppression de l\'utilisateur.'
                ];
            }
        } catch (Exception $e) {
            error_log("Erreur deleteUser (admin): " . $e->getMessage());
            $_SESSION['flash'] = [
                'success' => false,
                'message' => 'Une erreur est survenue.'
            ];
        }

        header('Location: ?controller=admin&action=users');
        exit;
    }

    public function toggleAdmin(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo 'Méthode non autorisée';
            return;
        }

        $userId = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
        $newRole = isset($_POST['role']) ? $_POST['role'] : '';

        if ($userId === 0 || !in_array($newRole, ['user', 'admin'])) {
            $_SESSION['flash'] = [
                'success' => false,
                'message' => 'Données invalides.'
            ];
            header('Location: ?controller=admin&action=users');
            exit;
        }

        if ($userId === $_SESSION['user']['id']) {
            $_SESSION['flash'] = [
                'success' => false,
                'message' => 'Vous ne pouvez pas modifier votre propre rôle.'
            ];
            header('Location: ?controller=admin&action=users');
            exit;
        }

        try {
            $result = $this->userModel->updateUserRole($userId, $newRole);
            $_SESSION['flash'] = $result;
        } catch (Exception $e) {
            error_log("Erreur toggleAdmin: " . $e->getMessage());
            $_SESSION['flash'] = [
                'success' => false,
                'message' => 'Une erreur est survenue.'
            ];
        }

        header('Location: ?controller=admin&action=users');
        exit;
    }

    public function offers(): void
    {
        if (!isset($this->offerModel)) {
            view::show('admin/offersView', [
                'offers' => [],
                'current_page' => 'offers',
                'message' => 'Le module des offres n\'est pas encore configuré.'
            ]);
            return;
        }

        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;


        $offers = method_exists($this->offerModel, 'getAllOffers')
            ? $this->offerModel->getAllOffers($limit, $offset)
            : [];

        $totalOffers = method_exists($this->offerModel, 'countOffers')
            ? $this->offerModel->countOffers()
            : 0;

        $totalPages = ceil($totalOffers / $limit);

        view::show('admin/offersView', [
            'offers' => $offers,
            'current_page' => 'offers',
            'page' => $page,
            'total_pages' => $totalPages
        ]);
    }

    public function deleteOffer(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo 'Méthode non autorisée';
            return;
        }

        $offerId = isset($_POST['offer_id']) ? (int)$_POST['offer_id'] : 0;

        if ($offerId === 0) {
            $_SESSION['flash'] = [
                'success' => false,
                'message' => 'ID d\'offre invalide.'
            ];
            header('Location: ?controller=admin&action=offers');
            exit;
        }

        if (!isset($this->offerModel)) {
            $_SESSION['flash'] = [
                'success' => false,
                'message' => 'Le module des offres n\'est pas configuré.'
            ];
            header('Location: ?controller=admin&action=offers');
            exit;
        }

        try {
            $result = method_exists($this->offerModel, 'deleteOffer')
                ? $this->offerModel->deleteOffer($offerId)
                : false;

            if ($result) {
                $_SESSION['flash'] = [
                    'success' => true,
                    'message' => 'Offre supprimée avec succès.'
                ];
            } else {
                $_SESSION['flash'] = [
                    'success' => false,
                    'message' => 'Erreur lors de la suppression de l\'offre.'
                ];
            }
        } catch (Exception $e) {
            error_log("Erreur deleteOffer (admin): " . $e->getMessage());
            $_SESSION['flash'] = [
                'success' => false,
                'message' => 'Une erreur est survenue.'
            ];
        }

        header('Location: ?controller=admin&action=offers');
        exit;
    }
}
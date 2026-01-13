<?php
/**
 * Sitemap controller
 */
final class sitemapController
{
    /**
     * Displays a map linked to every page
     */
    public function index(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Vérifier si l'utilisateur est connecté
        $isLoggedIn = !empty($_SESSION['user']) && is_array($_SESSION['user']);

        view::show('sitemapView', [
            'isLoggedIn' => $isLoggedIn,
            'user' => $_SESSION['user'] ?? null
        ]);
    }
}


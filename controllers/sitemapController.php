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

        $isLoggedIn = isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);

        view::show('sitemapView', [
            'isLoggedIn' => $isLoggedIn,
            'user' => $_SESSION['user'] ?? null
        ]);
    }
}


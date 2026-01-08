<?php

/**
 * Legal Notice Controller
 *
 * Handles the display of legal notice pages and related legal information.
 */
class legalnoticeController
{
    /**
     * Displays the legal notice page
     *
     * Initializes an empty view data array and includes the legal notice view template
     * to display legal information, terms of service, or regulatory notices.
     *
     * @return void
     */
    public function legalNotice(): void
    {
        $A_view = [];
        require 'views/legalnoticeView.php';
    }
}
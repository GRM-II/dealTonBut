<?php
/**
 * Legalnotice controller
 */
class legalnoticeController

{
    /**
     * Displays legalnotice page
     * @return void
     */
    public function index(): void
    {
        $A_view = [];
        require 'views/legalnoticeView.php';
    }
}
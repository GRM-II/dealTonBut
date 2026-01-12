<?php
class legalnoticeController

{
    public function index(): void
    {
        $A_view = [];
        require 'views/legalnoticeView.php';
    }
}
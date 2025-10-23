<?php

class envReader
{

    private const string HOST = '';
    private const string USER = '';
    private const string MDP = '';
    private const string PORT = '';
    private const string BD = '';

    public function __construct()
    {
        private $env = fopen(".env", "r") or die("Unable to open file");

        self::HOST . fgets($env);

        self::USER . fgets($env);

        self::MDP . fgets($env);

        self::PORT . fgets($env);

        self::BD . fgets($env);

        fclose($env);
    }

    public static function getHost(): string {
        return self::HOST;
    }

    public static function getUser(): string {
        return self::USER;
    }

    public static function getMdp(): string {
        return self::MDP;
    }

    public static function getPort(): string {
        return self::PORT;
    }

    public static function getBd(): string {
        return self::BD;
    }
}

?>
<?php

final class envReader
{
    private $env = fopen(".env", "r") or die("Unable to open file");

    private const HOST = fgets($env);

    private const USER = fgets($env);

    private const MDP = fgets($env);

    private const PORT = fgets($env);

    private const BD = fgets($env);

    fclose($env);

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
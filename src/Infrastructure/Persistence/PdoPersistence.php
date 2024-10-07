<?php

namespace Src\Infrastructure\Persistence;

use PDO;
use PDOException;

class PdoPersistence
{
    public static ?PDO $instance = null;

    public static function getInstance(): PDO
    {
        try {
            if (null === self::$instance) {
                $host = getenv('DATABASE_HOST');
                $dbName = getenv('DATABASE_NAME');
                $username = getenv('DATABASE_USER');
                $password = getenv('DATABASE_PASSWORD');

                self::$instance = new PDO("mysql:host=" . $host . ";dbname=" . $dbName, $username, $password);
                self::$instance->exec("set names utf8");
            }
        } catch (PDOException $exception) {
            echo "Error de conexión: " . $exception->getMessage();
        }

        return self::$instance;
    }
}

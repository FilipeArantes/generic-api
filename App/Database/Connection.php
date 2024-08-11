<?php

namespace App\Database;

use PDO;

class Connection
{
    protected static ?PDO $pdo = null;

    public static function connect(): PDO
    {
        if (is_null(self::$pdo)) {
            self::$pdo = new PDO('pgsql:host=localhost;dbname=name', getenv('DB_USER'), getenv('DB_PASSWORD'));
            self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }

        return self::$pdo;
    }
}

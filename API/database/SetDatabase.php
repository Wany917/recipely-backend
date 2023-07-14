<?php

namespace Recipely\Database;

use PDO, PDOException;
use Recipely\Config\ConfInc;
use Recipely\Config\ConnectDb;

class SetDatabase {
    private static String $DB_NAME;
    private static PDO $pdo;

    public static function init() {
        self::$pdo = ConnectDb::getInstance();
        self::$DB_NAME = (new confinc())->getDB_NAME();
    }

    
    public static function createTables()
    {
        try {
            self::$pdo->exec("USE ".self::$DB_NAME);
            $sql = file_get_contents("import.sql");
            self::$pdo->exec($sql);
            echo "Tables successfully created\n";
        } catch (PDOException $e) {
            echo "Error during tables creation :  " . $e->getMessage() . "\n";
            var_dump($e->getMessage());
        }
    }

    public static function closeConnexion() {
        ConnectDb::closeConnexion();
    }
}
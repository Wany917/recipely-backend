<?php
namespace Recipely\Config;
use Recipely\Config\ConfInc;
use PDO, Exception,PDOException;

class ConnectDb
{
    private static $instance = null;

    private static object $conf; 
    private static String $DB_NAME;
    private static String $DB_HOST;
    private static String $DB_USER;
    private static String $DB_DRIVER;
    private static String $DB_PASSWORD;

    private static function init() {
        self::$conf = new confInc();
        self::$DB_DRIVER = self::$conf->getDB_DRIVER();
        self::$DB_HOST = self::$conf->getDB_HOST();
        self::$DB_NAME = self::$conf->getDB_NAME();
        self::$DB_USER = self::$conf->getDB_USER();
        self::$DB_PASSWORD = self::$conf->getDB_PASSWORD();

        if (!self::databaseExist()) {
            self::createDatabase();
        }
    }

    public static function databaseExist() {
       $pdo = new PDO(self::$DB_DRIVER.":host=".self::$DB_HOST.";charset=utf8", self::$DB_USER, self::$DB_PASSWORD);
       $query = $pdo->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '".self::$DB_NAME."'");
       $result = $query->fetch();

        return $result ? true : false;
    }

    public static function createDatabase(){
        try {
            $pdo = new PDO(self::$DB_DRIVER.":host=".self::$DB_HOST.";charset=utf8", self::$DB_USER, self::$DB_PASSWORD);
            $sql = "CREATE DATABASE IF NOT EXISTS ".self::$DB_NAME;
            $pdo->exec($sql);
            echo "Database created successfully\n";
        } catch (PDOException $e) {
            echo "Error during database creation : " . $e->getMessage() . "\n";
        }
    }
    
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::init();
            try {
                $options = [
                    PDO::ATTR_PERSISTENT => true,
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
                ];
                self::$instance = new PDO(
                    self::$DB_DRIVER.":host=".self::$DB_HOST.";dbname=".self::$DB_NAME.";charset=utf8", self::$DB_USER, self::$DB_PASSWORD, $options
                );
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        }
        return self::$instance;
    }
   
    public static function closeConnexion() {
        self::$instance = null;
    }
    
}

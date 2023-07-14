<?php
namespace Recipely\Config;

use Dotenv\Dotenv;

class ConfInc{

    private String $DB_NAME;
    private String $DB_HOST;
    private String $DB_USER;
    private String $DB_DRIVER;
    private String $DB_PASSWORD;

    public function __construct()
    {
        $this->DB_DRIVER = $_ENV['DB_DRIVER'];
        $this->DB_HOST = $_ENV['DB_HOST'];
        $this->DB_NAME = $_ENV['DB_NAME'];
        $this->DB_USER = $_ENV['DB_USER'];
        $this->DB_PASSWORD = $_ENV['DB_PASSWORD'];
    }

    public function toArray(): array {
        return [
            'DB_NAME' => $this->DB_NAME,
            'DB_HOST' => $this->DB_HOST,
            'DB_USER' => $this->DB_USER,
            'DB_DRIVER' => $this->DB_DRIVER,
            'DB_PASSWORD' => $this->DB_PASSWORD
        ];
    }

    public function getDB_NAME(): String
    {
        return $this->DB_NAME;
    }
    public function getDB_HOST(): String
    {
        return $this->DB_HOST;
    }
    public function getDB_USER(): String
    {
        return $this->DB_USER;
    }
    public function getDB_DRIVER(): String
    {
        return $this->DB_DRIVER;
    }
    public function getDB_PASSWORD(): String
    {
        return $this->DB_PASSWORD;
    }
}
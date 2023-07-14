<?php 
require_once __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . '/../autoload.php';

use dotenv\Dotenv;
use Recipely\Database\SetDatabase;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->safeLoad();

SetDatabase::init();
SetDatabase::createTables();
SetDatabase::closeConnexion();


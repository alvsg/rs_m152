<?php
require_once("config.inc.php");

function connectDB()
{
    static $connectDB = null;

    if ($connectDB === null) {
        try {
            $connectDB = new PDO(
                "mysql:host=" . SERVERNAME . ";dbname=" . DBNAME . ";charset=utf8",
                DBUSER,
                DBPWD,
                array(
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
                    PDO::ATTR_PERSISTENT => true
                )
            );
        }

        catch (Exception $e) {
            echo 'Erreur : ' . $e->getMessage() . '<br />';
            echo 'N° : ' . $e->getCode();
            die(' Could not connect to MySQL');
        }
    }
    return $connectDB;
}
?>
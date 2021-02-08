<?php
require_once("config.inc.php");

$btnBlog = filter_input(INPUT_POST, 'btnBlog', FILTER_SANITIZE_STRING);

switch ($btnBlog) {
    case 'Publish':
        publishMedia();
        break;
}

/// Fonction qui permet la connexion à la base de donnée
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
        } catch (Exception $e) {
            echo 'Erreur : ' . $e->getMessage() . '<br />';
            echo 'N° : ' . $e->getCode();
            die(' Could not connect to MySQL');
        }
    }
    return $connectDB;
}

/// Fonction qui permet de publier une image
function publishMedia()
{
    for ($i = 0; $i < count($_FILES['mediaFile']['name']); $i++) {
        for ($i = 0; $i < count($_FILES['mediaFile']['type']); $i++) {
            $nameFile = $_FILES['mediaFile']['name'][$i];
            $typeFile = $_FILES['mediaFile']['type'][$i];
            databaseInsert($nameFile, $typeFile);
        }
    }
}

/// Fonction qui permet de vérifier si le fichier est dans la base de donnée
function databaseSelect($nameFile)
{
    $sql = "SELECT * FROM `media` WHERE `nomFichierMedia` LIKE :nameFile";
    $query = connectDB()->prepare($sql);
    $query->execute([':nameFile' => "%$nameFile%"]);
    return $query->fetch(PDO::FETCH_ASSOC);
}

/// Fonction qui permet d'insérer dans la base de donnée
function databaseInsert($nameFile, $typeFile)
{
    $result = databaseSelect($nameFile);
    if (!$result) {
        $sql = "INSERT INTO `media` (nomFichierMedia, typeMedia) VALUES (:nameFile, :typeFile)";
        $query = connectDB()->prepare($sql);
        $query->execute([':nameFile' => $nameFile, ':typeFile' => $typeFile]);
    }
}
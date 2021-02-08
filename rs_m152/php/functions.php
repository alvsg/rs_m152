<?php
require_once("config.inc.php");

$btnBlog = filter_input(INPUT_POST, 'btnBlog', FILTER_SANITIZE_STRING);
$comments = filter_input(INPUT_POST, 'text', FILTER_DEFAULT);

switch ($btnBlog) {
    case 'Publish':
        sizeFile($comments);
        header("Location: ../index.php");
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

/// Fonction qui permet de vérifier si la taille des fichiers n'est pas trop volumineuse
function sizeFile($comment)
{
    $totalSize = 0;
    for ($i = 0; $i < count($_FILES['mediaFile']['size']); $i++) {
        $sizeFile = $_FILES['mediaFile']['size'][$i];
        if ($sizeFile < 300000) {
            echo " ok";
            $totalSize += $sizeFile;
        }else{
            echo "Fichiers trop volumineux";
            break;
        }
    }
    if ($totalSize < 7000000) {
        publishMedia($comment);
        var_dump($totalSize);
    }
}

/// Fonction qui permet de publier une image
/// Note - Restart id : ALTER TABLE `meida` AUTO_INCREMENT = 0;
function publishMedia($comment)
{
    for ($i = 0; $i < count($_FILES['mediaFile']['name']); $i++) {
        for ($i = 0; $i < count($_FILES['mediaFile']['type']); $i++) {
            $nameFile = $_FILES['mediaFile']['name'][$i];
            $typeFile = $_FILES['mediaFile']['type'][$i];
            databaseInsert($nameFile, $typeFile);
        }
    }
    publishCom($comment);
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

/// Fonction qui permet d'insérer un commentaire et la date à laquel il a été posté
/// Note - Restart id : ALTER TABLE `post` AUTO_INCREMENT = 0;
function publishCom($comment)
{
    $dateTime = date('Y-m-d H:i:s');
    if ($comment != null) {
        $sql = "INSERT INTO `post` (commentaire, datePost) VALUES (:com, :dateP)";
        $query = connectDB()->prepare($sql);
        $query->execute([':com' => $comment, ':dateP' => $dateTime]);
    }
}

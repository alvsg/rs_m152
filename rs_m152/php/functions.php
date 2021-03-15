<?php
require_once("config.inc.php");

$btnBlog = filter_input(INPUT_POST, 'btnBlog', FILTER_SANITIZE_STRING);
$comments = filter_input(INPUT_POST, 'text', FILTER_DEFAULT);

switch ($btnBlog) {
    case 'Upload':
        sizeFile($comments);
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
    $nombreImages = 0;

    for ($i = 0; $i < count($_FILES['mediaFile']['size']); $i++) {
        $sizeFile = $_FILES['mediaFile']['size'][$i];
        if ($sizeFile < 3000000 && strpos($_FILES['mediaFile']['type'][$i], 'image/') || strpos($_FILES['mediaFile']['type'][$i], 'video/')) {
            $totalSize += $sizeFile;
            $nombreImages++;
            if ($nombreImages == count($_FILES['mediaFile']['size'])) {
                if ($totalSize < 70000000) {
                    publishMedia($comment);
                }
            }
        } else {
            echo '<div class="alert alert-danger" role="alert"> Fichier trop volumineux ! </div>';
            break;
        }
    }
}

/// Fonction qui permet de vérifier si le fichier est dans la base de donnée
function databaseSelectEveryImage($nameFile)
{
    $sql = "SELECT * FROM `media` WHERE `nomFichierMedia` LIKE :nameFile";
    $query = connectDB()->prepare($sql);
    $query->execute([':nameFile' => "%$nameFile%"]);
    $query->fetch(PDO::FETCH_ASSOC);
}

/// Fonction qui permet de définir un id unique et de publier une image
/// Note - Restart id : ALTER TABLE `media` AUTO_INCREMENT = 0
function publishMedia($comment)
{
    if ($comment != null) {
        publishCom($comment);
        for ($i = 0; $i < count($_FILES['mediaFile']['name']); $i++) {
            $uniqNameFile = uniqid($_FILES['mediaFile']['name'][$i]);
            var_dump($uniqNameFile);
            if (strpos($_FILES['mediaFile']['type'][$i], 'image/') !== false || strpos($_FILES['mediaFile']['type'][$i], 'video/') !== false) {
                $typeFile = $_FILES['mediaFile']['type'][$i];
            } else {
                echo '<div class="alert alert-warning" role="alert"> Le type du fichier ne convient pas ! </div>';
            }
            $tmpName = $_FILES["mediaFile"]["tmp_name"][$i];

            // Boucle qui vérifie si la méthode move_upload_file
            if (move_uploaded_file($tmpName, "../uploads/$uniqNameFile")) {
                if (databaseInsert($uniqNameFile, $typeFile, $comment)) {
                    header("Location: ../index.php");
                } else {
                    unlink(glob("../uploads/$uniqNameFile"));
                }
            } else {
                echo '<div class="alert alert-warning" role="alert"> Le téléchargement a echoué ! </div>';
            }
        }
    } else {
        echo '<div class="alert alert-danger" role="alert"> Veuillez entrer un commentaire ! </div>';
    }
}

/// Fonction qui permet de récupérer l'id du commentaire
function getLastId()
{
    $sql = "SELECT `idPost` FROM `post` ORDER BY `idPost` DESC LIMIT 1";
    $query = connectDB()->prepare($sql);
    $query->execute();
    return $query->fetch(PDO::FETCH_ASSOC);
}

/// Fonction qui permet d'insérer dans la base de donnée
function databaseInsert($nameFile, $typeFile)
{
    $idPost = getLastId()['idPost'];
    $sql = "INSERT INTO `media` (nomFichierMedia, typeMedia, idPost) VALUES (:nameFile, :typeFile, :idPost)";
    $query = connectDB()->prepare($sql);
    $query->execute([':nameFile' => $nameFile, ':typeFile' => $typeFile, ':idPost' => $idPost]);
}

/// Fonction qui permet d'insérer un commentaire et la date à laquel il a été posté
/// Note - Restart id : ALTER TABLE `post` AUTO_INCREMENT = 0
function publishCom($comment)
{
    $sql = "INSERT INTO `post` (commentaire) VALUES (:com)";
    $query = connectDB()->prepare($sql);
    $query->execute([':com' => $comment]);
}

/// Fonction qui permet de récupérer
function getAllForImg()
{
    $sql = "SELECT `nomFichierMedia`, `idPost`, `typeMedia` FROM `media`";
    $query = connectDB()->prepare($sql);
    $query->execute();
    return $query->fetchAll(PDO::FETCH_ASSOC);
}

/// Fonction qui permet de récupérer les commentaires selon l'id
function getComById($id)
{
    $sql = "SELECT `commentaire` FROM `post` WHERE `idPost` LIKE :id";
    $query = connectDB()->prepare($sql);
    $query->execute([':id' => $id]);
    return $query->fetch(PDO::FETCH_ASSOC);
}

/// Fonction qui permet de crée un post avec une image et un commentaire
function publishPost()
{
    $allImg = getAllForImg();
    foreach ($allImg as $value) {
        $commentaire = getComById($value["idPost"]);
        switch ($value["typeMedia"]) {
            case strpos($value["typeMedia"], 'image/'):
                echo "  <div class=\"panel panel-default\">
                <div class=\"panel-thumbnail\"><img src=\"uploads/" . $value["nomFichierMedia"] . "\" class=\"img-responsive\"></div>
                <div class=\"panel-body\">
                    <p>" . $commentaire["commentaire"] . "</p>
                </div>
            </div>";
                break;
            case strpos($value["typeMedia"], 'video/'):
                echo "  <div class=\"panel panel-default\">
                <div class=\"panel-thumbnail\"><video width=\"320\" height=\"240\" controls><source src=\"uploads/" . $value["nomFichierMedia"] . "\" type=\"".$value["typeMedia"]."\"></video></div>
                <div class=\"panel-body\">
                    <p>" . $commentaire["commentaire"] . "</p>
                </div>
            </div>";
                break;
        }
    }
}

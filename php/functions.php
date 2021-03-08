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
    for ($i = 0; $i < count($_FILES['mediaFile']['size']); $i++) {
        $sizeFile = $_FILES['mediaFile']['size'][$i];
        if ($sizeFile < 3000000) {
            $totalSize += $sizeFile;
        } else {
            echo '<div class="alert alert-danger" role="alert"> Fichier trop volumineux ! </div>';
            break;
        }
    }
    if ($totalSize < 70000000) {
        publishMedia($comment);
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
            if (str_contains($_FILES['mediaFile']['type'][$i], 'image/')) {
                $typeFile = $_FILES['mediaFile']['type'][$i];
            } else {
                echo '<div class="alert alert-warning" role="alert"> Le type du fichier ne confient pas ! </div>';
            }
            $tmpName = $_FILES["mediaFile"]["tmp_name"][$i];

            // Boucle qui vérifie si la méthode move_upload_file
            if (move_uploaded_file($tmpName, "../uploads/$uniqNameFile")) {
                databaseInsert($uniqNameFile, $typeFile, $comment);
                header("Location: ../index.php");
            } else {
                echo '<div class="alert alert-warning" role="alert"> Le téléchargement a echoué ! </div>';
            }
        }
    } else {
        echo '<div class="alert alert-danger" role="alert"> Veuillez entrer un commentaire ! </div>';
    }
}

/// Fonction qui permet de récupérer l'id du commentaire
function findIdOfComment($comment)
{
    $sql = "SELECT `idPost` FROM `post` WHERE `commentaire` LIKE :commentaire";
    $query = connectDB()->prepare($sql);
    $query->execute([':commentaire' => "%$comment%"]);
    return $query->fetch(PDO::FETCH_ASSOC);
}

/// Fonction qui permet d'insérer dans la base de donnée
function databaseInsert($nameFile, $typeFile, $comment)
{
    $idPost = findIdOfComment($comment)['idPost'];

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

/// Fonction qui permet de crée un post avec une image et un commentaire
function publishPost()
{

    echo '<div class="panel panel-default">
        <div class="panel-thumbnail"><img src="' . $img . '" class="img-responsive"></div>
        <div class="panel-body">
            <p>' . $com . '</p>
        </div>
    </div>';
}

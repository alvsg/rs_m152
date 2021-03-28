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
        if ($sizeFile < 3000000) {
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
function databaseSelectImage($nameFile)
{
    $sql = "SELECT * FROM `media` WHERE `nomFichierMedia` LIKE :nameFile";
    connectDB()->beginTransaction();
    $query = connectDB()->prepare($sql);
    try {
        $query->execute([':nameFile' => "%$nameFile%"]);
        connectDB()->commit();
        return $query->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        connectDB()->rollBack();
    }
}

/// Fonction qui permet de définir un id unique et de publier une image
/// Note - Restart id : ALTER TABLE `media` AUTO_INCREMENT = 0
function publishMedia($comment)
{
    $exist = "";
    if ($comment != null) {
        publishCom($comment);
        for ($i = 0; $i < count($_FILES['mediaFile']['name']); $i++) {
            $uniqNameFile = uniqid($_FILES['mediaFile']['name'][$i]);
            if (strpos($_FILES['mediaFile']['type'][$i], 'image/') !== false || strpos($_FILES['mediaFile']['type'][$i], 'video/') !== false || strpos($_FILES['mediaFile']['type'][$i], 'audio/') !== false) {
                $typeFile = $_FILES['mediaFile']['type'][$i];
            } else {
                echo '<div class="alert alert-danger" role="alert"> Le type du fichier ne convient pas ! </div>';
            }
            $tmpName = $_FILES["mediaFile"]["tmp_name"][$i];

            // Boucle qui vérifie si la méthode move_upload_file
            if (move_uploaded_file($tmpName, "../uploads/$uniqNameFile")) {
                if (file_exists("../uploads/$uniqNameFile") == true) {
                    databaseInsert($uniqNameFile, $typeFile, $comment);
                    $exist = databaseSelectImage($uniqNameFile);
                    if ($exist != null) {
                        header("Location: ../index.php");
                    } else {
                        echo '<div class="alert alert-warning" role="alert"> L\'ajout dans la base de donnée a echoué ! </div>';
                    }
                } else {
                    if (unlink("../uploads/$uniqNameFile") != true) {
                        echo '<div class="alert alert-warning" role="alert"> La supression du fichier dans le dossier a echoué ! </div>';
                    }
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
    connectDB()->beginTransaction();
    $query = connectDB()->prepare($sql);
    try {
        $query->execute();
        connectDB()->commit();
        return $query->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        connectDB()->rollBack();
    }
}

/// Fonction qui permet d'insérer dans la base de donnée
function databaseInsert($nameFile, $typeFile)
{
    $idPost = getLastId()['idPost'];
    $sql = "INSERT INTO `media` (nomFichierMedia, typeMedia, idPost) VALUES (:nameFile, :typeFile, :idPost)";
    connectDB()->beginTransaction();
    $query = connectDB()->prepare($sql);
    try {
        $query->execute([':nameFile' => $nameFile, ':typeFile' => $typeFile, ':idPost' => $idPost]);
        connectDB()->commit();
    } catch (Exception $e) {
        connectDB()->rollBack();
    }
}

/// Fonction qui permet d'insérer un commentaire et la date à laquel il a été posté
/// Note - Restart id : ALTER TABLE `post` AUTO_INCREMENT = 0
function publishCom($comment)
{
    $sql = "INSERT INTO `post` (commentaire) VALUES (:com)";
    connectDB()->beginTransaction();
    $query = connectDB()->prepare($sql);
    try {
        $query->execute([':com' => $comment]);
        connectDB()->commit();
    } catch (Exception $e) {
        connectDB()->rollBack();
    }
}

/// Fonction qui permet de récupérer
function getAllFromPost()
{
    $sql = "SELECT * FROM `post`";
    connectDB()->beginTransaction();
    $query = connectDB()->prepare($sql);
    try {
        $query->execute();
        connectDB()->commit();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        connectDB()->rollBack();
    }
}

/// Fonction qui permet de récupérer les commentaires selon l'id
function getComById($id)
{
    $sql = "SELECT `commentaire` FROM `post` WHERE `idPost` LIKE :id";
    connectDB()->beginTransaction();
    $query = connectDB()->prepare($sql);
    try {
        $query->execute([':id' => $id]);
        connectDB()->commit();
        return $query->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        connectDB()->rollBack();
    }
}

/// Fonction qui permet de récupérer les commentaires selon l'id
function getMediaByIdPost($id)
{
    $sql = "SELECT * FROM `media` WHERE `idPost` LIKE :id";
    connectDB()->beginTransaction();
    $query = connectDB()->prepare($sql);
    try {
        $query->execute([':id' => $id]);
        connectDB()->commit();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        connectDB()->rollBack();
    }
}

/// Fonction qui permet de crée un post avec une image et un commentaire
function publishPost()
{
    $commentaire = "";
    $value = [];
    $media = "";
    $allPost = getAllFromPost();

    foreach ($allPost as $post) {
        $value = getMediaByIdPost($post['idPost']);
        $commentaire = getComById($post['idPost']);
        if (count($value) > 1) {
            $media = "<div id=\"carouselExampleControls\" class=\"carousel slide\" data-ride=\"carousel\"><div class=\"carousel-inner\">";
            $cont = 0;
            foreach ($value as $v) {
                $media .= sprintf("<div class=\"carousel-item %s\">", $cont == 0 ? "active" : "");

                switch ($v["typeMedia"]) {
                    case strpos($v["typeMedia"], 'image/'):
                        $media .= " <img src=\"uploads/" . $v["nomFichierMedia"] . "\" width=\"435\" height=\"435\" class=\"img-responsive\">";
                        break;
                    case strpos($v["typeMedia"], 'video/'):
                        $media .= "<video width=\"435\" height=\"435\" autoplay loop muted><source src=\"uploads/" . $v["nomFichierMedia"] . "\" type=\"" . $v["typeMedia"] . "\"></video>";
                        break;
                    case strpos($v["typeMedia"], 'audio/'):
                        $media .= "<audio controls><source src=\"uploads/" . $v["nomFichierMedia"] . "\" type=\"" . $v["typeMedia"] . "\"></video>";
                        break;
                }

                $media .= "</div>";
            }
            $media .= "</div> <a class=\"carousel-control-prev\" href=\"#carouselExampleControls\" role=\"button\" data-slide=\"prev\"><span class=\"carousel-control-prev-icon\" aria-hidden=\"true\"></span><span class=\"sr-only\">Previous</span></a><a class=\"carousel-control-next\" href=\"#carouselExampleControls\" role=\"button\" data-slide=\"next\"><span class=\"carousel-control-next-icon\" aria-hidden=\"true\"></span><span class=\"sr-only\">Next</span></a></div>";
        } else {
            switch ($value[0]["typeMedia"]) {
                case strpos($value[0]["typeMedia"], 'image/'):
                    $media = "<img src=\"uploads/" . $value[0]["nomFichierMedia"] . "\" width=\"435\" height=\"435\" class=\"img-responsive\">";
                    break;
                case strpos($value[0]["typeMedia"], 'video/'):
                    $media = "<video width=\"435\" height=\"435\" autoplay loop muted><source src=\"uploads/" . $value[0]["nomFichierMedia"] . "\" type=\"" . $value[0]["typeMedia"] . "\"></video>";
                    break;
                case strpos($value[0]["typeMedia"], 'audio/'):
                    $media = "<audio controls><source src=\"uploads/" . $value[0]["nomFichierMedia"] . "\" type=\"" . $value[0]["typeMedia"] . "\"></video>";
                    break;
            }
        }
        
        echo "<div class=\"panel panel-default\"> <div class=\"panel-thumbnail\">" . $media . " </div> <div class=\"panel-body\"> <p>" . $commentaire["commentaire"] . "</p> </div> </div>";
        //echo "<div class=\"panel panel-default\"> <button onclick=\"" . deletePost($value[0]["nomFichierMedia"], $commentaire) . "\">Supprimer</button> <div class=\"panel-thumbnail\">" . $media . " </div> <div class=\"panel-body\"> <p>" . $commentaire["commentaire"] . "</p> </div> </div>";
    }
}

/// Fonction qui permet de supprimer de la base de donnée le fichier voulu
function deleteFileInDB($nameFile)
{
    $sql = "DELETE FROM `media` WHERE `nomFichierMedia` LIKE :nameFile";
    connectDB()->beginTransaction();
    $query = connectDB()->prepare($sql);
    try {
        $query->execute([':nameFile' => $nameFile]);
        connectDB()->commit();
    } catch (Exception $e) {
        connectDB()->rollBack();
    }
}

/// Fonction qui permet de supprimer le commentaire de la base de donnée voulu
function deleteComInDB($com)
{
    $sql = "DELETE FROM `post` WHERE `idPost` LIKE :com";
    connectDB()->beginTransaction();
    $query = connectDB()->prepare($sql);
    try {
        $query->execute([':com' => $com]);
        connectDB()->commit();
    } catch (Exception $e) {
        connectDB()->rollBack();
    }
}

/// Fonction qui permet de supprimer un post
function deletePost($nameFile, $com)
{
    var_dump($nameFile);
    unlink("uploads/$nameFile");
    deleteComInDB($com);
    deleteFileInDB($nameFile);
}

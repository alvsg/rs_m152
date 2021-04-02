<!--
    PROJET : rs_m152
    AUTEUR : ALVES GUASTTI Letitia (I.FA-P3A)
    DESC. : Tout au long de ce cours, nous allons créer un projet qui ressemble à une page facebook. Le but étant de créer une page, sur laquelle on peut poster principalement des images. On pourra également supprimer et éditer les posts et les images y relatives.
    VERSION : 25.01.2021 v1.0
-->

<?php
require_once("config.inc.php");
// Vérifie si la session existe
if (!isset($_SESSION)) {
    session_start();
}

$btnBlog = filter_input(INPUT_POST, 'btnBlog', FILTER_SANITIZE_STRING);
$comments = filter_input(INPUT_POST, 'text', FILTER_DEFAULT);
$btnBlog = explode("/", $btnBlog);

// Switch selon la valeur des différents boutons présent sur le site
switch ($btnBlog[0]) {
        // La creation d'un post
    case 'Upload':
        sizeFile($comments);
        break;
        // La supression d'un post depuis la page index
    case 'delete':
        deletePost($btnBlog[1]);
        break;
        // La modification d'un post depuis la page index
    case 'update':
        $file = getMediaByIdPost($btnBlog[1]);
        // Boucle qui vérifie si il y a aucun des medias
        if ($file == null) {
            $file = getComById($btnBlog[1]);
            // L'identifiant du commentaire
            $_SESSION['idCom'] = $btnBlog[1];
        }
        // Le(s) media(s)
        $_SESSION['file'] = $file;
        header("Location: php/post.php");
        break;
        // La modification d'un post depuis la page post
    case 'Modify':
        // Boucle qui Vérifie si il y a des meidas
        if ($_SESSION['file'][0]['idPost'] != null) {
            // Boucle qui vérifie si il y a des medias a changer
            if ($_POST['mediaToChange'] != null) {
                $_SESSION['mediaToChange'] = getMediaByName($_POST['mediaToChange']);
            }
        }
        modifyPost($_SESSION['file'], $_SESSION['mediaToChange']);
        $_SESSION['file'] = null;
        $_SESSION['mediaToChange'] = null;
        $_SESSION['idCom'] = null;
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
    $query = connectDB()->prepare($sql);
    $query->execute([':nameFile' => "%$nameFile%"]);
    return $query->fetch(PDO::FETCH_ASSOC);
}

// Fonction qui permet d'ajouter les medias à la base de donnée et dans le dossier upload
function addMediaInDB($comment)
{
    $exist = "";
    connectDB()->beginTransaction();
    try {
        // Pour chaque media dans $_FILE, vérifie le type du fichier et attribut un identifiant unique
        for ($i = 0; $i < count($_FILES['mediaFile']['name']); $i++) {
            $name = preg_replace('/\.([^ ]+)/', '', $_FILES['mediaFile']['name'][$i]);
            $uniqNameFile = uniqid($name);
            $extension = preg_replace('/.*(?=\.)/', '', $_FILES['mediaFile']['name'][$i]);
            $uniqNameFile .= $extension;
            if (strpos($_FILES['mediaFile']['type'][$i], 'image/') !== false || strpos($_FILES['mediaFile']['type'][$i], 'video/') !== false || strpos($_FILES['mediaFile']['type'][$i], 'audio/') !== false) {
                $typeFile = $_FILES['mediaFile']['type'][$i];
            } else {
                echo '<div class="alert alert-danger" role="alert"> Le type du fichier ne convient pas ! </div>';
            }
            $tmpName = $_FILES["mediaFile"]["tmp_name"][$i];

            // Boucle qui vérifie si la méthode move_upload_file
            if (move_uploaded_file($tmpName, "../uploads/$uniqNameFile")) {
                // Boucle qui vérifie si le media a bin été ajouté dans le dossier uploads
                if (file_exists("../uploads/$uniqNameFile") == true) {
                    databaseInsert($uniqNameFile, $typeFile, $comment);
                    $exist = databaseSelectImage($uniqNameFile);
                    // Boucle qui vérifie que le media soit dans la base de donnée
                    if ($exist != null) {
                        header("Location: ../index.php");
                    } else {
                        echo '<div class="alert alert-warning" role="alert"> L\'ajout dans la base de donnée a echoué ! </div>';
                    }
                } else {
                    // Boucle qui vérifie que le media soit supprimé du dossier uploads
                    if (unlink("../uploads/$uniqNameFile") != true) {
                        echo '<div class="alert alert-warning" role="alert"> La supression du fichier dans le dossier a echoué ! </div>';
                    }
                }
            } else {
                echo '<div class="alert alert-warning" role="alert"> Le téléchargement a echoué ! </div>';
                deleteComInDB(getLastId());
            }
        }
        // Boucle qui vérifie que $_FILE ne soit pas null
        if ($_FILES['mediaFile']['name'][0] == null) {
            header("Location: ../index.php");
        }
        connectDB()->commit();
    } catch (Exception $e) {
        connectDB()->rollBack();
    }
}

/// Fonction qui permet de lancer la fonction addMediaInDB() si le champs commentaire n'est pas null
function publishMedia($comment)
{
    if ($comment != null) {
        publishCom($comment);
        addMediaInDB($comment);
    } else {
        echo '<div class="alert alert-danger" role="alert"> Veuillez entrer un commentaire ! </div>';
    }
}

/// Fonction qui permet de récupérer l'id du dernier commentaire
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
function publishCom($comment)
{
    $sql = "INSERT INTO `post` (commentaire) VALUES (:com)";
    $query = connectDB()->prepare($sql);
    $query->execute([':com' => $comment]);
}

/// Fonction qui permet de récupérer tous les post
function getAllFromPost()
{
    $sql = "SELECT * FROM `post` ORDER BY `idPost` DESC";
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

/// Fonction qui permet de récupérer le media selon l'id
function getMediaByIdPost($id)
{
    $sql = "SELECT * FROM `media` WHERE `idPost` LIKE :id";
    $query = connectDB()->prepare($sql);
    $query->execute([':id' => $id]);
    return $query->fetchAll(PDO::FETCH_ASSOC);
}

/// Fonction qui permet de récupérer le media selon le nom
function getMediaByName($nom)
{
    $sql = "SELECT * FROM `media` WHERE `nomFichierMedia` LIKE :nom";
    $query = connectDB()->prepare($sql);
    $query->execute([':nom' => $nom]);
    return $query->fetchAll(PDO::FETCH_ASSOC);
}

// Fonction qu permet de de définir le code HMTL du media selon son type
function HtmlForMedia($thisMedia, $media)
{
    switch ($thisMedia["typeMedia"]) {
        case strpos($thisMedia["typeMedia"], 'image/'):
            $media .= " <img src=\"uploads/" . $thisMedia["nomFichierMedia"] . "\" width=\"399\" height=\"399\" class=\"img-responsive\">";
            break;
        case strpos($thisMedia["typeMedia"], 'video/'):
            $media .= "<video width=\"399\" height=\"399\" autoplay loop muted><source src=\"uploads/" . $thisMedia["nomFichierMedia"] . "\" type=\"" . $thisMedia["typeMedia"] . "\"></video>";
            break;
        case strpos($thisMedia["typeMedia"], 'audio/'):
            $media .= "<audio controls><source src=\"uploads/" . $thisMedia["nomFichierMedia"] . "\" type=\"" . $thisMedia["typeMedia"] . "\"></audio>";
            break;
    }

    return $media;
}

/// Fonction qui permet de crée un post avec une image et un commentaire
function publishPost()
{
    $commentaire = "";
    $aMedia = [];
    $media = "";
    $post = "";
    $allPost = getAllFromPost();

    foreach ($allPost as $post) {
        $aMedia = getMediaByIdPost($post['idPost']);
        $commentaire = getComById($post['idPost']);
        // Boucle qui compte de media il y a dans un post
        if (count($aMedia) > 1) {
            $media = "<div id=\"carouselExampleControls\" class=\"carousel slide\" data-ride=\"carousel\"><div class=\"carousel-inner\">";
            $cont = 0;
            foreach ($aMedia as $thisMedia) {
                $media .= sprintf("<div class=\"carousel-item %s\">", $cont == 0 ? "active" : "");
                $media = HtmlForMedia($thisMedia, $media);
                $media .= "</div>";
            }
            $media .= "</div> <a class=\"carousel-control-prev\" href=\"#carouselExampleControls\" role=\"button\" data-slide=\"prev\"><span class=\"carousel-control-prev-icon\" aria-hidden=\"true\"></span><span class=\"sr-only\">Previous</span></a><a class=\"carousel-control-next\" href=\"#carouselExampleControls\" role=\"button\" data-slide=\"next\"><span class=\"carousel-control-next-icon\" aria-hidden=\"true\"></span><span class=\"sr-only\">Next</span></a></div>";
        } else {
            $media = HtmlForMedia($aMedia[0], $media);
        }
        $post = "<div class=\"panel panel-default fixed\">
            <div class=\"panel-body\" style=\"padding : 0px\">
                <form method=\"POST\" action=\"index.php\">
                    <button type=\"submit\" name=\"btnBlog\" class=\"btn\" style=\" float : right\" value=\"update/" . $post["idPost"] . "\"><i class=\"bi bi-pencil\"></i></button>
                    <button type=\"submit\" name=\"btnBlog\" class=\"btn\" style=\" float : right\" value=\"delete/" . $post["idPost"] . "\"><i class=\"bi bi-trash\"></i></button>
                </form>
            </div>";

        // Boucle qui vérifie si le post possède un media
        if ($aMedia != null) {
            $post .= "<div class=\"panel-body\" style=\"padding : 0px\">" . $media . " </div>";
        }
        $post .= "<div class=\"panel-body\"> <p>" . $commentaire["commentaire"] . "</p>
            </div>
            </div>";

        echo $post;
    }
}

/// Fonction qui permet de supprimer de la base de donnée le fichier voulu
function deleteFileInDB($file)
{
    $sql = "DELETE FROM `media` WHERE `idPost` LIKE :nameFile";
    $query = connectDB()->prepare($sql);
    $query->execute([':nameFile' => $file]);
}

/// Fonction qui permet de supprimer le commentaire de la base de donnée voulu
function deleteComInDB($com)
{
    $sql = "DELETE FROM `post` WHERE `idPost` LIKE :com";
    $query = connectDB()->prepare($sql);
    $query->execute([':com' => $com]);
}

/// Fonction qui permet de supprimer un post en fonction de son identifiant
function deletePost($idPost)
{
    $nameFile = getMediaByIdPost($idPost);
    foreach ($nameFile as $item) {
        $n = $item["nomFichierMedia"];
        unlink("uploads/$n");
    }
    deleteFileInDB($idPost);
    deleteComInDB($idPost);
}

/// Fonction qui permet de modifier les post en mettant à jour les informations dans la base de donnée
function modifyPost($file, $mediaToChange)
{
    // Boucle qui vérifie si le post est un simple commentaire
    if ($_SESSION['idCom'] == null) {
        $DBcom = getComById($file[0]['idPost']);
    } else {
        $DBcom = getComById($_SESSION['idCom']);
    }

    //Boucle qui permet de vérifier si le commentaire de la base de donnée est identique a celui du formulaire de la page post
    if (strcmp($_POST['text'], $DBcom['commentaire']) != 0 && $_POST['text'] != null) {
        // Boucle qui vérifie si le post est un simple commentaire
        if ($_SESSION['idCom'] == null) {
            updateComPost($file[0]['idPost'], $_POST['text']);
        } else {
            updateComPost($_SESSION['idCom'], $_POST['text']);
        }
        // Boucle qui vérifie si le commentaire de la page post est vide
    } else if ($_POST['text'] == "") {
        echo '<div class="alert alert-danger" role="alert"> Le champs du commentaire est vide ! </div>';
    }

    foreach ($mediaToChange as $media) {
        unlink("../uploads/" . $media['nomFichierMedia']);
        deleteImgInDB($media['idMedia']);
    }

    if ($_FILES['mediaFile']['name'][0] != null) {
        addMediaInDB($_POST['text']);
    }
}

/// Fonction qui permet de mettre à jour les commentaires dans la base de donnée
function updateComPost($idPost, $newCom)
{
    $sql = "UPDATE `post` SET `commentaire` = :newCom WHERE `idPost` LIKE :idPost";
    $query = connectDB()->prepare($sql);
    $query->execute([':newCom' => $newCom, ':idPost' => $idPost]);
}

/// Fonction qui permet de supprimer l'image de la base de donnée voulu
function deleteImgInDB($idMedia)
{
    $sql = "DELETE FROM `media` WHERE `idMedia` LIKE :idMedia";
    $query = connectDB()->prepare($sql);
    $query->execute([':idMedia' => $idMedia]);
}

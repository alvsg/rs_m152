<!--
    PROJET : rs_m152
    AUTEUR : ALVES GUASTTI Letitia (I.FA-P3A)
    DESC. : Tout au long de ce cours, nous allons créer un projet qui ressemble à une page facebook. Le but étant de créer une page, sur laquelle on peut poster principalement des images. On pourra également supprimer et éditer les posts et les images y relatives.
    VERSION : 25.01.2021 v1.0
-->

<?php
require_once('functions.php');

if ($_SESSION['file'] != "") {
    $btnValue = "Modify";
    $com = getComById($_SESSION['file'][0]['idPost']);
    if($com == null){
        $com = $_SESSION['file']['commentaire'];
    }
} else {
    $btnValue = "Upload";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <title>Blog</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js" integrity="sha384-b5kHyXgcpbZJO/tY9Ul7kGkf1S0CWuKcCD38l8YkeH8z8QjE0GmW1gYU5S9FOnJ0" crossorigin="anonymous"></script>
</head>

<body>
    <div class="container-fluid mt-2">
        <form method="POST" action="" enctype="multipart/form-data">
            <textarea name="text" rows="5" cols="55" style="resize: none;"><?php if ($com != null) echo $com ?></textarea>
            </br>
            Select a file :
            <input type="file" id="mediaFile" accept="image/*, video/*, audio/*" name="mediaFile[]" multiple onchange="analyseFichiers(this.files);">
            <div id="infos"></div>
            <input type="submit" name="btnBlog" value="<?= $btnValue ?>">
            <script>
                // Fonction qui permet d'afficher la taille total, le nom et le nombre de(s) fichier(s)
                function analyseFichiers(fichiers) {
                    if (fichiers) {
                        var infos = document.getElementById('infos');
                        var nombreFichiers = fichiers.length;
                        var tailleTotale = 0;
                        infos.innerHTML = "<p>Il y a <b>" + nombreFichiers + "</b> fichiers</p>";
                        infos.innerHTML += "<ul>";
                        for (i = 0; i < nombreFichiers; i++) {
                            infos.innerHTML += "<li>" + fichiers[i].name + " (" + fichiers[i].type + ")</li>";
                            tailleTotale += fichiers[i].size;
                        }
                        infos.innerHTML += "</ul>";
                        infos.innerHTML += "<p>Total : <b>" + Math.round(tailleTotale / 1024) + "</b> Kio </p> ";
                    }
                }
            </script>
        </form>
    </div>
</body>

</html>
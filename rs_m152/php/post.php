<!--
    PROJET : rs_m152
    AUTEUR : ALVES GUASTTI Letitia (I.FA-P3A)
    DESC. : Tout au long de ce cours, nous allons créer un projet qui ressemble à une page facebook. Le but étant de créer une page, sur laquelle on peut poster principalement des images. On pourra également supprimer et éditer les posts et les images y relatives.
    VERSION : 25.01.2021 v1.0
-->

<?php
require_once('functions.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <title>Blog</title>
</head>

<body>
    <form method="POST" action="../index.php" enctype="multipart/form-data">
        Select a file :
        <input type="file" accept="img/*" name="mediaFile[]" multiple>
        <input type="submit" name="btnBlog" value="Publish">
    </form>
</body>

</html>
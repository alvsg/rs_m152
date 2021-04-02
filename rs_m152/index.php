<!--
    PROJET : rs_m152
    AUTEUR : ALVES GUASTTI Letitia (I.FA-P3A)
    DESC. : Tout au long de ce cours, nous allons créer un projet qui ressemble à une page facebook. Le but étant de créer une page, sur laquelle on peut poster principalement des images. On pourra également supprimer et éditer les posts et les images y relatives.
    VERSION : 25.01.2021 v1.0
-->

<?php
include_once('php/functions.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <title>Blog</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">
    <link href="css/bootstrap.css" rel="stylesheet">
    <link href="css/facebook.css" rel="stylesheet">
</head>

<body>
    <div class="wrapper">
        <div class="box">
            <div class="row row-offcanvas row-offcanvas-left">
                <div class="column col-sm-1 col-xs-1 sidebar-offcanvas" id="sidebar">
                </div>
                <div class="column col-sm-10 col-xs-11" id="main">
                    <div class="navbar navbar-blue navbar-static-top">
                        <div class="navbar-header">
                            <button class="navbar-toggle" type="button" data-toggle="collapse" data-target=".navbar-collapse">
                                <span class="sr-only">Toggle</span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                            </button>
                            <a href="index.php" class="navbar-brand logo">β</img></a>
                        </div>
                        <nav class="collapse navbar-collapse" role="navigation">
                            <form class="navbar-form navbar-left">
                                <div class="input-group input-group-sm" style="max-width:360px;">
                                    <input class="form-control" placeholder="Search" name="srch-term" id="srch-term" type="text">
                                    <div class="input-group-btn">
                                        <button class="btn btn-default" type="submit"><i class="bi bi-search"></i></button>
                                    </div>
                                </div>
                            </form>
                            <ul class="nav navbar-nav">
                                <li>
                                    <a href="index.php"><i class="bi bi-house"></i> Home</a>
                                </li>
                                <li>
                                    <a href="php/post.php" role="button" data-toggle="modal"><i class="bi bi-plus"></i></i> Post</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                    <div class="padding">
                        <div class="full col-sm-9">
                            <div class="row">
                                <div class="col-sm-5">
                                    <div class="panel panel-default">
                                        <div class="panel-thumbnail"><img src="img/bg_5.jpg" class="img-responsive"></div>
                                        <div class="panel-body">
                                            <p class="lead">Nom blog</p>
                                            <p>45 Followers, 13 Posts</p>
                                            <p>
                                                <img src="img/150x150.gif" height="28px" width="28px">
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-7">

                                    <div class="well">
                                        <form class="form">
                                            <b>BIENVENU</b>
                                        </form>
                                    </div>
                                    <?php publishPost(); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="postModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">׼/button>
                        Update Status
                </div>
                <div class="modal-body">
                    <form class="form center-block">
                        <div class="form-group">
                            <textarea class="form-control input-lg" autofocus="" placeholder="What do you want to share?"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <div>
                        <button class="btn btn-primary btn-sm" data-dismiss="modal" aria-hidden="true">Post</button>
                        <ul class="pull-left list-inline">
                            <li><a href=""><i class="bi bi-upload"></i></a></li>
                            <li><a href=""><i class="bi bi-camera"></i></a></li>
                            <li><a href=""><i class="bi bi-geo-alt"></i></a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
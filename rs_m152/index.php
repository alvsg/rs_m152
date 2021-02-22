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
                                                <img src="img/150x150.jpg" height="28px" width="28px">
                                            </p>
                                        </div>
                                    </div>
                                    <!--<div class="panel panel-default">
                                          <div class="panel-heading"><a href="" class="pull-right">View all</a>
                                            <h4>Bootstrap Examples</h4>
                                        </div>
                                        <div class="panel-body">
                                            <div class="list-group">
                                                <a href="" class="list-group-item">Modal / Dialog</a>
                                                <a href="" class="list-group-item">Datetime Examples</a>
                                                <a href="" class="list-group-item">Data Grids</a>
                                            </div>
                                        </div>
                                </div>
                                <div class="well">
                                    <form class="form-horizontal" role="form">
                                        <h4>What's New</h4>
                                        <div class="form-group" style="padding:14px;">
                                            <textarea class="form-control" placeholder="Update your status"></textarea>
                                        </div>
                                        <button class="btn btn-primary pull-right" type="button">Post</button>
                                        <ul class="list-inline">
                                            <li><a href=""><i class="bi bi-upload"></i></a></li>
                                            <li><a href=""><i class="bi bi-camera"></i></a></li>
                                            <li><a href=""><i class="bi bi-geo-alt"></i></a></li>
                                        </ul>
                                    </form>
                                </div>
                                <div class="panel panel-default">
                                    <div class="panel-heading"><a href="" class="pull-right">View all</a>
                                        <h4>More Templates</h4>
                                    </div>
                                    <div class="panel-body">
                                        <img src="img/150x150.jpg" class="img-circle pull-right"> <a href="">Free @Bootply</a>
                                        <div class="clearfix"></div>
                                        There a load of new free Bootstrap 3
                                        ready templates at Bootply. All of these templates are free and don't
                                        require extensive customization to the Bootstrap baseline.
                                        <hr>
                                        <ul class="list-unstyled">
                                            <li><a href="">Dashboard</a></li>
                                            <li><a href="">Darkside</a></li>
                                            <li><a href="">Greenfield</a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h4>What Is Bootstrap?</h4>
                                    </div>
                                    <div class="panel-body">
                                        Bootstrap is front end frameworkto
                                        build custom web applications that are fast, responsive &amp; intuitive.
                                        It consist of CSS and HTML for typography, forms, buttons, tables,
                                        grids, and navigation along with custom-built jQuery plug-ins and
                                        support for responsive layouts. With dozens of reusable components for
                                        navigation, pagination, labels, alerts etc.. </div>
                                </div>-->
                                </div>
                                <div class="col-sm-7">

                                    <div class="well">
                                        <form class="form">
                                            <b>BIENVENU</b>
                                        </form>
                                    </div>
                                    <!--<div class="panel panel-default">
                                        <div class="panel-heading"><a href="" class="pull-right">View all</a>
                                            <h4>Bootply Editor &amp; Code Library</h4>
                                        </div>
                                        <div class="panel-body">
                                            <p><img src="img/150x150.jpg" class="img-circle pull-right"> <a href="">The Bootstrap Playground</a></p>
                                            <div class="clearfix"></div>
                                            <hr>
                                            Design, build, test, and prototype
                                            using Bootstrap in real-time from your Web browser. Bootply combines the
                                            power of hand-coded HTML, CSS and JavaScript with the benefits of
                                            responsive design using Bootstrap. Find and showcase Bootstrap-ready
                                            snippets in the 100% free Bootply.com code repository.
                                        </div>
                                    </div>
                                    <div class="panel panel-default">
                                        <div class="panel-heading"><a href="" class="pull-right">View all</a>
                                            <h4>Stackoverflow</h4>
                                        </div>
                                        <div class="panel-body">
                                            <img src="img/150x150.jpg" class="img-circle pull-right"> <a href="">Keyword: Bootstrap</a>
                                            <div class="clearfix"></div>
                                            <hr>
                                            <p>If you're looking for help with Bootstrap code, the <code>twitter-bootstrap</code> tag at <a href="">Stackoverflow</a> is a good place to find answers.</p>
                                            <hr>
                                            <form>
                                                <div class="input-group">
                                                    <div class="input-group-btn">
                                                        <button class="btn btn-default">+1</button><button class="btn btn-default"><i class="bi bi-share"></i></button>
                                                    </div>
                                                    <input class="form-control" placeholder="Add a comment.." type="text">
                                                </div>
                                            </form>

                                        </div>
                                    </div>
                                    <div class="panel panel-default">
                                        <div class="panel-heading"><a href="" class="pull-right">View all</a>
                                            <h4>Portlet Heading</h4>
                                        </div>
                                        <div class="panel-body">
                                            <ul class="list-group">
                                                <li class="list-group-item">Modals</li>
                                                <li class="list-group-item">Sliders / Carousel</li>
                                                <li class="list-group-item">Thumbnails</li>
                                            </ul>
                                        </div>
                                    </div>-->

                                    <?php publishPost('img/bg_4.jpg', 'test'); ?>
                                    <!-- <div class="panel panel-default">
                                        <div class="panel-thumbnail"><img src="img/bg_4.jpg" class="img-responsive"></div>
                                        <div class="panel-body">
                                            <p class="lead">Social Good</p>
                                            <p>1,200 Followers, 83 Posts</p>
                                        </div>
                                    </div> -->

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
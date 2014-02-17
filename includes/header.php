<?php
if(!isset($titrePage)) $titrePage = "Sans titre";

$nomFichier = getNomFichier();
?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="Romain">

        <title><?php echo $titrePage; ?> - Mon super Blog</title>

        <!-- Bootstrap core CSS -->
        <link href="css/bootstrap.css" rel="stylesheet">
        
        <!-- Custom styles for this template -->
        <style>
            body {
                padding-top: 50px;
            }
        </style>

        <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <![endif]-->
    </head>

    <body>

        <div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
            <div class="container">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="index.php">Mon super blog</a>
                </div>
                <div class="collapse navbar-collapse">
                    <ul class="nav navbar-nav">
                        <li<?php if($nomFichier == "index.php") echo ' class="active"' ?>><a href="index.php">Liste des actus</a></li>
                        <li<?php if($nomFichier == "ajouterArticle.php") echo ' class="active"' ?>><a href="ajouterArticle.php">Ajouter article</a></li>
                        <?php if(isConnected()) : ?>
                            <li><a href="deconnexion.php">Se déconnecter</a></li>
                        <?php endif; ?>
                    </ul>
                </div><!--/.nav-collapse -->
            </div>
        </div>

        <div class="container">
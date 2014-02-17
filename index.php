<?php require_once './includes/fonctions.php'; ?>
<?php require_once './includes/config.php'; ?>
<?php
// Variable qui s'affiche dans la balise <title> de header.php
$titrePage = "Liste des articles";

$link = mysqli_connect_utf8(MYSQL_SERVER, MYSQL_USER, MYSQL_PASS, MYSQL_BASE);

$sql = "SELECT id, titre
        FROM article
        ORDER BY id DESC
        LIMIT 0, 10";

// execution de la requete, si $result vaut false (faire un var_dump(mysqli_error($link))) après
$result = mysqli_query($link, $sql);

// transmorfe les resultats de la requete en un tableau numérique de tableau associatif
// (lignes et colonnes)
$listArticles = mysqli_fetch_all($result, MYSQLI_ASSOC);

mysqli_close($link);

$flashMessage = flashMessageRead();
?>
<?php include_once './includes/header.php'; ?>
<?php if(isset($flashMessage)) : ?>
<div class="alert alert-success"><?php echo $flashMessage; ?></div>
<?php endif; ?>
<h1>Mes 10 derniers articles</h1>
<ol>
    <?php foreach ($listArticles as $artAssoc) : ?>
    <li>
        <a href="article.php?id=<?php echo (int) $artAssoc["id"]; ?>"><?php echo strip_tags($artAssoc["titre"]); ?></a>
    </li>
    <?php endforeach; ?>
</ol>
<?php include_once './includes/footer.php'; ?>


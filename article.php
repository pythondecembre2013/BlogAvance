<?php require_once './includes/fonctions.php'; ?>
<?php require_once './includes/config.php'; ?>
<?php
// Si pas d'id dans l'URL, on rediriger vers l'index
// TODO
if (!urlContient("id")) {
    redirigerEtQuitter("index.php");
}

// (int) protège des injections SQL
$id = (int) $_GET["id"];

$link = mysqli_connect_utf8(MYSQL_SERVER, MYSQL_USER, MYSQL_PASS, MYSQL_BASE);

$sql = "SELECT titre, login AS auteur, contenu, date_pub, photo
        FROM article
        JOIN membre ON membre.id = membre_id
        WHERE article.id = $id";

$result = mysqli_query($link, $sql);

$artAssoc = mysqli_fetch_assoc($result);

mysqli_close($link);

// Si pas d'article (l'id de l'URL ne correspond pas à un enregistrement)
if ($artAssoc === null) {
    redirigerEtQuitter("index.php");
}

$titrePage = $artAssoc["titre"];
setlocale(LC_TIME, "fr_FR.utf8", "fr_FR", "french", "france", "fr");
$dateFr = strftime("%d %B %Y à %Hh%M", strtotime($artAssoc["date_pub"]));
// $dateFr =  "5 janvier 2014 à 13h24";
?>
<?php include_once './includes/header.php'; ?>
<h1><?php echo strip_tags($artAssoc["titre"]); ?></h1>
<div class="contenu_article">
    <?php echo htmlspecialchars($artAssoc["contenu"]); ?>
</div>
<?php if (isset($artAssoc["photo"])) : ?>
    <div>
        <img src="images/<?php echo strip_tags($artAssoc["photo"]); ?>" alt="">
    </div>
<?php endif; ?>
<footer>
    Publié le <?php echo $dateFr; ?> par <?php echo strip_tags($artAssoc["auteur"]); ?>
</footer>
<?php include_once './includes/footer.php'; ?>

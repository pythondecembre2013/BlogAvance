<?php require_once './includes/fonctions.php'; ?>
<?php require_once './includes/config.php'; ?>
<?php
// On teste si on est connecté
// on redirige vers login.php sinon
if (!isConnected()) {
    saveSecuredPage();
    redirigerEtQuitter("login.php");
}

$titrePage = "Ajouter un article";

if (postContient("titre", "contenu") && isset($_FILES["photo"])) {
    $data = array_map("trim", $_POST);
//    $data["date_pub"] = date(DATE_ISO8601);

    // Protection contre les attaques XSS
    $data["titre"] = strip_tags($data["titre"]);
    $data["membre_id"] = $_SESSION["idUserConnected"];
    $data["contenu"] = htmlspecialchars($data["contenu"]);

    // Validation des champs
    $erreurs = [];

    if (strlen($data["titre"]) < 5 || strlen($data["titre"]) > 100) {
        $erreurs["titre"] = "Le titre doit être compris entre 5 et 100 caractères";
    }

    if (strlen($data["contenu"]) < 25) {
        $erreurs["contenu"] = "L'article doit contenir au moins 25 caractères";
    }

    // Validation et upload de la photo
    // error 4 veut dire pas de fichier uploadé
    if ($_FILES["photo"]["error"] !== 4) {
        if ($_FILES["photo"]["error"] !== 0 || $_FILES["photo"]["size"] > 2 * 1024 * 1024 || !in_array($_FILES["photo"]["type"], ["image/jpeg", "image/gif", "image/png"])) {
            $erreurs["photo"] = "Il faut uploader une image au format JPEG, GIF ou PNG inférieure à 2Mo";
        } else {
            $source = $_FILES["photo"]["tmp_name"];
            $destination = strip_tags(uniqid() . "_" . $_FILES["photo"]["name"]); // images/jhdsjhg_maphoto.png

            $estUploadee = move_uploaded_file($source, "images/" . $destination);

            if (!$estUploadee) {
                $erreurs["photo"] = "Une erreur s'est produite pendant l'upload du fichier";
            }
        }
    }

    if (empty($erreurs)) {

        $dsn = "mysql:host=".MYSQL_SERVER.";dbname=".MYSQL_BASE.";charset=UTF8";
        $pdo = new PDO($dsn, MYSQL_USER, MYSQL_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);

        if (isset($destination)) {
            $sql = "INSERT INTO article (titre, contenu, membre_id, photo) 
                    VALUES (:titre, :contenu, :membre_id, :photo)";
        }
        else {
            $sql = "INSERT INTO article (titre, contenu, membre_id) 
                    VALUES (:titre, :contenu, :membre_id)";
        }
        
        $stmt = $pdo->prepare($sql);
        
        $stmt->bindValue("titre", $data["titre"]);
        $stmt->bindValue("contenu", $data["contenu"]);
        $stmt->bindValue("membre_id", $data["membre_id"]);
        if (isset($destination)) { 
            $stmt->bindValue("photo", $data["photo"]);
        }
        
        $succes = $stmt->execute();
        
        if ($succes) {
            flashMessageEcrire("L'article a bien été publié");
            redirigerEtQuitter("index.php");
        } else {
            $erreurs["mysql"] = "Une erreur s'est produite à l'insertion";
        }
    }
}
?>
<?php include_once './includes/header.php'; ?>
<h1>Ajouter un article</h1>
<?php if (isset($messageSucces)) : ?>
    <div class="alert alert-success"><?php echo $messageSucces; ?></div>
<?php endif; ?>
<?php if (!empty($erreurs)) : ?>
    <div class="alert alert-danger">
        Voici la liste des erreurs :
        <ul>
    <?php foreach ($erreurs as $err) : ?>
                <li><?php echo $err; ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
        <?php endif; ?>
<form method="post" enctype="multipart/form-data" role="form">
    <div class="form-group<?php if (isset($erreurs["titre"])) echo " has-error" ?>">
        <label for="titre">Titre</label>
        <input type="text" class="form-control" id="titre" placeholder="Saisir un titre" name="titre" <?php if (isset($data["titre"])) echo "value=\"$data[titre]\"" ?>>
<?php if (isset($erreurs["titre"])) : ?>
            <span class="help-block"><?php echo $erreurs["titre"]; ?></span>
        <?php endif; ?>
    </div>
    <div class="form-group<?php if (isset($erreurs["photo"])) echo " has-error" ?>">
        <label for="photo">Photo</label>
        <input type="file" id="photo" name="photo">
<?php if (isset($erreurs["photo"])) : ?>
            <span class="help-block"><?php echo $erreurs["photo"]; ?></span>
        <?php endif; ?>
    </div>
    <div class="form-group<?php if (isset($erreurs["contenu"])) echo " has-error" ?>">
        <label for="contenu">Contenu de l'article</label>
        <textarea class="form-control" rows="10" id="contenu" placeholder="Saisir votre article" name="contenu"><?php if (isset($data["contenu"])) echo $data["contenu"] ?></textarea>
<?php if (isset($erreurs["contenu"])) : ?>
            <span class="help-block"><?php echo $erreurs["contenu"]; ?></span>
        <?php endif; ?>
    </div>
    <button type="submit" class="btn btn-primary">Insérer</button>
</form>
<?php include_once './includes/footer.php'; ?>
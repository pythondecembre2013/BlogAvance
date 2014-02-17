<?php include_once './includes/fonctions.php'; ?>
<?php include_once './includes/config.php'; ?>
<?php
// On teste si l'utilisateur a validé le formulaire
if (postContient("login", "pass")) {
    $login = $_POST["login"];
    $pass = $_POST["pass"];

    $link = mysqli_connect_utf8(MYSQL_SERVER, MYSQL_USER, MYSQL_PASS, MYSQL_BASE);
    $idBase = mysqli_verifier_login($link, ["table" => "membre", "col_login" => "login",
        "col_pass" => "pass", "login" => $login, "pass" => $pass,
        "salt"  => PASSWORD_SALT]);

    mysqli_close($link);

    if ($idBase === null) {
        $message = "Mauvais login/pass";
    } else {
        // On enregistre dans la session le login de l'utilisateur qui s'est connecté
        seConnecter($login, $idBase);

        // Si on vient d'une page sécurisée
        // on redirige vers la page d'origine
        if (containsSecuredPage()) {
            $page = securedPage();
        } else {
            // Sinon on redirigera vers l'index avec un message
            $page = "index.php";
            flashMessageEcrire("Bienvenu $loginBase, vous êtes connecté");
        }

        redirigerEtQuitter($page);
    }
}
?>
<?php include_once './includes/header.php'; ?>
<h2>Connexion</h2>
<?php if (isset($message)) : ?>
    <p><?php echo $message; ?></p>
<?php endif; ?>
<form method="post">
    <div class="form-group">
        Login : <input class="form-control" type="text" name="login">
    </div>
    <div class="form-group">
        Mot de passe : <input class="form-control" type="password" name="pass" autocomplete="off">
    </div>
    <p>
        <input type="submit" value="Se connecter">
    </p>
</form>
<?php include_once './includes/footer.php'; ?>
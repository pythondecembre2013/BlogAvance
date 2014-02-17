<?php require_once './includes/fonctions.php'; ?>
<?php require_once './includes/config.php'; ?>
<?php
if (postContient("login", "pass", "repeat", "email")) {

    $login = trim($_POST["login"]);
    $pass = trim($_POST["pass"]);
    $repeat = trim($_POST["repeat"]);
    $email = trim($_POST["email"]);


    $message = "";


    if (empty($login) || empty($pass)) {
        $message .= "Le login et le mot de passe sont obligatoires<br>";
    }

    // Le login doit contenir entre 5 et 25 caractères
    if (strlen($login) < 5 || strlen($login) > 25) {
        $message .= "Le login doit contenir entre 5 et 25 caractères<br>";
    }
    
    $link = new mysqli(MYSQL_SERVER, MYSQL_USER, MYSQL_PASS, MYSQL_BASE);
    $link->query("SET NAMES UTF8");
//    $link = mysqli_connect($host, $login, $pass, $base);
//    mysqli_query($link, "SET NAMES UTF8");
    
    $login = $link->escape_string($login);
//    $login = mysqli_escape_string($link, $login);
    
    $sql = "SELECT login FROM membre WHERE login = '$login'";
    
    $result = $link->query($sql);
    /* @var $result mysqli_result */ // Permet de retrouver la complétion sous NetBeans
    
    if($result->num_rows > 0) {
        $message .= "Ce login est déjà pris<br>";
    }

    // Mot de passe correspond à la confirmation
    if ($pass !== $repeat) {
        $message .= "Le mot de passe et sa répétition ne correspondent pas<br>";
    }

    // Le format d'email n'est pas correct
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message .= "L'email n'a un format valide<br>";
    }

    // Si pas d'erreur
    if (empty($message)) {
        $email = $link->escape_string($email);
        $pass = $link->escape_string($pass);
        $salt = $link->escape_string(PASSWORD_SALT);
        
        // Sauvegarde
        $link->autocommit(false);
        
        $sql = "INSERT INTO membre (login, pass)
                VALUES('$login', MD5('$login$pass$salt'))";
        
        $succes1 = $link->query($sql);
        
        $idMembre = $link->insert_id;
        
        $sql = "INSERT INTO profile(email, membre_id) 
                VALUES('$email', '$idMembre')";
        
        $succes2 = $link->query($sql);
        
        var_dump($link->error);
        
        if($succes1 && $succes2) {
            // Valider les modifs
            $link->commit();
            $link->close();
            flashMessageEcrire("Vous êtes bien inscrit");
            seConnecter($login);
            redirigerEtQuitter("index.php");
        }
        
        // Revenir à la sauvegarde
        $link->rollback();
        $message = "Une erreur s'est produite durant l'inscription";
    }
    $link->close();
} else {
    $message = "Veuillez remplir ce formulaire";
}
?>
<?php include_once './includes/header.php'; ?>
<h2>Inscription</h2>
<p><?php echo $message; ?></p>
<form method="post" novalidate>
    <p>
        <label>
            Login : <input type="text" name="login">
        </label>
    </p>
    <p>
        <label>
            Mot de passe : <input type="pass" name="pass">
        </label>
    </p>
    <p>
        <label>
            Répéter : <input type="pass" name="repeat">
        </label>
    </p>
    <p>
        <label>
            Email : <input type="email" name="email">
        </label>
    </p>
    <p>
        <input type="submit" value="Inscription">
    </p>
</form>
<?php include_once './includes/footer.php'; ?>

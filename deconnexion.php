<?php require_once './includes/fonctions.php'; ?>
<?php

deconnexion();

flashMessageEcrire("Vous êtes bien déconnecté");

redirigerEtQuitter("index.php");
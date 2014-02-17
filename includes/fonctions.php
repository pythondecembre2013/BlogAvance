<?php

/* Fonctions sur les Requetes/Réponses */

function urlContient() {
    $test = true;

    foreach (func_get_args() as $arg) {
        $test &= isset($_GET[$arg]);
    }

    return $test;
}

function postContient() {
    $test = true;

    foreach (func_get_args() as $arg) {
        $test &= isset($_POST[$arg]);
    }

    return $test;
}

function redirigerEtQuitter($url) {
    header("Location: $url");
    exit();
}

function getNomFichier() {
    return substr($_SERVER["PHP_SELF"], strrpos($_SERVER["PHP_SELF"], "/")+1);
}

/* Fonctions sur MySQL */

function mysqli_connect_utf8($host, $login, $pass, $base) {
    $link = mysqli_connect($host, $login, $pass, $base);
    mysqli_query($link, "SET NAMES UTF8");

    return $link;
}

function mysqli_insert($link, $table, array $donneesAssoc) {
    // Pour éviter les injections SQL
    foreach ($donneesAssoc as $col => $val) {
        $donneesAssoc[$col] = mysqli_escape_string($link, $val);
    }

    $listCols = array_keys($donneesAssoc);
    $listVals = array_values($donneesAssoc);

    $sql = "INSERT INTO $table (";
    $sql .= implode(", ", $listCols);
    $sql .= ") VALUES ('";
    $sql .= implode("', '", $listVals);
    $sql .= "')";

    return mysqli_query($link, $sql);
}

/* Fonction sur FlashMessage */

function session_start_once() {
    if (!isset($_SESSION)) {
        session_start();
    }
}

function flashMessageExists() {
    session_start_once();

    $test = isset($_SESSION["flashMessage"]);

    return $test;
}

function flashMessageRead() {
    $message = null;

    session_start_once();

    if (flashMessageExists()) {
        $message = $_SESSION["flashMessage"];
        unset($_SESSION["flashMessage"]);
    }

    return $message;
}

function flashMessageEcrire($message) {
    session_start_once();
    $_SESSION["flashMessage"] = $message;
}

/* Fonctions sur le login */

function deconnexion() {
    session_start_once();
    unset($_SESSION["userConnected"]);
    unset($_SESSION["idUserConnected"]);
}

function userConnected() {
    session_start_once();
    
    if(!isConnected()) {
        return null;
    }
    
    return $_SESSION["userConnected"];
}

function isConnected() {
    session_start_once();

    return isset($_SESSION["userConnected"]);
}

function saveSecuredPage() {
    session_start_once();
    $_SESSION["securedPage"] = $_SERVER["REQUEST_URI"];
}

function mysqli_verifier_login($link, array $config) {
    
    if(!isset($config["table"], $config["col_login"], $config["col_pass"], $config["login"]
            , $config["pass"], $config["salt"])) {
        return false;
    }
    
    // Problème de sécurité : Injection SQL
    // login et pass viennent de l'extérieur et finissent dans une requete SQL
    // donc mysqli_escape_string
    $login = mysqli_escape_string($link, $config["login"]);
    $pass = mysqli_escape_string($link, $config["pass"]);
    $table = mysqli_escape_string($link, $config["table"]);
    $col_login = mysqli_escape_string($link, $config["col_login"]);
    $col_pass = mysqli_escape_string($link, $config["col_pass"]);
    $salt = mysqli_escape_string($link, $config["salt"]);
    
    $sql = "SELECT id FROM $table WHERE $col_login = '$login' AND $col_pass = MD5('$login$pass$salt')";
    
    $result = mysqli_query($link, $sql);
    
    $membreAssoc = mysqli_fetch_assoc($result);
    
    if($membreAssoc === null) {
        return null;
    }
    
    return $membreAssoc["id"];
}


function seConnecter($login, $id = false) {
    session_start_once();
    session_regenerate_id(); // pour éviter la fixation de session (nouvel id de session)
    $_SESSION["userConnected"] = $login;
    if($id) {
        $_SESSION["idUserConnected"] = $id;
    }
} 
        
function containsSecuredPage() {
    session_start_once();
    
    return isset($_SESSION["securedPage"]);
}

function securedPage() {
    $page = null;

    session_start_once();

    if (containsSecuredPage()) {
        $page = $_SESSION["securedPage"];
        unset($_SESSION["securedPage"]);
    }

    return $page;
}

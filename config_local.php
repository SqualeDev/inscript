<?php
    $local = true;
    $debug = false;
    if ( $local ) {
        // On Windows
        // $strSlash = "\\"; 
        // On other
        $strSlash = "/";
        $baseUrl =  "http://" . $_SERVER['SERVER_NAME'];
        $pathUrl = "";
    } else {
        $strSlash = "/";
        // $strProtocol = $_SERVER['HTTP_X_FORWARDED_PROTO'];
        $strProtocol = $_SERVER['REQUEST_SCHEME'];
        $baseUrl =  $strProtocol . "://" . $_SERVER['SERVER_NAME'];
        $pathUrl = "licencies" . $strSlash;
    }
    
    # Definition des parametres de connexion à la base de donnees
    $db_host = "127.0.0.1";
    $db_driv = "mysql";
    $db_port = "3308";
    $db_name = "csg_tiralarc_fr";
    $db_user = "csg_rw";
    $db_pass = "motdepasse";
    
    # Nombre de jours avant le concours permettant de lancer l'inscription au concours
    $intJrsPre_Inscript = 30;
    # Nombre de jours avant le concours permettant de vérifier les renseignements du concours
    $_intJrsRenseigner = 80;

?>
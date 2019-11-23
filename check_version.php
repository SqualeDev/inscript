<?php
    // Ce fichier sera lancé via crontab lorsqu'une nouvelle version sera detectée sous Gitlab
    // Il recupérera tous les utilisateurs et leur adresse mails pour leur envoyer un mail les prévenants qu'une nouvelle version est disponible avec la note de version
    if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/licencies')) {
        // Config finale
        // require_once $_SERVER['DOCUMENT_ROOT'] . '/licencies/config_local.php';
        require_once $_SERVER['DOCUMENT_ROOT'] . '/licencies/constantes.php';
    } else {
        // Config Debug
        // require_once $_SERVER['DOCUMENT_ROOT'] . '/config_local.php';
        require_once $_SERVER['DOCUMENT_ROOT'] . '/constantes.php';
    }
    // require_once 'constantes.php';
    require_once PATH_MODELS . 'model_class_user.php';
    require_once PATH_MODELS . 'model_class_db.php';

    if ($debug) {
        echo "Timezone actuel : " . date_default_timezone_get();
        echo "<h3> PHP List All Post Variables</h3>";
        var_dump($_POST);
        echo "<h3> PHP List All Server Variables</h3>";
        var_dump($_SERVER);
    }

    // Niveau de Log demandé parmi INFO, ERROR, CRITIC
    $arrLevel = array("INFO", "ERROR");

    function logging($str, $level) {
        global $arrLevel;

        if (in_array($level, $arrLevel, $func=null)) {
            $log = date('Y-m-d H:i:s')."\t".str_pad($level,5)."\t".str_pad($str,90)."\t".$_SERVER['PHP_SELF']."/".$func.PHP_EOL;
            echo $log . "<br>" .PHP_EOL;
        }
    }

    function listeUtilisateurs() {
        // NOUVELLE INSTANCE DE MODEL DB
        $dbNotifies = new ClassDb();
        // REQUETE
        $strQuery = "SELECT usr_firstname, usr_lastname, usr_mail FROM csg_users WHERE usr_mail_notif_version = 1;";
        // echo $strQuery;
        // EXECUTER LA REQUETE AVEC LES PARAMETRES VIA MODEL DB
        $result = $dbNotifies->getAllData($strQuery);
        // RECUPERER LE RESULTAT
        return $result;
        $dbNotifies = null;
    }
    
logging("=================            Traitement des versions            =================", "INFO", "main");
logging("Vérification de la clé d'accès", "INFO", "main");
if (isset($_GET['cle'])){
    if ($_GET['cle'] == 'a14axQHk8JA6dfpjNmwPoRpCvcTQg5WG8pM6N6Tzmo75XcgIoy') {
        logging("Clé définit et autorisée !", "INFO", "main");
        // Récuperer la liste des utilisateurs et leur mail qui ont demandés une notification par mail
        $arrNotifies = listeUtilisateurs();
        // Affichage de la liste des notifiés
        foreach ($arrNotifies as $key => $row) {
            echo "<div id='notif'>";
            echo "<span id='prenom'>" . $row['usr_firstname'] . "</span>";
            echo "<span id='nom'>" . $row['usr_lastname'] . "</span>";
            echo "<span id='mail'>" . $row['usr_mail'] . "</span>";
            echo "</div>";
            echo "<br>".PHP_EOL;
        }
    } else {logging("Cle non autorisée", "INFO", "main");}
} else {logging("Pas de clé défini !", "INFO", "main");}
logging("=================         Fin de Traitement des versions        =================", "INFO", "main");
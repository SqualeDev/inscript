<?php
    if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/licencies')) {
        // Config finale
        // require_once $_SERVER['DOCUMENT_ROOT'] . '/licencies/config_local.php';
        require_once $_SERVER['DOCUMENT_ROOT'] . '/licencies/constantes.php';
    } else {
        // Config Debug
        // require_once $_SERVER['DOCUMENT_ROOT'] . '/config_local.php';
        require_once $_SERVER['DOCUMENT_ROOT'] . '/constantes.php';
    }
    // require $_SERVER['DOCUMENT_ROOT'] . '/licencies/constantes.php';
    require PATH_MODELS . 'model_class_dump.php';
    
    // Niveau de Log demandé parmi INFO, ERROR, CRITIC
    $arrLevel = array("INFO", "ALERT", "ERROR");

    function logging($str, $level, $func=null) {
        global $arrLevel;

        if (in_array($level, $arrLevel)) {
            $log = date('Y-m-d H:i:s')."\t".str_pad($level,5)."\t".str_pad($str,90)."\t".$_SERVER['PHP_SELF']."/".$func.PHP_EOL;
            
            $fileLog = fopen(PATH_LOGS . 'check_dump_'.date('Ymd').'.log', 'a+');
            fwrite($fileLog, $log);
            fclose($fileLog);
            echo $log . '<br>';
        }
    }

    logging("=================           Traitement des sauvegardes          =================", "INFO", "main");
    logging("Vérification de la clé d'accès", "INFO", "main");
    if (isset($_GET['cle'])){
        if ($_GET['cle'] == 'a14axQHk8JA6dfpjNmwPoRpCvcTQg5WG8pM6N6Tzmo75XcgIoy') {

            $mySqlDump = new MySqlDump();
            $mySqlDump->setMaxFileSize(2);
            $mySqlDump->dumpDatabase($db_host, $db_name, $db_user, $db_pass, '*');

        } else {logging("Cle non autorisée", "INFO", "main");}
    } else {logging("Pas de clé défini !", "INFO", "main");}
    logging("=================        Fin de Traitement des sauvegardes      =================", "INFO", "main");
?>
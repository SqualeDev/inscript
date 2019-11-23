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
    // require_once 'constantes.php';
    require_once PATH_MODELS . 'model_class_db.php';

    function existMandat($_strEpreuveID) {
        // NOUVELLE ISNTANCE DE MODEL DB
        $dbConcours = new ClassDb();
        // REQUETE
        $strQuery = "SELECT epr_mandat FROM csg_epreuves WHERE epr_id = ?;";
        // EXECUTE LA REQUETE AVEC LES PARAMETRES VIA MODEL DB
        $result = $dbConcours->getData($strQuery, array($_strEpreuveID));
        // var_dump($result);
        // si l'IPN existe dans la table
        if ($result[0]['epr_mandat'] != '') {
        //     // RETURN true
            return true;
        } else {
        //     // RETURN false
            return false;
        }
        $dbConcours = null;
    }

    function updateMandat($_strEpreuveID, $_strFileMandat) {
            // NOUVELLE INSTANCE DE MODEL DB
            $dbConcours = new ClassDb();
            // REQUETE
            $strQuery = "UPDATE csg_epreuves
                            SET 
                                epr_mandat = '".$_strFileMandat."'
                            WHERE 
                                epr_id = ".$_strEpreuveID."
                            ;";
            echo $strQuery;
            // EXECUTER LA REQUETE AVEC LES PARAMETRES VIA MODEL DB
            $result = $dbConcours->updateRow($strQuery);
            // RECUPERER LE RESULTAT
            if ($dbConcours->queryNbRows > 0) {
                return true;
            } else {
                return false;
            }
            $dbConcours = null;
    }

    $arrFileMandats = scandir(PATH_MANDATS);

    foreach ($arrFileMandats as $key => $strFileMandat) {
        if (!in_array($strFileMandat,array(".",".."))) {
            $strFileSansExt = pathinfo($strFileMandat, PATHINFO_FILENAME);
            if (!existMandat($strFileSansExt)) {
                // UPDATE de la base de données
                $result = updateMandat($strFileSansExt, $strFileMandat);
                echo "Ajout du mandat à l'epreuve : " . $strFileSansExt . " ; " .$result ."<br/>";
            }
        }
    }
    
?>
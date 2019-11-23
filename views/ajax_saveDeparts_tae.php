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
    // require_once $_SERVER['DOCUMENT_ROOT'] . '/licencies/constantes.php';
    require_once PATH_MODELS . 'model_class_concours.php';
    setlocale(LC_ALL, 'fr_FR');
    date_default_timezone_set('Europe/Paris');
    
    // echo "<h3> PHP List All Post - Session Variables</h3>";
    // var_dump($_POST);
    // echo "<h3> FIN List </h3>";

    if (isset($_POST['modalID'])) {
        // if ($_POST['bt-form-inscript'] == 'inscript') {
        $intConcours_ID = intval($_POST['modalID']);
        // $dateConcours = $_POST['inputDate'];
        $strLicencie_ID = strtoupper($_POST['userID']);
        $arrModal = array();
        
        $objInscript = new ClassConcours($intConcours_ID);
        
        // Si le concours est déjà Clos ne rien faire
        if(!$objInscript->isConcoursClos($intConcours_ID)) {
            // Supprimer tous les départs de ce concours pour le licencié
            $retourDelete = $objInscript->supprimerToutesInscriptions($intConcours_ID, $strLicencie_ID);
        
            for ($i = 1; $i <= 10; $i++) {
                if(!empty($_POST['inputDepart' . $i])) {
                    $strBlason = null;
                    if (isset($_POST['switchTypeDepart' . $i]) && $_POST['switchTypeDepart' . $i] == 'on') {
                        $strTypeEpr = "Championnat";
                    } else {
                        $strTypeEpr = "Coupe";
                    }
                    $strLieu = $objInscript->getLieuConcours($intConcours_ID);
                    // Pour chaque départ, Ecrire dans la base de données les informations $strLicencie_ID, $intConcours_ID, $_POST['inputDepart' . $i]
                    $arrDat = explode("|", $_POST['inputDepart' . $i]);
                    $retour = $objInscript->insertNewInscription($strLicencie_ID, $intConcours_ID, $arrDat[1], $strBlason, $strTypeEpr, $arrDat[0]);
                    $dateConcours = strtotime($arrDat[0]);
                    if($retour) {
                        $strTitre = "Préinscription Réussi !";
                        $strType = "success";
                        $strBouton = "Génial !";
                        $strMessage = "Vous avez été préinscrit pour le départ du :<br><b>".str_replace("_"," ",$arrDat[1])."</b><br>";
                        $strMessage .= "au concours de <b>". $strLieu . "</b>";
                        $strMessage .= "<br> qui se déroulera le :<br><b>".utf8_encode(strftime("%A %d %B %Y",$dateConcours))."</b><br>";
                        $strMessage .= "Vous avez choisi de tirer en <b>" . $strTypeEpr . "</b>.";
                        $arrQueue = array('title' => $strTitre,'html' => $strMessage,'type' => $strType,'confirmButtonText' => $strBouton, 'confirmButtonColor' => '#28A744');
                    } else {
                        $strTitre = "Préinscription Annulée !";
                        $strType = "error";
                        $strBouton = "Désolé...";
                        $strMessage = "Vous n'avez pas été préinscrit pour le départ du :<br><b>".str_replace("_"," ",$arrDat[1])."</b><br>";
                        $strMessage .= "au concours de <b>". $strLieu . "</b>";
                        $strMessage .= "<br> qui se déroulera le :<br><b>".strftime("%A %d %B %Y",$dateConcours)."</b><br>";
                        $strMessage .= "car vous êtes déjà préinscrit<br>à un concours pour ce créneau !";
                        $arrQueue = array('title' => $strTitre,'html' => $strMessage,'type' => $strType,'confirmButtonText' => $strBouton, 'confirmButtonColor' => '#dc3545');
                    }
                    array_push($arrModal, $arrQueue);
                }
            }
        } else {
            $strTitre = "Préinscription Annulée !";
            $strType = "error";
            $strBouton = "Désolé...";
            $strMessage = "Le concours de <b>" . $strLieu . "</b></br>";
            $strMessage .= "qui se déroulera le :<br><b>".strftime("%A %d %B %Y",$dateConcours)."</b>";
            $strMessage .= "<br> est clos, vous ne pouvez plus vous y inscrire !";
            $arrQueue = array('title' => $strTitre,'html' => $strMessage,'type' => $strType,'confirmButtonText' => $strBouton, 'confirmButtonColor' => '#dc3545');
            array_push($arrModal, $arrQueue);
        }

        echo json_encode($arrModal, JSON_FORCE_OBJECT);
    }
    
?>
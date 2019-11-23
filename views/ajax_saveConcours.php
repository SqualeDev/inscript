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
    
    // array (size=16)
    // 'inputAdresse' => string 'Salle LEMIRE Centre sportif Petite BOUVERIE AllÃ©e Pierre de Coubertin 76000 ROUEN FRANCE' (length=89)
    // 'inputGPSLat' => string '49.45877' (length=8)
    // 'inputGPSLong' => string '1.1479' (length=6)
    // 'radioEtat_Ouvert' => string 'on' (length=2)
    // 'inputTelephoneClub' => string '0232830441' (length=10)
    // 'inputMailClub' => string 'daniel.brugeron@orange.fr' (length=25)
    // 'inputAdresseClub' => string '' (length=0)
    // 'inputSiteWeb' => string 'http://arc-rouen-club.jimdo.com/' (length=32)
    // 'inputTarif1Adulte' => string '7.00' (length=4)
    // 'inputTarif2Adulte' => string '6.00' (length=4)
    // 'inputTarif1Jeune' => string '5.00' (length=4)
    // 'inputTarif2Jeune' => string '4.00' (length=4)
    // '933783G_48934_Dimanche_Matin' => string 'on' (length=2)
    // '933783G_48934_Dimanche_AprÃ¨s-Midi' => string 'on' (length=2)
    // 'modalID' => string '48934' (length=5)
    // 'userID' => string '933783g' (length=7)

    // echo "<h3> PHP List All Post - Session Variables</h3>";
    // var_dump($_POST);
    // echo "<h3> FIN List </h3>";
    
    // $strPathFileMandat = '/docs/mandats/';
    // $nameFileMandat = basename($_POST['fileInput']);

    // echo $_POST['fileInput'] . " => " . $strPathFileMandat . $nameFileMandat;
    // move_uploaded_file($nameFileMandat, $strPathFileMandat . $nameFileMandat);

    if (isset($_POST['modalID'])) {
        $intConcours_ID = intval($_POST['modalID']);
        $strLicencie_ID = strtoupper($_POST['userID']);
        $arrModal = array();

        // Traitement des données du concours

        $objConcours = new ClassConcours($intConcours_ID);

        // move_uploaded_file($nameFileMandat, $strPathFileMandat . $nameFileMandat);

        $strGPS = $_POST['inputGPSLat'] . "/" . $_POST['inputGPSLong'];
        
        if (isset($_POST['inputNameFileMandat']) && !empty($_POST['inputNameFileMandat'])) {
            $strFileMandat = $_POST['inputNameFileMandat'];
            $strFilePathMandat = $_POST['inputPathFileMandat'] . $_POST['inputNameFileMandat'];
        } else {
            $strFileMandat = 'null';
        }

        // $retourUploadFile = $objConcours->uploadMandat($strFilePathMandat, $strFileMandat);

        $strCategories = '';
        foreach ($_POST as $key => $value) {
            if (strpos($key, 'chkCategAdulte_') === 0) {
                $strCategories .= $value . ',';
            }
        }
        $strCategories = trim($strCategories, ',');

        // var_dump($_FILES);

        // var_dump($_POST);

        $retourConcours = $objConcours->updateConcours(
                                                        $intConcours_ID,
                                                        $_POST['inputAdresse'],
                                                        $strGPS,
                                                        $strFileMandat,
                                                        $_POST['inputTelephoneClub'],
                                                        $_POST['inputMailClub'],
                                                        $_POST['inputAdresseClub'],
                                                        $_POST['inputSiteWeb'],
                                                        $strCategories,
                                                        $_POST['inputTarif1Adulte'],
                                                        $_POST['inputTarif2Adulte'],
                                                        $_POST['inputTarif1Jeune'],
                                                        $_POST['inputTarif2Jeune']
                                                    );

        // var_export($retourConcours);

        // if($retourConcours && $retourUploadFile == 0) {
        if($retourConcours) {
            $strTitre = "Enregistrement Réussi !";
            $strType = "success";
            $strBouton = "Génial !";
            $strMessage = "Nickel !";
            // $strMessage = "Vous avez été préinscrit pour le départ du :<br><b>".str_replace("_"," ",$arrDat[1])."</b><br>";
            // $strMessage .= "au concours de <b>". $strLieu . "</b>";
            // $strMessage .= "<br> qui se déroulera le :<br><b>".strftime("%A %d %B %Y",$dateConcours)."</b><br>";
            // $strMessage .= "Vous avez choisi de tirer sur <b>" . $strBlason . "</b>.";
            $arrQueue = array('title' => $strTitre,'html' => $strMessage,'type' => $strType,'confirmButtonText' => $strBouton, 'confirmButtonColor' => '#28A744');
        } else {
            $strTitre = "Enregistrement Annulé !";
            $strType = "error";
            $strBouton = "Désolé...";
            $strMessage = "Problème !";
            // $strMessage = "Vous n'avez pas été préinscrit pour le départ du :<br><b>".str_replace("_"," ",$arrDat[1])."</b><br>";
            // $strMessage .= "au concours de <b>". $strLieu . "</b>";
            // $strMessage .= "<br> qui se déroulera le :<br><b>".strftime("%A %d %B %Y",$dateConcours)."</b><br>";
            // $strMessage .= "car vous êtes déjà préinscrit<br>à un concours pour ce créneau !";
            $arrQueue = array('title' => $strTitre,'html' => $strMessage,'type' => $strType,'confirmButtonText' => $strBouton, 'confirmButtonColor' => '#dc3545');
        }
        array_push($arrModal, $arrQueue);

        // Traitement des validation des départs

        // Permettre la sauvegarde des n° de chèque même sans case coché
        $flagRetourOK = true;
        $strMessageKO = '';

        foreach ($_POST as $key => $value) {
            // Parcours uniquement des départs
            if (strpos($key, 'hidDepart_') === 0) {
                // Récupérer l'index du départ
                $strNumHtmlDepart = explode('_', $key)[1];
                // Si une validation est faite ou un n° de chèque est renseigné
                if ($_POST['numChequeDepart_' . $strNumHtmlDepart] != '' || isset($_POST['chkDepart_' . $strNumHtmlDepart])) {
                    // Récupérer les clés du départs (n° de licence, n° du concours, départ)
                    $arrDepart = explode('_', $value, 3);
                    // Récupérer l'état de validation
                    if (isset($_POST['chkDepart_' . $strNumHtmlDepart])) {
                        $boolValideDepart = true;
                    } else {
                        $boolValideDepart = false;
                    }
                    // Mettre à jour les données (Validation du départ et n° du chèque) du départs
                    $retourDepart = $objConcours->updateValideDepart($arrDepart[1], $arrDepart[0], $arrDepart[2], $_POST['numChequeDepart_' . $strNumHtmlDepart], $boolValideDepart);
                    if (!$retourDepart) {
                        $flagRetourOK = false;
                        $strMessageKO .= "Problème sur Concours n° : " . $arrDepart[1] . "<br>" . "Licencié : " . $arrDepart[0] . "<br>" . "Départ : " . $arrDepart[2] . "<br>";
                    }
                    // Mettre à jour l'état du concours à Validé si tout les départs de ce concours sont validés
                    if ($objConcours->isConcoursHasDepart($arrDepart[1]) && !$objConcours->isConcoursHasDepartNonValide($arrDepart[1])) {
                        $objConcours->updateValideConcours($arrDepart[1]);
                    }
                }

            }
        }

        if($flagRetourOK) {
            $strTitre = "Validation du départ réussie !";
            $strType = "success";
            $strBouton = "Génial !";
            $strMessage = "Nickel !";
            // $strMessage = "Vous avez été préinscrit pour le départ du :<br><b>".str_replace("_"," ",$arrDat[1])."</b><br>";
            // $strMessage .= "au concours de <b>". $strLieu . "</b>";
            // $strMessage .= "<br> qui se déroulera le :<br><b>".strftime("%A %d %B %Y",$dateConcours)."</b><br>";
            // $strMessage .= "Vous avez choisi de tirer sur <b>" . $strBlason . "</b>.";
            $arrQueue = array('title' => $strTitre,'html' => $strMessage,'type' => $strType,'confirmButtonText' => $strBouton, 'confirmButtonColor' => '#28A744');
        } else {
            $strTitre = "Validation du départ annulée !";
            $strType = "error";
            $strBouton = "Désolé...";
            // $strMessage = "Problème !";
            $strMessage = $strMessageKO;
            // $strMessage = "Vous n'avez pas été préinscrit pour le départ du :<br><b>".str_replace("_"," ",$arrDat[1])."</b><br>";
            // $strMessage .= "au concours de <b>". $strLieu . "</b>";
            // $strMessage .= "<br> qui se déroulera le :<br><b>".strftime("%A %d %B %Y",$dateConcours)."</b><br>";
            // $strMessage .= "car vous êtes déjà préinscrit<br>à un concours pour ce créneau !";
            $arrQueue = array('title' => $strTitre,'html' => $strMessage,'type' => $strType,'confirmButtonText' => $strBouton, 'confirmButtonColor' => '#dc3545');
        }

        array_push($arrModal, $arrQueue);

        echo json_encode($arrModal, JSON_FORCE_OBJECT);
    }

?>
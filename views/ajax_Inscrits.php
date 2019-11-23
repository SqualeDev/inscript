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
    
    $Concoursid = $_POST['concoursid'];

    require_once PATH_MODELS . 'model_class_concours.php';
    $objInscrits  = new ClassConcours();
    $arrListe = $objInscrits->listeInscritsAUnConcours($Concoursid);

    $strNom = "";
    $listInscrits = "";
    $strDepart = "";

    $intNbLigne = sizeof($arrListe);
    
    for ($i = 0; $i < $intNbLigne; $i++) {
        $strDep = str_replace("_", " ",$arrListe[$i]['r_epr_usr_depart']);
        if ($i == 0) {
            $strNom = $arrListe[$i]['NomInscrit'];
            $strDepart .= " (" .$strDep;
            $listInscrits .= $strNom . $strDepart;
        } elseif ($i == ($intNbLigne - 1)) {
            if ($strNom == $arrListe[$i]['NomInscrit']) {
                $strDepart .= ", " .$strDep.")";
                $listInscrits .= $strDepart;
            } else {
                $listInscrits .= ")<br>";
                $strNom = $arrListe[$i]['NomInscrit'];
                $strDepart .= " (" .$strDep.")";
                $listInscrits .= $strNom . $strDepart;
            }
        } else {
            if ($strNom == $arrListe[$i]['NomInscrit']) {
                $strDepart .= ", " .$strDep;
                $listInscrits .= $strDepart;
            } else {
                $listInscrits .= ")<br>";
                $strNom = $arrListe[$i]['NomInscrit'];
                $strDepart .= " (" .$strDep;
                $listInscrits .= $strNom . $strDepart;
            }
        }
        $strDepart = "";
    }
    
    echo $listInscrits;
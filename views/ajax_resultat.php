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
    //require_once $_SERVER['DOCUMENT_ROOT'] . '/licencies/constantes.php';
    require_once PATH_MODELS . 'model_class_resultat.php';
    
    $json = json_decode($_POST['myJSON'], true);

    $strLicencie = $json['licencie'];
    $arrAnnee = $json['annee'];
    $strDiscipline = $json['discipline'];
    $strDistance = $json['distance'];

    $objResultat  = new classResultat($strLicencie);

    // $arrHTML = $objResultat->creationTable($arrAnnee, $arrDiscipline);
    // $arrHTML = $objResultat->creationTable_v2($arrAnnee, $arrDiscipline); 
    $arrHTML = $objResultat->creationTable_v3($arrAnnee, $strDiscipline, $strDistance); 
    // var_dump($arrHTML);
    echo json_encode($arrHTML);
        

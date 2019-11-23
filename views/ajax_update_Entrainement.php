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
    
    require_once PATH_MODELS . 'model_class_entrainement.php';
    $objEntrainement  = new ClassEntrainement();

    $result = $objEntrainement->updateEntrainement(  $_POST['IDEntrainement'],
                                                     $_POST['dateEnt'],
                                                     $_POST['userID'], 
                                                     $_POST['NbFlechePaille'], 
                                                     $_POST['NbFlecheVisuel'], 
                                                     $_POST['NbFlecheCible'], 
                                                     $_POST['NbFlecheTirCompte'], 
                                                     $_POST['NbFlecheCompet'], 
                                                     $_POST['NvForme'],
                                                     $_POST['IDConsigne'], 
                                                     $_POST['Observation']);
    $arrAjax[] = array(
        "retour" => $result
    );
    
    echo json_encode($arrAjax);
?>
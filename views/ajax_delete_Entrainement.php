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

    $result = $objEntrainement->removeEntrainement( $_POST['IDEntrainement'] );

    $arrAjax[] = array(
        "retour" => $result
    );
    
    echo json_encode($arrAjax);
?>
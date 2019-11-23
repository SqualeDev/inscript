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
    
    $method = $_GET['method'];

    require_once PATH_MODELS . 'model_class_entrainement.php';
    $objConsigne  = new ClassEntrainement();

    switch ($method) {
        case 'GET':
            $userID = $_GET['userID'];
            // Consigne Courante
            $consigneCourante = $objConsigne->recupererConsigneCourante($userID);
            
            $arrAjax[] = array(
                "consigneCourante" => $consigneCourante
            );
            break;

        case 'SET':
            $consigneNouvelle = $_GET['consigneNew'];
            $userID = $_GET['userID'];
            // Consigne Courante
            $consigneNouvelleResult = $objConsigne->creerConsigneNouvelle($consigneNouvelle, $userID);
            $consigneNouvelleID = $objConsigne->recupererConsigneCourante($userID);
            
            $arrAjax[] = array(
                "retourInsertConsigne" => $consigneNouvelleResult,
                "retourConsigneID" => $consigneNouvelleID
            );
            break;
        
        case 'VALID':
            // Valide la consigne courante
            $consigneID = $_GET['ConsigneID'];
            $consigneNouvelle = $_GET['consigneNew'];
            $userID = $_GET['userID'];
            if ($consigneID != 0) {
                $consigneCouranteResult = $objConsigne->valideConsigneCourante($consigneID);
            } else {
                $consigneCouranteResult = true;
            }
            $consigneNouvelleResult = $objConsigne->creerConsigneNouvelle($consigneNouvelle, $userID);

            $arrAjax[] = array(
                "retourValidConsigne" => $consigneCouranteResult,
                "retourInsertConsigne" => $consigneNouvelleResult
            );
            break;

        case 'CANCEL':
            // Annule la consigne courante
            $consigneNouvelle = $_GET['consigneNew'];

            $consigneCourante = $objConsigne->recupererConsigneCourante($userID);
            
            $arrAjax[] = array(
                "retour" => $consigneCourante
            );
            break;

        default:
            # code...
            break;
    }
    
    echo json_encode($arrAjax);
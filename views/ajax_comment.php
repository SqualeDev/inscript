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
    require_once PATH_MODELS . 'model_class_resultat.php';
    
    if (isset($_GET['cmd'])) {
        switch ($_GET['cmd']) {
            case 'afficher':
                $strDate = $_GET['resultatDate'];
                $strNom = $_GET['resultatNom'];
                $strLicencie = $_GET['resultatLicencie'];
                $strPoints = $_GET['resultatPoints'];

                // Récupérer le commentaire du concours choisi
                $objResultat  = new classResultat($strLicencie);

                $strComment = $objResultat->recupererCommentaire($strDate, $strNom, $strLicencie, $strPoints);

                echo html_entity_decode($strComment['Commentaires']);
                break;

            case 'sauvegarder':
                $strDate = $_GET['resultatDate'];
                $strNom = $_GET['resultatNom'];
                $strLicencie = $_GET['resultatLicencie'];
                $strPoints = $_GET['resultatPoints'];
                $strCommentaires = htmlentities($_GET['resultatComment'], ENT_QUOTES);

                // Sauvegarde du commentaire via requete SQL
                $objResultat  = new classResultat($strLicencie);

                $strResult = $objResultat->miseajourCommentaire($strDate, $strNom, $strLicencie, $strPoints, $strCommentaires);

                // Retour de l'état de la sauvegarde
                echo $strResult;
                
                break;
            
            default:
                # code...
                break;
        }
    }


    
        

<?php
    // CONTROLLER CONCOURS
    class ControlConcours {
        // ACTION INSCRIRE
        public function Inscrire() {
            // display_debug("ControlResultat : ",	"function Inscrire", 1);
            require_once PATH_MODELS . 'model_class_concours.php';
            require_once PATH_VIEWS . 'view_form_inscription.php';
        }

        // ACTION DESINSCRIRE
        public function Desinscrire() {
            // display_debug("ControlResultat : ",	"function Desinscrire", 1);
            require_once PATH_MODELS . 'model_class_concours.php';
            require_once PATH_VIEWS . 'view_desinscript.php';
            if ( isset($_POST['bt-form-inscript']) && isset($_POST['tbl-desinscript']) ) {
                // echo "<h3> PHP List All Session Variables</h3>";
                // var_dump($_SESSION);
                if ($_POST['bt-form-inscript'] == 'desinscript') {
                    $arrModal = array();
                    // echo "<h3> PHP List All Post Variables</h3>";
                    // var_dump($_POST);
                    foreach ($_POST['tbl-desinscript'] as $key => $value) {
                        $objDesinscript = new ClassConcours();

                        $arrResult = explode('|',$value);
                        if (!$objDesinscript->isConcoursClos($arrResult[0])) {
                            $retour = $objDesinscript->supprimerInscription($arrResult);
                            $retour = true;
                        } else {
                            $retour = false;
                        }
                        if($retour) {
                            $strTitre = "Suppression Réussi !";
                            $strType = "success";
                            $strBouton = "Génial !";
                            $strMessage = "";
                            // $strMessage .= " au concours de ".$_POST['inputLieu'];
                            // $strMessage .= "</b><br> qui se déroulera le :<br><b>".$dateConcours."</b>";
                            $strQueue = "{title:'".$strTitre."',type:'".$strType."',html:'".$strMessage."',confirmButtonText:'".$strBouton."', confirmButtonColor: '#28A744', closeOnConfirm: false}";
                        } else {
                            $strTitre = "Suppression Annulée !";
                            $strType = "error";
                            $strBouton = "Désolé...";
                            $strMessage = "";
                            $strMessage .= "le concours est déjà dans un état interdisant<br>la désincription !<br>Contacter la personne chargée des inscriptions.";
                            // $strMessage .= "</b><br> car vous êtes déjà préinscrit<br>à un concours pour ce créneau !";
                            $strQueue = "{title:'".$strTitre."',type:'".$strType."',html:'".$strMessage."',confirmButtonText:'".$strBouton."', confirmButtonColor: '#dc3545', closeOnConfirm: false}";
                        }
                        array_push($arrModal, $strQueue);
                    }
                }

                $strSwal = "swal.mixin({
                }).queue([".
                    join(", ", $arrModal).
                "]).then(
                    function(isConfirm){
                        // window.location.href='index.php?controller=concours&action=Desinscrire';
                    })";

                echo "<script>";
                echo $strSwal;
                echo "</script>";
            }
        }

        
    }
?>


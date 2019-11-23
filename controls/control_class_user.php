<?php
    // CONTROLLER USER
    class ControlUser {

        // ACTION
        public function Afficher() {
            require_once PATH_MODELS . 'model_class_user.php';
            // charger le formulaire de connexion
            require_once PATH_VIEWS . 'view_user.php';
            if (isset($_POST['bt-form-user'])) {
                if ($_POST['bt-form-user'] == 'sauvegarder') {
                    $strLicencie = $_POST['inputLicencie'];
                    $strFirstname = $_POST['inputFirstName'];
                    $strLastname = $_POST['inputName'];
                    $strMail = $_POST['inputMail'];
                    if (isset($_POST['inputNotifMailInscript'])) {$boolNotifMailInscript = $_POST['inputNotifMailInscript'];} else {$boolNotifMailInscript = 'off';}
                    if (isset($_POST['inputNotifMailVersion'])) {$boolNotifMailVersion = $_POST['inputNotifMailVersion'];} else {$boolNotifMailVersion = 'off';}
                    $strMobile = $_POST['inputMobile'];
                    if (isset($_POST['inputNotifMobile'])) {$boolNotifMobile = $_POST['inputNotifMobile'];} else {$boolNotifMobile = 'off';}
                    $intNbArtParLig = $_POST['inputNbArticleParLigne'];
                    
                    $objUser = new ClassUser($strLicencie);

                    $retour = $objUser->updateUser($strLicencie, $strFirstname, $strLastname, $strMail, $boolNotifMailInscript, $boolNotifMailVersion, $strMobile, $boolNotifMobile, $intNbArtParLig);

                    if($retour) {
                        $strTitre = "Mise à jour Réussie !";
                        $strType = "success";
                        $strBouton = "Génial !";
                        $strMessage = "Votre profil a bien été mis à jour";
                        $strQueue = "{title:'".$strTitre."',type:'".$strType."',html:'".$strMessage."',confirmButtonText:'".$strBouton."', confirmButtonColor: '#28A744'}";
                    } else {
                        $strTitre = "Mise à jour Annulée !";
                        $strType = "error";
                        $strBouton = "Désolé...";
                        $strMessage = "Votre profil n\'a pas pu être mis à jour, contactez votre administarteur pour y remédier !";
                        $strQueue = "{title:'".$strTitre."',type:'".$strType."',html:'".$strMessage."',confirmButtonText:'".$strBouton."', confirmButtonColor: '#dc3545'}";
                    }
                }
                
                $strSwal = "swal.mixin({
                }).queue([".
                    $strQueue.
                "]).then(
                    function(isConfirm){
                        window.location.href='index.php?controller=user&action=Afficher';
                    })";

                echo "<script>";
                echo $strSwal;
                echo "</script>";
            }
        }

        public function Securite() {
            require_once PATH_MODELS . 'model_class_user.php';
            require_once PATH_VIEWS . 'view_password.php';

            if (isset($_POST['bt-form-pass'])) {
                if ($_POST['bt-form-pass'] == 'sauvegarder') {
                    $arrModal = array();
                    if ($_POST['inputOldPass'] != $_POST['inputNewPass1']) {
                        // Vérifier que le user($_SESSION['licencie'], $_POST['inputOldPass']) est autorisé
                        $objUser  = new ClassUser($_SESSION['licencie'],md5($_POST['inputOldPass']));

                        if ($objUser->boolAutorise) {
                            // Vérifier que $_POST['inputNewPass1'] = $_POST['inputNewPass1'] et que $_POST['inputOldPass'] != $_POST['inputNewPass1']
                            // Fait dans le formulaire HTML5
                            // Faire un UPDATE du mot de base dans la base avec $_POST['inputNewPass1']
                            $retour = $objUser->updatePassUser($_SESSION['licencie'], md5($_POST['inputNewPass1']));
                            if ($retour) {
                                $_SESSION['pwd'] = md5($_POST['inputOldPass']);
                                $strTitre = "Modification Réussie !";
                                $strType = "success";
                                $strBouton = "Génial !";
                                $strMessage = "Votre mot de passe à bien été modifié, n\'oubliez pas de vous reconnecter pour qu\'il soit actif !";
                                $strQueue = "{title:'".$strTitre."',type:'".$strType."',html:'".$strMessage."',confirmButtonText:'".$strBouton."', confirmButtonColor: '#28A744', closeOnConfirm: false}";
                            } else {
                                // afficher un modal avec comme message "Problème de modification en base de données"
                                $strTitre = "Modification Annulée !";
                                $strType = "error";
                                $strBouton = "Désolé...";
                                $strMessage = "Vous n\'avez pas pû modifier votre mot de passe car une erreur liée à la base de données est survenue !";
                                $strQueue = "{title:'".$strTitre."',type:'".$strType."',html:'".$strMessage."',confirmButtonText:'".$strBouton."', confirmButtonColor: '#dc3545', closeOnConfirm: false}";
                            }
                        } else {
                            // afficher un modal avec comme message "Le mot de passe utilisé n'est pas votre mot de passe actuel"
                            $strTitre = "Modification Annulée !";
                            $strType = "error";
                            $strBouton = "Désolé...";
                            $strMessage = "Vous n\'avez pas pû modifier votre mot de passe car l\'ancien mot de passe renseigné ne correspond pas au mot de passe enregistré !";
                            $strQueue = "{title:'".$strTitre."',type:'".$strType."',html:'".$strMessage."',confirmButtonText:'".$strBouton."', confirmButtonColor: '#dc3545', closeOnConfirm: false}";
                        }
                    } else {
                        // afficher un modal avec comme message "Le nouveau mot de passe utilisé ne peut pas être identique àvotre mot de passe actuel"
                        $strTitre = "Modification Annulée !";
                        $strType = "error";
                        $strBouton = "Désolé...";
                        $strMessage = "Le nouveau mot de passe utilisé ne peut pas être identique à votre ancien mot de passe !";
                        $strQueue = "{title:'".$strTitre."',type:'".$strType."',html:'".$strMessage."',confirmButtonText:'".$strBouton."', confirmButtonColor: '#dc3545', closeOnConfirm: false}";
                    }
                    
                    array_push($arrModal, $strQueue);
                }
                $strSwal = "swal.mixin({
                }).queue([".
                    join(", ", $arrModal).
                "]).then(
                    function(isConfirm){
                        // window.location.href='index.php?controller=user&action=Securite';
                    })";

                echo "<script>";
                echo $strSwal;
                echo "</script>";
            }
        }
    }
?>
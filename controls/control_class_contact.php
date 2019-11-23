<?php
    // CONTROLLER CONTACT
    class ControlContact {
        // ACTION ENVOI MAIL
        public function Envoyer() {
            if(isset($_POST['bt-form-contact'])) {
                if ($_POST['bt-form-contact'] == 'envoyer') {

                    // VALIDATION DU CONTENU DES CHAMPS
                    if(!isset($_POST['first_name']) ||
                        !isset($_POST['email']) ||        
                        !isset($_POST['comments'])) {
                        died('We are sorry, but there appears to be a problem with the form you submitted.');       
                    }

                    $strExpe = $_POST['first_name'];
                    $strMail = $_POST['email'];
                    $strMesg = $_POST['comments'];

                    $error_message = "";

                    $email_exp = '/^[A-Za-z0-9._%-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,4}$/';
                    if(!preg_match($email_exp,$strMail)) {
                        $error_message .= 'L\'adresse mail entrée ne parait pas valide.<br />';
                    }
                    
                    $string_exp = "/^[A-Za-z\s.'-]+$/";
                    if(!preg_match($string_exp,$strExpe)) {
                        $error_message .= 'Le nom entré ne parait pas valide.<br />';
                    }
                
                    if(strlen($strMesg) < 2) {
                        $error_message .= 'Le contenu du message ne parait pas valide.<br />';
                    }
                    // SI UN PROBLEME A ETE DETECTE DANS LA VALIDATION DES CHAMPS
                    if(strlen($error_message) > 0) {
                        died($error_message);
                    }

                    require_once PATH_MODELS . 'models_browser.php';
                    $browser = new Browser();
            
                    $strMesg .= "<br>" . $browser->getBrowser() . " version " . $browser->getVersion();
                    $strMesg .= "<br>" . $browser->getPlatform();

                    require_once PATH_ROOT. 'phpmailer/PHPMailerAutoload.php';

                    $mail = new PHPMailer;
    
                    // $mail->SMTPDebug = 3;                                          // Enable verbose debug output
    
                    $mail->isSMTP();                                               // Set mailer to use SMTP
                    $mail->Host = 'send.one.com';                                  // Specify main and backup SMTP servers
                    $mail->SMTPAuth = true;                                        // Enable SMTP authentication
                    $mail->Username = 'webadmin@csg-tiralarc.fr';                  // SMTP username
                    $mail->Password = '5AWAgcyt0PaEpGm6eYn3';                      // SMTP password
                    $mail->SMTPSecure = 'tls';                                     // Enable TLS encryption, `ssl` also accepted
                    $mail->Port = 587;                                             // TCP port to connect to
    
                    // Expéditeur
                    $mail->setFrom('webadmin@csg-tiralarc.fr', $strExpe);
                    // Destinataire
                    $mail->addAddress('webadmin@csg-tiralarc.fr', 'Admin Web');    // Add a recipient
                    // $mail->addAddress('ellen@example.com');                     // Name is optional
                    // Adresse de retour
                    $mail->addReplyTo($strMail, $strExpe);
                    // $mail->addCC('cc@example.com');
                    // $mail->addBCC('bcc@example.com');
    
                    // Modifier l'encodage du mail
                    $mail->CharSet = "utf-8";
                    // Ajouter une pièce jointe
                    // $mail->addAttachment('/var/tmp/file.tar.gz');               // Add attachments
                    // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');          // Optional name
                    $mail->isHTML(true);                                           // Set email format to HTML
    
                    // Objet
                    $mail->Subject = 'Demande de contact CSG tir à l\'arc';
                    $mail->Body    = $strMesg;
                    
                    if(!$mail->send()) {
                        $strTitre = "Mail non envoyé !";
                        $strType = "error";
                        $strBouton = "Désolé...";
                        $strMessage = "Votre mail n\'a pas pu être envoyé à l\'administrateur du site, contactez via un autre moyen pour l\'en informer !";
                        $strQueue = "{title:'".$strTitre."',type:'".$strType."',html:'".$strMessage."',confirmButtonText:'".$strBouton."', confirmButtonColor: '#dc3545'}";
                    } else {
                        $strTitre = "Mail enovyé !";
                        $strType = "success";
                        $strBouton = "Génial !";
                        $strMessage = "Votre commentaire a bien été envoyé à l\'administrateur du site";
                        $strQueue = "{title:'".$strTitre."',type:'".$strType."',html:'".$strMessage."',confirmButtonText:'".$strBouton."', confirmButtonColor: '#28A744'}";
                    }

                    $strSwal = "swal.mixin({
                    }).queue([".
                        $strQueue.
                    "]).then(
                        function(isConfirm){
                            window.location.href='index.php?controller=home&action=homeDefault';
                        })";
    
                    echo "<script>";
                    echo $strSwal;
                    echo "</script>";
                }
            }
        }
    }
?>
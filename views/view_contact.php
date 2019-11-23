<?php
    require_once PATH_MODELS . 'model_class_user.php';

    if (isset($_SESSION['licencie'])) {
        $objUser  = new ClassUser($_SESSION['licencie'],$_SESSION['pwd']);
        $strNameContact = $_SESSION['firstname'] . " " . $_SESSION['lastname'];
        $strMailContact = $objUser->strMail;
    } else {
        $strNameContact = "Anonyme";
        $strMailContact = "mail_anonyme";
    }

?>

<!-- Modal -->
<div id="contactModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="contactModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Contactez l'administrateur</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body" style='text-align:justify;'>
                <!-- <div class="containter"> -->
                    <!-- <div class="row"> -->
                        <form method="post" action="index.php?controller=contact&action=Envoyer">
                            <!-- <div class="col-xs-8"> -->
                                <div class="form-group">
                                    <label for="InputName" class="control-label">Votre nom :</label>
                                    <!-- <div class="col-lg-8"> -->
                                        <input type="text" class="form-control" name="first_name" id="first_name" value="<?php echo $strNameContact?>">
                                    <!-- </div> -->
                                </div>
                                <div class="form-group">
                                    <label for="InputEmail" class="control-label">Votre Adresse mail :</label>
                                    <!-- <div class="col-lg-8"> -->
                                        <input type="email" class="form-control" id="email" name="email" value="<?php echo $strMailContact?>">
                                    <!-- </div> -->
                                </div>
                                <div class="form-group">
                                    <label for="InputMessage" class="control-label">Votre message :</label>
                                    <!-- <div class="col-lg-8"> -->
                                        <textarea name="comments" id="comments" class="form-control" rows="5" required></textarea>
                                    <!-- </div> -->
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-success" href='?controller=contact&action=Envoyer' name="bt-form-contact" value="envoyer">Envoyer</button>
                                    <button type="button" class="btn btn-danger" data-dismiss="modal">Annuler</button>
                                    <!-- <input type="submit" name="submit" id="submit" value="Submit" class="btn btn-info pull-right"> -->
                                </div>
                            <!-- </div> -->
                        </form>
                    <!-- </div> -->

                <!-- </div> -->
            </div><!-- End of Modal body -->
        </div><!-- End of Modal content -->
    </div><!-- End of Modal dialog -->
</div><!-- End of Modal -->
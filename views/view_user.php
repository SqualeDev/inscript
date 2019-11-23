<?php
    $objUser  = new ClassUser($_SESSION['licencie'],$_SESSION['pwd']);
?>
<!-- Formulaire de modification du profil d'un utilisateur -->
<form id="user" method='POST'>
    <fieldset class="form-group m-3">
        <legend>Mon Profil</legend>
        <div class="row">
            <div class="col-md-10">
                <div class="row">
                    <div class="form-group col-md-4">
                        <label for="inputLicencie">n° Licencié</label>
                        <input type="text" class="form-control" name="inputLicencie" readonly value="<?php echo strtoupper($_SESSION['licencie']); ?>">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="inputFirstname">Prénom</label>
                        <input type="text" class="form-control" name="inputFirstName" value="<?php echo $objUser->strFirstname; ?>">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="inputName">Nom</label>
                        <input type="text" class="form-control" name="inputName" value="<?php echo $objUser->strLastname; ?>">
                    </div>
                </div>
                <fieldset>
                    <legend>Affichage des articles</legend>
                    <p class="text-justify">
                        Ces options permettent de modifier l'affichage des articles sur la page d'Accueil.
                    </p>
                    <div class="form-group row">
                        <label for="inputNbArticleParLigne" class="col-sm-3 col-form-label">Nombre d'articles par ligne (1 à 9) : </label>
                        <div class="col-sm-1">
                            <input type="text" class="form-control" id="inputNbArticleParLigne" name="inputNbArticleParLigne" value="<?php echo $objUser->intNbArtParLigne; ?>">
                        </div>
                    </div>
                </fieldset>
                <fieldset>
                    <legend>Notification de l'avancement des inscriptions</legend>
                    <p class="text-justify">
                        Si vous activez les notifications, vous recevrez par le moyen choisi (Email, SMS, ou les deux) une alerte vous indiquant le changement d'état de votre inscription au concours.<br>
                        Si la notification par mail est activé, vous pourrez recevoir par ce biais l'information des nouvelles fonctionnalités ajoutées au site Web.
                    </p>
                    <div class="row">
                        <div class="form-group col-md-5 offset-md-1">
                            <div class="row">
                                <span class="switch switch-sm">
                                    <input type="checkbox" class="switch" id="switch-mail-inscript" name="inputNotifMailInscript" <?php echo $objUser->boolNotifMailInscript; ?>>
                                    <label for="switch-mail-inscript">Notification par mail des inscriptions</label>
                                </span>
                                <span class="switch switch-sm">
                                    <input type="checkbox" class="switch" id="switch-mail-version" name="inputNotifMailVersion" <?php echo $objUser->boolNotifMailVersion; ?>>
                                    <label for="switch-mail-version">Notification par mail des versions</label>
                                </span>
                            </div>
                            <div class="row">
                                <input type="email" class="form-control" name="inputMail" aria-describedby="emailHelp" value="<?php echo $objUser->strMail; ?>">
                                <small name="emailHelp" class="form-text text-muted">Renseignez votre adresse mail.</small>
                            </div>
                        </div>
                        <div class="form-group col-md-5 offset-md-1">
                            <div class="row">
                                <span class="switch switch-sm">
                                    <input type="checkbox" class="switch" id="switch-mobile" name="inputNotifMobile" <?php echo $objUser->boolNotifMobile; ?>>
                                    <label for="switch-mobile">Notification par SMS</label>
                                </span>
                            </div>
                            <div class="row">
                                <input type="text" class="form-control" name="inputMobile" aria-describedby="mobileHelp" value="<?php echo $objUser->strMobile; ?>">
                                <small name="mobileHelp" class="form-text text-muted">Renseignez votre n° de téléphone mobile.</small>
                            </div>
                        </div>
                    </div>
                </fieldset>
                
            </div>
            <div class="col-md-2 d-none" >
                <div class="row">
                    <label for="inputCat">Catégorie</label>
                    <select multiple class="form-control" name="inputCat">
                        <option>BFCL</option>
                        <option>BHCL</option>
                        <option>CF</option>
                        <option>CFCL</option>
                        <option>CH</option>
                        <option>CHCL</option>
                        <option>JF</option>
                        <option>JH</option>
                        <option>MFCL</option>
                        <option>MH</option>
                        <option>MHCL</option>
                        <option>PH</option>
                        <option>PHCL</option>
                        <option>SF</option>
                        <option>SFCL</option>
                        <option>SH</option>
                        <option selected>SHCL</option>
                        <option>SHCO</option>
                        <option>SVF</option>
                        <option>SVH</option>
                        <option>SVHCO</option>
                        <option>VF</option>
                        <option>VFCO</option>
                        <option>VH</option>
                        <option>VHCL</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-4">
                <button type="submit" class="btn btn-primary" name="bt-form-user" value="sauvegarder">Sauvegarder ...</button></p>
            </div>
        </div>
    </fieldset>
</form>

<div class="container-fluid d-none">
<?php

$strMsg = "Afficher :"."<br>";
$strMsg .= "<input type='checkbox' id='checkbox_1' checked><label for='checkbox_1'>le n° du licencié (pas modifiable)</label><br>";
$strMsg .= "<input type='checkbox' id='checkbox_2' checked><label for='checkbox_2'>le Nom et Prénom du licencié (modifiable)</label><br>";
$strMsg .= "<input type='checkbox' id='checkbox_3'><label for='checkbox_3'>la catégorie du licencié (modifiable)</label><br>";
$strMsg .= "<input type='checkbox' id='checkbox_4' checked><label for='checkbox_4'>l'adresse mail du licencié (modifiable) expliquer le besoin (pour envoi par mail de la validation des étapes de l'inscription)</label><br>";
$strMsg .= "<input type='checkbox' id='checkbox_5' checked><label for='checkbox_5'>un bouton radio Oui/Non lié à la notification par mail (pour envoi par mail des étapes de validation de l'inscription)</label><br>";
$strMsg .= "<input type='checkbox' id='checkbox_6' checked><label for='checkbox_6'>le n° de téléphone mobile du licencié (modifiable) expliquer le besoin (pour envoi par SMS des étapes de validation de l'inscription)</label><br>";
$strMsg .= "<input type='checkbox' id='checkbox_7' checked><label for='checkbox_7'>un bouton radio Oui/Non lié à la notification par SMS (pour envoi par SMS des étapes de validation de l'inscription)</label><br>";
$strMsg .= "<input type='checkbox' id='checkbox_8'><label for='checkbox_8'>l'avatar du licencié ?? (modifiable) prévoir de l'afficher dans la page d'accueil</label><br>";
$strMsg .= "<input type='checkbox' id='checkbox_9'><label for='checkbox_9'>les types de concours possible (Fita, Fédéral, Beursault, ...) pour préfiltrer le tableau des concours (modifiable)</label><br>";
$strMsg .= "<input type='checkbox' id='checkbox_10'><label for='checkbox_10'>le choix du blason (trispot ou blason) (modifiable)</label><br>";
$strMsg .= "<input type='checkbox' id='checkbox_11'><label for='checkbox_11'>Changer son mot de passe via un modal (ancien mot de passe, nouveau, confirmation)</label><br>";

$strMsg .= "Au clic du bouton \"Sauvegarder\", les informations seront updatées dans la base de données";

echo $strMsg;
?>

</div>

<script>
    $(document).ready(function () {
        $("form").bind("keypress", function(e) {
            if (e.keyCode == 13) {
                return false;
            }
        });
    });
</script>
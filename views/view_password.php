<?php
    //$objUser  = new ClassUser($_SESSION['licencie'],$_SESSION['pwd']);
?>
<!-- Formulaire de modification du mot de passe d'un utilisateur -->
<form id="pass" method='POST'>
    <fieldset class="form-group m-3">
        <legend>Changement de mot de passe</legend>
        <div class="row">
            <div class="form-group col-md-4 offset-md-4">
                <label for="inputOldPass">Ancien mot de passe</label>
                <input type="password" class="form-control" name="inputOldPass" required autofocus>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-4 offset-md-4">
                <label for="inputNewPass1">Nouveau mot de passe</label>
                <input type="password" id="inputNewPass1" class="form-control" name="inputNewPass1" pattern=".{6,}" title="Six caractères minimum" oninput="form.inputNewPass2.pattern = escapeRegExp(this.value)" required>
                <span toggle="#inputNewPass1" class="fa fa-fw fa-eye field-icon toggle-password"></span>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-4 offset-md-4">
                <label>Confirmation du nouveau mot de passe</label>
                <input type="password" id="inputNewPass2" class="form-control" name="inputNewPass2" pattern="" title="Les deux mots de passe doivent être identiques" required >
                <span toggle="#inputNewPass2" class="fa fa-fw fa-eye field-icon toggle-password"></span>
            </div>
        </div>
        <!-- bouton submit -->
        <div class="text-center col-md-4 offset-md-4">
            <input type='submit' name="bt-form-pass" value='sauvegarder' class='btn btn-primary'></button>
        </div>
    </fieldset>
</form>
<script>
    function escapeRegExp(str) {
      return str.replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, "\\$&");
    }

    

    $(document).ready(function () {
        $("form").bind("keypress", function(e) {
            if (e.keyCode == 13) {
                return false;
            }
        });
        $(".toggle-password").click(function() {
        //     $(this).toggleClass("fa-eye fa-eye-slash");
            var input = $($(this).attr("toggle"));
            if (input.attr("type") == "password") {
                input.attr("type", "text");
            } else {
                input.attr("type", "password");
            }
        });
    });
</script>
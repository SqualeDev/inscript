<?php 
$titre = "Formulaire d'authentification";

$legende = "Identifiez-vous avant de pouvoir accéder au site";

$lblLogin = "Votre n° de licencié :";
$holLogin = "n° licence";
$namLogin = "login";

$lblPass = "Votre mot de passe :";
$holPass = "mot de passe";
$namPass = "password";

$lblConn = "Connexion";

$lblAccueil = "Bonjour et bienvenue sur le site du CSG Tir à l'arc !";

$errorMessage = '';
?>

	<!-- formulaire de connexion utilisateur -->
	<form method="POST">
		<fieldset>
			<legend class="h4 text-center mb-4"><?php echo $legende?></legend>
			<!-- champ IPN -->
			<div class="form-group col-md-4 offset-md-4">
                <label for='<?php echo $namLogin ?>'><?php echo $lblLogin ?></label>
                <input type="text" name="login" class="form-control" placeholder="<?php echo $holLogin ?>" required>
            </div>
			<!-- champ pwd -->
			<div class="form-group col-md-4 offset-md-4">
                <label for='password'> Mot de passe </label>
                <input type="password" name="password" class="form-control" placeholder="<?php echo $holPass ?>" required>
            </div>
			<!-- bouton submit -->
			<div class="text-center col-md-4 offset-md-4">
                <input type='submit' name="LogIn" value='Connexion' class='btn btn-primary'></button>
            </div>
		</fieldset>
	</form>

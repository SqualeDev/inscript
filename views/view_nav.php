<?php
    require_once PATH_MODELS . 'model_class_user.php';
    if (!isset($_SESSION['licencie'])) {
        $objUser  = new ClassUser('inconnu','inconnu');
    } else {
        $objUser  = new ClassUser($_SESSION['licencie'],$_SESSION['pwd']);
    }
?>

    <!-- Navbar -->
        <nav class="navbar navbar-expand-lg navbar-light bg-white">
            <a class="navbar-brand" href="index.php?controller=home&action=homeDefault"><img alt="CSG" height="40" src="pictures/logoclub.svg"></a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?controller=home&action=homeDefault">Accueil</a>
                    </li>
<?php 
    // require_once 'view_nav_club.php';

    if($objUser->boolAutorise) {
        require_once 'view_nav_licencie.php';
    }

    if($objUser->boolCoach) {
        require_once 'view_nav_coach.php';
    }

    // if($objUser->boolRedac) {
    //     require_once 'view_nav_article.php';
    // }

    if($objUser->boolAdmin) {
        require_once 'view_nav_inscription.php';
    }
?>
                </ul>
            </div>
<?php
    if(!$objUser->boolAutorise) {
        echo "\t\t\t<a class='btn justify-content-end btn-primary' href='?controller=log&action=LogIn' role='button'>   Connexion  </a>".PHP_EOL;
    } else {
        echo "\t\t\t<a class='btn justify-content-end btn-primary' href='?controller=log&action=LogOut' role='button'>  Déconnexion </a>".PHP_EOL;
    }
?>
        </nav>
        <br/>

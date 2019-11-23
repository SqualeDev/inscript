<?php
    // SI SESSION OUVERTE
    // $boolAdministrateur = true;
    if ($debug) {
        echo "<h3> PHP List All Session Variables</h3>";
        var_dump($_SESSION);
        echo "<h3> PHP List All Post Variables</h3>";
        var_dump($_POST);
        echo "<h3> PHP List All Server Variables</h3>";
        var_dump($_SERVER);
    }

    if (!empty($_SESSION['licencie'])) {
        display_debug("Route : ",	"login rempli", 0);
        display_debug("Route SESSION login : ",	$_SESSION['licencie'], 0);
        $objLog = new ClassUser($_SESSION['licencie'], $_SESSION['pwd']);
        // SI UTILISATEUR AUTORISE
        if($objLog->boolAutorise) {
            // SI UNE ACTION EST DEMANDEE
            if(!empty($_GET['controller']) && !empty($_GET['action'])) {
                $controller = $_GET['controller'];
                $action     = $_GET['action'];
                if($_GET['controller'] == 'article') {
                    if(!$objLog->boolRedac) {
                        // L'utilisateur n'est pas administrateur
                        $controller = 'home';
                        $action     = 'homeDefault';
                    }
                }
                if($_GET['controller'] == 'resultat' && $_GET['action'] == 'Coach') {
                    if(!($objLog->boolCoach)) {
                        // L'utilisateur n'est pas administrateur
                        $controller = 'home';
                        $action     = 'homeDefault';
                    }
                }
                if($_GET['controller'] == 'inscription') {
                    if(!$objLog->boolAdmin) {
                        // L'utilisateur n'est pas administrateur
                        $controller = 'home';
                        $action     = 'homeDefault';
                    }
                }
                // call($controller, $action);
            } else {
                // aucune action demandée
                $controller = 'home';
                $action     = 'homeDefault';
                // call($controller, $action);
            }
        } else {
            // UTILISATEUR NON AUTORISE OU INEXISTANT
            $controller = 'log';
            $action     = 'logIn';
            // call($controller, $action);
        }
    } else {
        // DEMANDE DE CONNEXION
        $controller = 'log';
        $action     = 'logIn';
    }
    call($controller, $action);

    // CALL CONTROLLER ACTION
    function call($controller, $action) {
        // REQUIRE FICHIER control_class_CONTROLLER DEMANDE.php
        require_once PATH_CONTROLS .'control_class_' .  $controller . '.php';

        // CREATE NEW CONTROLLER INSTANCE
        switch ($controller) {
            case 'concours':
                $controller = new ControlConcours();
            break;
            case 'contact':
                $controller = new ControlContact();
            break;
            case 'entrainement':
                $controller = new ControlEntrainement();
            break;
            case 'home':
                $controller = new ControlHome();
            break;
            case 'inscription':
                $controller = new ControlInscription();
            break;
            case 'log':
                $controller = new ControlLog();
            break;
            case 'resultat':
                $controller = new ControlResultat();
            break;
            case 'user':
                $controller = new ControlUser();
            break;
        }

        // CALL
        $controller->{ $action }();
    }
?>
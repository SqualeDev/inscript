<?php
    // CONTROLLER LOG
    class ControlLog {

        // ACTION LOGIN
        public function LogIn() {
            // charger le formulaire de connexion
            require_once PATH_VIEWS . 'view_LogIn.php';
            // require_once PATH_ROOT . 'constantes.php';
            global $debug;
            
            // SI IPN & PASSWORD REMPLIS
            if(isset($_POST['LogIn'])) {
                display_debug("ControlLog POST[LogIn] : ",	$_POST['LogIn'], 0);
                display_debug("ControlLog POST[login] : ",	$_POST['login'], 0);
                display_debug("ControlLog POST[password] : ",	$_POST['password'], 0);
                // nouvelle instance de ClassLog
                $log = new ClassLog($_POST['login'], md5($_POST['password']));
                //si l'utilisateur est autorisé
                if ($log->boolAutorise) {
                    $_SESSION['licencie']   = strtolower($_POST['login']);
                    $_SESSION['pwd']        = md5($_POST['password']);
                    $_SESSION['lastname']   = $log->strLastname;
                    $_SESSION['firstname']  = $log->strFirstname;
                    $_SESSION['categorie']  = $log->strCateg;
                    // refresh vers l'accueil
                    echo '<meta http-equiv="refresh" content="0;URL=index.php">';
                } else {
                    // utilisateur inexistant ou non autorisé
                    echo 'Utilisateur introuvable ou non autorisé. <br/>';
                }
                echo '<meta http-equiv="refresh" content="0;URL=index.php">';
            }
        }

        // DECONNEXION
        public function LogOut() {
            // DESTRUCTION DE LA SESSION
            unset($_SESSION['licencie']);
            session_unset();
            session_destroy();
            session_write_close();
            // setcookie(session_name(),'',0,'/');
            // session_regenerate_id(true);
            // REFRESH VERS ACCUEIL
            echo '<meta http-equiv="refresh" content="0;URL=index.php">';
        }
    }
?>

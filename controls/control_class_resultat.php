<?php
    // CONTROLLER RESULTATS
    class ControlResultat {
        // ACTION AFFICHER RESULTAT
        public function Afficher() {
            // display_debug("ControlResultat : ",	"function Afficher", 1);
            require_once PATH_VIEWS . 'view_resultat.html';
        }

        public function Coach() {
            // display_debug("ControlResultat : ",	"function Afficher", 1);
            require_once PATH_VIEWS . 'view_resultat_coach_v2.html';
        }
    }
?>
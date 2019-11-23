<?php
    // CONTROLLER ENTRAINEMENTS
    class ControlEntrainement {
        // ACTION AFFICHER ENTRAINEMENT
        public function Afficher() {
            // display_debug("ControlEntrainement : ",	"function Afficher", 1);
            require_once PATH_VIEWS . 'view_entrainements.php';
        }

        public function Coach() {
            // display_debug("ControlEntrainement : ",	"function Afficher", 1);
            require_once PATH_VIEWS . 'view_entrainements_coach.php';
        }
    }
?>
<?php
    // modèle entrainement : intéractions relatives aux entrainement
    class ClassEntrainement {
        
        // Tableau des entrainement
        // public $arrConcours = array();

        function __construct() {
            require_once PATH_MODELS . 'model_class_db.php';
        }

        // VERIFIER SI LE ENTRAINEMENT EXISTE
        public function existEntrainement($_intEntrainement) {
            // NOUVELLE ISNTANCE DE MODEL DB
            $dbEntrainement = new ClassDb();
            // REQUETE
            $strQuery = "SELECT ent_id FROM csg_evt_tir WHERE ent_id = ?;";
            // EXECUTE LA REQUETE AVEC LES PARAMETRES VIA MODEL DB
            // si l'ID de l'entrainement existe dans la table
            if ($dbEntrainement->existData($strQuery, array($_intEntrainement))) {
                // RETURN true
                return true;
            } else {
                // RETURN false
                return false;
            }
            $dbEntrainement = null;
        }

        // RECUPERER LES INFORMATIONS D'UN ENTRAINEMENT
        public function recupererInfoEntrainement($_intEntrainementID) {
            // NOUVELLE INSTANCE DE MODEL DB
            $dbEntrainement = new ClassDb();
            // REQUETE
            $strQuery = "SELECT
                            *
                        FROM
                            csg_evt_tir
                        WHERE
                            ent_id = " . $_intEntrainementID . ";";
            
            // EXECUTER LA REQUETE AVEC LES PARAMETRES VIA MODEL DB
            $result = $dbEntrainement->getAllData($strQuery);
            // RECUPERER LE RESULTAT
            return $result;
            $dbEntrainement = null;
        }
        
        // RECUPERER LA CONSIGNE COURANTE
        public function recupererConsigneCourante($_strlicencie) {
            // NOUVELLE INSTANCE DE MODEL DB
            $dbEntrainement = new ClassDb();
            // REQUETE
            $strQuery = "SELECT
                            con_id, con_description
                        FROM
                            csg_consignes
                        WHERE
                            usr_login = '" . $_strlicencie . "'
                        AND
                            con_etat = 'En Cours'
                        ;";
            
            // EXECUTER LA REQUETE AVEC LES PARAMETRES VIA MODEL DB
            $result = $dbEntrainement->getAllData($strQuery);
            // RECUPERER LE RESULTAT
            return $result[0];
            $dbEntrainement = null;
        }
        
        // RECUPERER LA LISTE DES ENTRAINEMENTS D'UN ACRHER
        private function getAllEntrainement($_strlicencie, $_strAll) {
            if ($_strAll != 'all') {
                $_strWhereDate = ' WHERE E.ent_date > NOW() - INTERVAL 1 WEEK ';
            } else {
                $_strWhereDate = '';
            }
            // NOUVELLE INSTANCE DE MODEL DB
            $dbEntrainement = new ClassDb();
            // REQUETE
            $strQuery = "SELECT 
                            E.ent_id,
                            E.ent_date,
                            E.ent_nb_fleches_paille,
                            E.ent_nb_fleches_visuel,
                            E.ent_nb_fleches_cible,
                            E.ent_nb_fleches_tir_compte,
                            E.ent_nb_fleches_competition,
                            C.con_description,
                            E.ent_obs_archer,
                            E.ent_niv_forme,
                            E.ent_nb_fleches_paille + E.ent_nb_fleches_visuel + E.ent_nb_fleches_cible + E.ent_nb_fleches_tir_compte + E.ent_nb_fleches_competition AS Total
                        FROM 
                            csg_evt_tir E,
                            csg_consignes C
                        WHERE
                            E.con_id = C.con_id
                        AND
                            E.usr_login = '".$_strlicencie."'
                        ORDER BY
                            E.ent_date DESC
                        ;";
            // echo $strQuery;
            // EXECUTER LA REQUETE AVEC LES PARAMETRES VIA MODEL DB
            $result = $dbEntrainement->getAllData($strQuery);
            // RECUPERER LE RESULTAT
            if ($dbEntrainement->queryNbRows > 0) {
                return $result;
            } else {
                return 0;
            }
            
            $dbEntrainement = null;
        }

        // VALIDE LA CONSIGNE COURANTE
        public function valideConsigneCourante($_consigneID) {
            // La date du jour est ajoutée (dans le champ con_dat_fin) à la consigne En cours et l'état (con_etat) est passé à Validée
            // Un formulaire doit permettre l'entrée de la nouvelle consigne
            // La nouvelle consigne est enregistrée dans la base avec comme :
            //    - date de début (con_date_deb), maintenant
            //    - description (con_description), le contenu du champ du formulaire
            //    - etat (con_etat), En cours
            //    - l'archer (usr_login), le n° de licence de l'archer selectionné via la liste déroulante
            // NOUVELLE INSTANCE DE MODEL DB
            $dbEntrainement = new ClassDb();
            // REQUETE
            $strQuery = "UPDATE
                            csg_consignes
                        SET
                            con_date_fin = NOW(),
                            con_etat = 'Validée'
                        WHERE
                            con_id = ".$_consigneID."
                        ;";
            // echo $strQuery;
            // EXECUTER LA REQUETE AVEC LES PARAMETRES VIA MODEL DB
            $result = $dbEntrainement->updateRow($strQuery);
            // RECUPERER LE RESULTAT
            return $dbEntrainement->queryOK;
            $dbEntrainement = null;
        }
        
        // CREER LA NOUVELLE CONSIGNE COURANTE
        public function creerConsigneNouvelle($_consigneNouvelle, $_userID) {
            // La date du jour est ajoutée (dans le champ con_dat_fin) à la consigne En cours et l'état (con_etat) est passé à Validée
            // Un formulaire doit permettre l'entrée de la nouvelle consigne
            // La nouvelle consigne est enregistrée dans la base avec comme :
            //    - date de début (con_date_deb), maintenant
            //    - description (con_description), le contenu du champ du formulaire
            //    - etat (con_etat), En cours
            //    - l'archer (usr_login), le n° de licence de l'archer selectionné via la liste déroulante
            // NOUVELLE INSTANCE DE MODEL DB
            $dbEntrainement = new ClassDb();
            // REQUETE
            $strQuery = "INSERT INTO csg_consignes 
                        (
                            con_etat,
                            con_date_deb,
                            con_description,
                            usr_login
                        )
                    VALUES
                        (
                            'En Cours', 
                            NOW(), 
                            '". addslashes($_consigneNouvelle) . "',
                            '". $_userID . "'
                        )
                    ;";
            // echo $strQuery;
            // EXECUTER LA REQUETE AVEC LES PARAMETRES VIA MODEL DB
            $result = $dbEntrainement->insertRow($strQuery);
            // RECUPERER LE RESULTAT
            return $dbEntrainement->queryOK;
            $dbEntrainement = null;
        }

        // RECUPERER LA LISTE DES COMPETITEURS
        private function recupererListeCompetiteurs() {
            require_once PATH_MODELS . 'model_class_db.php';

            $dbCompetiteur = new ClassDb();
            $strQuery = "SELECT DISTINCT 
                            R.rsl_usr_id AS id,
                            U.usr_firstname AS firstname,
                            U.usr_lastname AS lastname
                        FROM 
                            csg_resultats AS R,
                            csg_users AS U
                        WHERE
                            R.rsl_usr_id = U.usr_login
                        ORDER BY
                            U.usr_firstname ASC,
                            U.usr_lastname ASC
                        ;";
            // echo $strQuery;
            // EXECUTER LA REQUETE AVEC LES PARAMETRES VIA MODEL DB
            $result = $dbCompetiteur->getAllData($strQuery);
            // RECUPERER LE RESULTAT
            return $result;
            $dbCompetiteur = null;
        }

        // AJOUTE UN NOUVELLE ENTRAINEMENT
        public function insertEntrainement(
                                            $_strDate, 
                                            $_strUserID, 
                                            $_intNbFlechePaille, 
                                            $_intNbFlecheVisuel, 
                                            $_intNbFlecheCible, 
                                            $_intNbFlecheTirCompte, 
                                            $_intNbFlecheCompet,
                                            $_intNvForme,
                                            $_intConsigneID,
                                            $_strObs
                                            ) {

            if ($_strDate != 'null') {
                $dateResultat = DateTime::createFromFormat('d/m/Y',$_strDate);
                $strdateResultat = date_format($dateResultat, 'Y-m-d');
            } else {
                $strdateResultat = 'null';
            }

            // NOUVELLE INSTANCE DE MODEL DB
            $dbEntrainement = new ClassDb();
            // REQUETE
            $strQuery = "INSERT INTO csg_evt_tir 
                            (
                                ent_date,
                                usr_login,
                                ent_nb_fleches_paille,
                                ent_nb_fleches_visuel,
                                ent_nb_fleches_cible,
                                ent_nb_fleches_tir_compte,
                                ent_nb_fleches_competition,
                                ent_niv_forme,
                                con_id,
                                ent_obs_archer
                            )
                        VALUES
                            (
                                '".$strdateResultat."', 
                                '".$_strUserID."', 
                                ".$_intNbFlechePaille.", 
                                ".$_intNbFlecheVisuel.", 
                                ".$_intNbFlecheCible.", 
                                ".$_intNbFlecheTirCompte.", 
                                ".$_intNbFlecheCompet.", 
                                ".$_intNvForme.", 
                                ".$_intConsigneID.", 
                                '". addslashes($_strObs) . "'
                            )
                        ;";
            // echo $strQuery;
            // EXECUTER LA REQUETE AVEC LES PARAMETRES VIA MODEL DB
            $result = $dbEntrainement->insertRow($strQuery);
            // RECUPERER LE RESULTAT
            return $dbEntrainement->queryOK;
            $dbEntrainement = null;
        }

        // MET A JOUR UN ENTRAINEMENT
        public function updateEntrainement(
                                            $_intEntrainementID,
                                            $_strDate, 
                                            $_strUserID, 
                                            $_intNbFlechePaille, 
                                            $_intNbFlecheVisuel, 
                                            $_intNbFlecheCible, 
                                            $_intNbFlecheTirCompte, 
                                            $_intNbFlecheCompet,
                                            $_intNvForme,
                                            $_intConsigneID,
                                            $_strObs) {

            if ($_strDate != 'null') {
                $dateResultat = DateTime::createFromFormat('d/m/Y',$_strDate);
                $strdateResultat = date_format($dateResultat, 'Y-m-d');
            } else {
                $strdateResultat = 'null';
            }

            // NOUVELLE INSTANCE DE MODEL DB
            $dbEntrainement = new ClassDb();
            
            $strQuery = "UPDATE csg_evt_tir
                            SET 
                                ent_date = '".$strdateResultat."',
                                usr_login = '".$_strUserID."',
                                ent_nb_fleches_paille = ".$_intNbFlechePaille.",
                                ent_nb_fleches_visuel = ".$_intNbFlecheVisuel.",
                                ent_nb_fleches_cible = ".$_intNbFlecheCible.",
                                ent_nb_fleches_tir_compte = ".$_intNbFlecheTirCompte.",
                                ent_nb_fleches_competition = ".$_intNbFlecheCompet.",
                                ent_niv_forme = ".$_intNvForme.",
                                con_id = ".$_intConsigneID.",
                                ent_obs_archer = '".$_strObs."'
                            WHERE 
                                ent_id = ".$_intEntrainementID."
                            ;";
            // echo $strQuery;
            
            $result = $dbEntrainement->updateRow($strQuery);
            return $dbEntrainement->queryOK;
            
            $dbEntrainement = null;
        }

        // SUPPRIME UN ENTRAINEMENT
        public function removeEntrainement($_intEntrainementID) {
            $arrData = [$_intEntrainementID];
            // NOUVELLE INSTANCE DE MODEL DB
            $dbEntrainement = new ClassDb();
            // REQUETE
            $strQuery = "DELETE FROM csg_evt_tir 
                         WHERE ent_id = ? ;
                        ";
            //     $strQuery = "DELETE FROM csg_rel_epreuve_user 
            //     WHERE epr_id = '".$arrData[0]."' AND usr_login = '".$arrData[1]."' AND r_epr_usr_depart = '".$arrData[2]."';
            //    ";
            // echo $strQuery;

            // EXECUTER LA REQUETE AVEC LES PARAMETRES VIA MODEL DB
            $result = $dbEntrainement->deleteRow($strQuery, $arrData);
            // RECUPERER LE RESULTAT
            // echo $result;
            return $dbEntrainement->queryOK;
            $dbEntrainement = null;
        }

        // AFFICHER LA LISTE DES COMPETITEURS
        public function afficherListeCompetiteurs() {
            // <option selected>Choisir l'archer ...</option>
            // <option value="1">One</option>
            // <option value="2">Two</option>
            // <option value="3">Three</option>


            $arrCompetiteurs = $this->recupererListeCompetiteurs();

            $strHTML = '';
            $strHTML .= "<option selected>Choisir l'archer ...</option>";

            foreach ($arrCompetiteurs as $key => $arrCompetiteur) {
                $strHTML .= '<option value="'.$arrCompetiteur['id'].'">'.$arrCompetiteur['firstname'].' '.$arrCompetiteur['lastname'].'</option>';
            }

            return $strHTML;
        }

        // AFFICHER LA LISTE DES ENTRAINEMENTS
        public function afficherListeEntrainement($_strlicencie, $_strAll) {
            //require_once PATH_MODELS . 'model_class_concours.php';
            // $arrResult = array();
            // $result = new ClassConcours();
            $arrEntrainement = $this->getAllEntrainement($_strlicencie, $_strAll);

            // $arrResult = (array) $result;
            // $arrConcours = (array) $arrResult['arrConcours'];
            // display_debug("arrConcours ", $arrConcours, 1);
            $htmlMsg = '';

            if ($arrEntrainement != 0) {
                foreach ($arrEntrainement as $key => $row) {
                
                    // $htmlInscrit = "<td class='text-center'></td>";
    
                    $htmlMsg .= "<tr>";
                    $htmlMsg .= "<td class='text-center' style='display: none;'>" . $row['ent_id'] . "</td>";
                    $htmlMsg .= "<td class='afficheInscript text-center'>" . date_format(date_create($row['ent_date']), 'd/m/Y') . "</td>";
                    
                    // Mettre la consigne en vert lorsque ce n'est plus la consigne courante
                    $htmlMsg .= "<td >" . $row['con_description'] . "</td>";
                    $htmlMsg .= "<td class='text-center'>" . $row['ent_nb_fleches_paille'] . "</td>";
                    $htmlMsg .= "<td class='text-center'>" . $row['ent_nb_fleches_visuel'] . "</td>";
                    $htmlMsg .= "<td class='text-center'>" . $row['ent_nb_fleches_cible'] . "</td>";
                    $htmlMsg .= "<td class='text-center'>" . $row['ent_nb_fleches_tir_compte'] . "</td>";
                    $htmlMsg .= "<td class='text-center'>" . $row['ent_nb_fleches_competition'] . "</td>";
                    $htmlMsg .= "<td class='text-center'>" . $row['Total'] . "</td>";
                    $htmlMsg .= "<td >" . $row['ent_obs_archer'] . "</td>";
                    $htmlMsg .= "<td class='text-center'>" . $row['ent_niv_forme'] . "</td>";
                    $htmlMsg .= "</tr>";
                }
            }
            
            return $htmlMsg;
        }

        // AFFICHER LA CONSIGNE COURANTE
        public function afficherConsigneCourante($_strlicencie) {
            if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/licencies')) {
                // Config finale
                // require_once $_SERVER['DOCUMENT_ROOT'] . '/licencies/config_local.php';
                require_once $_SERVER['DOCUMENT_ROOT'] . '/licencies/constantes.php';
            } else {
                // Config Debug
                // require_once $_SERVER['DOCUMENT_ROOT'] . '/config_local.php';
                require_once $_SERVER['DOCUMENT_ROOT'] . '/constantes.php';
            }
            global $lblConsigneNull;

            require_once PATH_MODELS . 'model_class_user.php';
            $objUser  = new ClassUser($_SESSION['licencie'],$_SESSION['pwd']);

            $arrConsigne = $this->recupererConsigneCourante($_strlicencie);

            
            $htmlMsg = '';
            $htmlMsg .= '<div class="d-flex flex-row justify-content-center">';
            $htmlMsg .= '<div class="p-3 bg-info text-white font-weight-bold">';
            $htmlMsg .= '<div id ="divID" style="display: none;">';
            $htmlMsg .= $arrConsigne['con_id'];;
            $htmlMsg .= '</div>';
            $htmlMsg .= '<div id="divConsigne">';
            if ($arrConsigne['con_description'] == '') {
                $htmlMsg .= $lblConsigneNull;
            } else {
                $htmlMsg .= $arrConsigne['con_description'];
            }
            $htmlMsg .= '</div>';
            
            $htmlMsg .= '</div>';
            // $htmlMsg .= '<!-- Afficher un bouton uniquement pour l\'entraineur pour valider la consigne et en entrer une nouvelle -->';
            // if($objUser->boolCoach) {
                // $htmlMsg .= '<div>';
                // $htmlMsg .= '<button type="button" class="btn btn-success" id="btValiderConsigne">Valider la consigne</button>';
                // $htmlMsg .= '</div>';
                // $htmlMsg .= '<div>';
                // $htmlMsg .= '<button type="button" class="btn btn-warning" id="btAnnulerConsigne">Annuler la consigne</button>';
                // $htmlMsg .= '</div>';
            // }
            $htmlMsg .= '</div>';

            return $htmlMsg;
        }
    }
?>
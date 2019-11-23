<?php
    // modèle concours : intéractions relatives aux concours
    class ClassConcours {
        
        // Tableau des concours
        // public $arrConcours = array();

        function __construct() {
            require_once PATH_MODELS . 'model_class_db.php';
            
            $this->arrExtAutorisees = array('.doc', '.pdf');
        }

        // VERIFIER SI LE CONCOURS EXISTE
        public function existConcours($_intConcours) {
            // NOUVELLE ISNTANCE DE MODEL DB
            $dbConcours = new ClassDb();
            // REQUETE
            $strQuery = "SELECT epr_id FROM csg_epreuves WHERE epr_id = ?;";
            // EXECUTE LA REQUETE AVEC LES PARAMETRES VIA MODEL DB
            // si l'IPN existe dans la table
            if ($dbConcours->existData($strQuery, array($_intConcours))) {
                // RETURN true
                return true;
            } else {
                // RETURN false
                return false;
            }
            $dbConcours = null;
        }

        // VERIFIER SI LE CONCOURS EST CLOS
        public function isConcoursClos($intIDConcours) {
            // NOUVELLE INSTANCE DE MODEL DB
            $dbConcours = new ClassDb();
            // REQUETE
            $strQuery = "SELECT 
                            (CASE
                                WHEN epr_etat = 'Ouvert' THEN 'False'
                                ELSE 'True'
                            END) AS etat
                        FROM csg_epreuves
                        WHERE epr_id = " . $intIDConcours . ";";
            // echo $strQuery;
            // EXECUTER LA REQUETE AVEC LES PARAMETRES VIA MODEL DB
            $result = $dbConcours->getAllData($strQuery);
            // RECUPERER LE RESULTAT
            return filter_var($result[0]['etat'], FILTER_VALIDATE_BOOLEAN);
            $dbConcours = null;
        }

        // RECUPERER LES INFORMATIONS D'UN CONCOURS
        public function recupererInfoConcours($_intConcoursID) {
            // NOUVELLE INSTANCE DE MODEL DB
            $dbConcours = new ClassDb();
            // REQUETE
            $strQuery = "SELECT
                            *
                        FROM
                            csg_epreuves
                        WHERE
                            epr_id = " . $_intConcoursID . ";";
            
            // EXECUTER LA REQUETE AVEC LES PARAMETRES VIA MODEL DB
            $result = $dbConcours->getAllData($strQuery);
            // RECUPERER LE RESULTAT
            return $result;
            $dbConcours = null;
        }
        
        // RECUPERER LE NOM DE L'UTILISATEUR DANS LA TABLE omsf_users
        // $_strIpn	STRING	IPN
        // RETURN 	STRING
        private function getAllConcours($_strlicencie, $_strAll) {
            if ($_strAll != 'all') {
                $_strWhereDate = ' WHERE E.epr_date_deb > NOW() - INTERVAL 1 WEEK ';
            } else {
                $_strWhereDate = '';
            }
            // NOUVELLE INSTANCE DE MODEL DB
            $dbConcours = new ClassDb();
            // REQUETE
            $strQuery = "SELECT 
                        E.epr_id,
                        E.epr_date_deb,
                        E.epr_date_fin,
                        (SELECT count(csg_rel_epreuve_user.epr_id) FROM csg_rel_epreuve_user WHERE csg_rel_epreuve_user.epr_id = E.epr_id) AS nb_inscrits,
                        (SELECT count(csg_rel_epreuve_user.usr_login) FROM csg_rel_epreuve_user WHERE csg_rel_epreuve_user.epr_id = E.epr_id AND csg_rel_epreuve_user.usr_login = '".$_strlicencie."') AS inscrit,
                        E.epr_lieu,
                        E.epr_orga,
                        E.epr_discipline,
                        E.epr_discipline_norme,
                        E.epr_caracteristique,
                        E.epr_etat,
                        E.epr_mandat,
                        E.epr_adresse
                    FROM csg_epreuves E
                    LEFT JOIN csg_rel_epreuve_user R ON E.epr_id = R.epr_id".$_strWhereDate." GROUP BY E.epr_id ORDER BY E.epr_date_deb ASC;";
            // echo $strQuery;
            // EXECUTER LA REQUETE AVEC LES PARAMETRES VIA MODEL DB
            $result = $dbConcours->getAllData($strQuery);
            // RECUPERER LE RESULTAT
            return $result;
            $dbConcours = null;
        }

        public function isConcoursHasDepart($_intConcoursID) {
            // NOUVELLE INSTANCE DE MODEL DB
            $dbConcours = new ClassDb();
            // REQUETE
            $strQuery = "SELECT 
                            COUNT(*) 
                        FROM 
                            `csg_rel_epreuve_user` 
                        WHERE 
                            epr_id = " . $_intConcoursID . 
                        ";";
            
            // EXECUTER LA REQUETE AVEC LES PARAMETRES VIA MODEL DB
            $result = $dbConcours->getAllData($strQuery);
            
            // RECUPERER LE RESULTAT
            if ($dbConcours->queryNbRows > 0) {
                return true;
            } else {
                return false;
            }
            $dbConcours = null;
        }

        public function isConcoursHasDepartNonValide($_intConcoursID) {
            // NOUVELLE INSTANCE DE MODEL DB
            $dbConcours = new ClassDb();
            // REQUETE
            $strQuery = "SELECT 
                            COUNT(*) 
                        FROM 
                            `csg_rel_epreuve_user` 
                        WHERE 
                            epr_id = " . $_intConcoursID . 
                        "AND 
                            r_epr_usr_valide IS NULL 
                        AND 
                            r_epr_usr_num_cheque IS NULL
                        ;";
            
            // EXECUTER LA REQUETE AVEC LES PARAMETRES VIA MODEL DB
            $result = $dbConcours->getAllData($strQuery);
            
            // RECUPERER LE RESULTAT
            if ($dbConcours->queryNbRows > 0) {
                return true;
            } else {
                return false;
            }
            $dbConcours = null;
        }
        
        // METTRE A JOUR UN CONCOURS
        public function updateConcours($_intCoucoursID, $_strAdresse=null, $_strGPS=null, $_strMandat=null, $_strTelephone=null, $_strMail=null, $_strAdressePostale=null, $_strWeb=null, $_strCategories=null, $_strTarifAdulte1=null, $_strTarifAdulte2=null, $_strTarifJeune1=null, $_strTarifJeune2=null) {
            // NOUVELLE INSTANCE DE MODEL DB
            $dbConcours = new ClassDb();
            // REQUETE
            $strQuery = "UPDATE csg_epreuves
                            SET 
                                epr_adresse = ?,
                                epr_gps = ?,
                                epr_mandat = ?,
                                epr_telephone = ?,
                                epr_mail = ?,
                                epr_adresse_inscript = ?,
                                epr_siteweb = ?,
                                epr_cat_adulte = ?,
                                epr_adulte_first_dep = ?,
                                epr_adulte_other_dep = ?,
                                epr_jeune_first_dep = ?,
                                epr_jeune_other_dep = ?,
                                epr_date_maj = NOW()
                            WHERE 
                                epr_id = ?
                            ;";
            // $strQuery = "UPDATE csg_epreuves
            //                 SET 
            //                     epr_adresse = '". $_strAdresse ."',
            //                     epr_gps = '". $_strGPS ."',
            //                     epr_mandat = '". $_strMandat ."',
            //                     epr_telephone = '". $_strTelephone ."',
            //                     epr_mail = '". $_strMail ."',
            //                     epr_adresse_inscript = '". $_strAdressePostale ."',
            //                     epr_siteweb = '". $_strWeb ."',
            //                     epr_adulte_first_dep = '". $_strTarifAdulte1 ."',
            //                     epr_adulte_other_dep = '". $_strTarifAdulte2 ."',
            //                     epr_jeune_first_dep = '". $_strTarifJeune1 ."',
            //                     epr_jeune_other_dep = '". $_strTarifJeune2 ."',
            //                     epr_date_maj = NOW()
            //                 WHERE 
            //                     epr_id = ". $_intCoucoursID ."
            //                 ;";
            // echo $strQuery;
            // EXECUTER LA REQUETE AVEC LES PARAMETRES VIA MODEL DB
            $result = $dbConcours->updateRow($strQuery, array($_strAdresse, $_strGPS, $_strMandat, $_strTelephone, $_strMail, $_strAdressePostale, $_strWeb, $_strCategories, $_strTarifAdulte1, $_strTarifAdulte2, $_strTarifJeune1, $_strTarifJeune2, $_intCoucoursID ));
            // $result = $dbConcours->updateRow($strQuery);
            // RECUPERER LE RESULTAT
            if ($dbConcours->queryNbRows > 0) {
                return true;
            } else {
                return false;
            }
            $dbConcours = null;
        }

        // RECUPERER LE NOM DE L'UTILISATEUR DANS LA TABLE omsf_users
        // $_strIpn	STRING	IPN
        // RETURN 	STRING
        public function listeInscritsAUnConcours($intIDConcours) {
            // NOUVELLE INSTANCE DE MODEL DB
            $dbConcours = new ClassDb();
            // REQUETE
            $strQuery = "SELECT
                        csg_rel_epreuve_user.epr_id,
                        csg_rel_epreuve_user.usr_login,
                        CONCAT( csg_users.usr_firstname, \" \", csg_users.usr_lastname ) AS NomInscrit,
                        csg_users.usr_cat,
                        r_epr_usr_depart,
                        r_epr_date,
                        r_epr_usr_valide,
                        r_epr_usr_blason,
                        r_epr_usr_type_epr,
                        r_epr_usr_num_cheque
                    FROM 
                        csg_rel_epreuve_user,
                        csg_users
                    WHERE 
                        csg_users.usr_login = csg_rel_epreuve_user.usr_login
                    AND
                        csg_rel_epreuve_user.epr_id = ".$intIDConcours."
                    ORDER BY NomInscrit ASC";
            
            // EXECUTER LA REQUETE AVEC LES PARAMETRES VIA MODEL DB
            $result = $dbConcours->getAllData($strQuery);
            if ($dbConcours->queryNbRows > 0) {
                return $result;
            } else {
                return false;
            }
            // RECUPERER LE RESULTAT
            // return $result;
            $dbConcours = null;
        }

        // RECUPERER LA LISTE DES CONCOURS
        public function afficherListeInscriptions($_strlicencie, $_strAll) {
            //require_once PATH_MODELS . 'model_class_concours.php';
            // $arrResult = array();
            // $result = new ClassConcours();
            $arrConcours = $this->getAllConcours($_strlicencie, $_strAll);

            // $arrResult = (array) $result;
            // $arrConcours = (array) $arrResult['arrConcours'];
            // display_debug("arrConcours ", $arrConcours, 1);
            $htmlMsg = '';

            foreach ($arrConcours as $key => $row) {
                switch ( $row['epr_etat']) {
                    case 'Clos':
                        $strSufRowColor = "danger";
                        break;
                    case 'Validé':
                        $strSufRowColor = "warning";
                        break;
                    case 'Préinscription':
                        $strSufRowColor = "info";
                        break;
                    case 'Ouvert':
                        $strSufRowColor = "success";
                        break;
                    default:
                        $strSufRowColor = "danger";
                        break;
                }
                $strRowColor = "table-" . $strSufRowColor;

                $strColor ="";
                if ($row['inscrit'] != 0) {$strColor = "table-warning";}
                if ($row['nb_inscrits'] > 0) {
                    $htmlInscrit = "<td data-html='true' id='tooltip_".$row['epr_id']."' class='text-center ". $strColor ."' data-toggle='tooltip'>" . $row['nb_inscrits'] . "</td>";
                } else {
                    $htmlInscrit = "<td class='text-center'></td>";
                }
                if ($row['epr_mandat'] != '' && $row['epr_mandat'] != 'NULL') {
                    $htmlMandat = "<a href='docs/mandats/".$row['epr_mandat']."' target='_blank'><button type='button' class='btn btn-sm btn-outline-primary waves-effect'>Mandat</button></a>";
                } else {
                    $htmlMandat = "";
                }
                if ($row['epr_adresse'] != '' && $row['epr_adresse'] != 'NULL') {
                    $htmlMaps = "href='http://maps.google.com/maps?q=" . $row['epr_adresse'];
                } else {
                    $htmlMaps = "href='http://maps.google.com/maps?q=" . $row['epr_lieu'];
                }
                $htmlMsg .= "<tr>";
                $htmlMsg .= "<td class='text-center' style='display: none;'>" . $row['epr_id'] . "</td>";
                $htmlMsg .= "<td class='afficheInscript text-center ".$strRowColor."'>" . date_format(date_create($row['epr_date_deb']), 'd/m/Y') . "</td>";
                $htmlMsg .= "<td class='afficheInscript text-center ".$strRowColor."'>" . date_format(date_create($row['epr_date_fin']), 'd/m/Y') . "</td>";
                $htmlMsg .= $htmlInscrit;
                $htmlMsg .= "<td class='text-center' >". $htmlMandat ."</td>";
                $htmlMsg .= "<td class='text-center' style='display: none;'>" . $row['epr_discipline_norme'] . "</td>";
                $htmlMsg .= "<td class='text-center'>" . $row['epr_discipline'] . "</td>";
                $htmlMsg .= "<td>" . ucfirst(mb_strtolower($row['epr_orga'])) . "</td>";
                $htmlMsg .= "<td><a target='_blank' rel='noopener noreferrer' " . $htmlMaps ."'>" . ucfirst(mb_strtolower($row['epr_lieu'])) . "</a></td>";
                $htmlMsg .= "<td>" . $row['epr_caracteristique'] . "</td>";
                $htmlMsg .= "</tr>";
            }

            return $htmlMsg;

        }

        // INSCRIRE LA NOUVELLE DEMANDE
        public function insertNewInscription($strLicencie, $intIDConcours, $strDepart, $strBlason, $strType, $strDate) {
            // NOUVELLE INSTANCE DE MODEL DB
            $dbConcours = new ClassDb();
            // REQUETE
            // $strQuery = "INSERT INTO csg_rel_epreuve_user (epr_id, usr_login, r_epr_usr_depart, r_epr_usr_blason, r_epr_usr_type_epr, r_epr_date) 
            //              VALUES (?,?,?,?,?,STR_TO_DATE(?,'%Y-%m-%d'));";
            $strQuery = "INSERT INTO csg_rel_epreuve_user (epr_id, usr_login, r_epr_usr_depart, r_epr_usr_blason, r_epr_usr_type_epr, r_epr_date) 
                         VALUES ($intIDConcours, '$strLicencie', '$strDepart', '$strBlason', '$strType', STR_TO_DATE('$strDate','%Y-%m-%d'));";
            // echo($strQuery);
            // EXECUTER LA REQUETE AVEC LES PARAMETRES VIA MODEL DB
            $result = $dbConcours->insertRow($strQuery, array($intIDConcours, $strLicencie, $strDepart, $strBlason, $strType, $strDate));
            return $dbConcours->queryOK;
            $dbConcours = null;
        }

        // RECUPERER LA LISTE DES CONCOURS AVEC INSCRIT
        public function creerListeConcoursLicencie() {
            // NOUVELLE INSTANCE DE MODEL DB
            $dbConcours = new ClassDb();
            // REQUETE
            $strQuery = "SELECT 
                            csg_epreuves.epr_id AS 'Concours ID',
                            csg_epreuves.epr_lieu AS 'Lieu',
                            csg_epreuves.epr_date_deb AS 'Date',
                            csg_users.usr_login AS 'n° licencié',
                            csg_users.usr_firstname AS 'Prénom',
                            csg_users.usr_lastname AS 'Nom', 
                            csg_epreuves.epr_discipline AS 'Discipline',
                            csg_rel_epreuve_user.r_epr_usr_depart AS 'Départ'
                        FROM
                            csg_rel_epreuve_user, csg_epreuves, csg_users 
                        WHERE
                            csg_rel_epreuve_user.epr_id = csg_epreuves.epr_id 
                        AND 
                            csg_rel_epreuve_user.usr_login = csg_users.usr_login
                        AND
                            csg_rel_epreuve_user.usr_login = '".$_SESSION['licencie']."'
                        ORDER BY 
                            csg_rel_epreuve_user.epr_id;";
            // echo $strQuery;
            // EXECUTER LA REQUETE AVEC LES PARAMETRES VIA MODEL DB
            $result = $dbConcours->getAllData($strQuery);
            // RECUPERER LE RESULTAT
            return $result;
            $dbConcours = null;
        }

        // RECUPERER LA LISTE DES CONCOURS AVEC INSCRIT
        public function estInscritAUnConcours() {
            // NOUVELLE INSTANCE DE MODEL DB
            $dbConcours = new ClassDb();
            // REQUETE
            $strQuery = "SELECT 
                            csg_epreuves.epr_id AS 'Concours ID',
                            csg_epreuves.epr_lieu AS 'Lieu',
                            csg_epreuves.epr_date_deb AS 'Date',
                            csg_users.usr_login AS 'n° licencié',
                            csg_users.usr_firstname AS 'Prénom',
                            csg_users.usr_lastname AS 'Nom', 
                            csg_epreuves.epr_discipline AS 'Discipline',
                            csg_rel_epreuve_user.r_epr_usr_depart AS 'Départ'
                        FROM
                            csg_rel_epreuve_user, csg_epreuves, csg_users 
                        WHERE
                            csg_rel_epreuve_user.epr_id = csg_epreuves.epr_id 
                        AND 
                            csg_rel_epreuve_user.usr_login = csg_users.usr_login
                        AND
                            csg_rel_epreuve_user.usr_login = '".$_SESSION['licencie']."'
                        ORDER BY 
                            csg_rel_epreuve_user.epr_id;";
            // echo $strQuery;
            // EXECUTER LA REQUETE AVEC LES PARAMETRES VIA MODEL DB
            $result = $dbConcours->getAllData($strQuery);
            // RECUPERER LE RESULTAT
            return $dbConcours->queryOK;
            $dbConcours = null;
        }

        // ACTIVE OU DESACTIVE LE BOUTON DESINSCRIRE
        public function afficheBoutonDesinscrire() {
            if ($this->estInscritAUnConcours()) {
                $strEtat = '';
            } else {
                $strEtat = 'disabled';
            }
            $strType = "submit";
            $strClass = "btn btn-primary float-right";
            $strId = "btnAgregarSelFabricante";
            $strName = "bt-form-inscript";
            $strValue = "desinscript";

            return '<button type="' . $strType . '" class="' . $strClass . '" id="' . $strId . '" name="' . $strName . '" value="' . $strValue . '" ' . $strEtat . '>Me désinscrire ...</button></p>';
        }

        //AFFICHER LA LISTE DES CONCOURS AVEC INSCRIT
        public function afficherlisteConcoursLicencie() {
            $result = $this->creerListeConcoursLicencie();
            
            if (!empty($result)) {
                foreach ($result as $key => $row) {
                    echo "<tr>";
                    echo "<td class='hidden'>" . $row['Concours ID'] . "|". $row['n° licencié'] ."|". $row['Départ'] . "</td>";
                    echo "<td class='text-center'></td>";
                    echo "<td>" . ucfirst(mb_strtolower($row['Lieu'])) . "</td>";
                    echo "<td class='text-center'>" . date_format(date_create($row['Date']), 'd/m/Y') . "</td>";
                    echo "<td class='text-center'>" . $row['n° licencié'] . "</td>";
                    echo "<td>" . $row['Prénom'] . "</td>";
                    echo "<td>" . $row['Nom'] . "</td>";
                    echo "<td class='text-center'>" . $row['Discipline'] . "</td>";
                    echo "<td>" . str_replace('_',' ',$row['Départ']) . "</td>";
                    echo "</tr>";
                }
            }
    
        }

        // SUPPRIMER L'INSCRIPTION AU CONCOURS
        public function supprimerInscription($arrData) {
            // NOUVELLE INSTANCE DE MODEL DB
            $dbConcours = new ClassDb();
            // REQUETE
            $strQuery = "DELETE FROM csg_rel_epreuve_user 
                         WHERE epr_id = ? AND usr_login = ? AND r_epr_usr_depart = ?;
                        ";
            //     $strQuery = "DELETE FROM csg_rel_epreuve_user 
            //     WHERE epr_id = '".$arrData[0]."' AND usr_login = '".$arrData[1]."' AND r_epr_usr_depart = '".$arrData[2]."';
            //    ";
            // echo $strQuery;

            // EXECUTER LA REQUETE AVEC LES PARAMETRES VIA MODEL DB
            $result = $dbConcours->deleteRow($strQuery, $arrData);
            // RECUPERER LE RESULTAT
            // echo $result;
            return $dbConcours->queryOK;
            $dbConcours = null;
        }

        // SUPPRIMER TOUTES LES INSCRIPTIONS D'UN CONCOURS POUR UN LICENCIE
        public function supprimerToutesInscriptions($strConcoursID, $strUserID) {
            $arrData = [$strConcoursID, $strUserID];
            // NOUVELLE INSTANCE DE MODEL DB
            $dbConcours = new ClassDb();
            // REQUETE
            $strQuery = "DELETE FROM csg_rel_epreuve_user 
                         WHERE epr_id = ? AND usr_login = ?;
                        ";
            // $strQuery ="DELETE FROM csg_rel_epreuve_user 
            //              WHERE epr_id = ". $strConcoursID ." AND usr_login = '". $strUserID ."';
            //              ";
            // echo $strQuery;

            // EXECUTER LA REQUETE AVEC LES PARAMETRES VIA MODEL DB
            $result = $dbConcours->deleteRow($strQuery, $arrData);
            // RECUPERER LE RESULTAT
            // echo $result;
            return $dbConcours->queryOK;
            $dbConcours = null;
        }

        // RECUPERER LA DATE DU DEBUT D'UN CONCOURS
        public function getDateDebutConcours($strConcoursID) {
            // NOUVELLE INSTANCE DE MODEL DB
            $dbConcours = new ClassDb();
            // REQUETE
            $strQuery = "SELECT
                            epr_date_deb
                        FROM
                            csg_epreuves
                        WHERE
                            epr_id = " . $strConcoursID . ";";
            // echo $strQuery;
            // EXECUTER LA REQUETE AVEC LES PARAMETRES VIA MODEL DB
            $result = $dbConcours->getAllData($strQuery);
            // RECUPERER LE RESULTAT
            return $result[0]['epr_date_deb'];
            $dbConcours = null;
        }

        // RECUPERER LA DATE DE FIN D'UN CONCOURS
        public function getDateFinConcours($strConcoursID) {
            // NOUVELLE INSTANCE DE MODEL DB
            $dbConcours = new ClassDb();
            // REQUETE
            $strQuery = "SELECT
                            epr_date_fin
                        FROM
                            csg_epreuves
                        WHERE
                            epr_id = " . $strConcoursID . ";";
            // echo $strQuery;
            // EXECUTER LA REQUETE AVEC LES PARAMETRES VIA MODEL DB
            $result = $dbConcours->getAllData($strQuery);
            // RECUPERER LE RESULTAT
            return $result[0]['epr_date_fin'];
            $dbConcours = null;
        }

        // RECUPERER TOUS LES DEPART D'UN UTILISATEUR POUR UN CONCOURS
        public function getAllDepartsUser($strConcoursID, $strUserID) {
            // NOUVELLE INSTANCE DE MODEL DB
            $dbConcours = new ClassDb();
            // REQUETE
            $strQuery = "SELECT
                            *
                        FROM
                            `csg_rel_epreuve_user`
                        WHERE
                            epr_id = " . $strConcoursID . "
                        AND
                            usr_login = '" . $strUserID . "';";
            // echo $strQuery;
            // EXECUTER LA REQUETE AVEC LES PARAMETRES VIA MODEL DB
            $result = $dbConcours->getAllData($strQuery);
            // RECUPERER LE RESULTAT
            return $result;
            $dbConcours = null;
        }

        // RECUPERER LA DATE DE FIN D'UN CONCOURS
        public function getLieuConcours($strConcoursID) {
            // NOUVELLE INSTANCE DE MODEL DB
            $dbConcours = new ClassDb();
            // REQUETE
            $strQuery = "SELECT
                            epr_lieu
                        FROM
                            csg_epreuves
                        WHERE
                            epr_id = " . $strConcoursID . ";";
            // echo $strQuery;
            // EXECUTER LA REQUETE AVEC LES PARAMETRES VIA MODEL DB
            $result = $dbConcours->getAllData($strQuery);
            // RECUPERER LE RESULTAT
            return $result[0]['epr_lieu'];
            $dbConcours = null;
        }

        // RECUPERER TOUS LES DEPARTS ET LES INFO DU DEPARTS POUR UN CONCOURS
        public function listeDepartsAPayer($intID_Concours) {
            // NOUVELLE INSTANCE DE MODEL DB
            $dbDeparts = new ClassDb();
            // REQUETE
            $strQuery = "SELECT 
                            E.epr_id,
                            E.epr_lieu,
                            R.r_epr_date,
                            U.usr_firstname,
                            U.usr_lastname,
                            U.usr_login,
                            U.usr_cat,
                            E.epr_discipline,
                            R.r_epr_usr_depart,
                            R.r_epr_usr_age,
                            E.epr_adulte_first_dep,
                            E.epr_adulte_other_dep,
                            E.epr_jeune_first_dep,
                            E.epr_jeune_other_dep
                        FROM 
                            csg_epreuves AS E,
                            csg_rel_epreuve_user AS R,
                            csg_users AS U
                        WHERE 
                            E.epr_id = R.epr_id
                        AND
                            U.usr_login = R.usr_login
                        AND 
                            E.epr_id = ". $intID_Concours ."
                        ORDER BY
                            E.epr_id ASC,
                            U.usr_login ASC;";
            // echo $strQuery;
            // EXECUTER LA REQUETE AVEC LES PARAMETRES VIA MODEL DB
            $result = $dbDeparts->getAllData($strQuery);
            // RECUPERER LE RESULTAT
            return $result;
            $dbDeparts = null;
        }
    
        // METTRE A JOUR LES DEPARTS
        public function updateValideDepart($_intCoucoursID, $_strUser, $_strDepart, $_strNumCheque=null, $_boolValideDepart) {
            if ($_boolValideDepart) {
                $strValideDep = ",r_epr_usr_valide = NOW()";
            } else {
                $strValideDep = ",r_epr_usr_valide = NULL";
            }
            
            // NOUVELLE INSTANCE DE MODEL DB
            $dbConcours = new ClassDb();
            // REQUETE
            // $strQuery = "UPDATE csg_rel_epreuve_user
            //                 SET 
            //                     r_epr_usr_num_cheque = ?,
            //                     r_epr_usr_valide = NOW()
            //                 WHERE 
            //                     epr_id = ?
            //                 AND 
            //                     usr_login = ?
            //                 AND 
            //                     r_epr_usr_depart = ?
            //                 ;";
            $strQuery = "UPDATE csg_rel_epreuve_user
                            SET 
                                r_epr_usr_num_cheque = '".$_strNumCheque."'"
                                . $strValideDep . "
                            WHERE 
                                epr_id = ".$_intCoucoursID."
                            AND 
                                usr_login = '".$_strUser."'
                            AND 
                                r_epr_usr_depart = '".$_strDepart."'
                            ;";
            // echo $strQuery;
            // EXECUTER LA REQUETE AVEC LES PARAMETRES VIA MODEL DB
            // $result = $dbConcours->updateRow($strQuery, array( $_strNumCheque, $_intCoucoursID, $_strUser, $_strDepart ));
            // $result = $strQuery;
            $result = $dbConcours->updateRow($strQuery);
            return $dbConcours->queryOK;
            // RECUPERER LE RESULTAT
            // if ($dbConcours->queryNbRows > 0) {
            //     return true;
            // } else {
            //     return false;
            // }
            $dbConcours = null;
        }

        // METTRE A JOUR LA VALIDATION DU CONCOURS
        public function updateValideConcours($_intConcoursID) {
            // NOUVELLE INSTANCE DE MODEL DB
            $dbConcours = new ClassDb();
            // REQUETE
            $strQuery = "UPDATE csg_epreuves
                            SET 
                                epr_etat = 'Validé'
                            WHERE 
                                epr_id = ".$_intConcoursID.
                            ";";
            
            $result = $dbConcours->updateRow($strQuery);
            return $dbConcours->queryOK;
            $dbConcours = null;
        }

        // public function uploadMandat($namePathFile, $nameFile) {
            
        //     $dossier = 'docs/mandats/';
        //     $fichier = basename($namePathFile);
        //     $taille_maxi = 2000000;
        //     $taille = filesize($namePathFile);
        //     // echo $taille;
        //     $extension = strrchr($nameFile, '.'); 

        //     //Début des vérifications de sécurité...
        //     //Si l'extension n'est pas dans le tableau
        //     if(!in_array($extension, $this->arrExtAutorisees)) {
        //         $erreur = 1;
        //     }
        //     // Si la taille du fichier n'est pas supérieur à la limite
        //     if($taille>$taille_maxi) {
        //         $erreur = 2;
        //     }
        //     //S'il n'y a pas d'erreur, on upload
        //     if(!isset($erreur)) {
        //         // On formate le nom du fichier ici...
        //         // FIXME Mettre l'id de l'article
        //         // $fichier = strtr($fichier, 
        //         //     'ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ', 
        //         //     'AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy');
        //         // $fichier = preg_replace('/([^.a-z0-9-_]+)/i', '-', $fichier);
        //         //Si la fonction renvoie TRUE, c'est que ça a fonctionné...
        //         if(move_uploaded_file($namePathFile, $dossier . $nameFile )) {
        //             $erreur = 0;
        //         } else {
        //             $erreur = 3;
        //         }
        //     }
        //     return $erreur;
        // }

    }
?>
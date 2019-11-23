<?php
	// modèle resultat : définit les résultats
	class classResultat {

        private $strLicencie;

        function __construct($strLogin=null) {
            // require_once PATH_MODELS . 'model_class_db.php';

            $this->strLicencie = $strLogin;

        }

        // public function creationTable($arrAnnee, $arrDiscipline) {
        //     $strPathPalmares = PATH_VIEWS . 'resultats/extracts/';
        //     $arrFiles = scandir($strPathPalmares);

        //     $arrPalmares = array();
        //     $arrResult = array();

        //     $intIndex = 0;
        //     foreach( $arrFiles as $key => $file ) {
        //         if (!in_array($file,array(".",".."))) {
        //             $arrFile = pathinfo($file);
        //             $arrName = explode("_", $arrFile['filename']);
        //             if ($arrName[0] == strtoupper($this->strLicencie) && in_array($arrName[2], $arrAnnee) && in_array($arrName[1], $arrDiscipline)) {
        //                 $intIndex = 0;

        //                 // Renvoyer le tableau array = [2018 => ['2018-1' => 550, '2018-2' => 560, ...]]
        //                 $arrResultat = $this->recupererValeurs($strPathPalmares . $arrFile['basename']);
        //                 // var_dump($arrResultat);
        //                 // $arrResult = [2018 => ['2018-1' => 550, '2018-2' => 560]];
        //                 // $strEnt = $arrName[2]."_".$arrName[1];
        //                 $arrResult[$arrName[1]][$arrName[2]] = $arrResultat;

        //                 switch ($arrName[1]) {
        //                     case 'FITA':
        //                         $intIndex = 9000000;
        //                         break;
        //                     case 'FEDERAL':
        //                         $intIndex = 8000000;
        //                         break;
        //                     case 'SALLE':
        //                         $intIndex = 7000000;
        //                         break;
        //                     default:
        //                         break;
        //                 }

        //                 $intIndex += intval($arrName[2]);
        //                 $arrPalmares[$intIndex] = $arrFile['basename'];
        //             }
        //         }
        //     }
        //     // var_dump($arrResult);

        //     krsort($arrPalmares);

        //     $strHTML = '';
        //     $strHTML .= '<table class="table table-bordered" style="width: 100%;" id="tableauScore">';
        //     $strHTML .= file_get_contents('resultats/resultat_entete.html');
        //     foreach ( $arrPalmares as $key => $value) {
        //         $strHTML .= file_get_contents('resultats/extracts/'.$value);
        //     }
        //     $strHTML .= '</table>';
            
        //     $arrAjax = array();

        //     $arrAjax[] = array(
        //         "table" => $strHTML,
        //         "resultat" => $arrResult
        //     );
            
        //     return $arrAjax;
        // }

        // public function recupererValeurs($strPathFile) {
        //     // Lire le fichier HTML
        //     $strContenu = file_get_contents($strPathFile);

        //     // Parser le ficheir HTML
        //     $dom = new DOMDocument();
        //     @$dom->loadHTML($strContenu);

        //     $xpath =  new DOMXPath($dom);
        //     $liste_resultats = $xpath->query('//tbody/tr[position()>1]');
        //     $nbResultats = $liste_resultats->length;
        //     $timeDate = new DateTime();

        //     $arrResultat = array();

        //     // Récupérer la date, le résultat du concours
        //     for($i = 0; $i < $nbResultats; $i++) {
        //         $td = $liste_resultats->item($i);
        //         $strDate = $td->childNodes[0]->nodeValue;
        //         $timeDate = strtotime(str_replace('/', '-', $strDate));
        //         $strResultat = str_replace(" pts", "", $td->childNodes[6]->nodeValue);
        //         $arrResultat[$timeDate] = $strResultat;
        //     }

        //     // Mettre ces données dans un array [date, resultat]
        //     // Trier ce tableau par date croissante
        //     ksort($arrResultat);

        //     return $arrResultat;
        // }

        private function recupererDataResultats($strLicencie, $intAnnee, $strDiscipline) {
            require_once PATH_MODELS . 'model_class_db.php';

            $dbResultat = new ClassDb();
            $strQuery = "SELECT
                            S.rsl_date AS date,
                            S.rsl_nom AS nom,
                            S.rsl_usr_id AS licencie,
                            S.rsl_usr_point AS points,
                            S.rsl_usr_commentaires AS commentaires
                        FROM 
                            csg_rel_classement_user AS R,
                            csg_classements AS C,
                            csg_resultats AS S
                        WHERE 
                            R.cla_id = C.cla_id
                        AND 
                            S.r_cla_usr_palmares = R.r_cla_usr_palmares
                        AND
                            C.cla_type LIKE '%".$strDiscipline."%'
                        AND
                            R.usr_id = '".$strLicencie."'
                        AND 
                            R.r_cla_usr_annee = ".$intAnnee." 
                        ORDER BY 
                            S.rsl_date ASC
                        ;";
            // echo $strQuery;
            // EXECUTER LA REQUETE AVEC LES PARAMETRES VIA MODEL DB
            $result = $dbResultat->getAllData($strQuery);
            // RECUPERER LE RESULTAT
            return $result;
            $dbResultat = null;
        }

        private function recupererDataResultats_v2($strLicencie, $intAnnee, $strDiscipline, $strDistance) {
            require_once PATH_MODELS . 'model_class_db.php';

            if (preg_match('/salle/i',      $strDiscipline)) {
                $strReqDiscipline = "C.cla_type LIKE '%Salle%'";
            } else {
                $strReqDiscipline = "(
                    C.cla_type LIKE '%Extérieur%'
                    OR
                    C.cla_type LIKE '%Fita%'
                    OR
                    C.cla_type LIKE '%Fédéral%'
                )";
            }

            $dbResultat = new ClassDb();
            $strQuery = "SELECT
                            S.rsl_date AS date,
                            S.rsl_nom AS nom,
                            S.rsl_usr_id AS licencie,
                            S.rsl_usr_point AS points,
                            S.rsl_usr_commentaires AS commentaires
                        FROM 
                            csg_rel_classement_user AS R,
                            csg_classements AS C,
                            csg_resultats AS S
                        WHERE 
                            R.cla_id = C.cla_id
                        AND 
                            S.r_cla_usr_palmares = R.r_cla_usr_palmares
                        AND "
                         . $strReqDiscipline .
                        " AND
                            C.cla_distance LIKE '%".$strDistance."%'
                        AND
                            R.usr_id = '".$strLicencie."'
                        AND 
                            R.r_cla_usr_annee = ".$intAnnee." 
                        ORDER BY 
                            S.rsl_date ASC
                        ;";
            // echo $strQuery."<br/>";
            // EXECUTER LA REQUETE AVEC LES PARAMETRES VIA MODEL DB
            $result = $dbResultat->getAllData($strQuery);
            // RECUPERER LE RESULTAT
            return $result;
            $dbResultat = null;
        }

        private function recupererHtmlEnteteDiscipline($strLicencie, $intAnnee, $strDiscipline) {
            require_once PATH_MODELS . 'model_class_db.php';

            $dbHTML = new ClassDb();
            $strQuery = "SELECT
                            C.cla_type AS Type,
                            C.cla_categ AS Categorie,
                            C.cla_annee AS Annee,
                            R.r_cla_usr_rang AS Rang
                        FROM 
                            csg_rel_classement_user AS R,
                            csg_classements AS C
                            
                        WHERE 
                            R.cla_id = C.cla_id
                        AND
                            C.cla_type LIKE '%".$strDiscipline."%'
                        AND
                            R.usr_id = '".$strLicencie."'
                        AND 
                            R.r_cla_usr_annee = ".$intAnnee."
                        ;";
            // echo $strQuery;
            // EXECUTER LA REQUETE AVEC LES PARAMETRES VIA MODEL DB
            $result = $dbHTML->getAllData($strQuery);
            // RECUPERER LE RESULTAT
            return $result[0];
            $dbHTML = null;
        }

        private function recupererHtmlEnteteDiscipline_v2($strLicencie, $intAnnee, $strDiscipline, $strDistance) {
            require_once PATH_MODELS . 'model_class_db.php';

            if (preg_match('/salle/i',      $strDiscipline)) {
                $strReqDiscipline = "C.cla_type LIKE '%Salle%'";
            } else {
                $strReqDiscipline = "(
                    C.cla_type LIKE '%Extérieur%'
                    OR
                    C.cla_type LIKE '%Fita%'
                    OR
                    C.cla_type LIKE '%Fédéral%'
                )";
            }
            
            $dbHTML = new ClassDb();
            $strQuery = "SELECT
                            C.cla_type AS Type,
                            C.cla_categ AS Categorie,
                            C.cla_annee AS Annee,
                            R.r_cla_usr_rang AS Rang
                        FROM 
                            csg_rel_classement_user AS R,
                            csg_classements AS C
                            
                        WHERE 
                            R.cla_id = C.cla_id
                        AND "
                         . $strReqDiscipline .
                        " AND
                            C.cla_distance LIKE '%".$strDistance."%'
                        AND
                            R.usr_id = '".$strLicencie."'
                        AND 
                            R.r_cla_usr_annee = ".$intAnnee." 
                        ;";
            // echo $strQuery;
            // EXECUTER LA REQUETE AVEC LES PARAMETRES VIA MODEL DB
            $result = $dbHTML->getAllData($strQuery);
            // RECUPERER LE RESULTAT
            return $result[0];
            $dbHTML = null;
        }

        private function recupererHtmlEnteteDisciplineMeilleursScore($strLicencie, $intAnnee, $strDiscipline) {
            require_once PATH_MODELS . 'model_class_db.php';

            $dbHTML = new ClassDb();
            $strQuery = "SELECT
                            S.rsl_usr_point
                        FROM 
                            csg_rel_classement_user AS R,
                            csg_classements AS C,
                            csg_resultats AS S
                        WHERE 
                            R.cla_id = C.cla_id
                        AND 
                            S.r_cla_usr_palmares = R.r_cla_usr_palmares
                        AND
                            C.cla_type LIKE '%".$strDiscipline."%'
                        AND
                            R.usr_id = '".$strLicencie."'
                        AND 
                            R.r_cla_usr_annee = ".$intAnnee."
                        ORDER BY 
                            S.rsl_usr_point DESC
                        LIMIT
                            3
                        ;";
            // echo $strQuery;
            // EXECUTER LA REQUETE AVEC LES PARAMETRES VIA MODEL DB
            $result = $dbHTML->getAllData($strQuery);
            // RECUPERER LE RESULTAT
            return $result;
            $dbHTML = null;
        }

        private function recupererHtmlEnteteDisciplineMeilleursScore_v2($strLicencie, $intAnnee, $strDiscipline, $strDistance) {
            require_once PATH_MODELS . 'model_class_db.php';

            if (preg_match('/salle/i',      $strDiscipline)) {
                $strReqDiscipline = "C.cla_type LIKE '%Salle%'";
            } else {
                $strReqDiscipline = "(
                    C.cla_type LIKE '%Extérieur%'
                    OR
                    C.cla_type LIKE '%Fita%'
                    OR
                    C.cla_type LIKE '%Fédéral%'
                )";
            }
            
            $dbHTML = new ClassDb();
            $strQuery = "SELECT
                            S.rsl_usr_point
                        FROM 
                            csg_rel_classement_user AS R,
                            csg_classements AS C,
                            csg_resultats AS S
                        WHERE 
                            R.cla_id = C.cla_id
                        AND "
                         . $strReqDiscipline .
                        " AND
                            C.cla_distance LIKE '%".$strDistance."%'
                        AND
                            R.usr_id = '".$strLicencie."'
                        AND 
                            R.r_cla_usr_annee = ".$intAnnee." 
                        ORDER BY 
                            S.rsl_usr_point DESC
                        LIMIT
                            3
                        ;";
            // echo $strQuery;
            // EXECUTER LA REQUETE AVEC LES PARAMETRES VIA MODEL DB
            $result = $dbHTML->getAllData($strQuery);
            // RECUPERER LE RESULTAT
            return $result;
            $dbHTML = null;
        }

        private function existClassement($strLicencie, $intAnnee, $strDiscipline) {
            require_once PATH_MODELS . 'model_class_db.php';

            $dbHTML = new ClassDb();
            $strQuery = "SELECT
                            1
                        FROM
                            csg_rel_classement_user AS R,
                            csg_classements AS C
                        WHERE 
                            R.cla_id = C.cla_id
                        AND
                            C.cla_type LIKE '%".$strDiscipline."%'
                        AND
                            R.usr_id = '".$strLicencie."'
                        AND 
                            R.r_cla_usr_annee = ".$intAnnee."
                        ;";
            // echo $strQuery;
            // EXECUTER LA REQUETE AVEC LES PARAMETRES VIA MODEL DB
            $result = $dbHTML->existData($strQuery);
            // RECUPERER LE RESULTAT
            return $result;
            $dbHTML = null;
        }

        private function existClassement_v2($strLicencie, $intAnnee, $strDiscipline, $strDistance) {
            require_once PATH_MODELS . 'model_class_db.php';

            if (preg_match('/salle/i',      $strDiscipline)) {
                $strReqDiscipline = "C.cla_type LIKE '%Salle%'";
            } else {
                $strReqDiscipline = "(
                    C.cla_type LIKE '%Extérieur%'
                    OR
                    C.cla_type LIKE '%Fita%'
                    OR
                    C.cla_type LIKE '%Fédéral%'
                )";
            }
            
            $dbHTML = new ClassDb();
            $strQuery = "SELECT
                            1
                        FROM
                            csg_rel_classement_user AS R,
                            csg_classements AS C
                        WHERE 
                            R.cla_id = C.cla_id
                        AND "
                         . $strReqDiscipline .
                        " AND
                            C.cla_distance LIKE '%".$strDistance."%'
                        AND
                            R.usr_id = '".$strLicencie."'
                        AND 
                            R.r_cla_usr_annee = ".$intAnnee." 
                        ;";
            // echo $strQuery;
            // EXECUTER LA REQUETE AVEC LES PARAMETRES VIA MODEL DB
            $result = $dbHTML->existData($strQuery);
            // RECUPERER LE RESULTAT
            return $result;
            $dbHTML = null;
        }

        private function recupererHtmlResultats($strLicencie, $intAnnee, $strDiscipline) {
            require_once PATH_MODELS . 'model_class_db.php';

            $dbResultat = new ClassDb();
            $strQuery = "SELECT
                            S.rsl_id AS URL_resultat,
                            S.rsl_date AS Date_resultat,
                            S.rsl_nom AS Nom_resultat,
                            S.rsl_usr_place AS Place_resultat,
                            S.rsl_usr_point AS Point_resultat,
                            S.rsl_usr_commentaires AS Commentaires_resultat
                        FROM 
                            csg_rel_classement_user AS R,
                            csg_classements AS C,
                            csg_resultats AS S
                        WHERE 
                            R.cla_id = C.cla_id
                        AND 
                            S.r_cla_usr_palmares = R.r_cla_usr_palmares
                        AND
                            C.cla_type LIKE '%".$strDiscipline."%'
                        AND
                            R.usr_id = '".$strLicencie."'
                        AND 
                            R.r_cla_usr_annee = ".$intAnnee." 
                        ORDER BY 
                            S.rsl_usr_point DESC
                        ;";
            // echo $strQuery;
            // EXECUTER LA REQUETE AVEC LES PARAMETRES VIA MODEL DB
            $result = $dbResultat->getAllData($strQuery);
            // RECUPERER LE RESULTAT
            return $result;
            $dbResultat = null;
        }

        private function recupererHtmlResultats_v2($strLicencie, $intAnnee, $strDiscipline, $strDistance) {
            require_once PATH_MODELS . 'model_class_db.php';

            if (preg_match('/salle/i',      $strDiscipline)) {
                $strReqDiscipline = "C.cla_type LIKE '%Salle%'";
            } else {
                $strReqDiscipline = "(
                    C.cla_type LIKE '%Extérieur%'
                    OR
                    C.cla_type LIKE '%Fita%'
                    OR
                    C.cla_type LIKE '%Fédéral%'
                )";
            }
            
            $dbResultat = new ClassDb();
            $strQuery = "SELECT
                            S.rsl_id AS URL_resultat,
                            S.rsl_date AS Date_resultat,
                            S.rsl_nom AS Nom_resultat,
                            S.rsl_usr_place AS Place_resultat,
                            S.rsl_usr_point AS Point_resultat,
                            S.rsl_usr_commentaires AS Commentaires_resultat
                        FROM 
                            csg_rel_classement_user AS R,
                            csg_classements AS C,
                            csg_resultats AS S
                        WHERE 
                            R.cla_id = C.cla_id
                        AND 
                            S.r_cla_usr_palmares = R.r_cla_usr_palmares
                        AND 
                            ". $strReqDiscipline ." 
                        AND
                            C.cla_distance LIKE '%".$strDistance."%'
                        AND
                            R.usr_id = '".$strLicencie."'
                        AND 
                            R.r_cla_usr_annee = ".$intAnnee." 
                        ORDER BY 
                            S.rsl_usr_point DESC
                        ;";
            // echo $strQuery;
            // EXECUTER LA REQUETE AVEC LES PARAMETRES VIA MODEL DB
            $result = $dbResultat->getAllData($strQuery);
            // RECUPERER LE RESULTAT
            return $result;
            $dbResultat = null;
        }

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
                            U.usr_lastname ASC,
                            U.usr_firstname ASC
                        ;";
            // echo $strQuery;
            // EXECUTER LA REQUETE AVEC LES PARAMETRES VIA MODEL DB
            $result = $dbCompetiteur->getAllData($strQuery);
            // RECUPERER LE RESULTAT
            return $result;
            $dbCompetiteur = null;
        }

        private function formaterArrayResultat($strLicencie, $intAnnee, $strDiscipline) {
            return $this->recupererDataResultats($strLicencie, $intAnnee, $strDiscipline);
        }

        private function formaterArrayResultat_v2($strLicencie, $intAnnee, $strDiscipline, $strDistance) {
            return $this->recupererDataResultats_v2($strLicencie, $intAnnee, $strDiscipline, $strDistance);
        }

        private function formaterHtmlResultats($strLicencie, $arrAnnee, $arrDiscipline) {
            $strHTML = '';
            $strHTML .= $this->formaterHtmlResultat_EnteteGlobal();

            // Pour chaque Annee
            foreach ($arrAnnee as $key => $intAnnee) {
                // Pour chaque Discipline
                foreach ($arrDiscipline as $key => $strDiscipline) {
                    if ($this->existClassement($strLicencie, $intAnnee, $strDiscipline)) {
                        $strHTML .= $this->formaterHtmlResultat_EnteteDiscipline($strLicencie, $intAnnee, $strDiscipline);
                        $arrResultats = $this->recupererHtmlResultats($strLicencie, $intAnnee, $strDiscipline);
                        if ($arrResultats) {
                            // Pour chaque résultat
                            foreach ($arrResultats as $key => $arrResultat) {
                                $strHTML .= $this->formaterHtmlResultat($arrResultat, $strLicencie);
                            }
                        }
                    }
                }
            }
            
            $strHTML .= $this->formaterHtmlResultat_Footer();

            return $strHTML;
        }

        private function formaterHtmlResultats_v2($strLicencie, $arrAnnee, $strDiscipline, $strDistance) {
            $strHTML = '';
            $strHTML .= $this->formaterHtmlResultat_EnteteGlobal();

            // Pour chaque Annee
            foreach ($arrAnnee as $key => $intAnnee) {
                // echo "Année : " . $intAnnee . "<br/>";
                // Pour chaque Discipline
                // foreach ($arrDiscipline as $key => $strDiscipline) {
                    if ($this->existClassement_v2($strLicencie, $intAnnee, $strDiscipline, $strDistance)) {
                        // echo "Classement existe pour année " . $intAnnee ."<br/>";
                        $strHTML .= $this->formaterHtmlResultat_EnteteDiscipline_v2($strLicencie, $intAnnee, $strDiscipline, $strDistance);
                        $arrResultats = $this->recupererHtmlResultats_v2($strLicencie, $intAnnee, $strDiscipline, $strDistance);
                        if ($arrResultats) {
                            // Pour chaque résultat
                            foreach ($arrResultats as $key => $arrResultat) {
                                $strHTML .= $this->formaterHtmlResultat($arrResultat, $strLicencie);
                            }
                        }
                    }
                // }
            }
            
            $strHTML .= $this->formaterHtmlResultat_Footer();

            return $strHTML;
        }

        private function formaterHtmlResultat_EnteteGlobal() {
            $strHTML = '';

            $strHTML .= '<table class="table table-bordered" style="width: 100%;" id="tableauScore">';
            $strHTML .= '<thead>';
            $strHTML .= '<tr>';
            $strHTML .= '<th class="text-center" style="display: none;">ID</th>';
            $strHTML .= '<th rowspan="2" colspan="2" style="vertical-align: middle; text-align: center;">';
            $strHTML .= '<span >Palmarès</span>';
            $strHTML .= '</th>';
            $strHTML .= '<th rowspan="2" style="vertical-align: middle; text-align: center;">Pl.</th>';
            $strHTML .= '<th colspan="4" style="vertical-align: middle; text-align: center;">Points</th>';
            $strHTML .= '<th rowspan="2" style="vertical-align: middle; text-align: center;">Commentaires</th>';
            $strHTML .= '</tr>';
            $strHTML .= '<tr>';
            $strHTML .= '<th style="vertical-align: middle; text-align: center;">Score 1</th>';
            $strHTML .= '<th style="vertical-align: middle; text-align: center;">Score 2</th>';
            $strHTML .= '<th style="vertical-align: middle; text-align: center;">Score 3</th>';
            $strHTML .= '<th style="vertical-align: middle; text-align: center;"><b>Moyenne</b></th>';
            $strHTML .= '</tr>';
            $strHTML .= '</thead>';
            $strHTML .= '<tbody>';

            return $strHTML;
        }

        private function formaterHtmlResultat_EnteteDiscipline($strLicencie, $intAnnee, $strDiscipline) {
            // echo "Formatage entete pour l'année " .$intAnnee."<br/>";

            $arrHTMLMeilleurScore = $this->recupererHtmlEnteteDisciplineMeilleursScore($strLicencie, $intAnnee, $strDiscipline);
            $strHTML = '';
            if ($arrHTMLMeilleurScore != array()) {
                $arrMeilleursScores = array($arrHTMLMeilleurScore[0][0], $arrHTMLMeilleurScore[1][0], $arrHTMLMeilleurScore[2][0]);
                $intMoyenne = floor(array_sum($arrMeilleursScores) / count($arrMeilleursScores));

                $arrHTML = $this->recupererHtmlEnteteDiscipline($strLicencie, $intAnnee, $strDiscipline);
                    
                $strHTML .= '<tr class="clmt">';
                $strHTML .= '<td colspan="2" style="color: #4778a2;">';
                $strHTML .= '<span style="font-size: 1.3em; font-weight: bold">'. $arrHTML['Type'].' '.$arrHTML['Annee'].'</span> - <strong>'. $arrHTML['Categorie'].' '. $arrHTML['Annee'].'</strong>';
                $strHTML .= '</td>';
                $strHTML .= '<td style="color: #4778a2; text-align: center">';
                $strHTML .= '<span style="font-size: 1.3em; font-weight: bold;">'. $arrHTML['Rang'].'</span>';
                $strHTML .= '</td>';
                // $strHTML .= '<td style="text-align: center" style="font-size: 1.3em; font-weight: bold"><strong>'. $arrHTML['Rang'].'</strong></td>';
                $strHTML .= '<td style="text-align: center">'.$arrHTMLMeilleurScore[0][0].'</td>';
                $strHTML .= '<td style="text-align: center">'.$arrHTMLMeilleurScore[1][0].'</td>';
                $strHTML .= '<td style="text-align: center">'.$arrHTMLMeilleurScore[2][0].'</td>';
                $strHTML .= '<td style="text-align: center"><strong>'.$intMoyenne.'</strong></td>';
                $strHTML .= '</tr>';
            }

            return $strHTML;
        }

        private function formaterHtmlResultat_EnteteDiscipline_v2($strLicencie, $intAnnee, $strDiscipline, $strDistance) {
            // echo "Formatage entete pour l'année " .$intAnnee."<br/>";

            $arrHTMLMeilleurScore = $this->recupererHtmlEnteteDisciplineMeilleursScore_v2($strLicencie, $intAnnee, $strDiscipline, $strDistance);
            $strHTML = '';
            if ($arrHTMLMeilleurScore != array()) {
                $arrMeilleursScores = array($arrHTMLMeilleurScore[0][0], $arrHTMLMeilleurScore[1][0], $arrHTMLMeilleurScore[2][0]);
                $intMoyenne = floor(array_sum($arrMeilleursScores) / count($arrMeilleursScores));

                $arrHTML = $this->recupererHtmlEnteteDiscipline_v2($strLicencie, $intAnnee, $strDiscipline, $strDistance);
                    
                $strHTML .= '<tr class="clmt">';
                $strHTML .= '<td colspan="2" style="color: #4778a2;">';
                $strHTML .= '<span style="font-size: 1.3em; font-weight: bold">'. $arrHTML['Type'].' '.$arrHTML['Annee'].'</span> - <strong>'. $arrHTML['Categorie'].' '. $arrHTML['Annee'].'</strong>';
                $strHTML .= '</td>';
                $strHTML .= '<td style="color: #4778a2; text-align: center">';
                $strHTML .= '<span style="font-size: 1.3em; font-weight: bold;">'. $arrHTML['Rang'].'</span>';
                $strHTML .= '</td>';
                // $strHTML .= '<td style="text-align: center" style="font-size: 1.3em; font-weight: bold"><strong>'. $arrHTML['Rang'].'</strong></td>';
                $strHTML .= '<td style="text-align: center">'.$arrHTMLMeilleurScore[0][0].'</td>';
                $strHTML .= '<td style="text-align: center">'.$arrHTMLMeilleurScore[1][0].'</td>';
                $strHTML .= '<td style="text-align: center">'.$arrHTMLMeilleurScore[2][0].'</td>';
                $strHTML .= '<td style="text-align: center"><strong>'.$intMoyenne.'</strong></td>';
                $strHTML .= '</tr>';
            }

            return $strHTML;
        }

        private function formaterHtmlResultat($arrData, $strLicencie) {
            $strHTML = '';

            $strKeyResultat = $arrData['Date_resultat'] . '_' . $arrData['Nom_resultat'] .'_' . $strLicencie . '_' . $arrData['Point_resultat'];
                
            $strHTML .= '<tr>';
            $strHTML .= "<td class='text-center' style='display: none;'>" . $strKeyResultat . "</td>";
            $strHTML .= '<td style="text-align: center; font-size: 0.7em;">';
            $strHTML .= '<a href="http://classements.ffta.fr/actions/outils/AjaxEprvResultats.php?act='.$arrData['URL_resultat'].'" target="_blank" rel="dialog" rev="900x800"><img src="../pictures/Coupe.png" class="detailsResultat" data-id="5489363"></a><br/>'.$arrData['Date_resultat'].'</td>';
            $strHTML .= '<td style="text-align: left; font-size: 0.7em;">'.$arrData['Nom_resultat'].'</td>';
            $strHTML .= '<td style="text-align: center">'.$arrData['Place_resultat'].'<sup>ème</sup>';
            $strHTML .= '</td>';
            $strHTML .= '<td style="text-align: center" colspan="4">'.$arrData['Point_resultat'].' pts</td>';
            $strHTML .= '<td style="text-align: left; font-size: 0.7em;">'.html_entity_decode($arrData['Commentaires_resultat']).'</td>';
            $strHTML .= '<td style="text-align: center;"><img class="modifieComment" src="../pictures/pen-on-square-of-paper-interface-symbol.svg" width="20"></td>';
            $strHTML .= '</tr>';
            
            return $strHTML;
        }

        private function formaterHtmlResultat_Footer() {
            $strHTML = '';
            
            $strHTML .= '<tbody>';
            $strHTML .= '</table>';
            
            return $strHTML;
        }

        public function formaterHtmlCompetiteurs() {
            $arrCompetiteurs = $this->recupererListeCompetiteurs();

            $strHTML = '';

            foreach ($arrCompetiteurs as $key => $arrCompetiteur) {
                $strHTML .= '<div class="form-check ml-4 mt-2">';
                $strHTML .= '<label class="form-check-label">';
                $strHTML .= '<input class="form-check-input csg-input" type="radio" name="LicencieRadios" id="radio_'.$arrCompetiteur['id'].'" value="'.$arrCompetiteur['id'].'">'.$arrCompetiteur['firstname'].' '.$arrCompetiteur['lastname'];
                $strHTML .= '</label>';
                $strHTML .= '</div>';
            }

            return $strHTML;
        }

        public function formaterHtmlDistances() {
            $arrDistances = ["Salle - 18m", "Extérieur - 20m", "Extérieur - 30m", "Extérieur - 40m", "Extérieur - 50m", "Extérieur - 60m", "Extérieur - 70m"];

            $strHTML = '';

            foreach ($arrDistances as $arrDistance) {
                $strHTML .= '<div class="form-check-inline ml-4 mt-2">';
                $strHTML .= '<label class="form-check-label">';
                $strHTML .= '<input class="form-check-input csg-input" type="radio" name="DistanceRadios" id="radio_'.$arrDistance.'" value="'.$arrDistance.'">'.$arrDistance;
                $strHTML .= '</label>';
                $strHTML .= '</div>';
            }

            return $strHTML;
        }

        public function creationTable_v2($arrAnnee, $arrDiscipline) {
            $arrPalmares = array();
            $arrResult = array();

            // Definition du tableau utilisé par les graphiques
            foreach ($arrDiscipline as $key => $valDiscipline) {
                foreach ($arrAnnee as $key => $valAnnee) {
                    $arrResultat = $this->formaterArrayResultat($this->strLicencie, $valAnnee, $valDiscipline);
                    if ($arrResultat) {
                        $arrResult[$valDiscipline][$valAnnee] = $arrResultat;
                    }
                }  
            }
            
            // Définition des tables utilisées par le récapitulatif
            // $strHTML = '';
            $strHTML = $this->formaterHtmlResultats($this->strLicencie, $arrAnnee, $arrDiscipline);
            
            $arrAjax = array();

            $arrAjax[] = array(
                "table" => $strHTML,
                "resultat" => $arrResult
            );
            
            return $arrAjax;
        }

        public function creationTable_v3($arrAnnee, $strDiscipline, $strDistance) {
            $arrPalmares = array();
            $arrResult = array();

            // Definition du tableau utilisé par les graphiques
            // foreach ($arrDiscipline as $key => $valDiscipline) {
                foreach ($arrAnnee as $key => $valAnnee) {
                    $arrResultat = $this->formaterArrayResultat_v2($this->strLicencie, $valAnnee, $strDiscipline, $strDistance);
                    if ($arrResultat) {
                        $arrResult[$strDiscipline][$valAnnee] = $arrResultat;
                    }
                }  
            // }
            
            // Définition des tables utilisées par le récapitulatif
            // $strHTML = '';
            $strHTML = $this->formaterHtmlResultats_v2($this->strLicencie, $arrAnnee, $strDiscipline, $strDistance);
            
            $arrAjax = array();

            $arrAjax[] = array(
                "table" => $strHTML,
                "resultat" => $arrResult
            );
            
            return $arrAjax;
        }

        public function recupererCommentaire($_strResultatDate, $_strResultatNom, $_strResultatLicencie, $_strResultatPoints) {
            require_once PATH_MODELS . 'model_class_db.php';

            $dbResultat = new ClassDb();
            $strQuery = "SELECT
                            rsl_usr_commentaires AS Commentaires
                        FROM 
                            csg_resultats
                        WHERE 
                            rsl_date = '".$_strResultatDate."'
                        AND 
                            rsl_nom = '".$_strResultatNom."'
                        AND
                            rsl_usr_id = '".$_strResultatLicencie."'
                        AND
                            rsl_usr_point = ".$_strResultatPoints."
                        ;";
            // echo $strQuery;
            // EXECUTER LA REQUETE AVEC LES PARAMETRES VIA MODEL DB
            $result = $dbResultat->getAllData($strQuery);
            // RECUPERER LE RESULTAT
            return $result[0];
            // return $strQuery;
            $dbResultat = null;
        }

        public function miseajourCommentaire($_strResultatDate, $_strResultatNom, $_strResultatLicencie, $_strResultatPoints, $_strResultatCommentaires) {
            require_once PATH_MODELS . 'model_class_db.php';

            // NOUVELLE ISNTANCE DE MODEL DB
            $dbResultat = new ClassDb();

            // REQUETE
            $strQuery = "UPDATE csg_resultats 
            				SET 
            					rsl_usr_commentaires = '".$_strResultatCommentaires."'
            				WHERE 
                                rsl_date = '".$_strResultatDate."' 
                            AND
                                rsl_nom = '".$_strResultatNom."' 
                            AND 
                                rsl_usr_id = '".$_strResultatLicencie."' 
                            AND 
                                rsl_usr_point = ".$_strResultatPoints."
                        ;";
            // echo $strQuery;

            // EXECUTER LA REQUETE AVEC LES PARAMETRES VIA MODEL DB
            $result = $dbResultat->updateRow($strQuery, array());
            // RETURN l'état de la requête
            return $dbResultat->queryOK;

            $dbResultat = null;
        }

    }
?>
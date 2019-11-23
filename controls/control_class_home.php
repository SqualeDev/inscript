<?php
    // controller home
    class ControlHome {

        private function lireNews($strType) {
            require_once PATH_ROOT . 'parsedown/parsedown.php';
            $Parsedown = new Parsedown();
            $strNews = '';

            
            if ($strType == 'prive') {
                $file = fopen( PATH_ROOT . 'docs/changelog/prive.md', "r" );
                $strNewsPriveRaw = "";
                while(!feof($file)) {
                    $strNewsPriveRaw .= fgets($file, 4096);
                }
                fclose($file);
                $strNews .= $Parsedown->text($strNewsPriveRaw);
            }

            $file = fopen( PATH_ROOT . 'docs/changelog/public.md', "r" );
            $strNewsPublicRaw = "";
            while(!feof($file)) {
                $strNewsPublicRaw .= fgets($file, 4096);
            }
            fclose($file);
            $strNews .= $Parsedown->text($strNewsPublicRaw);

            return $strNews;
        }

        // private function afficherArticleEpingle() {
        //     require_once PATH_VIEWS . 'view_article_epingle.php';
        //     if ($strArticleVide == true) {
        //         return '';
        //     } else {
        //         return $strEpingle;
        //     }
        // }

        // action homeDefault
        public function HomeDefault() {
            global $intNbConcoursMax;

            // Définition des Filtres d'affichage des Articles
            if (isset($_GET['dateDeb'])) {$dateDeb = $_GET['dateDeb'];} else {$dateDeb = null;}
            if (isset($_GET['dateFin'])) {$dateFin = $_GET['dateFin'];} else {$dateFin = null;}
            if (isset($_GET['categ'])) {$strCateg = $_GET['categ'];} else {$strCateg = null;}
            if (isset($_GET['limit'])) {
                $intLimit = $_GET['limit'];
            } elseif ($dateDeb == null && $dateFin == null && $strCateg == null) {
                $intLimit = 6;
            } else {
                $intLimit = null;
            }

            $intTab = 4;
            // display_debug("ControlHome : ",	"function HomeDefault", 1);
            // Générer le message pour un non logué
            if (empty($_SESSION['licencie'])) {
                $strMsgAccueil = str_repeat("\t", $intTab)."<h4 class=\"alert-heading\">Bonjour</h4>" . PHP_EOL ;
                $strMsgAccueil .= str_repeat("\t", $intTab)."<p>Bienvenue sur le site du Club Sportif de Gravenchon section Tir à l'arc.</p>" . PHP_EOL ;
                $strMsgAccueil .= "<hr>" . PHP_EOL ;
                $strMsgAccueil .= "<button class='btn btn-info' type='button' data-target='#MonCollapse' data-toggle='collapse' aria-expanded='false' aria-controls='MonCollapse'>Les Nouveautés du site</button>" . PHP_EOL ;
                $strMsgAccueil .= "<section id='MonCollapse' class='collapse'>" . PHP_EOL ;
                $strMsgAccueil .= $this->lireNews("public");
                $strMsgAccueil .= "</section>" . PHP_EOL ;
                
                // Générer les articles public
                // $strArtEpingle = $this->afficherArticleEpingle();
                // $strSidebar = $this->afficherSideBar('public');
                // $strArticles = $this->afficherArticles('public', $dateDeb, $dateFin, $strCateg, $intLimit);
                // $strModals = $this->afficherModals('public', $dateDeb, $dateFin, $strCateg, $intLimit);
            }

            // Générer le message pour un logué (licencié du club)
            if (!empty($_SESSION['licencie'])) {
                $objUser = new ClassUser($_SESSION['licencie'], $_SESSION['pwd']);

                if ($objUser->intNbInscriptConcoursInd == 0) {
                    $strMsgNbConcours = "Tu ne t'es pas encore inscrits à un concours, il te reste donc <b>". ($intNbConcoursMax - $objUser->intNbInscriptConcoursInd) . "</b> concours autorisé(s). ";
                } else {
                    $strMsgNbConcours = "Tu t'es déjà inscrits à ". $objUser->intNbInscriptConcoursInd . " concours, il te reste donc <b>". ($intNbConcoursMax - $objUser->intNbInscriptConcoursInd) . "</b> concours autorisé(s). ";
                }
                if ($objUser->intNbInscriptChampionnat == 0) {
                    $strMsgNbChampionnat = "Tu n'as participé à aucun Championnat.";
                } else {
                    $strMsgNbChampionnat = "Tu as aussi participé à ". $objUser->intNbInscriptChampionnat . " Championnat(s).";
                }

                $strMsgAccueil = str_repeat("\t", $intTab)."<h4 class=\"alert-heading\">Bonjour " . $objUser->strFirstname . "</h4>" . PHP_EOL ;
                $strMsgAccueil .= str_repeat("\t", $intTab)."<p>Bienvenue sur le site du Club Sportif de Gravenchon section Tir à l'arc.</p>" . PHP_EOL ;
                $strMsgAccueil .= str_repeat("\t", $intTab)."<hr>" . PHP_EOL ;
                $strMsgAccueil .= str_repeat("\t", $intTab)."<p class=\"mb-0\">Ce site te permettra de t'inscrire aux divers concours de la FFTA et de suivre tes résultats. ". $strMsgNbConcours . $strMsgNbChampionnat . "</p>" . PHP_EOL ;
                $strMsgAccueil .= str_repeat("\t", $intTab)."<hr>" . PHP_EOL ;
                $strMsgAccueil .= "<button class='btn btn-info' type='button' data-target='#MonCollapse' data-toggle='collapse' aria-expanded='false' aria-controls='MonCollapse'>Les Nouveautés du site</button>" . PHP_EOL ;
                $strMsgAccueil .= "<section id='MonCollapse' class='collapse'>" . PHP_EOL ;
                $strMsgAccueil .= $this->lireNews("prive");
                $strMsgAccueil .= "</section>" . PHP_EOL ;
                
                // Générer les articles privés
                // $strArtEpingle = $this->afficherArticleEpingle();
                // $strSidebar = $this->afficherSideBar('prive');
                // $strArticles = $this->afficherArticles('prive', $dateDeb, $dateFin, $strCateg, $intLimit);
                // $strModals = $this->afficherModals('prive', $dateDeb, $dateFin, $strCateg, $intLimit);
            }

            // Affichage de la page d'accueil
            require_once PATH_VIEWS . 'view_accueil.html';
        }

        // private function afficherSideBar($_strPublic) {
        //     if ($_strPublic == 'public') {$intPublic = 1;} else {$intPublic = 0;}
        //     $arrMonth = ['Jan.', 'Fev.', 'Mar.', 'Arv.', 'Mai', 'Jui.', 'Jul.', 'Aou.', 'Sep.', 'Oct.', 'Nov.', 'Dec.'];
        //     $arrDatesArticles = $this->getListDateArticles($intPublic);
        //     $arrCategArticles = $this->getListCategArticles($intPublic);

        //     $strSideBar ='';
        //     $strSideBar .= "<div class='col-lg-2'>".PHP_EOL;
        //     $strSideBar .= "<!-- <div id='demo' class='collapse in'> -->".PHP_EOL;
        //     $strSideBar .= "<hr>".PHP_EOL;
        //     $strSideBar .= "<div class='list-group list-group'>".PHP_EOL;
        //     $strSideBar .= "<h4>Dates</h4>".PHP_EOL;
            
        //     $intIndex = 1;
        //     foreach ($arrDatesArticles as $key => $value) {
        //         $strDateDeb = date("Y-m-d", mktime(0,0,0,$value['Mois'],1,$value['Annee']));
        //         $strDateFin = date("Y-m-t", mktime(0,0,0,$value['Mois'],1,$value['Annee']));;

        //         $strARefDeb = "<a href='".URL_ROOT."index.php?controller=home&action=homeDefault&dateDeb=".$strDateDeb."&dateFin=".$strDateFin."' class='list-group-item'>";
        //         $strBadge = "<span class='badge badge-pill grey float-right'>".$value['NbArticle']."</span>".$arrMonth[$value['Mois']-1]." ".$value['Annee'];
        //         // N'afficher que si le nbre d'articles de ce mois datant de 15 jours est supérieur à 0
        //         if (date("m") == $value['Mois'] && date("Y") == $value['Annee']) {
        //             $strBadgeNew = "<span class='badge badge-success float-right'>New</span>";
        //         } else {
        //             $strBadgeNew = "";
        //         }
        //         $strARefFin = "</a>"; 
        //         $strSideBar .= $strARefDeb. $strBadge . $strBadgeNew . $strARefFin . PHP_EOL;
        //         if ($intIndex == 3) {$strSideBar .= "<div id='categories' class='collapse'>".PHP_EOL;}
        //         $intIndex++;
        //     }
        //     $strSideBar .= "</div>".PHP_EOL;
        //     $strSideBar .= "<button class='btn btn-primary btn-sm btn-block' data-toggle='collapse' data-target='#categories'>Plus ...</button>".PHP_EOL;
        
        //     $strSideBar .= "<hr>".PHP_EOL;
        //     $strSideBar .= "<h4>Catégories</h4>".PHP_EOL;
        //     foreach ($arrCategArticles as $key => $value) {
        //         // Ajouter le badge 'New', N'afficher que si le nbre d'articles de cette catégorie datant de 15 jours est supérieur à 0
        //         $strARefDeb = "<a href='".URL_ROOT."index.php?controller=home&action=homeDefault&categ=".$value['Categorie']."' class='list-group-item'>";
        //         $strBadge = "<span class='badge badge-pill grey float-right'>".$value['NbArticles']."</span>". $value['Categorie'];
        //         $strARefFin = "</a>"; 
        //         $strSideBar .= $strARefDeb.$strBadge.$strARefFin.PHP_EOL;
        //     }
        //     $strSideBar .= "<hr>".PHP_EOL;

        //     $strSideBar .= "</div>".PHP_EOL;
        //     $strSideBar .= "<!-- </div> -->".PHP_EOL;
        //     $strSideBar .= "</div>".PHP_EOL;
        //     return $strSideBar;
        // }

        // private function getListDateArticles($_intPublic) {
        //     // RECUPERATION DES DONNES DES ARTICLES UNIQUEMENT PUBLICS
        //     if ($_intPublic == 1) {
        //         $strWHERE = "WHERE art_public = 1";
        //     } else {
        //         $strWHERE = "";
        //     }
        //     // NOUVELLE ISNTANCE DE MODEL DB
		// 	$dbArticles = new ClassDb();
		// 	// REQUETE
		// 	$strQuery = "SELECT 
        //                     count(art_id) AS NbArticle,
        //                     YEAR(art_date_parution) AS Annee,
        //                     MONTH(art_date_parution) AS Mois 
        //                 FROM 
        //                     csg_articles 
        //                 ".$strWHERE."
        //                 GROUP BY 
        //                     YEAR(art_date_parution),
        //                     MONTH(art_date_parution) 
        //                 ORDER BY 
        //                     YEAR(art_date_parution) DESC,
        //                     MONTH(art_date_parution) DESC ;";
		// 	// EXECUTER LA REQUETE AVEC LES PARAMETRES VIA MODEL DB
		// 	$result = $dbArticles->getAllData($strQuery);
		// 	// RECUPERER LE RESULTAT
		// 	return $result;
		// 	$dbArticles = null;
        // }

        // private function getListCategArticles($_intPublic) {
        //     // RECUPERATION DES DONNES DES ARTICLES UNIQUEMENT PUBLICS
        //     if ($_intPublic == 1) {
        //         $strWHERE = "WHERE art_public = 1";
        //     } else {
        //         $strWHERE = "";
        //     }
        //     // NOUVELLE ISNTANCE DE MODEL DB
		// 	$dbArticles = new ClassDb();
		// 	// REQUETE
		// 	$strQuery = "SELECT 
        //                     count(art_id) AS NbArticles,
        //                     art_categorie AS Categorie
        //                 FROM 
        //                     csg_articles 
        //                 ".$strWHERE."
        //                 GROUP BY 
        //                     art_categorie 
        //                 ORDER BY 
        //                     art_categorie ASC;";
		// 	// EXECUTER LA REQUETE AVEC LES PARAMETRES VIA MODEL DB
		// 	$result = $dbArticles->getAllData($strQuery);
		// 	// RECUPERER LE RESULTAT
		// 	return $result;
		// 	$dbArticles = null;
        // }

        // private function afficherArticles($strZone, $strDateDebut, $strDateFin, $strCategorie, $intLimit) {
        //     require_once PATH_MODELS . 'model_class_mur.php';

        //     $strArticles = '';
        //     $objMur  = new classMur();
        //     switch ($strZone) {
        //         case 'prive':
        //             $strArticles = $objMur->afficherArticles('prive', $strDateDebut, $strDateFin, $strCategorie, $intLimit);
        //             break;
        //         default:
        //             $strArticles = $objMur->afficherArticles('public', $strDateDebut, $strDateFin, $strCategorie, $intLimit);
        //             break;
        //     }
            
        //     return $strArticles;
        // }

        // private function afficherModals($strZone, $strDateDebut, $strDateFin, $strCategorie, $intLimit) {
        //     require_once PATH_MODELS . 'model_class_mur.php';

        //     $strModals = '';
        //     $objMur  = new classMur();
        //     switch ($strZone) {
        //         case 'prive':
        //             $strModals = $objMur->afficherModals('prive', $strDateDebut, $strDateFin, $strCategorie, $intLimit);
        //             break;
        //         default:
        //             $strModals = $objMur->afficherModals('public', $strDateDebut, $strDateFin, $strCategorie, $intLimit);
        //             break;
        //     }
            
        //     return $strModals;
        // }

    }
?>

<?php
    echo '<style type="text/css">
    a.button {
        -webkit-appearance: button;
        -moz-appearance: button;
        appearance: button;

        text-decoration: none;
        color: initial;
    }
    </style>';

    if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/licencies')) {
        // Config finale
        // require_once $_SERVER['DOCUMENT_ROOT'] . '/licencies/config_local.php';
        require_once $_SERVER['DOCUMENT_ROOT'] . '/licencies/constantes.php';
        require $_SERVER['DOCUMENT_ROOT'] . '/licencies/check_resultat_array.php';
    } else {
        // Config Debug
        // require_once $_SERVER['DOCUMENT_ROOT'] . '/config_local.php';
        require_once $_SERVER['DOCUMENT_ROOT'] . '/constantes.php';
        require $_SERVER['DOCUMENT_ROOT'] . '/check_resultat_array.php';
    }
    // require $_SERVER['DOCUMENT_ROOT'] . '/licencies/constantes.php';
    // echo "Timezone actuel : " . date_default_timezone_get();

    function recupererClassements($arrAnnee, $arrLicencie) {
        foreach ($arrAnnee as $key => $value) {
            recupererClassement($value, $arrLicencie);
            // echo "n° page HTML : " . $value . " -> " . $DateMAJ;
        }
    }

    function trouveDistBlason($_strTypeConcours, $_strCategorie) {
        $_strDist = '';

        if (preg_match('/salle/i',      $_strTypeConcours)) { $_strTypeConcours = 'Tir en Salle National'; }
        if (preg_match('/fita/i',       $_strTypeConcours)) { $_strTypeConcours = 'Tir Fita National'; }
        if (preg_match('/fédéral/i',    $_strTypeConcours)) { $_strTypeConcours = 'Tir Fédéral National'; }

        if (preg_match('/poussin/i',    $_strCategorie)) { $strCat = 'Poussin'; }
        if (preg_match('/benjamin/i',   $_strCategorie)) { $strCat = 'Benjamin'; }
        if (preg_match('/minime/i',     $_strCategorie)) { $strCat = 'Minime'; }
        if (preg_match('/cadet/i',      $_strCategorie)) { $strCat = 'Cadet'; }
        if (preg_match('/junior/i',     $_strCategorie)) { $strCat = 'Junior'; }
        if (preg_match('/senior/i',     $_strCategorie)) { $strCat = 'Senior'; }
        if (preg_match('/vétéran/i',    $_strCategorie)) { $strCat = 'Vétéran'; }
        // if (preg_match('/benjamin/i',   $_strCategorie)) { $strCat = 'Benjamin'; }

        if (preg_match('/homme/i',      $_strCategorie)) { $strSex = 'Homme'; }
        if (preg_match('/femme/i',      $_strCategorie)) { $strSex = 'Femme'; }

        if (preg_match('/classique/i',  $_strCategorie)) { $strArc = 'Classique'; }
        if (preg_match('/poulies/i',    $_strCategorie)) { $strArc = 'Poulies'; }

        require_once PATH_MODELS . 'model_class_db.php';

        $dbTypeEpreuves = new ClassDb();
        $strQuery = "SELECT 
                        typ_epr_dist, typ_epr_blason
                    FROM 
                        csg_types_epreuve
                    WHERE 
                        typ_epr_type_concours = '" . $_strTypeConcours . "'
                    AND
                        typ_epr_categorie LIKE '". $strCat . "%" . $strSex . "%" . $strArc . "'
                    ;";

        echo $strQuery."<br/>";
        
        // EXECUTER LA REQUETE AVEC LES PARAMETRES VIA MODEL DB
        $result = $dbTypeEpreuves->getAllData($strQuery);
        // RECUPERER LE RESULTAT
        return $result[0]['typ_epr_dist'] . " - " . $result[0]['typ_epr_blason'];
        $dbTypeEpreuves = null;

        // Tir en Salle : 
        // Distance pour tous 18m
        // Benjamin et Minime => 60cm
        // Reste => 40cm

        // Tir à l'Arc Extérieur : 
        // Arc classique : Senior et Junior => distance 70m et 122cm
        // Arc classique : Cadet => distance 60m et 122cm
        // Arc classique : Minime => distance 40m et 80cm

        // Classement Tir en Salle National
        // Classement Tir Fédéral National
        // Classement Tir Fita National
        // Classement Tir Fita Scratch
        // Classement Tir en Campagne Scratch
        // Classement Tir en Campagne National
        // Tir à l'Arc Extérieur Scratch
        // Tir à l'Arc Extérieur National

        // Benjamin Femme Classique
        // Benjamin Homme Classique
        // Minime Femme Classique
        // Minime Homme Arc classique
        // Cadet Femme Classique
        // Cadet Homme Arc classique
        // Junior Femme Classique
        // Junior Homme Arc classique
        // Senior Femme Arc classique
        // Senior Homme Arc classique
        // Veteran Femme Arc classique
        // Veteran Homme Arc classique
        // Super Veteran Femme Arc classique
        // Super Veteran Homme Arc classique
        // Junior Femme Poulies
        // Junior Homme Poulies
        // Senior Femme Poulies
        // Senior Homme Arc a poulies
        // Vétéran Femme Poulies
        // Vétéran Homme Poulies
        // Super Vétéran Femme Poulies
        // Super Vétéran Homme Poulies
        // Cadet Homme Arc à poulies
        // Cadet Femme Arc à poulies
        // Benjamin Femme Arc Classique

        return $_strDist;
    }

    function recupererClassement($numClassement, $_arrLicencie) {
        $strURL = 'http://classements.ffta.fr/iframe/classements.html/';
        $proxy = '10.237.89.12:3128';
        $proxyauth = 'a749328:squale07';

        // $strPathExtract = "C:\\wamp64\\www\\inscript\\views\\resultats\\extracts\\classements\\";
        $strPathExtract = PATH_CLASSEMENTS;
        $strExt = ".html";

        $url = $strURL . $numClassement . '.html';
        echo "recupererClassement : " . $url . "<br/>";

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        // curl_setopt($curl, CURLOPT_PROXY, $proxy);
        // curl_setopt($curl, CURLOPT_PROXYUSERPWD, $proxyauth);
        // curl_setopt($curl, CURLOPT_COOKIESESSION, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $strURL_contenu = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        // echo $strURL_contenu;

        switch ($httpcode) {
            case 200:  # OK
                // Extraire les données de la page récupérée vers la base de données
                parserClassement($numClassement, $_arrLicencie, $strURL_contenu);
            break;
            default:
            echo 'Unexpected HTTP code: ', $httpcode, "\n";
        }        
    }

    function recupererPalmares($_strHash) {
        // . "JYq9DYAgEFNquQkAA8pUdnLnClGvME4wHJ1kaoOW7ydvwC3woGVkje0omYJgBHpWSa7SVhvQnKIIn5zbsmp0Tk0ZcL6uQP0tOfNfHcjryXdU4WwB";
        $strURL = 'http://classements.ffta.fr' . '/actions/outils/AjaxPalmares.php?act=';
        $proxy = '10.237.89.12:3128';
        $proxyauth = 'a749328:squale07';

        // $strPathExtract = "C:\\wamp64\\www\\inscript\\views\\resultats\\extracts\\classements\\";
        $strPathExtract = PATH_CLASSEMENTS;
        $strExt = ".html";

        $url = $strURL . $_strHash;
        echo "URL à traiter : " . $url . "<br/>";

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        // curl_setopt($curl, CURLOPT_PROXY, $proxy);
        // curl_setopt($curl, CURLOPT_PROXYUSERPWD, $proxyauth);
        // curl_setopt($curl, CURLOPT_COOKIESESSION, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $strURL_contenu = curl_exec($curl);
        curl_close($curl);
        // echo $strURL_contenu;

        return $strURL_contenu;
    }

    function parserClassement($_numClassement, $_arrLicencie, $_strContenu) {
        $strURL = 'http://classements.ffta.fr/iframe/classements.html/';
        $strExt = ".html";
        $strHTML = $strURL . $_numClassement . $strExt;
        echo "Classement n° " . $_numClassement . "<br/>";

        # Create a DOM parser object
        $dom = new DOMDocument();

        @$dom->loadHTML($_strContenu);

        $xpath =  new DOMXPath($dom);
        //*[@id="SelClass"]/table/tbody/tr[2]/td/table/tbody/tr/td[1]
        //*[@id="SelClass"]/table/tbody/tr[2]/td/div[1]/a[3]/span[2]
        $query_DateMAJ = $xpath->query('//form[@id="SelClass"]/table/tbody/tr[2]/td/div[1]/a[3]');
        // var_export($query_DateMAJ->length);

        if ($query_DateMAJ->length > 0) {
            $html_DateArret = str_replace(array('(',')'),'',explode(' ',$query_DateMAJ->item(0)->nodeValue)[1]);
        } else {
            $html_DateArret = 'null';
        }

        $evaluate_TypeConcours = null;
        //*[@id="SelClass"]/table/tbody/tr[2]/td/div[2]/h5
        //*[@id="SelClass"]/table/tbody/tr[2]/td/div[3]/h5
        $evaluate_TypeConcours = $xpath->evaluate('//form[@id="SelClass"]/table/tbody/tr[2]/td/div/h5[@class="mxgt"]');
        
        var_dump($evaluate_TypeConcours);

        if ($evaluate_TypeConcours['length']) {
            $query_TypeConcours = $xpath->query('//form[@id="SelClass"]/table/tbody/tr[2]/td/div/h5[@class="mxgt"]');
            // dom_dump($query_TypeConcours);
            echo "Chaine à parser : " . $query_TypeConcours->item(0)->nodeValue . "<br/>";
            $arrRslt = explode('-',$query_TypeConcours->item(0)->nodeValue);

            preg_match( '%Classement (?<typeConcours>.*) - (?<categorie>.*) (?<annee>[0-9]{4})( \((?<distance>.*)\))? - (?<nbArchers>\d*) archers%', $query_TypeConcours->item(0)->nodeValue, $arrParams );
            echo "<pre>".var_dump($arrParams)."</pre>";
            $strTypeConcours = $arrParams["typeConcours"];
            $strCategorie = $arrParams["categorie"];
            $strAnnee = $arrParams["annee"];
            if (array_key_exists("distance", $arrParams)) {
                $strDistance = $arrParams["distance"];
            } else {
                $strDistance = trouveDistBlason($strTypeConcours, $strCategorie);
            }
            $strNbArchers = $arrParams["nbArchers"];

            // echo "n° classement : ". $_numClassement . '<br/>';
            // echo "URL du classement : " . $strHTML . '<br/>';
            // echo "Date d'arrêt : " . $html_DateArret . '<br/>';
            // echo "Type Classement : " . $strTypeConcours . '<br/>';
            // echo "Année : " . $strAnnee . '<br/>';
            // echo "Catégorie : " . $strCategorie . '<br/>';
            // echo "Distance : " . $strDistance . '<br/>';
            // echo "Nbre d'archers : " . $strNbArchers . '<br/>';

            majClassement($_numClassement, $html_DateArret, $strTypeConcours, $strAnnee, $strCategorie, $strDistance, $strNbArchers);

            $liste_noms = $xpath->query('//table[@class="orbe3 full clmt keep-heights no-hover"]/tbody/tr');

            $nombre_de_noms = $liste_noms->length;
            // echo "nombre_de_noms : ".$nombre_de_noms."<br/>";

            $boolTrouve = false;
            // parcours
            for($i = 0; $i < $nombre_de_noms; $i++) {
                $td = $liste_noms->item($i);
                foreach ($td->childNodes as $value) {
                    // echo "nodeValue (i:".$i.") : ".$value->nodeValue."<br/>";
                    if (in_array(strtoupper($value->nodeValue), $_arrLicencie)) {
                        // echo "Ajout licencie : " . $value->nodeValue . "<br/>";
                        // Ajouter le licencie
                        $strLicencie = strtoupper($value->nodeValue);

                        $strXpath_HREF = '//table[@class="orbe3 full clmt keep-heights no-hover"]/tbody/tr['. (intval($i) + 1) .']/td[4]/a';
                        $xPathHREF_srch = $xpath->query($strXpath_HREF);
                        $url_Palmares = "http://classements.ffta.fr" . $xPathHREF_srch->item(0)->getAttribute('href');

                        preg_match( '%act=(.*)$%', $xPathHREF_srch->item(0)->getAttribute('href'), $arrPalmaresID );
                        // var_dump($arrPalmaresID);
                        $strPalmaresID = $arrPalmaresID[1];
                        // $strPalmaresID = $xPathHREF_srch->item(0)->getAttribute('href');
                        
                        $strXpath_Rang = '//table[@class="orbe3 full clmt keep-heights no-hover"]/tbody/tr['. (intval($i) + 1) .']/td[3]';
                        $xPathRang_srch = $xpath->query($strXpath_Rang);
                        $intRang = $xPathRang_srch->item(0)->nodeValue;

                        // return "http://classements.ffta.fr" . $url_Palmares;

                        echo "Licencie : " . $strLicencie . '<br/>';
                        echo "Année : " . $strAnnee . '<br/>';
                        // echo "URL du palmares : " . $url_Palmares . '<br/>';
                        echo "ID du palmares : " . $strPalmaresID . '<br/>';

                        majPalmares($_numClassement, $strLicencie, $strAnnee, $strPalmaresID, $intRang);

                        //traiterPalmares($strPalmaresID, $strLicencie);
                    }
                }
            }

            // "http://classements.ffta.fr" . "/actions/outils/AjaxPalmares.php?act=" . "JYq9DYAgEFNquQkAA8pUdnLnClGvME4wHJ1kaoOW7ydvwC3woGVkje0omYJgBHpWSa7SVhvQnKIIn5zbsmp0Tk0ZcL6uQP0tOfNfHcjryXdU4WwB";

        } else {
            echo "Pas de données à parser !<br/>";
        }
        $dom = null;
    }

    function recupererClassementsLicencie($_strLicencieID, $_strAnnee=null) {
        // Récupérer tout les palmares du licencié
        $arrPalmares = recupererPalmaresLicencie($_strLicencieID, $_strAnnee);

        // Pour chaque r_cla_usr_palmares
        $intNbPalmares = 0;
        foreach ($arrPalmares as $key => $value) {
            $intNbPalmares += traiterPalmares($value['r_cla_usr_palmares'], $_strLicencieID);
        }
        return $intNbPalmares;
    }

    function traiterPalmares($_strPalmaresID, $_strLicencie) {
        $domPalmares = new DOMDocument();
        @$domPalmares->loadHTML(recupererPalmares($_strPalmaresID));

        $xpathPalmares =  new DOMXPath($domPalmares);
        // Récupérer l'array() des concours du palmares
        $strXpath_Array = '//table[@id="AjaxPalmaresTableau"]/tbody/tr';
        //*[@id="AjaxPalmaresTableau"]/tbody/tr[1]
        $xPathHREF_table = $xpathPalmares->query($strXpath_Array);
        // dom_dump($xPathHREF_table);
        $intnbResult = $xPathHREF_table->length;
        // echo "Nbre Resultat : " . $intnbResult ."<br/>";
        for ($i=1; $i < $intnbResult; $i++) { 
            // echo "item (".$i.") : " . $xPathHREF_table->item($i)->nodeValue . "<br/>";
            // $td = $xPathHREF_table->item($i);
            // $j = 0;
            
            // echo "Palmares ID : " . $_strPalmaresID . "<br/>";

            // FIXME Traiter le cas d'un 2nd départ sur le même concours. Pas de HREF sur le score le plus faible
            // Créer une clé primaire associant rsl_date, rsl_nom, rsl_usr_id, rsl_usr_point

            // Récupérer l'ID ajaxEprvResultats.php?act=<...>
            $xPathHREF_href = $xpathPalmares->query('//table[@id="AjaxPalmaresTableau"]/tbody/tr['. ($i + 1) .']/td[1]/a/@href');
            if ($xPathHREF_href->length > 0) {
                preg_match( '%act=(.*)$%', $xPathHREF_href->item(0)->nodeValue, $arrResultatID );
                $strResultatID = $arrResultatID[1];
                // echo "Resultat ID : ". $strResultatID ."<br/>";
            } else {
                $strResultatID = 'null';
                // echo "Resultat ID : ". $strResultatID ."<br/>";
            }
                
            // Récupérer la date de l'épreuve
            $xPathHREF_date = $xpathPalmares->query('//table[@id="AjaxPalmaresTableau"]/tbody/tr['. ($i + 1) .']/td[1]');
            preg_match( '%(\d{2}/\d{2}/\d{4})%', $xPathHREF_date->item(0)->nodeValue, $arrDateResultat );
            $dateResultat = $arrDateResultat[0];
            // echo "date : ". $dateResultat ."<br/>";
            
            // Récupérer le nom de l'épreuve
            $xPathHREF_nom = $xpathPalmares->query('//table[@id="AjaxPalmaresTableau"]/tbody/tr['. ($i + 1) .']/td[2]');
            $strNomResultat = $xPathHREF_nom->item(0)->nodeValue;
            // echo "nom : ". $strNomResultat ."<br/>";
            
            // Récupérer la place
            $xPathHREF_rang = $xpathPalmares->query('//table[@id="AjaxPalmaresTableau"]/tbody/tr['. ($i + 1) .']/td[3]');
            $rslt_pregmatch_rang = preg_match( '%(\d+)%', $xPathHREF_rang->item(0)->nodeValue, $arrPlaceResultat );
            // echo "count rslt_pregmatc : ".count($rslt_pregmatch_rang) ."<br/>";
            if ($rslt_pregmatch_rang > 0) {
                $intPlaceResultat = $arrPlaceResultat[0];
                // echo "rang : ". $intPlaceResultat ."<br/>";
            } else {
                $intPlaceResultat = 'null';
            }
            
            // Récupérer les points
            $xPathHREF_points = $xpathPalmares->query('//table[@id="AjaxPalmaresTableau"]/tbody/tr['. ($i + 1) .']/td[4]');
            preg_match( '%(\d+)%', $xPathHREF_points->item(0)->nodeValue, $arrPointResultat );
            $intPointResultat = $arrPointResultat[0];
            // echo "points : ". $intPointResultat ."<br/>";

            // Commentaires
            $strCommentaireResultat = "";

            // Sauvegarder le tout dans la base de données
            majResultat($strResultatID, $_strPalmaresID, $dateResultat, $strNomResultat, $intPlaceResultat, $_strLicencie, $intPointResultat, $strCommentaireResultat);
        }
        // echo "Traitement de " . $intnbResult - 1 . " resultats pour le licencié " . $_strLicencie .".<br/>";
        return $intnbResult - 1;
    }

    function dom_test($DOM) {
        echo '<h1>'.get_class($DOM).'</h1>';
        // easiest way to traverse:
        echo 'LENGTH: '. @$DOM->length ."\n"; // if NodeList
        echo 'TAG: '. @$DOM->tagName ."\n"; // if Element
        echo 'CHILDS: '. @$DOM->childNodes->length ."\n"; // etc.
    }

    function dom_dump($DOM) {
        $i = 0;
        foreach ($DOM as $node) {
            echo "node (".$i.") : ".$node->nodeName . "<br/>";
            echo "node (".$i.") : ".$node->nodeValue . "<br/>";
            // echo "node (".$i.") : ".$node->getAttribute('href') . "<br/>";
            $i++;
        }
    }

    function recupererPalmaresLicencie($_strLicencieID, $_strAnnee=null) {
        require_once PATH_MODELS . 'model_class_db.php';
        
        if ($_strAnnee == null) {
            $strClauseAnnee = "";
        } else {
            $strClauseAnnee = " AND r_cla_usr_annee = " . $_strAnnee;
        }

        $dbPalmares = new ClassDb();
        $strQuery = "SELECT 
                    r_cla_usr_palmares
                    FROM csg_rel_classement_user
                    WHERE usr_id = '" . $_strLicencieID . "'".
                    $strClauseAnnee .
                    ";";
        // echo $strQuery;
        // EXECUTER LA REQUETE AVEC LES PARAMETRES VIA MODEL DB
        $result = $dbPalmares->getAllData($strQuery);
        // RECUPERER LE RESULTAT
        return $result;
        $dbPalmares = null;
    }

    function majResultat($_strResultatID, $_strPalmaresID, $_dateResultat, $_strNomResultat, $_intPlaceResultat, $_strLicencieResultat, $_intPointResultat, $_strCommentaireResultat) {
        require_once PATH_MODELS . 'model_class_db.php';

        if ($_dateResultat != 'null') {
            // $dateArret = new DateTime($_dateResultat);
            $dateResultat = DateTime::createFromFormat('d/m/Y',$_dateResultat);
            $strdateResultat = date_format($dateResultat, 'Y-m-d');
        } else {
            $strdateResultat = 'null';
        }

        $dbResultat = new ClassDb();
        // REQUETE
        // $strQuery = "UPDATE csg_rel_epreuve_user 
        //                 SET 
        //                 r_epr_usr_inscript = ?
        //                 WHERE 
        //                 epr_id = ?
        //                 AND 
        //                 usr_login = ?
        //                 AND
        //                 r_epr_usr_depart = ?;
        //             ";
        $strQuery = "INSERT INTO csg_resultats 
                        (
                            rsl_id,
                            r_cla_usr_palmares,
                            rsl_date,
                            rsl_nom,
                            rsl_usr_place,
                            rsl_usr_id,
                            rsl_usr_point,
                            rsl_usr_commentaires
                        )
                    VALUES
                        (
                            '". $_strResultatID . "', 
                            '". $_strPalmaresID . "', 
                            '". $strdateResultat . "', 
                            '". addslashes($_strNomResultat) . "',
                            ". $_intPlaceResultat . ",
                            '". $_strLicencieResultat . "',
                            ". $_intPointResultat . ",
                            '". addslashes($_strCommentaireResultat) . "'
                        )
                    ;";
        // echo $strQuery."<br/>";
        // EXECUTER LA REQUETE AVEC LES PARAMETRES VIA MODEL DB
        $result = $dbResultat->insertRow($strQuery, array());
        echo "Enregistrement de ".$_strNomResultat ." du ".$strdateResultat."<br/>";
        return $dbResultat->queryOK;
        $dbResultat = null;
    }

    function majPalmares($_intClassementID, $_strLicencie, $_intAnnee, $_strPalmaresID, $_intRang) {
        require_once PATH_MODELS . 'model_class_db.php';

        $dbPalmares = new ClassDb();
        // REQUETE
        // $strQuery = "UPDATE csg_rel_epreuve_user 
        //                 SET 
        //                 r_epr_usr_inscript = ?
        //                 WHERE 
        //                 epr_id = ?
        //                 AND 
        //                 usr_login = ?
        //                 AND
        //                 r_epr_usr_depart = ?;
        //             ";
        $strQuery = "INSERT INTO csg_rel_classement_user 
                        (
                            cla_id,
                            usr_id,
                            r_cla_usr_annee,
                            r_cla_usr_palmares,
                            r_cla_usr_rang
                        )
                    VALUES
                        (
                            ". $_intClassementID . ", 
                            '". $_strLicencie . "', 
                            ". $_intAnnee . ", 
                            '". $_strPalmaresID . "',
                            ". $_intRang . "
                        )
                    ON DUPLICATE KEY UPDATE
                        r_cla_usr_annee = ". $_intAnnee . ", 
                        r_cla_usr_palmares = '". $_strPalmaresID . "',
                        r_cla_usr_rang = ". $_intRang . "
                    ;";
        echo $strQuery."<br/>";
        // EXECUTER LA REQUETE AVEC LES PARAMETRES VIA MODEL DB
        $result = $dbPalmares->insertRow($strQuery, array());

        return $dbPalmares->queryOK;
        $dbPalmares = null;
    }

    function majClassement($_intClassementID, $_strDateArret, $_strType, $_intAnnee, $_strCateg, $_strDistance, $_intNbArcher) {
        require_once PATH_MODELS . 'model_class_db.php';

        if ($_strDateArret != 'null') {
            // $dateArret = new DateTime($_strDateArret);
            $dateArret = DateTime::createFromFormat('d/m/Y',$_strDateArret);
            $strdateArret = "'". date_format($dateArret, 'Y-m-d') . "'";
        } else {
            $strdateArret = 'null';
        }

        // NOUVELLE INSTANCE DE MODEL DB
        $dbClassement = new ClassDb();
        // REQUETE
        // $strQuery = "UPDATE csg_rel_epreuve_user 
        //                 SET 
        //                 r_epr_usr_inscript = ?
        //                 WHERE 
        //                 epr_id = ?
        //                 AND 
        //                 usr_login = ?
        //                 AND
        //                 r_epr_usr_depart = ?;
        //             ";
        $strQuery = "INSERT INTO csg_classements 
                        (
                            cla_id,
                            cla_date_arret,
                            cla_type,
                            cla_annee,
                            cla_categ,
                            cla_distance,
                            cla_nb_archers
                        )
                    VALUES
                        (
                            ". $_intClassementID . ", 
                            ". $strdateArret . ", 
                            '". str_replace("'", "\'", $_strType) . "', 
                            ". $_intAnnee . ", 
                            '". $_strCateg . "',
                            '". $_strDistance . "', 
                            ". $_intNbArcher . "
                        )
                    ON DUPLICATE KEY UPDATE
                        cla_date_arret = ". $strdateArret . ",
                        cla_distance = '". $_strDistance . "',
                        cla_nb_archers = ". $_intNbArcher . "
                    ;";
        echo "majClassement : <br/>" . $strQuery . '<br/>';
        // EXECUTER LA REQUETE AVEC LES PARAMETRES VIA MODEL DB
        $result = $dbClassement->insertRow($strQuery, array());

        return $dbClassement->queryOK;
        echo "majClassement => " . $dbClassement->queryOK . "<br/>";
        $dbClassement = null;
    }

    function recupererLicencies() {
        require_once PATH_MODELS . 'model_class_db.php';

        $dbUsers = new ClassDb();
        $strQuery = "SELECT 
                    usr_login
                    FROM csg_users
                    ;";
        // echo $strQuery;
        // EXECUTER LA REQUETE AVEC LES PARAMETRES VIA MODEL DB
        $result = $dbUsers->getAllData($strQuery);
        // RECUPERER LE RESULTAT
        return $result;
        $dbUsers = null;
    }


    $arrLicencie = array();
    $arrLicencieBrute = recupererLicencies();
    foreach ($arrLicencieBrute as $key => $value) {
        array_push($arrLicencie, $value['usr_login']);
    }
    
    // URL pour récupérer les n° des Classements
    //   http://classements.ffta.fr/iframe/classements.html#
    // Choisir l'année puis inspecter un élément

    // Parametres
    // - recupererClassements=2018 
    //    - Définir les tableau des classements en fonction des années
    //      ex : http://inscript/check_resultat_db.php?cle=a14axQHk8JA6dfpjNmwPoRpCvcTQg5WG8pM6N6Tzmo75XcgIoy&recupererClassements=2018
    // - recupererClassementsLicencie=918516M
    //    - Mets à jour les fichiers de Palmares du licencié
    //      ex : http://inscript/check_resultat_db.php?cle=a14axQHk8JA6dfpjNmwPoRpCvcTQg5WG8pM6N6Tzmo75XcgIoy&recupererClassementsLicencie=918516M
    //    - Mets à jour les fichiers de Palmares du licencié de l'année 2018
    //      ex : http://inscript/check_resultat_db.php?cle=a14axQHk8JA6dfpjNmwPoRpCvcTQg5WG8pM6N6Tzmo75XcgIoy&recupererClassementsLicencie=918516M&annee=2018

    if (isset($_GET['cle'])){
        if ($_GET['cle'] == 'a14axQHk8JA6dfpjNmwPoRpCvcTQg5WG8pM6N6Tzmo75XcgIoy') {

            if (isset($_GET['recupererClassements'])) {
                switch ($_GET['recupererClassements']) {
                    case '2020':
                        recupererClassements($arr2020, $arrLicencie);
                        break;
                    case '2019':
                        recupererClassements($arr2019, $arrLicencie);
                        break;
                    case '2018':
                        recupererClassements($arr2018, $arrLicencie);
                        break;
                    case '2017':
                        recupererClassements($arr2017, $arrLicencie);
                        break;
                    case '2016':
                        recupererClassements($arr2016, $arrLicencie);
                        break;
                    case '2015':
                        recupererClassements($arr2015, $arrLicencie);
                        break;
                    
                    default:
                        # code...
                        break;
                }
                echo "Année " . $_GET['recupererClassements'] . " récupérée.";
            }

            if (isset($_GET['recupererClassementsLicencie'])) {
                if (isset($_GET['annee'])) { $strAnnee = $_GET['annee']; } else { $strAnnee = null; }
                $intNbResultat = recupererClassementsLicencie($_GET['recupererClassementsLicencie'], $strAnnee);
                echo "Mise à jour de ". $intNbResultat . " résultats pour le licencié n° " . $_GET['recupererClassementsLicencie'];
                echo "<br>";
                echo "<br>";
                echo "Cliquez sur le bouton <a class='button' href='http://csg-tiralarc.fr/index.php?controller=resultat&action=Afficher'>Afficher les résultats</a> pour recharger les modifications.";
                echo "<br>";
                echo "<br>";
                echo "Si vous ne voyez pas votre nouveau résultat, attendez le mercredi après le concours pour que la mise à jour du site de la Fédération soit faite."."<br>";
                echo "Ensuite vous pourrez réessayer..."."<br>";
                echo "Si vous ne voyez toujours pas d'évolutions, contacter votre administrateur WEB.";
            }
        } else {
            echo "La clé n'est pas conforme !";
        }
    } else {
        echo "La clé est absente !";
    }
?>    
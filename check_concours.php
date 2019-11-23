<?php
    // Ce fichier sera lancé par crontab tous les jours à 03h00

    // Ce fichier permet de :
    //  - récupérer en base la liste des concours dont la date de début est inférieur à aujourd'hui plus delta (à voir avec le club en fonction des saisons)
    //  - Pour chaque concours de cette liste :
    //      - récupérer la liste des participants et de leurs départs
    //      - générer un mail avec cette liste au format prévu (participant, nom, prénom, catégorie, n° de licence, départ(s))
    //      - envoyer ce mail à l'adresse mail du club organisateur (prévoir une alerte si l'adresse mail n'est pas renseignée 15 jours avant le delta pour en informer l'administrateur)
    //      - si le mail est bien envoyé :
    //          - renseigner la date d'inscription au concours pour chaque départ de chaque inscrit (prévoir de changer la couleur du tableau) dans la base de données
    //          - envoyer une notification à chaque participant (via le flux choisi SMS (attention à l'heure) ou Mail)
    //          - envoyer un mail à l'inscripteur pour rappel d'envoi de chèque
    //          - envoyer un mail au trésorier pour information sur le montant de laparticipation
    //      - si le mail n'a pas pu être envoyé :
    //          - alerter l'administrateur pour qu'il s'occupe manuellement de tout le processus

// Requete pour récupérer la liste des concours dont la date de début est inférieur à aujourd'hui plus delta (à voir avec le club en fonction des saisons)
// $strRequete = "SELECT * FROM csg_epreuves WHERE epr_date_deb BETWEEN (NOW() - INTERVAL " . $intJrsPre_Inscript . " DAY) AND NOW() ORDER BY epr_date_deb, epr_orga";

// Requete pour récupérer la liste des inscrits et leurs départ pour un concours qui n'ont pas été déjà inscrits
// $strRequete = "SELECT * FROM csg_rel_epreuve_user WHERE epr_id = 100 AND r_epr_usr_inscript IS NULL;

// Requete pour modifier l'état des concours :
    // A l'état Ouvert : 
    // les inscriptions sont toujours possibles via le formulaire WEB
    // UPDATE csg_epreuves SET epr_etat = "Ouvert" WHERE epr_date_deb > NOW() + INTERVAL 15 DAY;
    // A l'état Préinscription :
    // Les inscriptions ne sont plus possible car la demande de préinscriptions à été faite auprès du club organiateur
    // UPDATE csg_epreuves SET epr_etat = "Préinscription" WHERE epr_date_deb BETWEEN NOW() AND NOW() + INTERVAL 15 DAY;
    // A l'état Validé :
    // Les inscriptions sont validées par le club organisateur et l'inscripteur a validé l'inscription dans la base
    // Fait lors de la validation par l'inscripteur
    // UPDATE csg_epreuves SET epr_etat = "Validé" WHERE epr_etat = "Préinscription" AND epr_id = '<n° du concours>';
    // A l'état Clos :
    // Le concours est passé
    // UPDATE csg_epreuves SET epr_etat = "Clos" WHERE epr_date_deb <= NOW();

if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/licencies')) {
    // Config finale
    // require_once $_SERVER['DOCUMENT_ROOT'] . '/licencies/config_local.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/licencies/constantes.php';
} else {
    // Config Debug
    // require_once $_SERVER['DOCUMENT_ROOT'] . '/config_local.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/constantes.php';
}
// require_once 'constantes.php';
require_once PATH_MODELS . 'model_class_user.php';
require_once PATH_MODELS . 'model_class_db.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($debug) {
    echo "Timezone actuel : " . date_default_timezone_get();
    echo "<h3> PHP List All Post Variables</h3>";
    var_dump($_POST);
    echo "<h3> PHP List All Server Variables</h3>";
    var_dump($_SERVER);
}

// $levelLog = 2;

// Niveau de Log demandé parmi INFO, ERROR, CRITIC
$arrLevel = array("INFO", "ALERT", "ERROR");

// $intJrsPre_Inscript = 80;

    function logging($str, $level, $func=null) {
        global $arrLevel;

        if (in_array($level, $arrLevel)) {
            $log = date('Y-m-d H:i:s')."\t".str_pad($level,5)."\t".str_pad($str,90)."\t".$_SERVER['PHP_SELF']."/".$func.PHP_EOL;
            
            $fileLog = fopen(PATH_LOGS . 'check_concours_'.date('Ymd').'.log', 'a+');
            fwrite($fileLog, $log);
            fclose($fileLog);
            echo $log . '<br>';
        }
    }

    function formaterDateConcours($_strDateDeb, $_strDateFin) {
        $dateDateDeb = new DateTime($_strDateDeb);
        $dateDateFin = new DateTime($_strDateFin);
        // Si le debut et la fin du concours sont identiques
        if ($dateDateDeb == $dateDateFin) {
            return "le " . date_format($dateDateDeb, 'd/m/Y');
        } else {
            return "du " . date_format($dateDateDeb, 'd/m/Y') . " au " . date_format($dateDateFin, 'd/m/Y');
        }
    }

    function formaterTableauParticipants($_strTypeEpreuve, $_arrParticipants) {
        global $debug;

        $strListeParticipants = "";
        
        $intPad_H = 10;
        $intPad_V = 5;

        if ($debug) {
            echo "<pre>";
            print_r($_arrParticipants);
            echo "</pre>";
        }

        switch ($_strTypeEpreuve) {
            case 'SALLE':
                foreach ($_arrParticipants as $key => $rowInscrit) {
                    $strListeParticipants .= "<tr>";
                    $strListeParticipants .= "<td style='padding:". $intPad_V ." ".$intPad_H.";'>" . $rowInscrit['usr_lastname'] . "</td>";
                    $strListeParticipants .= "<td style='padding:". $intPad_V ." ".$intPad_H.";'>" . $rowInscrit['usr_firstname'] . "</td>";
                    $strListeParticipants .= "<td style='text-align:center; padding:". $intPad_V ." ".$intPad_H.";'>" . $rowInscrit['usr_login'] . "</td>";
                    $strListeParticipants .= "<td style='text-align:center; padding:". $intPad_V ." ".$intPad_H.";'>" . $rowInscrit['usr_cat'] . "</td>";
                    $strListeParticipants .= "<td style='padding:". $intPad_V ." ".$intPad_H.";'>" . $rowInscrit['epr_discipline'] . "</td>";
                    $strListeParticipants .= "<td style='padding:". $intPad_V ." ".$intPad_H.";'>" . str_replace('_',' ',$rowInscrit['r_epr_usr_depart']) . "</td>";
                    $strListeParticipants .= "<td style='padding:". $intPad_V ." ".$intPad_H.";'>" . $rowInscrit['r_epr_usr_blason'] . "</td>";
                    $strListeParticipants .= "</tr>";
                }
                break;
                case 'TAE':
                foreach ($_arrParticipants as $key => $rowInscrit) {
                    $strListeParticipants .= "<tr>";
                    $strListeParticipants .= "<td style='padding:". $intPad_V ." ".$intPad_H.";'>" . $rowInscrit['usr_lastname'] . "</td>";
                    $strListeParticipants .= "<td style='padding:". $intPad_V ." ".$intPad_H.";'>" . $rowInscrit['usr_firstname'] . "</td>";
                    $strListeParticipants .= "<td style='text-align:center; padding:". $intPad_V ." ".$intPad_H.";'>" . $rowInscrit['usr_login'] . "</td>";
                    $strListeParticipants .= "<td style='text-align:center; padding:". $intPad_V ." ".$intPad_H.";'>" . $rowInscrit['usr_cat'] . "</td>";
                    $strListeParticipants .= "<td style='padding:". $intPad_V ." ".$intPad_H.";'>" . $rowInscrit['epr_discipline'] . "</td>";
                    $strListeParticipants .= "<td style='padding:". $intPad_V ." ".$intPad_H.";'>" . str_replace('_',' ',$rowInscrit['r_epr_usr_depart']) . "</td>";
                    // en fonction de la catégorie de l'archer et du type de départ choisi (Champ. ou Coupe), trouver le couple distance/blason correpsondant
                    $strListeParticipants .= "<td style='padding:". $intPad_V ." ".$intPad_H.";'>" . $rowInscrit['r_epr_usr_type_epr'] . "</td>";
                    $strListeParticipants .= "</tr>";
                }
                break;
            default:
                # code...
                break;
        }

        return $strListeParticipants;
    }

    function formaterTableauConcours($_arrConcours) {
        global $debug;

        $strListeConcours = "";
        
        $intPad_H = 10;
        $intPad_V = 5;

        if ($debug) {
            echo "<pre>";
            print_r($_arrConcours);
            echo "</pre>";
        }
        
        $ind = 1;

        foreach ($_arrConcours as $key => $rowConcours) {
            $strDate = strftime('%#d %b %Y',strtotime($rowConcours['r_epr_date']));
            $strListeConcours .= "<tr>";
            $strListeConcours .= "<td style='text-align:center; padding:". $intPad_V ." ".$intPad_H.";'>" . $ind . "</td>";
            $strListeConcours .= "<td style='text-align:center; padding:". $intPad_V ." ".$intPad_H.";'>" . $rowConcours['epr_discipline'] . "</td>";
            $strListeConcours .= "<td style='text-align:center; padding:". $intPad_V ." ".$intPad_H.";'>" . $strDate . "</td>";
            $strListeConcours .= "<td style='text-align:center; padding:". $intPad_V ." ".$intPad_H.";'>" . str_replace('_',' ',$rowConcours['r_epr_usr_depart']) . "</td>";
            switch ($rowConcours['epr_discipline_norme']) {
                case 'SALLE':
                    $strListeConcours .= "<td style='text-align:center; padding:". $intPad_V ." ".$intPad_H.";'>" . $rowConcours['r_epr_usr_blason'] . "</td>";
                    break;
                case 'TAE':
                    $strListeConcours .= "<td style='text-align:center; padding:". $intPad_V ." ".$intPad_H.";'>" . $rowConcours['r_epr_usr_type_epr'] . "</td>";
                    break;
            }
            $strListeConcours .= "<td style='text-align:center; padding:". $intPad_V ." ".$intPad_H.";'>" . $rowConcours['epr_lieu'] . "</td>";
            $strListeConcours .= "</tr>";
            $ind++;
        }

        return $strListeConcours;
    }

    function formaterTableauConcoursARenseigner($_arrConcours) {
        global $debug;

        $strListeConcours = "";
        
        $intPad_H = 10;
        $intPad_V = 5;

        if ($debug) {
            echo "<pre>";
            print_r($_arrConcours);
            echo "</pre>";
        }
        
        foreach ($_arrConcours as $key => $rowConcours) {
            $strListeConcours .= "<tr>";
            $strListeConcours .= "<td style='text-align:center; padding:". $intPad_V ." ".$intPad_H.";'>" . $rowConcours['epr_id'] . "</td>";
            $strListeConcours .= "<td style='text-align:center; padding:". $intPad_V ." ".$intPad_H.";'>" . $rowConcours['epr_discipline'] . "</td>";
            $strListeConcours .= "<td style='text-align:center; padding:". $intPad_V ." ".$intPad_H.";'>" . $rowConcours['epr_date_deb'] . "</td>";
            $strListeConcours .= "<td style='text-align:center; padding:". $intPad_V ." ".$intPad_H.";'>" . $rowConcours['epr_lieu'] . "</td>";
            $strListeConcours .= "</tr>";
        }

        return $strListeConcours;
    }

    function formaterTableauConcoursAPayer($_arrConcours) {
        global $debug;

        $strListeConcours = "";
        
        $intPad_H = 10;
        $intPad_V = 5;

        if ($debug) {
            echo "<pre>";
            print_r($_arrConcours);
            echo "</pre>";
        }
        
        foreach ($_arrConcours as $key => $rowConcours) {
            $strListeConcours .= "<tr>";
            $strListeConcours .= "<td style='text-align:center; padding:". $intPad_V ." ".$intPad_H.";'>" . $rowConcours['epr_id'] . "</td>";
            $strListeConcours .= "<td style='text-align:center; padding:". $intPad_V ." ".$intPad_H.";'>" . $rowConcours['epr_discipline'] . "</td>";
            $strListeConcours .= "<td style='text-align:center; padding:". $intPad_V ." ".$intPad_H.";'>" . $rowConcours['epr_date_deb'] . "</td>";
            $strListeConcours .= "<td style='text-align:center; padding:". $intPad_V ." ".$intPad_H.";'>" . $rowConcours['epr_lieu'] . "</td>";
            $strListeConcours .= "<td style='text-align:center; padding:". $intPad_V ." ".$intPad_H.";'>" . $rowConcours['usr_login'] . "</td>";
            $strListeConcours .= "<td style='text-align:center; padding:". $intPad_V ." ".$intPad_H.";'>" . $rowConcours['r_epr_usr_depart'] . "</td>";
            $strListeConcours .= "<td style='text-align:center; padding:". $intPad_V ." ".$intPad_H.";'>" . $rowConcours['r_epr_date'] . "</td>";
            $strListeConcours .= "</tr>";
        }

        return $strListeConcours;
    }

    function formaterTableauDepartsAValider($_arrConcours) {
        global $debug;

        $strListeConcours = "";
        
        $intPad_H = 10;
        $intPad_V = 5;

        if ($debug) {
            echo "<pre>";
            print_r($_arrConcours);
            echo "</pre>";
        }
        
        foreach ($_arrConcours as $key => $rowConcours) {
            $strListeConcours .= "<tr>";
            $strListeConcours .= "<td style='text-align:center; padding:". $intPad_V ." ".$intPad_H.";'>" . $rowConcours['usr_login'] . "</td>";
            $strListeConcours .= "<td style='text-align:center; padding:". $intPad_V ." ".$intPad_H.";'>" . $rowConcours['r_epr_usr_depart'] . "</td>";
            $strListeConcours .= "<td style='text-align:center; padding:". $intPad_V ." ".$intPad_H.";'>" . $rowConcours['r_epr_date'] . "</td>";
            $strListeConcours .= "<td style='text-align:center; padding:". $intPad_V ." ".$intPad_H.";'>" . $rowConcours['epr_id'] . "</td>";
            $strListeConcours .= "<td style='text-align:center; padding:". $intPad_V ." ".$intPad_H.";'>" . $rowConcours['epr_discipline'] . "</td>";
            $strListeConcours .= "<td style='text-align:center; padding:". $intPad_V ." ".$intPad_H.";'>" . $rowConcours['epr_date_deb'] . "</td>";
            $strListeConcours .= "<td style='text-align:center; padding:". $intPad_V ." ".$intPad_H.";'>" . $rowConcours['epr_lieu'] . "</td>";
            $strListeConcours .= "</tr>";
        }

        return $strListeConcours;
    }

    function listeConcours($_intJrsPre_Inscript) {
        // NOUVELLE INSTANCE DE MODEL DB
        $dbConcours = new ClassDb();
        // REQUETE
        $strQuery = "SELECT 
                    *
                    FROM csg_epreuves
                    WHERE epr_date_deb BETWEEN NOW() AND NOW() + INTERVAL " . $_intJrsPre_Inscript . " DAY
                    AND epr_etat <> 'Préinscription'
                    AND epr_mandat <> ''
                    ;";
        // echo $strQuery;
        // EXECUTER LA REQUETE AVEC LES PARAMETRES VIA MODEL DB
        $result = $dbConcours->getAllData($strQuery);
        // RECUPERER LE RESULTAT
        return $result;
        $dbConcours = null;
    }

    function listeConcoursARenseigner($_intJrsRenseigner) {
        logging("Recherche les concours à renseigner", "INFO", "listeConcoursARenseigner");
        // NOUVELLE INSTANCE DE MODEL DB
        $dbConcours = new ClassDb();
        // REQUETE
        $strQuery = "SELECT DISTINCT 
                        E.epr_id,
                        E.epr_discipline,
                        E.epr_date_deb,
                        E.epr_lieu
                    FROM 
                        csg_epreuves AS E,
                        csg_rel_epreuve_user AS R
                    WHERE 
                        E.epr_date_deb BETWEEN NOW() AND NOW() + INTERVAL " . $_intJrsRenseigner . " DAY
                    AND 
                        E.epr_id = R.epr_id
                    AND 
                        E.epr_etat <> 'Clos'
                    AND (
                        E.epr_mail = ''
                        OR E.epr_adulte_first_dep = 0
                        OR E.epr_jeune_first_dep = 0
                        OR R.r_epr_usr_age = ''
                        )
                    ORDER BY epr_date_deb
                    ;";
        // echo $strQuery;
        // EXECUTER LA REQUETE AVEC LES PARAMETRES VIA MODEL DB
        $result = $dbConcours->getAllData($strQuery);
        logging($dbConcours->queryNbRows . " concours sont à renseigner", "INFO", "listeConcoursARenseigner");
        // RECUPERER LE RESULTAT
        logging("Fin de recherche les concours à renseigner", "INFO", "listeConcoursARenseigner");
        return $result;
        $dbConcours = null;
    }

    function listeConcoursNonPayes() {
        logging("Recherche les concours non payés", "INFO", "listeConcoursNonPayes");
        $dbConcours = new ClassDb();
        // REQUETE
        $strQuery = "SELECT 
                        E.epr_id,
                        E.epr_discipline,
                        E.epr_date_debut,
                        E.epr_lieu,
                        R.usr_login,
                        R.epr_usr_depart,
                        R.epr_date
                    FROM 
                        csg_rel_epreuve_user AS R,
                        csg_epreuves AS E
                    WHERE
                        R.epr_id = E.epr_id
                    AND 
                        E.epr_date_fin > NOW()
                    AND
                        R.r_epr_usr_inscript IS NOT NULL
                    AND 
                        R.r_epr_usr_num_cheque = ''
                    ;";
        // echo $strQuery;
        // EXECUTER LA REQUETE AVEC LES PARAMETRES VIA MODEL DB
        $result = $dbConcours->getAllData($strQuery);
        logging($dbConcours->queryNbRows . " concours sont non payés", "INFO", "listeConcoursNonPayes");
        // RECUPERER LE RESULTAT
        logging("Fin de recherche les concours non payés", "INFO", "listeConcoursNonPayes");
        return $result;
        $dbConcours = null;
    }

    function listeDepartsNonValides() {
        logging("Recherche les concours non validés pour le participant", "INFO", "listeDepartsNonValides");
        $dbConcours = new ClassDb();
        // REQUETE
        $strQuery = "SELECT 
                        R.epr_id,
                        E.epr_discipline,
                        E.epr_date_deb,
                        E.epr_lieu,
                        R.usr_login,
                        R.r_epr_usr_depart,
                        R.r_epr_date
                    FROM 
                        csg_rel_epreuve_user AS R,
                        csg_epreuves AS E
                    WHERE
                        R.epr_id = E.epr_id
                    AND 
                        E.epr_date_fin > NOW()
                    AND
                        R.r_epr_usr_inscript IS NOT NULL
                    AND 
                        R.r_epr_usr_num_cheque <> ''
                    AND 
                        R.r_epr_usr_valide IS NULL
                    ORDER BY 
                        E.epr_date_deb ASC,
                        E.epr_lieu ASC,
                        R.r_epr_date ASC,
                        R.usr_login ASC
                    ;";
        // echo $strQuery;
        // EXECUTER LA REQUETE AVEC LES PARAMETRES VIA MODEL DB
        $result = $dbConcours->getAllData($strQuery);
        logging($dbConcours->queryNbRows . " concours sont non validés pour le participant", "INFO", "listeDepartsNonValides");
        logging("Fin de recherche les concours non validés pour le participant", "INFO", "listeDepartsNonValides");
        return $result;
        $dbConcours = null;
    }

    function listeParticipants($_intIDConcours) {
        // NOUVELLE INSTANCE DE MODEL DB
        $dbInscrits = new ClassDb();
        // REQUETE
        $strQuery = "SELECT
                        R.epr_id,
                        R.usr_login,
                        R.r_epr_usr_depart,
                        R.r_epr_date,
                        U.usr_firstname,
                        U.usr_lastname,
                        U.usr_cat,
                        E.epr_discipline,
                        R.r_epr_usr_blason,
                        R.r_epr_usr_type_epr
                        FROM 
                            csg_rel_epreuve_user AS R,
                            csg_users AS U,
                            csg_epreuves AS E
                        WHERE 
                            U.usr_login = R.usr_login
                        AND
                            E.epr_id = R.epr_id
                        AND
                            R.epr_id = " . $_intIDConcours . " 
                        AND 
                            r_epr_usr_inscript IS NULL;";
        // echo $strQuery;
        // EXECUTER LA REQUETE AVEC LES PARAMETRES VIA MODEL DB
        $result = $dbInscrits->getAllData($strQuery);
        // RECUPERER LE RESULTAT
        return $result;
        $dbInscrits = null;
    }

    function listeDepartsSansAge() {
        // NOUVELLE INSTANCE DE MODEL DB
        $dbDeparts = new ClassDb();
        // REQUETE
        $strQuery = "SELECT DISTINCT 
                        epr_id, usr_login 
                    FROM 
                        csg_rel_epreuve_user 
                    WHERE 
                        r_epr_usr_age = '' 
                    OR 
                        r_epr_usr_age IS NULL;";
        // echo $strQuery;
        // EXECUTER LA REQUETE AVEC LES PARAMETRES VIA MODEL DB
        $result = $dbDeparts->getAllData($strQuery);
        // RECUPERER LE RESULTAT
        return $result;
        $dbDeparts = null;
    }

    function recupererCategEpreuve($_intIDConcours) {
        // NOUVELLE INSTANCE DE MODEL DB
        $dbCategories = new ClassDb();
        // REQUETE
        $strQuery = "SELECT 
                        epr_cat_adulte 
                    FROM 
                        csg_epreuves 
                    WHERE 
                        epr_id = " . $_intIDConcours . ";";
        // echo $strQuery;
        // EXECUTER LA REQUETE AVEC LES PARAMETRES VIA MODEL DB
        $result = $dbCategories->getAllData($strQuery);
        // RECUPERER LE RESULTAT
        if ($dbCategories->queryOK) {
            return $result[0]['epr_cat_adulte'];
        } else {
            return false;
        }
        $dbCategories = null;
    }

    function recupererCategLicencie($_strLicencieID) {
        // NOUVELLE INSTANCE DE MODEL DB
        $dbLicencie = new ClassDb();
        // REQUETE
        $strQuery = "SELECT 
                        usr_cat_simple 
                    FROM 
                        csg_users 
                    WHERE 
                        usr_login = '" . $_strLicencieID . "';";
        // echo $strQuery;
        // EXECUTER LA REQUETE AVEC LES PARAMETRES VIA MODEL DB
        $result = $dbLicencie->getAllData($strQuery);
        // RECUPERER LE RESULTAT
        return $result[0]['usr_cat_simple'];
        $dbLicencie = null;
    }

    function majAgeDepart($_strTypeAge, $_intIDConcours, $_strLicencieID) {
        // NOUVELLE INSTANCE DE MODEL DB
        $dbDepart = new ClassDb();
        // REQUETE
        $strQuery = "UPDATE csg_rel_epreuve_user
                    SET 
                        r_epr_usr_age = ?
                    WHERE 
                        epr_id = ?
                    AND
                        usr_login = ?
                    ;";
        // echo $strQuery;
        // EXECUTER LA REQUETE AVEC LES PARAMETRES VIA MODEL DB
        $result = $dbDepart->updateRow($strQuery, array($_strTypeAge, $_intIDConcours, $_strLicencieID));

        return $dbDepart->queryOK;
        $dbDepart = null;
    }

    function envoiMail($strDestNom, $strDestMail, $strReplyNom, $strReplyMail, $strMessage, $strSujet, $arrICS=null) {
        require_once PATH_ROOT. 'phpmailer/PHPMailer.php';
        require_once PATH_ROOT. 'phpmailer/SMTP.php';
        require_once PATH_ROOT. 'phpmailer/Exception.php';

        $mail = new PHPMailer(true);

        try {
            $mail->SMTPDebug = 3;                                          // Enable verbose debug output
            $mail->Debugoutput = function($str, $level) {
                $log = date('Y-m-d H:i:s')."\t".str_pad($level,5)."\t".str_pad($str,120)."\t".$_SERVER['PHP_SELF'].PHP_EOL;
                $fileLog = fopen(PATH_LOGS . 'mail_inscription_'.date('Ymd').'.log', 'a+');
                fwrite($fileLog, $log);
                fclose($fileLog);
            };
    
            $mail->isSMTP();                                               // Set mailer to use SMTP
            $mail->Host = 'send.one.com';                                  // Specify main and backup SMTP servers
            $mail->SMTPAuth = true;                                        // Enable SMTP authentication
            $mail->Username = 'squaledev@grippon.fr';                      // SMTP username
            $mail->Password = 'RvY96MJLfbBAPOxbvm91';                      // SMTP password
            $mail->SMTPSecure = 'tls';                                     // Enable TLS encryption, `ssl` also accepted
            $mail->Port = 587;                                             // TCP port to connect to
    
            // Expéditeur
            // l'expéditeur doit être la bonne mail du send.one.com
            $mail->setFrom('squaledev@grippon.fr', 'CSG Tir à l\'arc Inscription');
            // Destinataire
            $mail->addAddress($strDestMail, $strDestNom);                  // Add a recipient
            // $mail->addAddress('ellen@example.com');                     // Name is optional
            // Adresse de retour
            if ($strReplyMail <> '') {
                $mail->addReplyTo($strReplyMail, $strReplyNom);
            }
            // $mail->addCC('cc@example.com');
            $mail->addBCC('webadmin@csg-tiralarc.fr');
    
            // Modifier l'encodage du mail
            $mail->CharSet = "utf-8";
            // Ajouter les fichiers ICS en pièce jointe
            if (!is_null($arrICS)) {
                $index = 1;
                foreach ($arrICS as $key => $strICS) {
                    $mail->addStringAttachment($strICS,'depart_'.$index.'.ics','base64','text/calendar');
                    $index++;
                }
            }
            // Modifier le format du mail en HTML
            $mail->isHTML(true);
    
            // Objet
            $mail->Subject = $strSujet;
            $mail->Body    = $strMessage;
            
            if(!$mail->send()) {
                return false;
                echo $strSujet . ": false<br>";
            } else {
                return true;
                echo $strSujet . ": true<br>";
            }
        } catch (phpmailerException $e) {
            logging("Erreur phpMailer lors de l'envoi du mail : " . $e->errorMessage(), "ERROR", "envoiMail");
        } catch (Exception $e) {
            logging("Erreur lors de l'envoi du mail : " . $e->errorMessage(), "ERROR", "envoiMail");
        }
    }

    function creerMailInscription($_strDateDeb, $_strDateFin, $_strLieu, $_strDiscipline, $_strDisciplineNorme, $_strNomClub, $_strMailClub, $_intID, $_arrParticipants) {
        global $debug;

        if (is_string($_strMailClub) && $_strMailClub !== '') {
            logging("Adresse mail du club organisateur valide sur le concours " . $_intID , "INFO", 'creerMailInscription');
            $strDuree = formaterDateConcours($_strDateDeb, $_strDateFin);
            $strListeParticipants = formaterTableauParticipants($_strDisciplineNorme, $_arrParticipants);
    
            $strSujet = "Demande d'inscription au concours " . $_strDiscipline . " de " . $_strLieu . " se déroulant " . $strDuree;
            switch ($_strDisciplineNorme) {
                case 'SALLE':
                    $strMessage = file_get_contents(PATH_VIEWS . 'view_mail_inscription_salle.html', true);
                    break;
                
                default:
                    $strMessage = file_get_contents(PATH_VIEWS . 'view_mail_inscription_tae.html', true);
                    break;
            }
            // $strMessage = file_get_contents(PATH_VIEWS . 'view_mail_inscription_salle.html', true);
            
            $strMessage = str_replace('%%strDuree%%', $strDuree, $strMessage);
            $strMessage = str_replace('%%strLieu%%', $_strLieu, $strMessage);
            $strMessage = str_replace('%%strDiscipline%%', $_strDiscipline, $strMessage);
            $strMessage = str_replace('%%arrTableau%%', $strListeParticipants, $strMessage);
    
            // Coordonnées du club organisateur
            $strDestNom = $_strNomClub;
            if ($debug) {
                $strDestMail = 'sylvain@grippon.fr';
            } else {
                $strDestMail = $_strMailClub;
            }
            // Coordonnées de l'inscripteur
            $strReplyNom = 'Suivi des Inscription';
            $strReplyMail = 'inscription@csg-tiralarc.fr';
    
            if ($debug) {echo $strMessage;}
            $retour = envoiMail($strDestNom, $strDestMail, $strReplyNom, $strReplyMail, $strMessage, $strSujet);
            return $retour;    
        } else {
            logging("Pas de d'adresse mail du club organisateur sur le concours " . $_intID , "ALERT", 'creerMailInscription');
        }
    }

    function creerMailInscripteur($_strDateDeb, $_strDateFin, $_strLieu, $_strDiscipline, $_strDisciplineNorme, $_strOrganisateur, $_strAdresse, $_strMontant, $_arrParticipants) {
        global $debug;

        $strDuree = formaterDateConcours($_strDateDeb, $_strDateFin);
        $strListeParticipants = formaterTableauParticipants($_strDisciplineNorme, $_arrParticipants);

        $strSujet = "Information d'inscription au concours " . $_strDiscipline . " de " . $_strLieu . " se déroulant " . $strDuree;
        
        switch ($_strDisciplineNorme) {
            case 'SALLE':
                $strMessage = file_get_contents(PATH_VIEWS . 'view_mail_inscripteur_salle.html', true);
                break;
            
            default:
                $strMessage = file_get_contents(PATH_VIEWS . 'view_mail_inscripteur_tae.html', true);
                break;
        }
        // $strMessage = file_get_contents(PATH_VIEWS . 'view_mail_inscripteur.html', true);
        
        $strMessage = str_replace('%%strDuree%%', $strDuree, $strMessage);
        $strMessage = str_replace('%%strLieu%%', $_strLieu, $strMessage);
        $strMessage = str_replace('%%strDiscipline%%', $_strDiscipline, $strMessage);
        $strMessage = str_replace('%%dateNow%%', date('d/m/Y'), $strMessage);
        $strMessage = str_replace('%%strOrganisateur%%', $_strOrganisateur, $strMessage);
        $strMessage = str_replace('%%strAdresse%%', $_strAdresse, $strMessage);
        $strMessage = str_replace('%%intMontant%%', sprintf('%01.2f', $_strMontant), $strMessage);
        $strMessage = str_replace('%%arrTableau%%', $strListeParticipants, $strMessage);

        // Coordonnées de l'inscripteur
        $strDestNom = 'Suivi des Inscription';
        $strDestMail = 'inscription@csg-tiralarc.fr';
        // Pas forcément utile (backup inscripteur ?)
        $strReplyNom = 'Backup Inscripteur';
        $strReplyMail = 'webadmin@csg-tiralarc.fr';

        if ($debug) {echo $strMessage;}
        $retour = envoiMail($strDestNom, $strDestMail, $strReplyNom, $strReplyMail, $strMessage, $strSujet);
        return $retour;
    }

    function listeConcoursClos() {
                // NOUVELLE INSTANCE DE MODEL DB
                $dbCouts = new ClassDb();
                // REQUETE
                // Récupère les informations entre il y a 4 semaines et aujourd'hui qui ont un n° de chèque
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
                                E.epr_jeune_other_dep,
                                R.r_epr_usr_num_cheque
                            FROM 
                                csg_epreuves AS E,
                                csg_rel_epreuve_user AS R,
                                csg_users AS U
                            WHERE 
                                E.epr_id = R.epr_id
                            AND
                                U.usr_login = R.usr_login
                            AND 
                                R.r_epr_usr_num_cheque <> ''
                            AND 
                                E.epr_date_fin BETWEEN (CURDATE() - INTERVAL 1 WEEK) AND CURDATE() 
                            ORDER BY
                                R.r_epr_usr_num_cheque ASC,
                                U.usr_login ASC;";
        // echo $strQuery;
        // EXECUTER LA REQUETE AVEC LES PARAMETRES VIA MODEL DB
        $result = $dbCouts->getAllData($strQuery);
        // RECUPERER LE RESULTAT
        return $result;
        $dbCouts = null;
    }

    function formaterTableauConcoursClos($_arrConcoursClos) {
        global $debug;
        // global $arrCatAdultes;

        $strListeConcoursClos = "";
        
        $intPad_H = 10;
        $intPad_V = 5;

        if ($debug) {
            echo "<pre>";
            print_r($_arrConcours);
            echo "</pre>";
        }
        
        // $arrCatAdultes = array("S1F","S2F","S3F","S1FCL","S2FCL","S3FCL",
        //                         "S1H","S2H","S3H","S1HCL","S2HCL","S3HCL",
        //                         "S1F","S2F","S3F","S1FCO","S2FCO","S3FCO",
        //                         "S1H","S2H","S3H","S1HCO","S2HCO","S3HCO",
        //                         "SF","SFCL","SH","SHCL","SHCO","SVF","SVH","SVHCO","VF","VFCO","VH","VHCL");

        $floatMontantGlobal = 0;
        $strLogin = '';

        foreach ($_arrConcoursClos as $key => $rowConcours) {
            // Calcul du montant du départ
            // Si le licencié courant est le même que le licencié précédent
            if ($strLogin == $rowConcours['usr_login']) {
                // Si le licencié est adulte
                // if (in_array($rowConcours['usr_cat'], $arrCatAdultes)) {
                if ($rowConcours['r_epr_usr_age'] == 'Adulte') {
                    // Prendre l'autre départ adulte
                    $floatMontant = floatval($rowConcours['epr_adulte_other_dep']);
                } else {
                    // Prendre l'autre départ jeune
                    $floatMontant = floatval($rowConcours['epr_jeune_other_dep']);
                }
            } else {
                // if (in_array($rowConcours['usr_cat'], $arrCatAdultes)) {
                if ($rowConcours['r_epr_usr_age'] == 'Adulte') {
                    // Prendre le 1er départ adulte
                    $floatMontant = floatval($rowConcours['epr_adulte_first_dep']);
                } else {
                    // Prendre le 1er départ jeune
                    $floatMontant = floatval($rowConcours['epr_jeune_first_dep']);
                }
                // Changer le licencié
                $strLogin = $rowConcours['usr_login'];
            }
            $floatMontantGlobal = $floatMontantGlobal + $floatMontant;
            $strDate = strftime('%#d %b %Y',strtotime($rowConcours['r_epr_date']));

            $strListeConcoursClos .= "<tr>";
            $strListeConcoursClos .= "<td style='padding:". $intPad_V ." ".$intPad_H.";'>" . $rowConcours['usr_lastname'] . "</td>";
            $strListeConcoursClos .= "<td style='padding:". $intPad_V ." ".$intPad_H.";'>" . $rowConcours['usr_firstname'] . "</td>";
            $strListeConcoursClos .= "<td style='text-align:center; padding:". $intPad_V ." ".$intPad_H.";'>" . $rowConcours['usr_login'] . "</td>";
            $strListeConcoursClos .= "<td style='text-align:center; padding:". $intPad_V ." ".$intPad_H.";'>" . $rowConcours['usr_cat'] . "</td>";
            $strListeConcoursClos .= "<td style='padding:". $intPad_V ." ".$intPad_H.";'>" . $rowConcours['epr_discipline'] . "</td>";
            $strListeConcoursClos .= "<td style='padding:". $intPad_V ." ".$intPad_H.";'>" . $rowConcours['epr_lieu'] . "</td>";
            $strListeConcoursClos .= "<td style='padding:". $intPad_V ." ".$intPad_H.";'>" . $strDate . "</td>";
            $strListeConcoursClos .= "<td style='padding:". $intPad_V ." ".$intPad_H.";'>" . str_replace('_',' ',$rowConcours['r_epr_usr_depart']) . "</td>";
            $strListeConcoursClos .= "<td style='padding:". $intPad_V ." ".$intPad_H.";'>" . sprintf('%01.2f', $floatMontant) . " €</td>";
            $strListeConcoursClos .= "<td style='padding:". $intPad_V ." ".$intPad_H.";'>" . $rowConcours['r_epr_usr_num_cheque'] . "</td>";
            $strListeConcoursClos .= "</tr>";
        }

        $arrConcoursClos = array($floatMontantGlobal, $strListeConcoursClos);

        return $arrConcoursClos;
    }

    function creerMailTresorier() {
        global $debug;

        $flag = 2;

        // N'envoyer le mail que si la date courante est un mardi
        if (date('N') == 2) {
            $arrConcoursClos = listeConcoursClos();
            if (!empty($arrConcoursClos)) {
                $arrListeConcoursClos = formaterTableauConcoursClos($arrConcoursClos);
    
                $strListeConcoursClos = $arrListeConcoursClos[1];
                $floatListeConcoursClos = $arrListeConcoursClos[0];
        
                $strSujet = "Information d'inscription au concours clôturés";
                
                $strMessage = file_get_contents(PATH_VIEWS . 'view_mail_tresorier.html', true);
                
                $strMessage = str_replace('%%intMontant%%', sprintf('%01.2f', $floatListeConcoursClos), $strMessage);
                $strMessage = str_replace('%%arrTableauCout%%', $strListeConcoursClos, $strMessage);
        
                // Coordonnées de l'inscripteur
                $strDestNom = 'Trésorier';
                $strDestMail = 'tresorier@csg-tiralarc.fr';
                // Pas forcément utile (backup inscripteur ?)
                $strReplyNom = 'Backup Trésorier';
                $strReplyMail = 'webadmin@csg-tiralarc.fr';
        
                if ($debug) {echo $strMessage;}
                $retour = envoiMail($strDestNom, $strDestMail, $strReplyNom, $strReplyMail, $strMessage, $strSujet);
                if ($retour) { $flag = 1; } else { $flag = 0; }
            }
        }

        return $flag;
    }

    function creerMailParticipant($_strPrenom, $_strNom, $_strMail, $_arrDeparts, $_strTypeMail) {
        global $debug;
        
        $strListeConcours = formaterTableauConcours($_arrDeparts);
    
        
        $strMessage = file_get_contents(PATH_VIEWS . 'view_mail_participant.html', true);

        if ($_strTypeMail == 'Preinscription') {
            $strSujet = "Récapitulatif des pré-inscriptions aux concours";
            $strMessage = str_replace('%%MessageEntete%%', "Vous venez d'être inscrit au(x) concours suivants :", $strMessage);
            $strMessage = str_replace('%%MessageFooter%%', "Vous pouvez encore annuler en contactant directement la personne chargée des inscriptions.<br>", $strMessage);
        } elseif ($_strTypeMail == 'validation') {
            $strSujet = "Récapitulatif des validations d'inscriptions aux concours";
            $strMessage = str_replace('%%MessageEntete%%', "Les inscriptions au(x) concours suivants ont été validés :", $strMessage);
            $strMessage = str_replace('%%MessageFooter%%', "Les frais d'inscriptions étant déjà engagé, l'annulation de ces concours ne pourra se faire que sous certaines conditions.<br>Contactez la personne chargée des inscriptions pour les connaitre.<br>", $strMessage);
        }
    
        $strMessage = str_replace('%%Prenom%%', $_strPrenom, $strMessage);
        $strMessage = str_replace('%%Nom%%', $_strNom, $strMessage);
        $strMessage = str_replace('%%arrTableau%%', $strListeConcours, $strMessage);
    
        // Coordonnées du participant
        $strDestNom = $_strPrenom." ".$_strNom;
        if ($debug) {
            $strDestMail = 'sylvain@grippon.fr';
        } else {
            $strDestMail = $_strMail;
        }
        // Coordonnées de l'inscripteur
        $strReplyNom = 'Inscripteur';
        $strReplyMail = 'inscription@csg-tiralarc.fr';
    
        $arrICS = array();
    
        if ($debug) {
            echo "Array Departs : ";
            echo "<pre>";
            print_r($_arrDeparts);
            echo "</pre>";
        }
    
        // Création du fichier ICS
        // https://github.com/zcontent/icalendar
        require_once("icalendar/zapcallib.php");
        foreach ($_arrDeparts as $key => $rowDepart) {
            $strDate = strftime('%Y-%m-%d',strtotime($rowDepart['r_epr_date']));
    
            $title = "Concours " . $rowDepart['epr_lieu'];
            // date/time is in SQL datetime format
            $event_start = $rowDepart['r_epr_date'];
            $event_end = $rowDepart['r_epr_date'];
            // Lieu du concours
            $strLocation = $rowDepart['epr_adresse'];
    
            $icalobj = new ZCiCal();
            $eventobj = new ZCiCalNode("VEVENT", $icalobj->curnode);
    
            // add title
            $eventobj->addNode(new ZCiCalDataNode("SUMMARY:" . $title));
    
            // add start date
            $eventobj->addNode(new ZCiCalDataNode("DTSTART;VALUE=DATE:" . ZCiCal::fromSqlDateTime($event_start)));
    
            // add end date
            $eventobj->addNode(new ZCiCalDataNode("DTEND;VALUE=DATE:" . ZCiCal::fromSqlDateTime($event_end)));
    
            // UID is a required item in VEVENT, create unique string for this event
            // Adding your domain to the end is a good way of creating uniqueness
            $uid = date('Y-m-d-H-i-s') . "@csg-tiralarc.fr";
            $eventobj->addNode(new ZCiCalDataNode("UID:" . $uid));
    
            // DTSTAMP is a required item in VEVENT
            $eventobj->addNode(new ZCiCalDataNode("DTSTAMP:" . ZCiCal::fromSqlDateTime()));
    
            // Add description
            $eventobj->addNode(new ZCiCalDataNode("DESCRIPTION:" . ZCiCal::formatContent(
                    "Concours de " . $rowDepart['epr_lieu'] . " en " . $rowDepart['epr_discipline'] . ".\n " .
                    $rowDepart['epr_siteweb'])));
    
            // add end date
            $eventobj->addNode(new ZCiCalDataNode("LOCATION:" . $strLocation));
            
            // // Récupére l'object ICS dans une variable
            array_push($arrICS, $icalobj->export());
        }
    
        // Ajouter fichier ICS en pièce jointe
        // https://blog.webnersolutions.com/how-to-send-ical-attachment-via-phpmailer
    
        
        if ($debug) {echo $strMessage;}
        logging("Envoi du mail au participant " . $strDestNom . " à l'adresse  " . $strDestMail, "INFO", "creerMailParticipant");
        $retour = envoiMail($strDestNom, $strDestMail, $strReplyNom, $strReplyMail, $strMessage, $strSujet, $arrICS);
        return $retour;
    }
    
    function creerMailConcoursARenseigner($_arrConcoursHS) {
        global $debug;
        
        logging("Création du mail des concours à renseigner pour " . count($_arrConcoursHS) . " concours", "INFO", "creerMailConcoursARenseigner");
        $strListeConcours = formaterTableauConcoursARenseigner($_arrConcoursHS);

        $strSujet = "Récapitulatif des concours à renseigner";

        $strMessage = file_get_contents(PATH_VIEWS . 'view_mail_concoursHS.html', true);

        $strMessage = str_replace('%%arrTableau%%', $strListeConcours, $strMessage);

        // Coordonnées de l'administrateur WEB
        $strDestNom = 'SqualeDev';
        $strDestMail = 'webadmin@csg-tiralarc.fr';

        if ($debug) {echo $strMessage;}
        $retour = envoiMail($strDestNom, $strDestMail, '', '', $strMessage, $strSujet);
        return $retour;
    }

    function creerMailConcoursAPayer($_arrConcoursAPayer) {
        global $debug;
        
        logging("Création du mail des concours à payer pour " . count($_arrConcoursAPayer) . " concours", "INFO", "creerMailConcoursApayer");
        $strListeConcours = formaterTableauConcoursAPayer($_arrConcoursAPayer);

        $strSujet = "Récapitulatif des concours à payer";

        $strMessage = file_get_contents(PATH_VIEWS . 'view_mail_concoursApayer.html', true);

        $strMessage = str_replace('%%arrTableau%%', $strListeConcours, $strMessage);

        // Coordonnées de l'administrateur WEB
        $strDestNom = 'SqualeDev';
        $strDestMail = 'webadmin@csg-tiralarc.fr';

        if ($debug) {echo $strMessage;}
        $retour = envoiMail($strDestNom, $strDestMail, '', '', $strMessage, $strSujet);
        return $retour;
    }

    function creerMailDepartsAValider($_arrDepartsAValider) {
        global $debug;
        
        logging("Création du mail des départs à valider pour " . count($_arrDepartsAValider) . " départs", "INFO", "creerMailDepartsAValider");
        $strListeDeparts = formaterTableauDepartsAValider($_arrDepartsAValider);

        $strSujet = "Récapitulatif des départs à valider";

        $strMessage = file_get_contents(PATH_VIEWS . 'view_mail_departsAvalider.html', true);

        $strMessage = str_replace('%%arrTableau%%', $strListeDeparts, $strMessage);

        // Coordonnées de l'administrateur WEB
        $strDestNom = 'SqualeDev';
        $strDestMail = 'webadmin@csg-tiralarc.fr';

        if ($debug) {echo $strMessage;}
        $retour = envoiMail($strDestNom, $strDestMail, '', '', $strMessage, $strSujet);
        return $retour;
    }

    function calculMontantInscription($_intIDConcours) {
        // global $arrCatAdultes;
        
        logging("Calcul du montant des inscriptions pour le concours  n° " . $_intIDConcours, "INFO", "calculMontantInscription");
        
        // Pour le concours choisi récupérer :
        // - les tarifs des 4 départs
        // - La categorie de chaque participant
        // - Le nombre de départ de chaque participant

        // NOUVELLE INSTANCE DE MODEL DB
        $dbDepart = new ClassDb();
        // REQUETE
        $strQuery = "SELECT 
                        R.epr_id,
                        COUNT(*) AS 'nb_depart',
                        R.usr_login,
                        R.r_epr_usr_age,
                        U.usr_cat,
                        E.epr_adulte_first_dep,
                        E.epr_adulte_other_dep,
                        E.epr_jeune_first_dep,
                        E.epr_jeune_other_dep
                    FROM 
                        `csg_epreuves` AS E,
                        `csg_rel_epreuve_user` AS R,
                        `csg_users` AS U 
                    WHERE 
                        U.usr_login = R.usr_login 
                    AND 
                        R.epr_id = E.epr_id 
                    AND 
                        E.epr_id = " . $_intIDConcours . "
                    GROUP BY
                        R.usr_login;";
        // EXECUTER LA REQUETE AVEC LES PARAMETRES VIA MODEL DB
        $_arrParticipants = $dbDepart->getAllData($strQuery);

        // $arrCatAdultes = array("SF","SFCL","SH","SHCL","SHCO","SVF","SVH","SVHCO","VF","VFCO","VH","VHCL");

        // Montant du concours choisi
        $floatMontantConcours = 0;
        
        // Pour chaque ligne (Participant), en fonction du type (Adulte ou jeune) calculer le montant du concours
        foreach ($_arrParticipants as $key => $rowParticipants) {
            // if (in_array($rowParticipants['usr_cat'], $arrCatAdultes)) {
            if ($rowParticipants['r_epr_usr_age'] == 'Adulte') {
                // Calculer Montant du 1er départ + nbre départ supplémentaire x Montant du départ supplémentaire pour un adulte
                $floatMontantParticipant = floatval($rowParticipants['epr_adulte_first_dep']) + (intval(($rowParticipants['nb_depart']) - 1) * floatval($rowParticipants['epr_adulte_other_dep']));
            } else {
                // Calculer Montant du 1er départ + nbre départ supplémentaire x Montant du départ supplémentaire pour un jeune
                $floatMontantParticipant = floatval($rowParticipants['epr_jeune_first_dep']) + (intval(($rowParticipants['nb_depart']) - 1) * floatval($rowParticipants['epr_jeune_other_dep']));
            }
            // Ajouter ce Montant à la somme des départ du concours choisi
            $floatMontantConcours = $floatMontantConcours + $floatMontantParticipant;
        }

        logging("Montant du concours n° " . $_intIDConcours . " : " . $floatMontantConcours, "INFO", "calculMontantInscription");

        logging("Fin du Calcul du montant du concours n° " . $_intIDConcours, "INFO", "calculMontantInscription");
        return $floatMontantConcours;
        $dbDepart = null;
    }
    
    // MET A JOUR LA DATE D'INSCRIPTION AU CONCOURS SUR LA LIGNE DU DEPART
    function majInscription($_intIDConcours, $_strLoginUser, $_strDepart) {
        // NOUVELLE INSTANCE DE MODEL DB
        $dbDepart = new ClassDb();
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
        $strQuery = "UPDATE csg_rel_epreuve_user 
                        SET 
                        r_epr_usr_inscript = '". date('Y-m-d H:i:s') . "'
                        WHERE 
                        epr_id = ".$_intIDConcours."
                        AND 
                        usr_login = '".$_strLoginUser."'
                        AND
                        r_epr_usr_depart = '".$_strDepart."';
                    ";
        // echo $strQuery;
        // EXECUTER LA REQUETE AVEC LES PARAMETRES VIA MODEL DB
        // $result = $dbDepart->updateRow($strQuery, array(date('Y-m-d H:i:s'), $_intIDConcours, $_strLoginUser, $_strDepart));
        $result = $dbDepart->updateRow($strQuery, array());
        // echo $dbDepart->queryOK;

        return $dbDepart->queryOK;
        $dbDepart = null;
    }

    function closConcours() {
        logging("Clôture des concours dont la date est dépassée", "INFO", "closConcours");
        
        // NOUVELLE INSTANCE DE MODEL DB
        $dbDepart = new ClassDb();
        // REQUETE
        $strQuery = "UPDATE csg_epreuves SET epr_etat = ? WHERE epr_date_deb <= NOW();";

        // EXECUTER LA REQUETE AVEC LES PARAMETRES VIA MODEL DB
        $result = $dbDepart->updateRow($strQuery, array("Clos"));
        logging("Clôture de " . $dbDepart->queryNbRows . " concours dont la date est dépassée", "INFO", "closConcours");

        logging("Fin de clôture des concours", "INFO", "closConcours");
        return $dbDepart->queryOK;
        $dbDepart = null;
    }

    function majEtatConcours($_intIDConcours) {
        // NOUVELLE INSTANCE DE MODEL DB
        $dbDepart = new ClassDb();
        // REQUETE
        $strQuery = "UPDATE csg_epreuves
                        SET 
                        epr_etat = ?
                        WHERE 
                        epr_id = ?;
                    ";
        // echo $strQuery;
        // EXECUTER LA REQUETE AVEC LES PARAMETRES VIA MODEL DB
        $result = $dbDepart->updateRow($strQuery, array("Préinscription", $_intIDConcours));

        return $dbDepart->queryOK;
        $dbDepart = null;
    }

    function recupererDepartUserPreInscrit($_strLogin) {
        // NOUVELLE INSTANCE DE MODEL DB
        $dbConcours = new ClassDb();
        // REQUETE
        $strQuery = "SELECT 
                        E.epr_discipline, 
                        E.epr_discipline_norme, 
                        R.r_epr_date, 
                        R.r_epr_usr_depart, 
                        E.epr_lieu, 
                        E.epr_adresse, 
                        E.epr_siteweb, 
                        R.r_epr_usr_blason, 
                        R.r_epr_usr_type_epr
                    FROM 
                        csg_rel_epreuve_user AS R, csg_epreuves AS E
                    WHERE 
                        R.epr_id = E.epr_id AND R.usr_login = '" . $_strLogin . "' AND DATE(R.r_epr_usr_inscript) = CURDATE();";
        // echo $strQuery;
        // EXECUTER LA REQUETE AVEC LES PARAMETRES VIA MODEL DB
        $result = $dbConcours->getAllData($strQuery);
        // RECUPERER LE RESULTAT
        return $result;
        $dbConcours = null;
    }

    function recupererParticipantsValide() {
        // NOUVELLE INSTANCE DE MODEL DB
        $dbUsersValide = new ClassDb();
        // REQUETE
        $strQuery = "SELECT DISTINCT 
                        usr_login 
                    FROM 
                        csg_rel_epreuve_user 
                    WHERE 
                        r_epr_usr_valide >= NOW() - INTERVAL 24 HOUR;";
        // echo $strQuery;
        // EXECUTER LA REQUETE AVEC LES PARAMETRES VIA MODEL DB
        $result = $dbUsersValide->getAllData($strQuery);
        // RECUPERER LE RESULTAT
        return $result;
        $dbUsersValide = null;
    }
    
    function recupererDepartUserValide($_strLogin) {
        // NOUVELLE INSTANCE DE MODEL DB
        $dbConcours = new ClassDb();
        // REQUETE
        $strQuery = "SELECT 
                        E.epr_discipline, 
                        E.epr_discipline_norme, 
                        R.r_epr_date, 
                        R.r_epr_usr_depart, 
                        E.epr_lieu, 
                        E.epr_adresse, 
                        E.epr_siteweb, 
                        R.r_epr_usr_blason, 
                        R.r_epr_usr_type_epr
                    FROM 
                        csg_rel_epreuve_user AS R, csg_epreuves AS E
                    WHERE 
                        R.epr_id = E.epr_id AND R.usr_login = '".$_strLogin."' 
                    AND 
                        r_epr_usr_valide >= NOW() - INTERVAL 24 HOUR
                    ;";
        // echo $strQuery;
        // EXECUTER LA REQUETE AVEC LES PARAMETRES VIA MODEL DB
        $result = $dbConcours->getAllData($strQuery);
        // RECUPERER LE RESULTAT
        return $result;
        $dbConcours = null;
    }
    
    function recupererPass($_strLogin) {
        // NOUVELLE INSTANCE DE MODEL DB
        $dbInscrits = new ClassDb();
        // REQUETE
        $strQuery = "SELECT usr_pass FROM csg_users WHERE usr_login = ?;";
        // echo $strQuery;
        // EXECUTER LA REQUETE AVEC LES PARAMETRES VIA MODEL DB
        $result = $dbInscrits->getData($strQuery, array($_strLogin));
        // RECUPERER LE RESULTAT
        return $result[0]['usr_pass'];
        $dbInscrits = null;
    }

logging("=================            Traitement des concours            =================", "INFO", "main");
logging("Vérification de la clé d'accès", "INFO", "main");
if (isset($_GET['cle'])){
    if ($_GET['cle'] == 'a14axQHk8JA6dfpjNmwPoRpCvcTQg5WG8pM6N6Tzmo75XcgIoy') {
        logging("Clé définit et autorisée !", "INFO", "main");
        // ------------------------------------------------------------------------------------------------
        //                                          GESTION DES INSCRIPTIONS
        // ------------------------------------------------------------------------------------------------
        //     - TRAITEMENT DES CATEGORIES D'AGE DES PARTICIPANTS
        //        - Mettre à jour l'age dans la table des départs si le mandat est présent en fonction de :
        //           - la catégorie du participant
        //           - la liste des catégorie adulte de l'épreuve
        // ------------------------------------------------------------------------------------------------
        logging("Traitement des catégories d'âge", "INFO", "main");
        // SELECT DISTINCT epr_id, usr_login FROM csg_rel_epreuve_user WHERE r_epr_usr_age = '' OR r_epr_usr_age IS NULL;
        $arrListeDepartsSansAge = listeDepartsSansAge();
        // Pour chaque ligne de csg_rel_epreuve_user dont r_epr_usr_age est null (récupérer distincte epr_id, usr_login)
        foreach ($arrListeDepartsSansAge as $key => $arrListeDepartSansAge) {
            // Récupérer les catégories "adultes" de l'épreuve (csg_epreuves.epr_cat_adulte)
            $strCategEpreuve = recupererCategEpreuve($arrListeDepartSansAge['epr_id']);
            // SELECT epr_cat_adulte FROM csg_epreuves WHERE epr_id = 999
            // Si quand csg_epreuves.epr_id = epr_id, csg_epreuves.epr_cat_adulte est different de vide
            if ($strCategEpreuve != false) {
                // Récupérer la catégorie simple du participant (csg_users.usr_cat_simple)
                $strCategLicencie = recupererCategLicencie($arrListeDepartSansAge['usr_login']);
                // SELECT usr_cat_simple FROM csg_users WHERE usr_login = '942866E' 
                // Splitter les catégories "adultes" de l'épreuve
                $arrCategoriesEpreuve = explode(',',$strCategEpreuve);
                // Si la catégorie du participant est dans le tableau de catégories de l'épreuve
                logging("Mise à jour de la catégorie d'âge pour " . $arrListeDepartSansAge['usr_login'] . " sur l'épreuve " . $arrListeDepartSansAge['epr_id'], "INFO", "main");
                if (in_array($strCategLicencie, $arrCategoriesEpreuve)) {
                    // Mettre 'Adulte' dans le champ
                    // UPDATE csg_rel_epreuve_user SET r_epr_usr_age = 'Adulte' WHERE epr_id = 'epreuve_id' AND usr_login = 'n° de licence'
                    majAgeDepart('Adulte', $arrListeDepartSansAge['epr_id'], $arrListeDepartSansAge['usr_login']);
                } else {
                    majAgeDepart('Jeune', $arrListeDepartSansAge['epr_id'], $arrListeDepartSansAge['usr_login']);
                }
            }
        }
        logging("Fin du traitement des catégories d'âge", "INFO", "main");

        
        // ------------------------------------------------------------------------------------------------
        //                                          GESTION DES INSCRIPTIONS
        // ------------------------------------------------------------------------------------------------
        //     - GESTION DES MAILS AU TRESORIER
        //        - Envoi au trésorier les montants des concours à clôturer
        // ------------------------------------------------------------------------------------------------
        logging("Traitement des concours clôturés pour le trésorier", "INFO", "main");
        switch (creerMailTresorier()) {
            case 0:
                logging("Problème lors de l'envoi du mail au trésorier", "ERROR", "main");
                break;
            case 1:
                logging("Envoi du mail au trésorier réussi", "INFO", "main");
                break;
            case 2:
                logging("Pas d'envoi de mail au trésorier aujourd'hui", "INFO", "main");
                break;
            default:
                break;
        }
        logging("Fin du traitement des concours clôturés pour le trésorier", "INFO", "main");

        // ------------------------------------------------------------------------------------------------
        //                                          GESTION DES INSCRIPTIONS
        // ------------------------------------------------------------------------------------------------
        //     - GESTION DES MAILS A CLOTURER
        //        - Changement d'état des concours dont la date de début est aujourd'hui
        // ------------------------------------------------------------------------------------------------
        logging("Traitement de la clôture des concours", "INFO", "main");
        closConcours();
        logging("Fin de traitement de la clôture des concours", "INFO", "main");

        // ------------------------------------------------------------------------------------------------
        //                                          GESTION DES INSCRIPTIONS
        // ------------------------------------------------------------------------------------------------
        //     - GESTION DES CONCOURS MAL RENSEIGNES
        //        - Envoi à la personne chargée des inscriptions la liste des concours où il manque :
        //           - l'adresse mail
        //           - le tarif adulte
        //           - le tarif jeune
        //           - la catégorie du participant        
        // ------------------------------------------------------------------------------------------------
        // Avertir l'administrateur Web des concours non renseignés
        logging("Vérification des concours non renseignés", "INFO", "main");
        $arrConcoursHS = listeConcoursARenseigner($_intJrsRenseigner);
        if ($arrConcoursHS) {
            creerMailConcoursARenseigner($arrConcoursHS);
        } else {
            logging("Aucun concours à renseigner n'a été détecté", "INFO", "main");
        }
        logging("Fin de vérification des concours non renseignés", "INFO", "main");
        
        // ------------------------------------------------------------------------------------------------
        //                                          GESTION DES INSCRIPTIONS
        // ------------------------------------------------------------------------------------------------
        //     - GESTION DES CONCOURS NON PAYES
        //        - Envoi à la personne chargée des inscriptions la liste des concours dont :
        //           - le mandat est présent
        //           - des particiapants existe
        //           - les n° de chèque sont absents
        // ------------------------------------------------------------------------------------------------
        // Avertir l'administrateur Web des concours non renseignés
        logging("Vérification des concours non payés", "INFO", "main");
        $arrConcoursAPayer = listeConcoursNonPayes();
        if ($arrConcoursAPayer) {
            creerMailConcoursAPayer($arrConcoursAPayer);
        } else {
            logging("Aucun concours à payer n'a été détecté", "INFO", "main");
        }
        logging("Fin de vérification des concours non payés", "INFO", "main");
        
        // ------------------------------------------------------------------------------------------------
        //                                          GESTION DES INSCRIPTIONS
        // ------------------------------------------------------------------------------------------------
        //     - GESTION DES DEPARTS DONT LES PARTICIAPANTS NON PAS ETE VALIDES
        //        - Envoi à la personne chargée des inscriptions la liste des départs dont :
        //           - le n° de cheque est présent
        //           - la validation du participant est absente
        // ------------------------------------------------------------------------------------------------
        // Avertir l'administrateur Web des concours non renseignés
        logging("Vérification des départs non validés", "INFO", "main");
        $arrDepartsAValider = listeDepartsNonValides();
        if ($arrDepartsAValider) {
            creerMailDepartsAValider($arrDepartsAValider);
        } else {
            logging("Aucun départ non valider n'a été détecté", "INFO", "main");
        }
        logging("Fin de vérification des départs non validés", "INFO", "main");
        
        // ------------------------------------------------------------------------------------------------
        //                                          GESTION DES INSCRIPTIONS
        // ------------------------------------------------------------------------------------------------
        //     - GESTION DES VALIDATION DES PARTICIPATIONS
        //        - Envoi à chaque participant d'un mail lui indiquant que ses départs sont validés
        // ------------------------------------------------------------------------------------------------

        // Récupérer la liste des participants dont la validation date de moins de 24h
        logging("Récupération des participants validés aujourd'hui", "INFO", "main");
        $arrParticipantsValide = recupererParticipantsValide();
        // VERIFIE SI AU MOINS UN CONCOURS EST A TRAITER
        if ($arrParticipantsValide) {
            // pour chaque participant de cette liste
            foreach ($arrParticipantsValide as $key => $rowParticipantValide) {
                // Récupérer le mot de passe de chaque participant
                logging("Récupération du mot de passe du User : " . $rowParticipantValide['usr_login'], "INFO", "main");
                $Pass = recupererPass($rowParticipantValide['usr_login']);
                // Récuperer les informations concernant un participant (nom, prénom, mail, ...)
                $objUser = new ClassUser($rowParticipantValide['usr_login'], $Pass);
        
                // Vérifier que le participant veut être notifié par mail
                if ($objUser->boolNotifMailInscript) {
                    logging("le User " . $rowParticipantValide['usr_login'] . " a demandé à être notifié par mail", "INFO", "main");
                    // Récupérer son adresse mail
                    $strMail = $objUser->strMail;
                    // Récupérer le nom
                    $strPrenom = $objUser->strFirstname;
                    // Récupérer le prénom
                    $strNom = $objUser->strLastname;
                    // Récupérer la liste des concours et départs validés aujourd'hui
                    $arrDepartsValide = recupererDepartUserValide($rowParticipantValide['usr_login']);
                    if ($arrDepartsValide) {
                        // Créer le mail pour le participant
                        logging("Envoi du mail au participant " . $rowParticipantValide['usr_login'] . " validé", "INFO", "main");
                        $retour = creerMailParticipant($strPrenom, $strNom, $strMail, $arrDepartsValide, 'validation');
                        if (!$retour) {
                            logging("Validation : Problème lors de la création du mail pour le participant " . $rowParticipantValide['usr_login'] . " => mail non envoyé !", "ERROR");
                        }
                    } else {
                        logging("Aucun départ pour le participant validé n'a été détecté", "INFO", "main");
                    }
                }
            }
        } else {
            logging("Aucun participant n'a été validé aujourd'hui ...", "INFO", "main");
        }

        // Récupération des concours à préinscrire
        logging("Récupération des concours à préinscrire", "INFO", "main");
        $arrConcours = listeConcours($intJrsPre_Inscript);
        
        // VERIFIE SI AU MOINS UN CONCOURS EST A TRAITER
        if ($arrConcours) {
        
            if ($debug) {
                echo "<h3> List All arrConcours Variables</h3>";
                var_dump($arrConcours);
            }
        
            // Liste des concours validé aujourd'hui
            $arrConcoursValide = array();
            $arrInscritsValide = array();
        
            // ------------------------------------------------------------------------------------------------
            //                                          GESTION DES INSCRIPTIONS
            // ------------------------------------------------------------------------------------------------
            //     - GESTION DES MAILS AUX CLUB ORGANISATEUR
            //     - GESTION DES MAILS A LA PERSONNE CHARGE DES INSCRIPTONS
            //     - GESTION DES MAILS AU TRESORIER
            // ------------------------------------------------------------------------------------------------
            // Pour chaque concours de cette liste
            foreach ($arrConcours as $key => $rowConcours) {
                // Récupérer la liste des participants et leurs départs
                logging("Récupération des participants à préinscrire", "INFO", "main");
                $arrInscrits = listeParticipants($rowConcours['epr_id']);
                // Vérifier qu'il existe bien un inscrit pour ce concours
                if ($arrInscrits) {
                    // Créer le Mail pour le club organisateur
                    logging("Création du mail pour l'inscription au concours n° " . $rowConcours['epr_id'], "INFO", "main");
                    $retour = creerMailInscription($rowConcours['epr_date_deb'], $rowConcours['epr_date_fin'], $rowConcours['epr_lieu'], $rowConcours['epr_discipline'], $rowConcours['epr_discipline_norme'], $rowConcours['epr_orga'], $rowConcours['epr_mail'], $rowConcours['epr_id'], $arrInscrits);
                    // Désactivation de l'envoi automatique des mails d'inscriptions aux concours
                    // $retour = false;
                    if ($retour) {
                        // Créer le mail pour l'inscripteur
                        logging("Création du mail pour l'inscripteur du concours n° " . $rowConcours['epr_id'], "INFO", "main");
                        $floatMontant = calculMontantInscription($rowConcours['epr_id']);
                        creerMailInscripteur($rowConcours['epr_date_deb'], $rowConcours['epr_date_fin'], $rowConcours['epr_lieu'], $rowConcours['epr_discipline'], $rowConcours['epr_discipline_norme'], $rowConcours['epr_orga'], $rowConcours['epr_adresse_inscript'], $floatMontant, $arrInscrits);
                        // Mettre à jour la colonne inscription de la table csg_rel_epreuve_user à la date du jour pour bloquer sa modification
                        foreach ($arrInscrits as $key => $rowInscrit) {
                            logging("Mise à jour de la base du départ du ". $rowInscrit['r_epr_usr_depart'] . " pour le licencié n° ". $rowInscrit['usr_login'] . " pour le concours n° " . $rowConcours['epr_id'], "INFO", "main");
                            majInscription($rowConcours['epr_id'], $rowInscrit['usr_login'], $rowInscrit['r_epr_usr_depart']);
                            if (!in_array($rowInscrit['usr_login'], $arrInscritsValide)) {array_push($arrInscritsValide, $rowInscrit['usr_login']);}
                        }
                        // Mettre à jour la colonne etat de la table csg_epreuves pour le mettre à Préinscription bloquant sa modification
                        majEtatConcours($rowConcours['epr_id']);
                        // Ajouter ce concours à l'array des concours validés aujourd'hui
                        array_push($arrConcoursValide, $rowConcours['epr_id']);
                    } else {
                        logging("Problème lors de la création du mail pour le concours n° " . $rowConcours['epr_id'] . " => mail non envoyé !", "ERROR", "main");
                        envoiMail('Admin Web Bug', 'squaldev@grippon.fr', '', '', 'Envoi de mail impossible' . $rowConcours['epr_id'], 'Problème d\'envoi de mail');
                    }
                } else {
                    logging("Aucun participant pour le concours " . $rowConcours['epr_id'], "INFO", "main");
                }
            }
        
            // ------------------------------------------------------------------------------------------------
            //                                GESTION DES MAILS AUX PARTICIPANTS
            // ------------------------------------------------------------------------------------------------
        
            if ($debug) {
                array_push($arrInscritsValide, '933783G');
                echo "<h3> List All arrInscritsValide Variables</h3>";
                var_dump($arrInscritsValide);
            }
        
            // Pour chaque participants dont la préinscription est faite
            foreach ($arrInscritsValide as $key => $rowInscritValide) {
                // Récupérer le mot de passe de chaque participant
                logging("Récupération du mot de passe du User : " . $rowInscritValide, "INFO", "main");
                $Pass = recupererPass($rowInscritValide);
                // Récuperer les informations concernant un participant (nom, prénom, mail, ...)
                $objUser = new ClassUser($rowInscritValide, $Pass);
        
                // Vérifier que le participant veut être notifié par mail
                if ($objUser->boolNotifMailInscript) {
                    logging("le User " . $rowInscritValide . " a demandé à être notifié par mail", "INFO", "main");
                    // Récupérer son adresse mail
                    $strMail = $objUser->strMail;
                    // Récupérer le nom
                    $strPrenom = $objUser->strFirstname;
                    // Récupérer le prénom
                    $strNom = $objUser->strLastname;
                    // Récupérer la liste des concours et départs préinscrits aujourd'hui
                    $arrDepartsPreInscrit = recupererDepartUserPreInscrit($rowInscritValide);
                    if ($arrDepartsPreInscrit) {
                        // Créer le mail pour le participant
                        logging("Envoi du mail au préinscrit " . $strPrenom . " " . $strNom, "INFO", "main");
                        $retour = creerMailParticipant($strPrenom, $strNom, $strMail, $arrDepartsPreInscrit, 'Preinscription');
                        if (!$retour) {
                            logging("Préinscription : Problème lors de la création du mail pour le participant " . $rowInscritValide . " => mail non envoyé !", "ERROR");
                        }
                    } else {
                        logging("Aucun départ pour le participant pré inscrit n'a été détecté", "INFO", "main");
                    }
                }
            }

            // 
        } else {
            logging("Aucun concours a traiter ...", "INFO", "main");
        }
    } else {
        logging("Cle non autorisée", "INFO", "main");}
} else {logging("Pas de clé défini !", "INFO", "main");}
logging("=================         Fin de Traitement des concours        =================", "INFO", "main");
<?php
    // Cette apge sera appelé par la procédure onChangeID() de jvs_inscript.js
    // Création du formulaire via AJAX
    // Récupérer les informations nécessaires :
        // les départs entre les deux dates
        // les départs de ce concours où s'est inscrit l'utilisateur
    // Construire le formulaire avec ces informations en activant les départ où l'utilisateur est inscrit
    
    function getDatesBetween($start, $end) {
        if($start > $end) {
            return false;
        }
        
        $sdate    = strtotime($start);
        $edate    = strtotime($end);        
        $dates = array();
        for($i = $sdate; $i <= $edate; $i += strtotime('+1 day', 0)) {
            $dates[] = date('Y-m-d', $i);
        }
        return $dates;
    }

    function isInArray($strFind, $arrTest) {
        $flagTest = FALSE;
        foreach ($arrTest as $arrNiv2) {
            if (in_array($strFind, $arrNiv2, true)) {
                $flagTest = TRUE;
                break;
            }
        }
        return $flagTest;
    }
    
    $arrJour = Array( "Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi" );
    $arrMois = Array( "Jan", "Fev", "Mar", "Avr", "Mai", "Jui", "Jul", "Aou", "Sep", "Oct", "Nov", "Dec" );

    $arrDepTheoric = array(
        ["Matin", "Après-Midi"], // Dimanche
		["Matin", "Après-Midi"], // Lundi
		["Matin", "Après-Midi"], // Mardi
		["Matin", "Après-Midi"], // Mercredi
		["Matin", "Après-Midi"], // Jeudi
		["Matin", "Après-Midi"], // Vendredi
		["Après-Midi", "Soir"] // Samedi
    );

    if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/licencies')) {
        // Config finale
        // require_once $_SERVER['DOCUMENT_ROOT'] . '/licencies/config_local.php';
        require_once $_SERVER['DOCUMENT_ROOT'] . '/licencies/constantes.php';
    } else {
        // Config Debug
        // require_once $_SERVER['DOCUMENT_ROOT'] . '/config_local.php';
        require_once $_SERVER['DOCUMENT_ROOT'] . '/constantes.php';
    }
    // require_once $_SERVER['DOCUMENT_ROOT'] . '/licencies/constantes.php';
    
    $Concoursid = $_GET['ConcoursID'];

    require_once PATH_MODELS . 'model_class_concours.php';
    $objInscrits  = new ClassConcours();

    // Date de début du concours
    $dateDebConcours = $objInscrits->getDateDebutConcours($Concoursid);
    // Date de fin du concours
    $dateFinConcours = $objInscrits->getDateFinConcours($Concoursid);

    $arrDates = getDatesBetween($dateDebConcours, $dateFinConcours);

    // Récupérer les départs pour ce concours de l'utilisateur
    $arrDepartsUser = $objInscrits->getAllDepartsUser($Concoursid, $_GET['userID']);
    
    $strEtatBtEnregistrer = "";
    if ($objInscrits->isConcoursClos($Concoursid)) {
        $strEtatBtEnregistrer = "disabled";
    }

    $intNbDep = 1;
    $strHTML = '';
    // $strHTML_Dep = '';
    // $strHTML_Blason = '';

    // Pour chaque date du tableau
    foreach ($arrDates as $key => $value) {
        $numJour = date('w',strtotime($value));
        $arrDepDuJour = $arrDepTheoric[$numJour];

        foreach ($arrDepDuJour as $keyDuJour => $depDuJour) {
            $strDepartUser = "";
            $strChampHTML = '';
            $strChampChk = '';

            $strJour = date('j',strtotime($value));
            $strMois = $arrMois[date('n',strtotime($value))-1];
            $strDate = date('Y-m-d',strtotime($value));

            if (!empty($arrDepartsUser)) {
                $intNbValue = count($arrDepartsUser);
                for ($i=0; $i < $intNbValue; $i++) {
                    if($arrJour[$numJour] . "_" . $depDuJour == $arrDepartsUser[$i]['r_epr_usr_depart']) {
                        $strDepartUser = "checked";
                        $strBlason = null;

                        if($arrDepartsUser[$i]['r_epr_usr_type_epr'] == 'Championnat') {
                            $strChampChk = 'checked';
                        } else {
                            $strChampChk = '';
                        }
                    }
                }
            }

            $strHTML .= '<div class="d-flex justify-content-left">';
            // $strHTML .= '<div class="align-self-center">';
            $strHTML .= '<div class="custom-control custom-checkbox custom-control-inline h5">';
            $strHTML .= '<input type="checkbox" class="custom-control-input" name="inputDepart' . $intNbDep . '" id="inputDepart' . $intNbDep . '" value="' . $strDate . "|" . $arrJour[$numJour] . '_' . $depDuJour .'" ' . $strEtatBtEnregistrer . ' '. $strDepartUser . '>';
            $strHTML .= '<label class="custom-control-label" for="inputDepart' . $intNbDep . '">' . $arrJour[$numJour] . ' ' . $depDuJour . '</label> ';
            $strHTML .= '</div>';
            $strHTML .= '<div class="switch switch-sm">';
            $strHTML .= '<input type="checkbox" class="switch" id="switch-depart' . $intNbDep . '" name="switchTypeDepart' . $intNbDep . '" ' . $strEtatBtEnregistrer . ' '. $strChampChk . '>';
            $strHTML .= '<label for="switch-depart' . $intNbDep . '">Coupe / Championnat</label>';
            $strHTML .= '</div>';
            // $strHTML .= '</div>';
            $strHTML .= '</div>';
            $intNbDep++;
        }
    }

    print $strHTML;
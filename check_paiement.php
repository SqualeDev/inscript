<?php
    // Ce fichier sera accessible via une URL avec clé
    // http://csg-tiralarc.fr/check_paiement?cle=a14axQHk8JA6dfpjNmwPoRpCvcTQg5WG8pM6N6Tzmo75XcgIoy&concours=<ID_Concours>

    // Ce fichier permet de :
    //  - récupérer le courrier type joint au chèque de paiement des départs aux concours
    
    // La documentation de ce plugin est accessible sur http://www.tinybutstrong.com/manual.php

    // Niveau de Log demandé parmi INFO, ERROR, CRITIC
    
    # Definition des variables de Zone
    setlocale(LC_ALL, 'fr_FR');
    setlocale(LC_TIME, 'fr_FR.utf8','fra'); 
    $arrLevel = array("INFO", "ALERT", "ERROR");

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

    function calculDistanceBlason($strCategorie,$strTypeEpr) {
        $arrDistBlason = [
            "PFCL" => ["Coupe" => ["distance" => "20m", "blason" => "80cm"], "Championnat" => ["distance" => "20m", "blason" => "80cm"]],
            "PHCL" => ["Coupe" => ["distance" => "20m", "blason" => "80cm"], "Championnat" => ["distance" => "20m", "blason" => "80cm"]],
            "BFCL" => ["Coupe" => ["distance" => "30m", "blason" => "80cm"], "Championnat" => ["distance" => "30m", "blason" => "80cm"]],
            "BHCL" => ["Coupe" => ["distance" => "30m", "blason" => "80cm"], "Championnat" => ["distance" => "30m", "blason" => "80cm"]],
            "MFCL" => ["Coupe" => ["distance" => "40m", "blason" => "80cm"], "Championnat" => ["distance" => "40m", "blason" => "80cm"]],
            "MHCL" => ["Coupe" => ["distance" => "40m", "blason" => "80cm"], "Championnat" => ["distance" => "40m", "blason" => "80cm"]],
            "CFCL" => ["Coupe" => ["distance" => "60m", "blason" => "122cm"], "Championnat" => ["distance" => "60m", "blason" => "122cm"]],
            "CHCL" => ["Coupe" => ["distance" => "60m", "blason" => "122cm"], "Championnat" => ["distance" => "60m", "blason" => "122cm"]],
            "JFCL" => ["Coupe" => ["distance" => "50m", "blason" => "122cm"], "Championnat" => ["distance" => "70m", "blason" => "122cm"]],
            "JHCL" => ["Coupe" => ["distance" => "50m", "blason" => "122cm"], "Championnat" => ["distance" => "70m", "blason" => "122cm"]],
            "S1FCL" => ["Coupe" => ["distance" => "50m", "blason" => "122cm"], "Championnat" => ["distance" => "70m", "blason" => "122cm"]],
            "S1HCL" => ["Coupe" => ["distance" => "50m", "blason" => "122cm"], "Championnat" => ["distance" => "70m", "blason" => "122cm"]],
            "S2FCL" => ["Coupe" => ["distance" => "50m", "blason" => "122cm"], "Championnat" => ["distance" => "70m", "blason" => "122cm"]],
            "S2HCL" => ["Coupe" => ["distance" => "50m", "blason" => "122cm"], "Championnat" => ["distance" => "70m", "blason" => "122cm"]],
            "S3FCL" => ["Coupe" => ["distance" => "50m", "blason" => "122cm"], "Championnat" => ["distance" => "70m", "blason" => "122cm"]],
            "S3HCL" => ["Coupe" => ["distance" => "50m", "blason" => "122cm"], "Championnat" => ["distance" => "70m", "blason" => "122cm"]],

            "CFCO" => ["Coupe" => ["distance" => "50m", "blason" => "80cm"], "Championnat" => ["distance" => "50m", "blason" => "80cm"]],
            "CHCO" => ["Coupe" => ["distance" => "50m", "blason" => "80cm"], "Championnat" => ["distance" => "50m", "blason" => "80cm"]],
            "JFCO" => ["Coupe" => ["distance" => "50m", "blason" => "122cm"], "Championnat" => ["distance" => "50m", "blason" => "80cm"]],
            "JHCO" => ["Coupe" => ["distance" => "50m", "blason" => "122cm"], "Championnat" => ["distance" => "50m", "blason" => "80cm"]],
            "S1FCO" => ["Coupe" => ["distance" => "50m", "blason" => "122cm"], "Championnat" => ["distance" => "50m", "blason" => "80cm"]],
            "S1HCO" => ["Coupe" => ["distance" => "50m", "blason" => "122cm"], "Championnat" => ["distance" => "50m", "blason" => "80cm"]],
            "S2FCO" => ["Coupe" => ["distance" => "50m", "blason" => "122cm"], "Championnat" => ["distance" => "50m", "blason" => "80cm"]],
            "S2HCO" => ["Coupe" => ["distance" => "50m", "blason" => "122cm"], "Championnat" => ["distance" => "50m", "blason" => "80cm"]],
            "S3FCO" => ["Coupe" => ["distance" => "50m", "blason" => "122cm"], "Championnat" => ["distance" => "50m", "blason" => "80cm"]],
            "S3HCO" => ["Coupe" => ["distance" => "50m", "blason" => "122cm"], "Championnat" => ["distance" => "50m", "blason" => "80cm"]]
        ];

        return $arrDistBlason[$strCategorie][$strTypeEpr];
    }

    function calculMontantDeparts($arrListeDeparts) {
        $floatMontantGlobal = 0;
        $strLogin = '';

        foreach ($arrListeDeparts as $key => $rowConcours) {
            // Calcul du montant du départ
            // Si le licencié courant est le même que le licencié précédent
            if ($strLogin == $rowConcours['usr_login']) {
                // Si le licencié est adulte
                if ($rowConcours['r_epr_usr_age'] == 'Adulte') {
                    // Prendre l'autre départ adulte
                    $floatMontant = floatval($rowConcours['epr_adulte_other_dep']);
                } else {
                    // Prendre l'autre départ jeune
                    $floatMontant = floatval($rowConcours['epr_jeune_other_dep']);
                }
            } else {
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
        }

        return number_format($floatMontantGlobal, 2, ',', ' ');
    }

    function Hex2String($hex){
        $string='';
        for ($i=0; $i < strlen($hex)-1; $i+=2){
            $string .= chr(hexdec($hex[$i].$hex[$i+1]));
        }
        return $string;
    }

    $adr_ligne1 = 0;
    $adr_ligne2 = 0;
    $adr_ligne3 = 0;

    if (isset($_GET['cle']) && isset($_GET['concours'])) {
        if ($_GET['cle'] == 'a14axQHk8JA6dfpjNmwPoRpCvcTQg5WG8pM6N6Tzmo75XcgIoy') {
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
            require_once PATH_MODELS . 'model_class_concours.php';
                
            $intID_Concours = $_GET['concours'];
            $objConcours = new ClassConcours();

            // Test si le n° de concours existe
            if ($objConcours->existConcours($intID_Concours)) {
                
                require_once PATH_ROOT . 'phpodt/tbs_class.php';            // Load the TinyButStrong template engine 
                require_once PATH_ROOT . 'phpodt/tbs_plugin_opentbs.php';   // Load the OpenTBS plugin 
    
                if ($debug) {
                    echo "Timezone actuel : " . date_default_timezone_get();
                    echo "<h3> PHP List All Post Variables</h3>";
                    var_dump($_POST);
                    echo "<h3> PHP List All Server Variables</h3>";
                    var_dump($_SERVER);
                }

                // echo "Traitement du concours " . $intID_Concours;
    
                // Vérification de la présence de données à traiter pour ce concours
                $arrListeDeparts = $objConcours->listeDepartsAPayer($intID_Concours);
                if (!empty($arrListeDeparts)) {
    
                    // Initialise l'instance TBS
                    $TBS = new clsTinyButStrong;                                         // Nouvelle instance TBS
                    $TBS->Plugin(TBS_INSTALL, OPENTBS_PLUGIN);                            // Charge le plugin OpenTBS qui gère les documents OpenOffice 
    
                    // charge le template OpenOffice
                    // $TBS->LoadTemplate(PATH_ROOT . 'phpodt/template_courrier.odt');        // Charge le document OpenOffice 'template_courrier.odt'
    
                    // Définition des variables du document
                    
                    $montant = calculMontantDeparts($arrListeDeparts);
                    // $montant = "14,00";

                    $arrInfoConcours = $objConcours->recupererInfoConcours($intID_Concours);
                    // var_dump($arrInfoConcours);
                    // Charge le document OpenOffice 'template_courrier.odt'
                    switch ($arrInfoConcours[0]['epr_discipline_norme']) {
                        case 'SALLE':
                            $TBS->LoadTemplate(PATH_ROOT . 'phpodt/template_courrier_salle.odt');
                            break;
                        case 'TAE':
                            $TBS->LoadTemplate(PATH_ROOT . 'phpodt/template_courrier_tae.odt');
                            break;
                        
                        default:
                            # code...
                            break;
                    }
                    $adresseBrute = utf8_decode($arrInfoConcours[0]['epr_adresse_inscript']);
                    // $adresseBrute = $arrInfoConcours[0]['epr_adresse_inscript'];

                    $genre = "Madame, Monsieur";
                    if (strpos(ucfirst($adresseBrute), "Monsieur") !== false) {$genre = "Monsieur";}
                    if (strpos(ucfirst($adresseBrute), "Madame") !== false) {$genre = "Madame";}
                    // $genre = "Monsieur";
                    
                    $adresseBrute = str_replace("’", "'", $adresseBrute);
                    $arrAdresse = explode('\n',$adresseBrute,3);

                    $adr_ligne1 = "Adresse inconnue";
                    $adr_ligne2 = 0;
                    $adr_ligne3 = 0;

                    switch (count($arrAdresse)) {
                        case 3:
                            $adr_ligne1 = $arrAdresse[0];
                            $adr_ligne2 = $arrAdresse[1];
                            $adr_ligne3 = $arrAdresse[2];
                            break;
                        case 2:
                            $adr_ligne1 = $arrAdresse[0];
                            $adr_ligne2 = $arrAdresse[1];
                            $adr_ligne3 = 0;
                            break;
                        case 1:
                            $adr_ligne1 = $arrAdresse[0];
                            $adr_ligne2 = 0;
                            $adr_ligne3 = 0;
                            break;
                        default:
                            
                            break;
                    }

                    $adr = array();
                    
                    foreach ($arrAdresse as $key => $value) {
                        $adr[] = array('line' => $value);
                    }
                    
                    $arrDeparts = $objConcours->listeInscritsAUnConcours($intID_Concours);
                    
                    $data = array(); 
                    
                    foreach ($arrDeparts as $key => $value) {
                        $dateConcours = new DateTime($value['r_epr_date']);
                        switch ($arrInfoConcours[0]['epr_discipline_norme']) {
                            case 'SALLE':
                                $strblason = $value['r_epr_usr_blason'];
                                $strdistance = '';
                                break;
                            case 'TAE':
                                $strblason = calculDistanceBlason($value['usr_cat'], $value['r_epr_usr_type_epr'])['blason'];
                                $strdistance = calculDistanceBlason($value['usr_cat'], $value['r_epr_usr_type_epr'])['distance'];
                                break;
                            
                            default:
                                # code...
                                break;
                        }
                        $data[] = array(
                            'name' => $value['NomInscrit'],
                            'licence' => $value['usr_login'],
                            'depart' => utf8_decode(str_replace('_',' ',$value['r_epr_usr_depart'])),
                            'date' => date_format($dateConcours, 'd/m/Y'),
                            'blason' => $strblason,
                            'distance' => $strdistance
                        );
                    }
                    
                    // $data[] = array(
                    //     'firstname'=>'Maxence',
                    //     'name'=>'POLLET',
                    //     'licence'=>'929334W',
                    //     'depart'=>utf8_decode('Dimanche Après-Midi'),
                    //     'date'=>'07 octobre 2018',
                    //     'blason'=>'blason'
                    // );
                    // $data[] = array(
                    //     'firstname'=>'Maxence',
                    //     'name'=>'POLLET',
                    //     'licence'=>'929334W',
                    //     'depart'=>utf8_decode('Dimanche Matin'),
                    //     'date'=>'07 octobre 2018',
                    //     'blason'=>'blason'
                    // );
    
                    // Définition du document final
                    // $output_file_name = PATH_ROOT . 'phpodt/' . date_format($dateConcours, 'Ymd') . ' - ' . $arrInfoConcours[0]['epr_lieu'] . '.odt';
                    $output_file_name = date_format($dateConcours, 'Ymd') . ' - ' . $arrInfoConcours[0]['epr_lieu'] . '.odt';
    
                    // Traitement des données
                    $TBS->MergeBlock('adr', $adr);
                    $TBS->MergeBlock('dep', $data);

                    // Compilation du document modifié et téléchargement du document compilé ($output_file_name est optionnel)
                    $TBS->Show(OPENTBS_DOWNLOAD, $output_file_name);            // Fusionne tous les champs [onshow] automatiquement.
    
                    // Compilation du document modifié et sortie sur le fichier sauvegardé sur le serveur
                    // $TBS->Show(OPENTBS_FILE, $output_file_name);                // Fusionne tous les champs [onshow] automatiquement.
    
                    // echo "Fin du traitement du concours " . $intID_Concours;
                } else {
                    echo "Pas de données à traiter pour ce concours !";
                }
    
            } else {
                echo "le n° de concours indiqué n'existe pas !";
            }
        } else {
            echo "La clé n'est pas valide !";
        }
    } else {
        echo "Les paramètres ne sont pas bien définie !";
    }
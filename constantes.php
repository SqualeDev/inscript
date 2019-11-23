<?php
    # Definition des variables de Zone
    setlocale(LC_ALL, 'fr_FR');
    setlocale(LC_TIME, 'fr_FR.utf8','fra'); 
    date_default_timezone_set('Europe/Paris');

    require_once 'config_local.php';
    
    # mise en forme debug echo
    function display_debug($key, $value, $debug=false) {
        if($debug) {
            echo 'Fonction debug activée !<br>';
            echo ''.$key . " < = > ";
            switch (gettype($value)) {
                case 'string' :
                    echo $value.'<br>';
                    break;
                case 'array' :
                case 'object' :
                default :
                    echo '<pre>';
                    print_r($value);
                    echo '</pre>';
                    break;
            }
        }
    }

    $lblAccueil = "Bienvenue sur le site du CSG Tir à l'arc !";

    $lblConsigneNull = "Pas de consigne pour le moment ! Voir avec votre entraineur pour en avoir une.";

    // Nombre de concours maximum payé par le club (Décision du bureau en AG)
    $intNbConcoursMax = 11;

    $arrCatAdultes = array(
        "S1F","S1FCL","S1FCO","SF","SFCL","SFCO",
        "S2F","S2FCL","S2FCO","VF","VFCL","VFCO",
        "S3F","S3FCL","S3FCO","SVF","SVFCL","SVFCO",
        "S1H","S1HCL","S1HCO","SH","SHCL","SHCO",
        "S2H","S2HCL","S2HCO","VH","VHCL","VHCO",
        "S3H","S3HCL","S3HCO","SVH","SVHCL","SVHCO"
        );

    $arrCatJeunes = array(
        "PF","PFCL","PFCO",
        "BF","BFCL","BFCO",
        "MF","MFCL","MFCO",
        "CF","CFCL","CFCO",
        "JF","JFCL","JFCO",
        "PH","PHCL","PHCO",
        "BH","BHCL","BHCO",
        "MH","MHCL","MHCO",
        "CH","CHCL","CHCO",
        "JH","JHCL","JHCO"
        );
    
    // Pour récupérer la distance et le blason, utiliser le tableau comme ceci : $arrDistBlason[<Catégorie de l'archer>][<Type d'épreuve>]
    $arrDistBlason = [
        "PFCL" => [
            "Coupe" => ["distance" => "20m", "blason" => "80cm"],
            "Championnat" => ["distance" => "20m", "blason" => "80cm"],
            "FITA" => ["distance" => "20m", "blason" => "80cm"],
            "FEDERAL" => ["distance" => "20m", "blason" => "80cm"],
            "SALLE" => ["distance" => "18m", "blason" => "60cm"],
        ],
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
    
    // if ($local) {
    //     $strSlash = "\\"; 
    // } else {
        $strSlash = "/";
    // }

    // PATH
    define("PATH_ROOT", $_SERVER['DOCUMENT_ROOT'] . $strSlash . $pathUrl);
    // define("PATH_ROOT", $_SERVER['DOCUMENT_ROOT'] . $strSlash);
    // echo PATH_ROOT;
    define("PATH_CONTROLS", PATH_ROOT . "controls". $strSlash);
    define("PATH_CSS", PATH_ROOT . "css". $strSlash);
    define("PATH_DATABASE", PATH_ROOT . "database". $strSlash);
    define("PATH_JVS", PATH_ROOT . "js". $strSlash);
    define("PATH_LANG", PATH_ROOT . "lang". $strSlash);
    define("PATH_MODELS", PATH_ROOT . "models". $strSlash);
    define("PATH_PICTURES", PATH_ROOT . "pictures". $strSlash);
    define("PATH_VIEWS", PATH_ROOT . "views". $strSlash);
    define("PATH_LOGS", PATH_ROOT . "logs". $strSlash);
    define("PATH_EXTRACTS", PATH_VIEWS . "resultats". $strSlash . "extracts" . $strSlash);
    define("PATH_CLASSEMENTS", PATH_EXTRACTS . "classements". $strSlash);
    define("PATH_DOCS", PATH_ROOT . "docs". $strSlash);
    define("PATH_MANDATS", PATH_DOCS . "mandats". $strSlash);
    
    
    // URL
    // $strProtocol = $_SERVER['REQUEST_SCHEME'];
    // $baseUrl =  $strProtocol . "://" . $_SERVER['SERVER_NAME'];
    // $baseUrl =  "https://" . "csg.grippon.fr";
    // define("URL_ROOT", $baseUrl . $strSlash . $pathUrl);
    define("URL_ROOT", $baseUrl . $strSlash);
    // echo URL_ROOT;
    define("URL_INDEX", URL_ROOT . "index.php". $strSlash);
    define("URL_CSS", URL_ROOT . "css". $strSlash);
    define("URL_JVS", URL_ROOT . "js". $strSlash);
    define("URL_PCT", URL_ROOT . "pictures". $strSlash);
    define("URL_DOC", URL_ROOT . "docs". $strSlash);
    // define("URL_ART", URL_DOC . "articles". $strSlash);
?>

<?php
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
        
    $intConcoursID = $_POST['ConcoursID'];

    require_once PATH_MODELS . 'model_class_concours.php';
    $objInscrits  = new ClassConcours();

    // Récupération des autres données via Ajax :
        //  - email, adresse postale, tarifs, ...
        //  - départ(s) à valider

    $arrConcours = $objInscrits->recupererInfoConcours($intConcoursID)[0];
    $boolIsClos = $objInscrits->isConcoursClos($intConcoursID);
    $arrInscrits = $objInscrits->listeInscritsAUnConcours($intConcoursID);

    $strHTML = '';

    // $strHTML = var_dump($arrConcours);

    if ($boolIsClos) {
        $strModalHeaderDate = 'modal-header-danger';
        $strEtatBtEnregistrer = 'disabled';
    } else {
        $strModalHeaderDate = 'modal-header-success';
        $strEtatBtEnregistrer = '';
    }

    $strHTML .= '<div class="d-flex justify-content-center">';
    $strHTML .= '<div class="modal-header text-center">';
    // Affichage Dates
    $strDateDeb = strftime("%d/%m/%Y",strtotime($arrConcours['epr_date_deb']));
    $strDateFin = strftime("%d/%m/%Y",strtotime($arrConcours['epr_date_fin']));;
    $strHTML .= '<h4 class="title ' . $strModalHeaderDate . '" id="modalHeaderDate">Concours du '. $strDateDeb . ' au ' . $strDateFin . '</h4>';
    $strHTML .= '</div>';
    $strHTML .= '</div>';

    $strHTML .= '<div class="modal-header">';
    $strHTML .= '<h5 class="modal-title" id="modalHeaderLieu">';
    // Affichage Discipline
    $strHTML .= $arrConcours['epr_discipline'] . '<br>';
    // Affichage Lieu
    $strHTML .= $arrConcours['epr_lieu'];
    $strHTML .= '</h5>';
    $strHTML .= '</div>';

    $strHTML .= '<div class="modal-header">';
    $strHTML .= '<h5 class="modal-title" id="modalHeaderDescription">';
    // Affichage Descritpion
    $strHTML .= $arrConcours['epr_nom'];
    $strHTML .= ' (' . $arrConcours['epr_caracteristique'] . ')';
    $strHTML .= '</div>';

    $strHTML .= '<div class="modal-body">';
    $strHTML .= '<div class="md-form">';

    // Fieldset Informations Concours
    $strHTML .= '<form id="concoursForm" name="concours" role="form">';
        $strHTML .= '<legend class="col-form-label text-center blue lighten-3"><strong>Informations du concours n° '. $intConcoursID . '</strong></legend>';
            // Input Adresse du concours (lieu de la compétition)
            $strHTML .= '<div class="md-form input-group">';
            $strHTML .= '<div class="input-group-prepend">';
            $strHTML .= '<span class="input-group-text" style="font-size: 0.8rem;">Adresse</span>';
            $strHTML .= '</div>';
            $strHTML .= '<textarea class="form-control md-textarea" name="inputAdresse">'. $arrConcours['epr_adresse'] .'</textarea>';
            $strHTML .= '</div>';

            if ($arrConcours['epr_gps'] != '' && $arrConcours['epr_gps'] != 'NULL') {$arrGPS = explode('/', $arrConcours['epr_gps']);} else {$arrGPS[0] = 0.00000; $arrGPS[1] = 0;}
            // Input coordonnées GPS du concours
            $strHTML .= '<div class="md-form input-group">';
            $strHTML .= '<div class="input-group-prepend">';
            $strHTML .= '<span class="input-group-text" style="font-size: 0.8rem;">Coordonnées GPS</span>';
            $strHTML .= '</div>';
            $strHTML .= '<input type="text" class="form-control text-center" name="inputGPSLat" value="'. $arrGPS[0] . '" placeholder="49.00000">';
            $strHTML .= '/';
            $strHTML .= '<input type="text" class="form-control text-center" name="inputGPSLong" value="'. $arrGPS[1] . '" placeholder="1.00000">';
            $strHTML .= '</div>';

            // Upload du mandat
            if ($arrConcours['epr_mandat'] != '' && $arrConcours['epr_mandat'] != 'NULL') {
                $strLblNameFileMandat = $arrConcours['epr_mandat'];
                $strInputFileMandat = $arrConcours['epr_mandat'];
            } else {
                $strLblNameFileMandat = 'Choisir le mandat';
                $strInputFileMandat = '';
            }
            $strHTML .= '<div class="input-group">';
            $strHTML .= '<div class="custom-file">';
            $strHTML .= '<input type="hidden" class="form-control" id="inputNameFileMandat" name="inputNameFileMandat" value="'. $strLblNameFileMandat . '">';
            $strHTML .= '<input type="hidden" class="form-control" id="inputPathFileMandat" name="inputPathFileMandat" value="">';
            $strHTML .= '<input type="file" class="custom-file-input" id="inputFileMandat" name="inputFileMandat" data-show-preview="false" value="'.$strInputFileMandat.'">';
            $strHTML .= '<label class="custom-file-label" id="labelFileMandat" for="inputFileMandat">'. $strLblNameFileMandat .'</label>';
            $strHTML .= '</div>';
            $strHTML .= '</div>';
            $strHTML .= '<br/>';
            
            // Liste déroulante de l'état (Clos, Ouvert, Préinscription, ...)
            // $arrEtat = ['Clos', 'Préinscription', 'Validé', 'Ouvert'];

            // $strHTML .= '<div class="form-group">';
            // $strHTML .= '<div class="row">';
            
            // foreach ($arrEtat as $key => $value) {
            //     $strCheck = "";
            //     if ($arrConcours['epr_etat'] == $value) {$strCheck = "checked";}
            //     $strHTML .= '<div class="col-3">';
            //     $strHTML .= '<div class="custom-control custom-radio">';
            //     $strHTML .= '<label><input type="radio" name="radioEtat_' . $value . '" ' . $strCheck . '/> ' . $value . '</label>';
            //     $strHTML .= '</div>';
            //     $strHTML .= '</div>';
            // }
                
            // $strHTML .= '</div>';
            // $strHTML .= '</div>';

            $strHTML .= '<legend class="col-form-label text-center blue lighten-3"><strong>Informations du club organisateur</strong></legend>';
            // Input n° de téléphone du club organisateur
            $strHTML .= '<div class="md-form input-group">';
            $strHTML .= '<div class="input-group-prepend">';
            $strHTML .= '<span class="input-group-text" style="font-size: 0.8rem;">n° de téléphone</span>';
            $strHTML .= '</div>';
            $strHTML .= '<input type="text" class="form-control text-center" name="inputTelephoneClub" value="'. $arrConcours['epr_telephone'] .'">';
            $strHTML .= '</div>';

            // Input adresse mail du club organisateur
            $strHTML .= '<div class="md-form input-group">';
            $strHTML .= '<div class="input-group-prepend">';
            $strHTML .= '<span class="input-group-text" style="font-size: 0.8rem;">Adresse mail</span>';
            $strHTML .= '</div>';
            $strHTML .= '<input type="email" class="form-control text-center" name="inputMailClub" value="'. $arrConcours['epr_mail'] .'">';
            $strHTML .= '</div>';

            // Input adresse postale du club organisateur
            $strHTML .= '<div class="md-form input-group">';
            $strHTML .= '<div class="input-group-prepend">';
            $strHTML .= '<span class="input-group-text" style="font-size: 0.8rem;">Adresse postale</span>';
            $strHTML .= '</div>';
            $strHTML .= '<textarea class="form-control md-textarea" name="inputAdresseClub">'. $arrConcours['epr_adresse_inscript'] .'</textarea>';
            $strHTML .= '</div>';

            // Input URL du site WEB du club organisateur
            $strHTML .= '<div class="md-form input-group">';
            $strHTML .= '<div class="input-group-prepend">';
            $strHTML .= '<span class="input-group-text" style="font-size: 0.8rem;">site Web</span>';
            $strHTML .= '</div>';
            $strHTML .= '<input type="url" class="form-control text-center" name="inputSiteWeb" value="'. $arrConcours['epr_siteweb'] .'">';
            $strHTML .= '</div>';

            // Fieldset Tarifs
            $strHTML .= '<legend class="col-form-label text-center blue lighten-3"><strong>Montant des départs</strong></legend>';
            
                $strHTML .= '<div class="d-flex flex-row justify-content-between">';
                    $strHTML .= '<div class="p-2"><p style="text-align: center;">Catégories Adulte : </p></div>';
                    $arrCateg = ['S3','S2','S1','J','C','M','B','P'];
                    $arrCategConcours = explode(',',$arrConcours['epr_cat_adulte']);
                    foreach ($arrCateg as $key => $strCateg) {
                        $strChecked = "";
                        if (in_array($strCateg, $arrCategConcours)) { $strChecked = "checked";}
                        $strHTML .= '<div class="col-1">';
                        $strHTML .= '<div class="custom-control custom-checkbox">';
                            $strHTML .= '<label style="font-size: 0.9rem;"><input type="checkbox" name="chkCategAdulte_'.$strCateg.'" value="'.$strCateg.'" '.$strChecked.'/> '.$strCateg.' </label>';
                        $strHTML .= '</div>';
                        $strHTML .= '</div>';
                    }
                $strHTML .= '</div>';
                
                $strHTML .= '<div class="row">';
                    $strHTML .= '<div class="col">';
                        // Input tarif 1er départ Adulte
                        $strHTML .= '<div class="md-form input-group">';
                        $strHTML .= '<div class="input-group-prepend">';
                        $strHTML .= '<span class="input-group-text" style="font-size: 0.8rem;">1er départ Adulte</span>';
                        $strHTML .= '</div>';
                        $strHTML .= '<input id="tarif" type="number" min="0.00" step="0.01" class="form-control text-center" name="inputTarif1Adulte" value="'. $arrConcours['epr_adulte_first_dep'] .'">';
                        $strHTML .= '<div class="input-group-prepend">';
                        $strHTML .= '<span class="input-group-text" style="font-size: 0.8rem;">€</span>';
                        $strHTML .= '</div>';
                        $strHTML .= '</div>';

                    $strHTML .= '</div>';
                    $strHTML .= '<div class="col">';
                        // Input tarif départ(s) suivant(s) Adulte
                        $strHTML .= '<div class="md-form input-group">';
                        $strHTML .= '<div class="input-group-prepend">';
                        $strHTML .= '<span class="input-group-text" style="font-size: 0.8rem;">départs suivants</span>';
                        $strHTML .= '</div>';
                        $strHTML .= '<input id="tarif" type="number" min="0.00" step="0.01" class="form-control text-center" name="inputTarif2Adulte" value="'. $arrConcours['epr_adulte_other_dep'] .'">';
                        $strHTML .= '<div class="input-group-prepend">';
                        $strHTML .= '<span class="input-group-text" style="font-size: 0.8rem;">€</span>';
                        $strHTML .= '</div>';
                        $strHTML .= '</div>';

                    $strHTML .= '</div>';
                $strHTML .= '</div>';

                $strHTML .= '<div class="row">';
                    $strHTML .= '<div class="col">';
                        // Input tarif 1er départ Jeune
                        $strHTML .= '<div class="md-form input-group">';
                        $strHTML .= '<div class="input-group-prepend">';
                        $strHTML .= '<span class="input-group-text" style="font-size: 0.8rem;">1er départ Jeune</span>';
                        $strHTML .= '</div>';
                        $strHTML .= '<input id="tarif" type="number" min="0.00" step="0.01" class="form-control text-center" name="inputTarif1Jeune" value="'. $arrConcours['epr_jeune_first_dep'] .'">';
                        $strHTML .= '<div class="input-group-prepend">';
                        $strHTML .= '<span class="input-group-text" style="font-size: 0.8rem;">€</span>';
                        $strHTML .= '</div>';
                        $strHTML .= '</div>';

                    $strHTML .= '</div>';
                    $strHTML .= '<div class="col">';
                        // Input tarif départ(s) suivant(s) Jeune
                        $strHTML .= '<div class="md-form input-group">';
                        $strHTML .= '<div class="input-group-prepend">';
                        $strHTML .= '<span class="input-group-text" style="font-size: 0.8rem;">départs suivants</span>';
                        $strHTML .= '</div>';
                        $strHTML .= '<input id="tarif" type="number" min="0.00" step="0.01" class="form-control text-center" name="inputTarif2Jeune" value="'. $arrConcours['epr_jeune_other_dep'] .'">';
                        $strHTML .= '<div class="input-group-prepend">';
                        $strHTML .= '<span class="input-group-text" style="font-size: 0.8rem;">€</span>';
                        $strHTML .= '</div>';
                        $strHTML .= '</div>';

                    $strHTML .= '</div>';
                $strHTML .= '</div>';

            // Bouton de validation des informations
    
        // Fieldset validation inscription
            $strHTML .= '<legend class="col-form-label text-center blue lighten-3"><strong>Liste des départs du concours pour validation</strong></legend>';
        
            // Liste des départs à valider :
            if ($arrInscrits) {
                $ind = 1;
                foreach ($arrInscrits as $key => $value) {
                    // Pour la case à cocher, utiliser un bouton comme pour le choix des blasons lors des départs
                    // Case à cocher pour valider    
                    // n° de licence
                    // Prénom et Nom du licencié inscrit
                    // Date - Horaire du départ à valider

                    $arrDate = explode('_',$value['r_epr_usr_depart']);
                    $strDepartDate = "départ du " . $arrDate[0] . " " . $arrDate[1] . " le " . strftime("%d %B %Y",strtotime($value['r_epr_date']));
                    
                    if ($value['r_epr_usr_valide'] == '') {
                        $strDepartUser = '';
                    } else {
                        $strDepartUser = 'checked';
                    }

                    if ($value['r_epr_usr_num_cheque'] == '' || $value['r_epr_usr_num_cheque'] == 'NULL') {
                        $strNumCheque = 'placeholder="n° de chèque"';
                    } else {
                        $strNumCheque = 'value="' . $value['r_epr_usr_num_cheque'] . '"';
                    }
                    
                    $strNameInput = $value['usr_login'] . '_' . $value['epr_id']. '_' . $value['r_epr_usr_depart'];
                    $strLabelInput = $value['NomInscrit'] . " " . $strDepartDate;
                    if ($value['r_epr_usr_blason'] != "") { $strLabelInput = $strLabelInput . " sur "  . $value['r_epr_usr_blason'];}
                    if ($value['r_epr_usr_type_epr'] != "") { $strLabelInput = $strLabelInput . " en "  . $value['r_epr_usr_type_epr'];}
                    
                    $strHTML .= '<div class="form-group">';
                    $strHTML .= '<div class="row">';
                    $strHTML .= '<div class="col-9">';
                    $strHTML .= '<input name="hidDepart_' . $ind . '" type="hidden" value="' . $strNameInput . '">';
                    $strHTML .= '<div class="custom-control custom-checkbox">';
                    $strHTML .= '<label style="font-size: 0.9rem;"><input type="checkbox" ' . $strDepartUser . ' name="chkDepart_' . $ind . '" value="' . $strNameInput . '"/> ' . $strLabelInput . '</label>';
                    $strHTML .= '</div>';
                    $strHTML .= '</div>';
                    $strHTML .= '<div class="col-3">';
                    $strHTML .= '<input class="form-control form-control-sm" type="text" name="numChequeDepart_' . $ind . '" ' . $strNumCheque . '>';
                    $strHTML .= '</div>';
                    $strHTML .= '</div>';
                    $strHTML .= '</div>';

                    $ind++;
                }
            } else {
                $strHTML .= "Aucun participants pour l'instant à ce concours";
            }
            
            // Bouton de validation des départs

            $strHTML .= '<div class="modal-footer" style="display: none;">';
            $strHTML .= '<label for="modalID">ID</label>';
            $strHTML .= '<input type="text" class="form-control" name="modalID" id="modalID" value="'. $intConcoursID .'">';
            $strHTML .= '</div>';

            $strHTML .= '<div class="modal-footer">';
            $strHTML .= '    <a class="btn btn-info mr-auto" href="'. URL_ROOT . '/check_paiement.php?cle=a14axQHk8JA6dfpjNmwPoRpCvcTQg5WG8pM6N6Tzmo75XcgIoy&concours='. $intConcoursID .'" target="_blank">Télécharger courrier paiement</a>';
            $strHTML .= '    <button type="submit" class="btn btn-primary" id="modalConcoursSubmit">Valider</button>';
            $strHTML .= '    <button type="button" class="btn btn-danger" data-dismiss="modal">Annuler</button>';
            $strHTML .= '</div>';
        
        $strHTML .= '</form>';

    $strHTML .= '</br>';
    $strHTML .= '</div>';

echo $strHTML;

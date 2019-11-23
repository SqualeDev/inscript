    <div class="m-2">
        <h2>Liste de vos entrainements</h2>
        <p>Sélectionner l'archer pour lequel vous voulez voir les entrainements.</p>
        <div class="d-flex flex-row justify-content-center">
            <div class="col-4">
                <select class="browser-default custom-select" id="selectArcher">
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
                        require_once PATH_MODELS . 'model_class_Entrainement.php';
                        $objCompetiteur  = new classEntrainement();
                        echo $objCompetiteur->afficherListeCompetiteurs();
                    ?>
                </select>
            </div>
        </div>
        <br/>
        <p>La consigne actuelle de l'archer selectionné :</p>
        <div id="divConsigne"></div>
        <br/>
        <div class="row align-items-center">
            <div class="col-8">
                <input class="form-control" id="inputSearchEntrainement" type="text" placeholder="Filtrer le tableau.." autofocus>
            </div>
            <div class="col">
                <button type="button" class="btn btn-info" id="btAllEntrainement">Tous les entrainements</button>
            </div>
        </div>
        <br>
        <table class="table table-sm table-bordered table-striped">
            <thead>
                <tr>
                    <th class="text-center" rowspan="2" style="display: none;">ID</th>
                    <th class="text-center" rowspan="2">
                        Date
                        <!-- <a><i class="fas fa-plus-square" id="btAjoutEntrainenement"></i></a> -->
                    </th>
                    <th class="text-center" rowspan="2">A travailler pour la scéance</th>
                    <th class="text-center" colspan="6">Volume</th>
                    <th class="text-center" rowspan="2">Retour de la scéance</th>
                    <th class="text-center" rowspan="2">Nv de forme</th>
                </tr>
                <tr>
                    <th class="text-center">Paille</th>
                    <th class="text-center">Visuel</th>
                    <th class="text-center">Cible</th>
                    <th class="text-center">Compté</th>
                    <th class="text-center">Compét.</th>
                    <th class="text-center">Total</th>
                </tr>
            </thead>
            <tbody id="divEntrainement"></tbody>
        </table>
    </div>

    <!-- Modals -->
    <!-- Modals Consigne Salle -->
    <div id="modalConsigne" class="modal fade" role="dialog">
        <div class="modal-dialog">
            
            <div class="modal-content" >
                <div class="modal-header">
                    <h4 class="title" id="modalHeader">Editer la consigne courante</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="consigneForm" name="consigne" role="form">
                    <div class="modal-body" id="modalConsigne">
                        <div class="form-group shadow-textarea">
                            <i class="fas fa-angle-double-right prefix"></i>
                            <label for="modalOldConsigneInput">Ancienne Consigne</label>
                            <textarea class="form-control z-depth-1" id="modalOldConsigneInput" rows="2" readonly></textarea>
                        </div>
                        <div class="form-group shadow-textarea">
                            <i class="fas fa-pencil-alt prefix"></i>
                            <label for="modalNewConsigneInput">Nouvelle Consigne</label>
                            <textarea class="form-control z-depth-1" id="modalNewConsigneInput" rows="2" placeholder="Quelle est la nouvelle consigne..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" id="modalSubmitValider">Valider la consigne</button>
                        <button type="button" class="btn btn-warning" id="modalSubmitAnnuler">Annuler la consigne</button>
                    </div>
                    <div class="modal-footer" style="display: none;">
                        <label for="modalID">ID</label>
                        <input type="text" class="form-control" name="modalID" id="modalID">
                    </div>
                    <!-- <div class="modal-footer" style="display: none;">
                        <label for="modalType">Type</label>
                        <input type="text" class="form-control" name="modalType" id="modalType">
                    </div> -->
                </form>
                <div class="modal-header" id="modalHeaderLegend">
                    <details>
                        <summary>Explications</summary>
                        <p>Si vous cliquez sur "Valider la Consigne", Le travail effectué par l'archer sera valider et la nouvelle consigne lui sera affectée.</p>
                        <p>Si vous cliquez sur "Annuler la Consigne", Le travail effectué par l'archer ne sera pas valider et la nouvelle consigne lui sera affectée.</p>
                    </details>
                </div>
                <div class="modal-footer">
                    <h5 class="modal-title" id="modalFooterConsigne"></h5>
                </div>
            </div>

        </div>
    </div>

    <script>
        $(document).ready(function () {
            $("#inputSearchEntrainement").on("keyup", function () {
                var value = $(this).val().toLowerCase();
                $("#divEntrainement tr").filter(function () {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });
            $("form").bind("keypress", function(e) {
                if (e.keyCode == 13) {
                    return false;
                }
            });
            $("#modalSubmitValider").click(function() {
                var strUserID = $("#selectArcher option:selected").val();
                if ($("#divID").html() == "") {
                    var strConsigneID = 0;
                } else {
                    var strConsigneID = $("#divID").html();
                }
                var strConsigneID = $("#divID").html();
                submitValiderConsigne(strUserID, strConsigneID);
            });
            $("#modalSubmitAnnuler").click(function() {
                var strUserID = $("#selectArcher option:selected").val();
                var strConsigneID = $("#divID").html();
                submitAnnulerConsigne(strUserID, strConsigneID);
            });
            $("#btAllEntrainement").click(function() {
                var strUserID = $("#selectArcher option:selected").val();

                if ($(this).text() == 'Tous les entrainements') {
                    $(this).text('Que les prochains entrainements');
                    $(this).toggleClass("btn-info");
                    $(this).toggleClass("btn-primary");
                    $("#Entrainement").empty();

                    afficherListeEntrainements(strUserID, 'all');
                } else {
                    $(this).text('Tous les Entrainement');
                    $(this).toggleClass("btn-info");
                    $(this).toggleClass("btn-primary");
                    $("#Entrainement").empty();

                    afficherListeEntrainements(strUserID, 'now');
                }
            });
            
        });

        $(document).on('click', '#divConsigne', function () {
            var strUserID = $("#selectArcher option:selected").val();
            // Vérifier que le licencie est un coach

            var consigneID = $("#divID").html();
            // alert("test Valider Consigne " + consigneID);
            // $("#modalID").val(consigneID);
            // $("#modalConsigne").modal('show');
            url_ajax_consigne = '../views/ajax_consigne.php';

            $.ajax({
                url         : url_ajax_consigne,
                type        : 'GET',
                data        : 'ConsigneID=' + consigneID + '&userID=' + strUserID + '&method=GET',
                dataType    : 'json',
                success     : function(response, statut) {
                    if (response[0].consigneCourante != null) {
                        // alert("Old Consigne " + response[0].consigneCourante.con_description);
                        $('#modalOldConsigneInput').val(response[0].consigneCourante.con_description);
                        $('#modalSubmitAnnuler').removeClass('d-none');
                        $("#modalConsigne").modal('show');
                    } else {
                        $('#modalOldConsigneInput').val("Pas de consigne !");
                        $('#modalSubmitAnnuler').addClass('d-none');
                        $("#modalConsigne").modal('show');
                    }
                },
                error       : function(response, statut, erreur) {
                    afficheModalErreur();
                },
                complete    : function(response, statut) {

                }
            });
        });

        $(document).on('change', '#selectArcher', function() {
            var strUserID = $("#selectArcher option:selected").val();

            afficherConsigne(strUserID);
            afficherListeEntrainements(strUserID, 'now');
        });

        function afficherConsigne(_strUserID) {
            url_ajax_consigne = '../views/ajax_entrainement_consigne.php';
            $.ajax({
                url         : url_ajax_consigne,
                type        : 'POST',
                data        : 'userID=' + _strUserID,
                dataType : 'html',
                success : function(code_html, statut){
                    $('#divConsigne').html(code_html); // On passe code_html à jQuery() qui va nous créer l'arbre DOM !
                },
                error : function(code_html, statut, erreur){
                    alert("erreur Ajax");
                },
                complete    : function(code_html, statut) {

                }
            });

        }

        function afficherListeEntrainements(_strUserID, _NowOrAll) {
            url_ajax_list = '../views/ajax_entrainement_list.php';
            $.ajax({
                url         : url_ajax_list,
                type        : 'POST',
                data        : 'userID=' + _strUserID + '&Entrainement=' + _NowOrAll,
                dataType : 'html',
                success : function(code_html, statut){
                    $('#divEntrainement').html(code_html); // On passe code_html à jQuery() qui va nous créer l'arbre DOM !
                },
                error : function(code_html, statut, erreur){
                    alert("erreur Ajax");
                },
                complete    : function(code_html, statut) {

                }
            });
        }

        function submitValiderConsigne(_strUserID, _strConsigneID){
            // alert("test Valider Consigne" + _strUserID + _strConsigneID);
            // La date du jour est ajoutée (dans le champ con_dat_fin) à la consigne En cours et l'état (con_etat) est passé à Validée
            // Un formulaire doit permettre l'entrée de la nouvelle consigne
            // La nouvelle consigne est enregistrée dans la base avec comme :
            //    - date de début (con_date_deb), maintenant
            //    - description (con_description), le contenu du champ du formulaire
            //    - etat (con_etat), En cours
            //    - l'archer (usr_login), le n° de licence de l'archer selectionné via la liste déroulante
            var strConsigneNew = $("#modalNewConsigneInput").val();
            // alert("test Valider Consigne" + "\n" + _strUserID + "\n" + _strConsigneID + "\n" + strConsigneNew);
            
            url_ajax_consigne = '../views/ajax_consigne.php';

            $.ajax({
                url         : url_ajax_consigne,
                type        : 'GET',
                data        : 'ConsigneID=' + _strConsigneID + '&userID=' + _strUserID + '&consigneNew=' + strConsigneNew + '&method=VALID',
                dataType    : 'json',
                success     : function(response, statut) {
                    if (response[0].retourValidConsigne && response[0].retourInsertConsigne) {
                    //    alert("retourValidConsigne " + response[0].retourValidConsigne);
                        $("#modalConsigne").modal('hide');
                        // FIXME Forcer le rechargement de la page
                        window.location.href='index.php?controller=entrainement&action=Coach';
                    // } else {
                    //     $('#modalOldConsigneInput').val("Pas de consigne !");
                    //     $("#modalConsigne").modal('show');
                    }
                },
                error       : function(response, statut, erreur) {
                    afficheModalErreur();
                },
                complete    : function(response, statut) {

                }
            });
        }

        function submitAnnulerConsigne(_strUserID, _strConsigneID){
            alert("test Annuler Consigne" + _strUserID + _strConsigneID);
            // La date du jour est ajoutée (dans le champ con_dat_fin) à la consigne En cours et l'état (con_etat) est passé à Annulée
            // Un formulaire doit permettre l'entrée de la nouvelle consigne
            // La nouvelle consigne est enregistrée dans la base avec comme :
            //    - date de début (con_date_deb), maintenant
            //    - description (con_description), le contenu du champ du formulaire
            //    - etat (con_etat), En cours
            //    - l'archer (usr_login), le n° de licence de l'archer selectionné via la liste déroulante
            url_ajax_consigne = '../views/ajax_consigne.php';

            $.ajax({
                url         : url_ajax_consigne,
                type        : 'GET',
                data        : 'ConsigneID=' + consigneID + '&method=CANCEL',
                dataType    : 'json',
                success     : function(response, statut) {
                    // if (response[0].consigneCourante.length != 0) {
                    //     // alert("Old Consigne " + response[0].consigneCourante.con_description);
                    //     $('#modalOldConsigneInput').val(response[0].consigneCourante.con_description);
                    //     $("#modalConsigne").modal('show');
                    // } else {
                    //     $('#modalOldConsigneInput').val("Pas de consigne !");
                    //     $("#modalConsigne").modal('show');
                    // }
                },
                error       : function(response, statut, erreur) {
                    afficheModalErreur();
                },
                complete    : function(response, statut) {

                }
            });
        }
        
        function afficheModalErreur() {
            // FIXME Correction du message affiché si une inscription n'a pas aboutie à cause d'un problème de base de données
            swal.mixin({
            }).queue([{
                title:              "Modification de la consigne Annulée !",
                html:               "Un problème lors de la sauvegarde est survenu, contactez votre administrateur WEB par la petite enveloppe en bas du site.",
                type:               "error",
                confirmButtonText:  "Désolé...",
                confirmButtonColor: '#dc3545'
            }]).then(
            function(isConfirm){
                window.location.href='index.php?controller=entrainement&action=Coach';
            });
        }

    </script>
    <div class="m-2">
        <h2>Liste de vos entrainements</h2>
        <p>Sélectionner votre entrainement en cliquant sur la ligne correspondant à l'entrainement choisi dans le tableau ci-dessous.</p>
        <p>Votre consigne actuelle :</p>
        <div id="divConsigneRow"></div>
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
                        <a><i class="fas fa-plus-square" id="btAjoutEntrainenement"></i></a>
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
    <!-- Modal Entrainement -->
    <div id="modalEntrainement" class="modal fade" role="dialog">
        <div class="modal-dialog">
            
            <div class="modal-content" >
                <div class="d-flex justify-content-center">
                    <div class="modal-header text-center">
                        <h4 class="title" id="modalHeader">Editer l'entrainement</h4>
                    </div>
                </div>


                <div class="modal-body" id="modalEntrainement">
                    <!-- <div class="md-form"> -->
                    <form id="entrainementForm" name="entrainement" role="form">
                        <div class="form-row flex-v-center">
                            <!-- Date de l'entrainement -->
                            <div class="col">
                                <label for="modalDateEntrainement">Date de l'entrainement</label>
                            </div>
                            <div class="col">
                                <input type="text" class="form-control datepicker" id="modalDateEntrainement" >
                            </div>
                        </div>
                        <br/>
                        <!-- Rappel de la consigne -->
                        <legend class="col-form-label text-center blue lighten-3"><strong>Consigne</strong></legend>
                        <div id="modalConsigneID" style="display: none;"></div>
                        <div>
                            <!-- <i class="fas fa-angle-double-right prefix"></i> -->
                            <!-- <label for="modalConsigne">Consigne</label> -->
                            <textarea class="form-control z-depth-1" id="modalConsigne" rows="2" readonly></textarea>
                        </div>
                        <!-- <br/> -->
                        <!-- Volumes -->
                        <legend class="col-form-label text-center blue lighten-3"><strong>Volumes</strong></legend>
                            <div class="form-row">
                                <!-- Paille -->
                                <div class="col">
                                    <div class="md-form input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" style="font-size: 0.8rem;">Paille</span>
                                        </div>
                                        <input class="form-control" type="number" id="inputNbPaille">
                                    </div>
                                </div>
                                <!-- Visuel -->
                                <div class="col">
                                    <div class="md-form input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" style="font-size: 0.8rem;">Visuel</span>
                                        </div>
                                        <input class="form-control" type="number" id="inputNbVisuel">
                                    </div>
                                </div>
                                <!-- Cible -->
                                <div class="col">
                                    <div class="md-form input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" style="font-size: 0.8rem;">Cible</span>
                                        </div>
                                        <input class="form-control" type="number" id="inputNbCible">
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-1"></div>
                                <!-- Tir compté -->
                                <div class="col">
                                    <div class="md-form input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" style="font-size: 0.8rem;">Tir Compté</span>
                                        </div>
                                        <input class="form-control" type="number" id="inputNbTirCompte">
                                    </div>
                                </div>
                                <!-- <div class="col-2"></div> -->
                                <!-- Compétition -->
                                <div class="col">
                                    <div class="md-form input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" style="font-size: 0.8rem;">Compétition</span>
                                        </div>
                                        <input class="form-control" type="number" id="inputNbCompet">
                                    </div>
                                </div>
                                <div class="col-1"></div>
                            </div>
                        <!-- Retour -->
                        <legend class="col-form-label text-center blue lighten-3"><strong>Retour de la scénance</strong></legend>
                            <div class="form-row">
                                <!-- Retour -->
                                <div class="col">
                                    <div class="form-group shadow-textarea">
                                        <i class="fas fa-pencil-alt prefix"></i>
                                        <label for="inputObservations">Observations</label>
                                        <textarea class="form-control z-depth-1" id="inputObservations" rows="3" placeholder="Qu'avez-vous retenu de l'entrainement..."></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="form-row justify-content-md-center">
                                <!-- Niveau de forme -->
                                <div class="col-8">
                                    <div class="md-form input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" style="font-size: 0.8rem;">Niveau de forme (0-10)</span>
                                        </div>
                                        <input class="form-control" type="number" min="0" max="9" id="idNvForme">
                                    </div>
                                </div>
                            </div>
                        <div Boutons -->
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" id="btSupprimerEntrainement">Supprimer</button>
                            <button type="button" class="btn btn-success" id="btValiderEntrainement">Valider</button>
                            <button type="button" class="btn btn-primary" data-dismiss="modal">Quitter</button>
                        </div>

                        <div class="modal-footer" style="display: none;">
                            <label for="idEntrainement">ID</label>
                            <input type="text" class="form-control" name="idEntrainement" id="idEntrainement">
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>

    <script>
        $('.datepicker').datepicker({
            Dateformat: "dd/MM/yy",
            language: 'fr-FR',
            todayHighlight: true,
            autoclose: true
        })
    </script>

    <script>
        $(document).ready(function () {
            var strUserID = '<?php echo $_SESSION['licencie']; ?>'; 
            afficherConsigne(strUserID);
            afficherListeEntrainements(strUserID, 'now');

            $("#inputSearchEntrainement").on("keyup", function () {
                var value = $(this).val().toLowerCase();
                $("#Entrainement tr").filter(function () {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });
            $("form").bind("keypress", function(e) {
                if (e.keyCode == 13) {
                    return false;
                }
            });
            $("#btAjoutEntrainenement").click(function() {
                // alert($("#divConsigne").text());
                var d = new Date();
                var twoDigitMonth = (d.getMonth()+1)+""; if(twoDigitMonth.length==1)	twoDigitMonth ="0" + twoDigitMonth;
                var twoDigitDate = d.getDate()+"";       if(twoDigitDate.length==1)     twoDigitDate ="0"  + twoDigitDate;

                var strDate = twoDigitDate + "/" + twoDigitMonth + "/" + d.getFullYear();

                $('#idEntrainement').val(0);
                $('#modalDateEntrainement').val(strDate);
                // Récupérer la consigne
                $("#modalConsigneID").val($("#divID").text());
                $("#modalConsigne").val($("#divConsigne").text());
                $("#inputNbPaille").val(0);
                $("#inputNbVisuel").val(0);
                $("#inputNbCible").val(0);
                $("#inputNbTirCompte").val(0);
                $("#inputNbCompet").val(0);
                $("#inputObservations").val("");
                $("#idNvForme").val(5);
                $("#btSupprimerEntrainement").hide();
                $("#btValiderEntrainement").text("Sauver");

                // Affiche le modal avec les données de l'entrainement choisi
                $("#modalEntrainement").modal('show');
            });
            $("#btAllEntrainement").click(function() {
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

        $(document).on('click', '#divEntrainement td', function () {
            // alert($("#divConsigne").text());

            // Récupérer la ligne
            var EntrainementTR = $(this).parent();
            // Récupérer l'ID de l'entrainement'
            var idEntrainement = $(EntrainementTR).find('td').eq(0).html();
            // Récupérer la date de la ligne cliquée
            var dateEntrainement = $(EntrainementTR).find('td').eq(1).html();
            // Récupérer la consigne de la ligne cliquée
            var consigneEntrainement = $(EntrainementTR).find('td').eq(2).html();
            // Récupérer le volume sur Paille
            var volPailleEntrainement = $(EntrainementTR).find('td').eq(3).html();
            // Récupérer le volume sur Visuel
            var volVisuelEntrainement = $(EntrainementTR).find('td').eq(4).html();
            // Récupérer le volume sur Cible
            var volCibleEntrainement = $(EntrainementTR).find('td').eq(5).html();
            // Récupérer le volume en Tir compté
            var volTirCompteEntrainement = $(EntrainementTR).find('td').eq(6).html();
            // Récupérer le volume en Compétition
            var volCompetEntrainement = $(EntrainementTR).find('td').eq(7).html();
            // Récupérer les observations
            var ObservationsEntrainement = $(EntrainementTR).find('td').eq(9).html();
            // Récupérer le niveau de forme
            var NvFormeEntrainement = $(EntrainementTR).find('td').eq(10).html();

            // alert("divID : " + $("#divID").text());

            $('#idEntrainement').val(idEntrainement);
            $('#modalDateEntrainement').val(dateEntrainement);
            $("#modalConsigneID").text($("#divID").text());
            $("#modalConsigne").val(consigneEntrainement);
            $("#inputNbPaille").val(volPailleEntrainement);
            $("#inputNbVisuel").val(volVisuelEntrainement);
            $("#inputNbCible").val(volCibleEntrainement);
            $("#inputNbTirCompte").val(volTirCompteEntrainement);
            $("#inputNbCompet").val(volCompetEntrainement);
            $("#inputObservations").val(ObservationsEntrainement);
            $("#idNvForme").val(NvFormeEntrainement);
            $("#btSupprimerEntrainement").show();
            $("#btValiderEntrainement").text("Sauver");

            // Affiche le modal avec les données de l'entrainement choisi
            $("#modalEntrainement").modal('show');
        });

        $("#btValiderEntrainement").click(function(event){
            if ($('#idEntrainement').val() == 0) {
                insertEntrainement();
            } else {
                updateEntrainement($('#idEntrainement').val());
            }
        });

        $("#btSupprimerEntrainement").click(function(event){
            removeEntrainement($('#idEntrainement').val());
            // window.location.href='index.php?controller=entrainement&action=Afficher';
        });

        function insertEntrainement(){
            // alert("insertEntrainement | Ajout de l'entrainement !");
            var strDate = $('#modalDateEntrainement').val();
            var strUserID = '<?php echo $_SESSION['licencie']; ?>';
            var intNbFlechePaille = $('#inputNbPaille').val();
            var intNbFlecheVisuel = $('#inputNbVisuel').val();
            var intNbFlecheCible = $('#inputNbCible').val();
            var intNbFlecheTirCompte = $('#inputNbTirCompte').val();
            var intNbFlecheCompet = $('#inputNbCompet').val();
            var intNvForme = $('#idNvForme').val();
            var intConsigneID = null;
            var strConsigneNouvelle = $('#modalConsigne').val();
            var strObs = $('#inputObservations').val();
            
            // Si l'ID de la consigne n'existe pas, c'est qu'aucune consigne n'a été définie.
            // Il faut donc ajouter la consigne "Pas de consigne..." à cet archer
            if ($('#modalConsigneID').text() == '') {
                // alert("insertEntrainement | intConsigneID is undefined");
            
                // Ajout de la consigne "Pas de consigne..." à cet archer
                url_ajax_consigne = '../views/ajax_consigne.php';

                $.ajax({
                    url         : url_ajax_consigne,
                    type        : 'GET',
                    data        : 'userID=' + strUserID + '&consigneNew=' + strConsigneNouvelle + '&method=SET',
                    dataType    : 'json',
                    success     : function(response, statut) {
                        if (response[0].retourInsertConsigne) {
                            // alert("insertConsigne | retourInsertConsigne " + response[0].retourInsertConsigne);
                            // alert("insertConsigne | retourConsigneID " + response[0].retourConsigneID.con_id);
                            intConsigneID = response[0].retourConsigneID.con_id;
                            
                            // Ajout du nouvel entrainement
                            url_ajax_entrainement = '../views/ajax_insert_entrainement.php';
                            $.ajax({
                                url         : url_ajax_entrainement,
                                type        : 'POST',
                                data        : 'dateEnt=' + strDate + 
                                              '&userID=' + strUserID + 
                                              '&NbFlechePaille=' + intNbFlechePaille + 
                                              '&NbFlecheVisuel=' + intNbFlecheVisuel + 
                                              '&NbFlecheCible=' + intNbFlecheCible + 
                                              '&NbFlecheTirCompte=' + intNbFlecheTirCompte + 
                                              '&NbFlecheCompet=' + intNbFlecheCompet + 
                                              '&NvForme=' + intNvForme + 
                                              '&IDConsigne=' + intConsigneID + 
                                              '&Observation=' + strObs,
                                dataType    : 'json',
                                success     : function(code_html, statut){
                                    // Rechargement de la page entrainement
                                    window.location.href='index.php?controller=entrainement&action=Afficher';
                                },
                                error       : function(code_html, statut, erreur){
                                    alert("erreur Ajax lors de la création de l'entrainement/consigne : " + erreur);
                                },
                                complete    : function(code_html, statut) {

                                }
                            });
                        }
                    },
                    error       : function(response, statut, erreur) {
                        alert("erreur Ajax lors de la création de la consigne : " + erreur);
                    },
                    complete    : function(response, statut) {
                        
                    }
                });
            } else {
                intConsigneID = $('#modalConsigneID').text();
                url_ajax_entrainement = '../views/ajax_insert_entrainement.php';
                $.ajax({
                    url         : url_ajax_entrainement,
                    type        : 'POST',
                    data        : 'dateEnt=' + strDate + 
                                  '&userID=' + strUserID + 
                                  '&NbFlechePaille=' + intNbFlechePaille + 
                                  '&NbFlecheVisuel=' + intNbFlecheVisuel + 
                                  '&NbFlecheCible=' + intNbFlecheCible + 
                                  '&NbFlecheTirCompte=' + intNbFlecheTirCompte + 
                                  '&NbFlecheCompet=' + intNbFlecheCompet + 
                                  '&NvForme=' + intNvForme + 
                                  '&IDConsigne=' + intConsigneID + 
                                  '&Observation=' + strObs,
                    dataType    : 'json',
                    success     : function(code_html, statut){
                        // Rechargement de la page entrainement
                        window.location.href='index.php?controller=entrainement&action=Afficher';
                    },
                    error       : function(code_html, statut, erreur){
                        alert("erreur Ajax lors de la création de l'entrainement : " + erreur);
                    },
                    complete    : function(code_html, statut) {

                    }
                });
            }            
        }

        function updateEntrainement(_IDEntrainement){
            // alert("Mise à jour de l'entrainement " + _IDEntrainement + " !");

            var strDate = $('#modalDateEntrainement').val();
            var strUserID = '<?php echo $_SESSION['licencie']; ?>';
            var intNbFlechePaille = $('#inputNbPaille').val();
            var intNbFlecheVisuel = $('#inputNbVisuel').val();
            var intNbFlecheCible = $('#inputNbCible').val();
            var intNbFlecheTirCompte = $('#inputNbTirCompte').val();
            var intNbFlecheCompet = $('#inputNbCompet').val();
            var intNvForme = $('#idNvForme').val();
            var intConsigneID = $('#modalConsigneID').text();
            // var strConsigneNouvelle = $('#modalConsigne').val();
            var strObs = $('#inputObservations').val();
            
            
            url_ajax_entrainement = '../views/ajax_update_entrainement.php';
            $.ajax({
                url         : url_ajax_entrainement,
                type        : 'POST',
                data        : 'IDEntrainement=' + _IDEntrainement +
                                '&dateEnt=' + strDate + 
                                '&userID=' + strUserID + 
                                '&NbFlechePaille=' + intNbFlechePaille + 
                                '&NbFlecheVisuel=' + intNbFlecheVisuel + 
                                '&NbFlecheCible=' + intNbFlecheCible + 
                                '&NbFlecheTirCompte=' + intNbFlecheTirCompte + 
                                '&NbFlecheCompet=' + intNbFlecheCompet + 
                                '&NvForme=' + intNvForme + 
                                '&IDConsigne=' + intConsigneID + 
                                '&Observation=' + strObs,
                dataType    : 'json',
                success     : function(code_html, statut){
                    // Rechargement de la page entrainement
                    window.location.href='index.php?controller=entrainement&action=Afficher';
                },
                error       : function(code_html, statut, erreur){
                    alert("erreur Ajax lors de la mise à jour de l'entrainement : " + erreur);
                },
                complete    : function(code_html, statut) {

                }
            });
        }

        function removeEntrainement(_IDEntrainement){
            // alert("Suppression de l'entrainement " + _IDEntrainement + " !");
            
            // Ajout du nouvel entrainement
            url_ajax_entrainement = '../views/ajax_delete_entrainement.php';
            $.ajax({
                url         : url_ajax_entrainement,
                type        : 'POST',
                data        : 'IDEntrainement=' + _IDEntrainement,
                dataType    : 'json',
                success     : function(code_html, statut){
                    // Rechargement de la page entrainement
                    window.location.href='index.php?controller=entrainement&action=Afficher';
                },
                error       : function(code_html, statut, erreur){
                    alert("erreur Ajax lors de la création de l'entrainement/consigne : " + erreur);
                },
                complete    : function(code_html, statut) {

                }
            });
        }

        function afficherConsigne(_strUserID) {
            url_ajax_consigne = '../views/ajax_entrainement_consigne.php';
            $.ajax({
                url         : url_ajax_consigne,
                type        : 'POST',
                data        : 'userID=' + _strUserID,
                dataType : 'html',
                success : function(code_html, statut){
                    $('#divConsigneRow').html(code_html); // On passe code_html à jQuery() qui va nous créer l'arbre DOM !
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
    </script>
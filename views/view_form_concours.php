<!--
    Sous le nav-bar créer une nouvelle entrée inscription accessible uniquement au profil inscripteur
    La page de base sera sur la base du listing des concours
    Après sélection via un clic sur la ligne, un modal formulaire s'affichera contenant :
        - les dates du concours (fixes)
        - le nom du concours (fixe)
        - l'état du concours (fixe ?) (possibilité de réouvrir le concours sous certaines conditions)
        - le mail du club organisateur (à renseigner)
        - les prix des départs (à renseigner) les 4
        - l'adresse postale du club organisateur (à renseigner)
        - un autre formulaire :
            - la liste des paricipants
            - une case à cocher pour valider le retour d'inscription par l'inscripteur

            Une fois ce formulaire validé, un mail sera envoyé aux participants 
            dont le retour d'inscription sera à OUI et les données seront enregistrées 
            dans la table csg_rel_epreuve_user de la base de données.

    Une fois ce formulaire validé, les données seront enregistrées dans la table csg_epreuves
    de la base de données
-->

<?php
    require_once PATH_MODELS . 'model_class_concours.php';
    $objConcours  = new ClassConcours();
?>

<!-- Formulaire de renseignement d'un concours -->
<!-- <form id="inscript" method='POST'> -->
    <!-- https://openclassrooms.com/forum/sujet/lancer-une-fonction-php-a-partir-d-un-bouton-quot-buttonq-21030 -->
    <!-- <fieldset class="form-group m-3"> -->
        <!-- <legend>Archer</legend> -->
        <!-- <div class="form-row"> -->
            <!-- <div class="form-group col-md-2"> -->
                <!-- <label for="inputCat">Catégorie</label> -->
                <!-- <input type="text" class="form-control" id="inputCat" readonly value="<?php echo $_SESSION['categorie']; ?>"> -->
            <!-- </div> -->
            <!-- <div class="form-group col-md-4"> -->
                <!-- <label for="inputCat">Nom</label> -->
                <!-- <input type="text" class="form-control" id="inputName" readonly value="<?php echo $_SESSION['firstname'] . ' ' .$_SESSION['lastname']; ?>"> -->
            <!-- </div> -->
        <!-- </div> -->
    <!-- </fieldset> -->
<!-- </form> -->
<!-- </br> -->

<div class="m-2">
    <h1>Gestion des inscriptions aux concours</h1>
    <h2>Liste des concours en Normandie</h2>
    <p>Sélectionner votre concours en cliquant sur la ligne correspondant au concours choisi dans le tableau ci-dessous.</p>
    <p>Taper quelque chose dans la zone de texte ci-dessous pour filtrer le tableau</p>
    <p>Exemple : "<b>Fita</b>" pour n'afficher que les concours Fita ou "<b>23/11/2018</b>" pour n'afficher que les concours à la date du 23 Novembre 2018</p>
    <div class="row align-items-center">
        <div class="col-8">
            <input class="form-control" id="inputSearchConcours" type="text" placeholder="Filtrer le tableau.." autofocus>
        </div>
        <div class="col">
            <button type="button" class="btn btn-info" id="btAllConcours">Tous les concours</button>
        </div>
    </div>
    <br>
    <table class="table table-sm table-bordered table-striped">
        <thead>
            <tr>
                <th class="text-center" style="display: none;">ID</th>
                <th class="text-center">Date début</th>
                <th class="text-center">Date fin</th>
                <th class="text-center">Nb d'inscrits</th>
                <th class="text-center">Mandat</th>
                <th class="text-center">Discipline</th>
                <th class="text-center">Organisateur</th>
                <th class="text-center">Lieu</th>
                <th class="text-center">Caractéristiques</th>
            </tr>
        </thead>
        <tbody id="Concours">
            <?php
                require_once PATH_VIEWS . "view_list_concours.php";
            ?>
        </tbody>
    </table>
    <div>
        Légende :<br>
        <div>
            Colonnes Date :
            <div class="row">
                <div class="col-sm alert alert-success text-center">Concours Ouvert</div>
                <div class="col-sm alert alert-info text-center">Concours clos par la préinscription</div>
                <div class="col-sm alert alert-warning text-center">Concours validé par le club</div>
                <div class="col-sm alert alert-danger text-center">Concours passé</div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div id="modalConcours" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <!-- Modal content-->
        <div class="modal-content" id="modalData">
            <!-- <div class="modal-header"> -->
                <!-- <h4 class="title" id="modalHeaderDate"></h4> -->
            <!-- </div> -->
            <!-- <div class="modal-header"> -->
                <!-- <h5 class="modal-title" id="modalHeaderLieu"></h5> -->
            <!-- </div> -->
            <!-- <form id="concoursForm" name="concours" role="form"> -->
                <!-- <div class="modal-body" id="modalData"> -->
                <!-- </div> -->
                <!-- <div class="modal-footer"> -->
                    <!-- <button type="submit" class="btn btn-primary" id="modalSubmit">Valider</button> -->
                    <!-- <button type="button" class="btn btn-danger" data-dismiss="modal">Annuler</button> -->
                <!-- </div> -->
                <!-- <div class="modal-footer" style="display: none;"> -->
                    <!-- <label for="modalID">ID</label> -->
                    <!-- <input type="text" class="form-control" name="modalID" id="modalID"> -->
                <!-- </div> -->
            <!-- </form> -->
        </div>

    </div>
</div>


<script>
    $(document).ready(function () {
        $("#inputSearchConcours").on("keyup", function () {
            var value = $(this).val().toLowerCase();
            $("#Concours tr").filter(function () {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
        $("form").bind("keypress", function(e) {
            if (e.keyCode == 13) {
                return false;
            }
        });
        $("#btAllConcours").click(function() {
            if ($(this).text() == 'Tous les concours') {
                $(this).text('Que les prochains concours');
                $(this).toggleClass("btn-info");
                $(this).toggleClass("btn-primary");
                $("#Concours").empty();
                // $("#Concours").load("views/view_concours.php?concours=all");
                $.ajax({
                    url : 'views/view_list_concours.php', // Page à charger
                    data : 'concours=all',
                    type : 'POST', // Le type de la requête HTTP, ici devenu POST
                    dataType : 'html',
                    success : function(code_html, statut){
                        $(code_html).appendTo("#Concours"); // On passe code_html à jQuery() qui va nous créer l'arbre DOM !
                    },
                    error : function(resultat, statut, erreur){
                        alert("erreur Ajax");
                    },
            
                    complete : function(resultat, statut){
                        $('[data-toggle="tooltip"]').tooltip({
                            // delay: 50,
                            placement: "top",
                            title: userDetails,
                            html: true
                        });
                    } 
                });
            } else {
                $(this).text('Tous les concours');
                $(this).toggleClass("btn-info");
                $(this).toggleClass("btn-primary");
                $("#Concours").empty();
                // $("#Concours").load("views/view_concours.php?concours=now");
                $.ajax({
                    url : 'views/view_list_concours.php', // Page à charger
                    data : 'concours=now',
                    type : 'POST', // Le type de la requête HTTP, ici devenu POST
                    dataType : 'html',
                    success : function(code_html, statut){
                        $(code_html).appendTo("#Concours"); // On passe code_html à jQuery() qui va nous créer l'arbre DOM !
                    },
                    error : function(resultat, statut, erreur){
                        alert("erreur Ajax");
                    },
            
                    complete : function(resultat, statut){
                        $('[data-toggle="tooltip"]').tooltip({
                            // delay: 50,
                            placement: "top",
                            title: userDetails,
                            html: true
                        });
                    } 
                });
            }
        });


        $(document).on('click', '#Concours td', function () {
            var column_num = parseInt( $(this).index() ) + 1;

            if ( column_num < 4 ) {
                var concoursTR = $(this).parent();
                if ($(concoursTR).find('td').eq(1).attr("class").indexOf("table-success") != -1) {
                    $("#modalSubmit").prop('disabled',false);
                    $("#modalHeaderDate").removeClass('modal-header-danger');
                    $("#modalHeaderDate").addClass('modal-header-success');
                } else {
                    $("#modalSubmit").prop("disabled", true);
                    $("#modalHeaderDate").removeClass('modal-header-success');
                    $("#modalHeaderDate").addClass('modal-header-danger');
                }
                var concoursID = $(concoursTR).find('td').eq(0).html();
                
                // Récupération des variables
                var strUserID = '<?php echo $_SESSION['licencie']; ?>'; 
                var dateJourDeb = $(concoursTR).find('td').eq(1).html();
                var dateJourFin = $(concoursTR).find('td').eq(2).html();
                var strDiscipline = $(concoursTR).find('td').eq(4).html();
                var strLieu = $(concoursTR).find('td').eq(6).html();
                // Récupération des autres données via Ajax :
                //  - email, adresse postale, tarifs, ...
                //  - départ(s) à valider

                //Mise à jour des départs du concours sélectionné
                $.ajax({
                    url         : '../views/ajax_form_concours.php',
                    type        : 'POST',
                    data        : 'ConcoursID=' + concoursID,
                    dataType    : 'html',
                    success     : function(code_html, statut) {
                        $("#modalData").html(code_html);
                        $("#modalConcours").modal('show');
                        $("#modalID").val(concoursID);
                        $('#inputFileMandat').on('change', function(event) {
                            var filename = $('#inputFileMandat').val().split('\\').pop();

                            $('#labelFileMandat').html(filename);
                            $('#inputNameFileMandat').val(filename);
                            $('#inputPathFileMandat').val('C:\\fakepath\\');
                        });
                        $('#tarif').on('change', function(event) {
                            var valThis = parseFloat($(this).val());
                            $(this).val(valThis.toFixed(2));
                        });
                        $("#concoursForm").submit(function(event){
                            submitForm($('#inputFileMandat').val());
                            return false;
                        });
                    },
                    error       : function(resultat, statut, erreur) {

                    },
                    complete    : function(resultat, statut) {

                    }
                });
                
                $("#modalHeaderDate").html("Concours du " + dateJourDeb + " au " + dateJourFin);
                $("#modalHeaderLieu").html(strDiscipline + " à " + strLieu);
            }
        });

        function submitForm(arrFile){
            var strUserID = '<?php echo $_SESSION['licencie']; ?>';
            $.ajax({
                type:   "POST",
                url:    "../views/ajax_saveConcours.php",
                cache:  false,
                data:   $('form#concoursForm').serialize() + '&userID=' + strUserID,
                dataType: 'json',
                success: function(response){
                    var arrQueue = [];
                    for (i = 0; i < Object.keys(response).length; i++) {
                        arrQueue.push(response[i]);
                    }
                    swal.mixin({
                    }).queue(arrQueue).then(
                    function(isConfirm){
                        window.location.href='index.php?controller=inscription&action=Modifier';
                    });
                },
                error: function(){
                    swal.mixin({
                    }).queue([{
                        title:              "Sauvegarde du départ Annulé !",
                        html:               "Un problème lors de la sauvegarde est survenu, contactez votre administrateur WEB par la petite enveloppe en bas du site.",
                        type:               "error",
                        confirmButtonText:  "Désolé...",
                        confirmButtonColor: '#dc3545'
                    }]).then(
                    function(isConfirm){
                        window.location.href='index.php?controller=inscription&action=Modifier';
                    });
                }
            });
        }
    });
</script>
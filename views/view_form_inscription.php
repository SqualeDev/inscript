<?php
    $intJrsPre_Inscript = 30;
    $objConcours  = new ClassConcours();
?>

<!-- Formulaire d'inscription à un concours -->
<form id="inscript" method='POST'>

        <!-- https://openclassrooms.com/forum/sujet/lancer-une-fonction-php-a-partir-d-un-bouton-quot-buttonq-21030 -->
    <fieldset class="form-group m-3">
        <legend>Archer</legend>
        <div class="form-row">
            <div class="form-group col-md-2">
                <label for="inputCat">Catégorie</label>
                <input type="text" class="form-control" id="inputCat" readonly value="<?php echo $_SESSION['categorie']; ?>">
            </div>
            <div class="form-group col-md-4">
                <label for="inputCat">Nom</label>
                <input type="text" class="form-control" id="inputName" readonly value="<?php echo $_SESSION['firstname'] . ' ' .$_SESSION['lastname']; ?>">
            </div>
        </div>
    </fieldset>
</form>
<!-- </br> -->

<div class="m-2">
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
                // $_GET['concours'] = 'auj';
                require_once PATH_VIEWS . "view_list_concours.php";
                // echo $objConcours->afficherListeInscriptions($_SESSION['licencie']);
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
        <div>
            Colonne Nb d'inscrits :
            <div class="row">
                <div class="col-sm alert alert-warning text-center">Concours où vous êtes inscrit</div>
            </div>
        </div>
    </div>
</div>

<!-- Modals -->
<!-- Modals Concours Salle -->
<div id="modalConcoursDeparts" class="modal fade" role="dialog">
    <div class="modal-dialog">
        
        <div class="modal-content" >
            <div class="modal-header">
                <h4 class="title" id="modalHeaderDate"></h4>
            </div>
            <div class="modal-header">
                <h5 class="modal-title" id="modalHeaderLieu"></h5>
            </div>
            <form id="departForm" name="depart" role="form">
                <div class="modal-body" id="modalDeparts">
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="modalSubmit">Valider</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Annuler</button>
                </div>
                <div class="modal-footer" style="display: none;">
                    <label for="modalID">ID</label>
                    <input type="text" class="form-control" name="modalID" id="modalID">
                </div>
                <div class="modal-footer" style="display: none;">
                    <label for="modalType">Type</label>
                    <input type="text" class="form-control" name="modalType" id="modalType">
                </div>
            </form>
            <div class="modal-header" id="modalHeaderLegend">
                <details>
                    <summary></summary>
                    <p></p>
                </details>
            </div>
            <div class="modal-footer">
                <h5 class="modal-title" id="modalFooterDatePreinscription"></h5>
            </div>
        </div>

    </div>
</div>
<!-- Fin Modal Concours Salle -->
<!-- Modal Concours Extérieur -->
<!-- <div id="modalConcoursDeparts_Ext" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content" >
            <div class="modal-header">
                <h4 class="title" id="modalHeaderDate"></h4>
            </div>
            <div class="modal-header">
                <h5 class="modal-title" id="modalHeaderLieu"></h5>
            </div>
            <form id="departForm" name="depart" role="form">
                <div class="modal-body" id="modalDeparts">
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="modalSubmit">Valider</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Annuler</button>
                </div>
                <div class="modal-footer" style="display: none;">
                    <label for="modalID">ID</label>
                    <input type="text" class="form-control" name="modalID" id="modalID">
                </div>
            </form>
            <div class="modal-header" id="modalHeaderLegend">
                <details>
                    <summary>Légende du bouton Championnat/Coupe</summary>
                    <p>
                        Lorsque le bouton est <mark class="mark-red"><strong>rouge</strong></mark>, le choix sera de tirer en Coupe.<br>
                        Lorsqu'il est <mark class="mark-green"><strong>vert</strong></mark>, le choix de tirer sera en Championnat.<br>
                        Le choix de tirer en Coupe n'est autorisé que pour les catégories supérieures à Junior inclus.
                    </p>
                </details>
            </div>
            <div class="modal-footer">
                <h5 class="modal-title" id="modalFooterDatePreinscription"></h5>
            </div>
        </div>

    </div>
</div> -->
<!-- Fin du Modal Concours Extérieur -->
<!-- Fin des Modals -->

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
        $("#departForm").submit(function(event){
            // alert($('#modalType').val());
            submitForm($('#modalType').val());
            return false;
        });
    });
    $(document).on('click', '#Concours td', function () {
        var column_num = parseInt( $(this).index() ) + 1;
        var typeConcours = "Ext";

        var legendSalle = "Lorsque le bouton est <mark class='mark-red'><strong>rouge</strong></mark>, le choix sera de tirer sur Blason.<br>" +
                          "Lorsqu'il est <mark class='mark-green'><strong>vert</strong></mark>, le choix de tirer sera sur Trispot.<br>" +
                          "Le choix de tirer sur Trispot n'est autorisé que pour les catégories supérieures à Cadet(te) inclus.";

        var legendExt =   "Lorsque l'interrupteur est à <b>gauche</b>, le choix sera de tirer en <b>Coupe de France</b> en distance \"réduite\".<br>" +
                          "Lorsqu'il est <mark class='mark-green'><strong>vert</strong></mark> et à <b>droite</b>, le choix de tirer sera en <b>Championnat de France</b> sur la distance \"longue\".<br>" +
                          "Le choix de tirer <b>en Coupe de France n'est autorisé que</b> pour les catégories supérieures à Juniors inclus."

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
            $("#modalID").val(concoursID);
            // Récupération des variables
            var strUserID = '<?php echo $_SESSION['licencie']; ?>'; 
            var dateJourDeb = $(concoursTR).find('td').eq(1).html();
            var dateJourFin = $(concoursTR).find('td').eq(2).html();
            var strDisciplineNorme = $(concoursTR).find('td').eq(5).html();
            $("#modalType").val(strDisciplineNorme);
            var strDiscipline = $(concoursTR).find('td').eq(6).html();
            var strLieu = $(concoursTR).find('td').eq(8).html();
            var url_ajax_depart = '';
            switch (strDisciplineNorme) {
                case 'SALLE':
                    $('#modalHeaderLegend > details > summary').html("Légende du bouton TRISPOT");
                    $('#modalHeaderLegend > details > p').html(legendSalle);
                    url_ajax_depart = '../views/ajax_departs_salle.php';
                    break;
                case 'TAE':
                    $('#modalHeaderLegend > details > summary').html("Légende de l'interrupteur Coupe / Championnat");
                    $('#modalHeaderLegend > details > p').html(legendExt);
                    url_ajax_depart = '../views/ajax_departs_tae.php';
                    break;
                default:
                    break;
            }

            //Mise à jour des départs du concours sélectionné
            $.ajax({
                url         : url_ajax_depart,
                type        : 'GET',
                data        : 'ConcoursID=' + concoursID + '&userID=' + strUserID,
                dataType    : 'html',
                success     : function(code_html, statut) {
                    $("#modalDeparts").html(code_html);
                    $("#modalConcoursDeparts").modal('show'); 
                },
                error       : function(resultat, statut, erreur) {
                    afficheModalErreur();
                },
                complete    : function(resultat, statut) {

                }
            });
            var nbJrsPreinscript = parseInt(<?php echo $intJrsPre_Inscript; ?>);
            var dateDebConcours = new Date();
            var TabDate=dateJourDeb.split('/');
            dateDebConcours.setFullYear(TabDate[2]);
            dateDebConcours.setMonth(TabDate[1]);
            dateDebConcours.setDate(TabDate[0]-nbJrsPreinscript);
            var FinalDate=((dateDebConcours.getDate().toString().length==1)?'0'+dateDebConcours.getDate():dateDebConcours.getDate())+'/'+((dateDebConcours.getMonth().toString().length==1)?'0'+dateDebConcours.getMonth():dateDebConcours.getMonth())+'/'+dateDebConcours.getFullYear()

            $("#modalHeaderDate").html("Concours du " + dateJourDeb + " au " + dateJourFin);
            $("#modalHeaderLieu").html(strDiscipline + " à " + strLieu);
            console.log(typeConcours);
            $("#modalFooterDatePreinscription").html("Date limite de préinscription : " + FinalDate );
        }
    });

    function submitForm(strDisciplineNorme){
        var strUserID = '<?php echo $_SESSION['licencie']; ?>';
        // alert(document.querySelector('#modalType').html());
        // var strDisciplineNorme = 'SALLE';

        switch (strDisciplineNorme) {
            case 'SALLE':
                url_ajax_saveDepart = '../views/ajax_saveDeparts_salle.php';
                break;
            case 'TAE':
                url_ajax_saveDepart = '../views/ajax_saveDeparts_tae.php';
                break;
            default:
                break;
        }

        $.ajax({
            type:   'POST',
            url:    url_ajax_saveDepart,
            cache:  false,
            data:   $('form#departForm').serialize() + '&userID=' + strUserID,
            dataType: 'json',
            success: function(response){
                var arrQueue = [];
                for (i = 0; i < Object.keys(response).length; i++) {
                    arrQueue.push(response[i]);
                }
                swal.mixin({
                }).queue(arrQueue).then(
                function(isConfirm){
                    window.location.href='index.php?controller=concours&action=Inscrire';
                });
            },
            error: function(){
                afficheModalErreur();
            }
        });
    }

    function afficheModalErreur() {
        // FIXME Correction du message affiché si une inscription n'a pas aboutie à cause d'un problème de base de données
        swal.mixin({
        }).queue([{
            title:              "Préinscription Annulée !",
            html:               "Un problème lors de l'inscription est survenu, contactez votre administrateur WEB par la petite enveloppe en bas du site.",
            type:               "error",
            confirmButtonText:  "Désolé...",
            confirmButtonColor: '#dc3545'
        }]).then(
        function(isConfirm){
            window.location.href='index.php?controller=concours&action=Inscrire';
        });
    }

</script>

<script>
    // $('.datepicker').datepicker({
    //     Dateformat: "dd/MM/yy",
    //     language: 'fr-FR'
    // })
</script>

<script type="text/javascript">
    // Afficher le tooltip au clic sur la case
    $("#Concours").ready(function(){
        $('[data-toggle="tooltip"]').tooltip({
            // delay: 50,
            placement: "top",
            title: userDetails,
            html: true
        });
    });

    function userDetails() {
        var id = this.id;
        var split_id = id.split('_');
        var Concoursid = split_id[1];

        var tooltipText = "";
        $.ajax({
            url: '/views/ajax_Inscrits.php',
            type: 'POST',
            async: false,
            // data: {
            //     fonction: 'listeInscrits',
            //     concoursid: Concoursid
            // },
            data: 'concoursid=' + Concoursid,
            success: function(response){
                tooltipText = response;
            }
        });
        return tooltipText;
    }
</script>

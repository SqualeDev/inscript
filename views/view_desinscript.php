<?php
    $objConcours  = new ClassConcours();
?>
<div class="m-2">
    <h2>Liste de vos inscriptions aux concours</h2>
    <p>Taper quelque chose dans la zone de texte ci-dessous pour filtrer le tableau.</br>Exemple : "<b>Fita</b>" pour n'afficher que les concours Fita ou "<b>23/11/2018</b>" pour n'afficher que les concours à la date du 23 Novembre 2018</p>
    <input class="form-control" id="inputSearchConc" type="text" placeholder="Chercher un coucours..">
    <br>
</div>
<form id="desinscript" method="post">
    <div class="form-group m-2">
        <div>
        <p>Sélectionner les concours que vous souhaitez annulé et cliquer sur le bouton "Me désinscrire ..."
        <?php echo $objConcours->afficheBoutonDesinscrire(); ?>
        </div>
        <table class="table" data-toggle="table" data-id-field="id" data-select-item-name="tbl-desinscript[]">
            <thead>
                <tr>
                    <th data-field="id" data-visible="false" data-switchable="false" class="hidden">ID</th>
                    <th class="text-center" data-field="etat" data-checkbox="true"></th>
                    <th class="text-center" data-field="lieu">Lieu</th>
                    <th class="text-center" data-field="dateDeb">Date début</th>
                    <th class="text-center" data-field="numLicencie">n° Licencié</th>
                    <th class="text-center" data-field="prenom">Prénom</th>
                    <th class="text-center" data-field="nom">Nom</th>
                    <th class="text-center" data-field="discipline">Discipline</th>
                    <th class="text-center" data-field="depart">Départ</th>
                </tr>
            </thead>
            <tbody id="Inscrits">
                <?php
                    echo $objConcours->afficherlisteConcoursLicencie();
                ?>
            </tbody>
        </table>
    </div>
</form>

<script>
    $(document).ready(function () {
        $("#inputSearchConc").on("keyup", function () {
            var value = $(this).val().toLowerCase();
            $("#Inscrits tr").filter(function () {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
        $("form").bind("keypress", function(e) {
            if (e.keyCode == 13) {
                return false;
            }
        });
    });
</script>
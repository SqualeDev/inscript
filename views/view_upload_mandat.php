<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
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
     ?>
    <meta name="google-site-verification" content="zMZruuZIUtZtlc0uFxU65oov4SrLYDEhLzYBU9veO4o" />

    <link rel="stylesheet" type="text/css" href="<?php echo(URL_CSS . 'bootstrap-4.1.0.min.css');?>" />
    <link rel="stylesheet" type="text/css" href="<?php echo(URL_CSS . 'style.css');?>" />
    
    <script src="<?php echo(URL_JVS . 'jquery-3.3.1.min.js');?>"></script>
    <script src="<?php echo(URL_JVS . 'bootstrap-4.1.0.min.js');?>"></script>

    <script src="<?php echo(URL_JVS . 'bootstrap-fileselect.js');?>"></script>

    <!-- TITRE & ICONE -->
    <title>CSG Tir à l'arc</title>
    <link rel='icon' type='image/xicon' href='<?php echo URL_PCT ?>favicon.ico' />

</head>

<body>
    <div class="container-fluid">
        <form id="uploadFile">
            <h1>Upload Mandat</h1>
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <input id="inputFile" type="file" name="inputFile" />
                    </div>
                </div>
                <div class="col">
                    <button id="btValider" type="submit" class="btn btn-primary">Valider</button>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div id="output">Réponse</div>
                </div>
            </div>
        </form>
    </div>
    <script>
        $(document).ready(function () {
            $('#inputFile').fileselect({
                browseBtnClass: 'btn btn-primary',
                allowedFileExtensions: ['pdf', 'doc'],
                browserBtnPosition: 'right',
                language: 'fr'
            });
            $("#btValider").click(function () {
                console.log("submit event");
                var fd = new FormData(document.getElementById("uploadFile"));
                fd.append("label", "WEBUPLOAD");
                $.ajax({
                    url: "ajax_upload_mandat.php",
                    type: "POST",
                    data: fd,
                    processData: false, // tell jQuery not to process the data
                    contentType: false // tell jQuery not to set contentType
                }).done(function (data) {
                    console.log("PHP Output:");
                    console.log(data);
                    $("#output").html(data);
                });
                return false;
            });
        });
    </script>
</body>

</html>
        
        <?php require_once PATH_VIEWS . 'view_contact.php' ?>
        <footer >
            <div class="row justify-content-md-center">
                <div class="col-2 col-md-auto text-center">
                    <a href="http://www.notre-dame-de-gravenchon.fr/" target="_blank"><img src="<?php echo URL_PCT ?>logo_PJ2S.jpg" alt="PJ2S" height="60"></a>
                </div>
                <div class="col col-1"></div>
                <div class="col col-2 col-md-auto text-center">
                <a href="https://www.seinemaritime.fr/" target="_blank"><img src="<?php echo URL_PCT ?>logo_Seine-Maritime.png" alt="Seine Maritime" height="60"></a>
                </div>
                <div class="col col-1"></div>
                <div class="col-2 col-md-auto text-center">
                    <a href="https://www.tiralarc76.com/" target="_blank"><img src="<?php echo URL_PCT ?>logo_CD76.png" alt="CD76" height="60"></a>
                </div>
                <div class="col col-1"></div>
                <div class="col-2 col-md-auto text-center">
                    <a href="https://www.tiralarc-normandie.fr/" target="_blank"><img src="<?php echo URL_PCT ?>logo_Ligue-Normandie.jpg" alt="Ligue Normandie" height="60"></a>
                </div>
                <div class="col col-1"></div>
                <div class="col-2 col-md-auto text-center">
                    <a href="http://www.ffta.fr/" target="_blank"><img src="<?php echo URL_PCT ?>logo_FFTA.jpg" alt="FFTA" height="60"></a>
                </div>
            </div>

            <div class="row blue lighten-5 light-blue-text" style="font-size: 0.6rem;">
                <!-- Copyright -->
                <div class="col-md-3">
                    version : <?php require_once PATH_ROOT . 'version' ?> © 2018 Copyright SqualeDev
                </div>
                <!-- Copyright -->
                <!-- Contacts -->
                <div class="col-md-6 text-center h6" style="font-size: 0.8rem;">
                    contactez l'administrateur de ce site 
                    <a href="#contactModal" data-toggle="modal" ><i class="fas fa-envelope-square"></i></a>
                    <a href="https://fr-fr.facebook.com/arc.club.gravenchon.76/"><i class="fab fa-facebook-square"></i></a>
                </div>
                <!-- Contacts -->
                <!-- Balise Xiti -->
                <div class="col-md-3 text-right">
                    <?php
                        global $local;
                        // if (!$local) {require_once 'xiti.html';}
                    ?>
                </div>
                <!-- Balise Xiti -->
            </div>
        </footer>
        <script>
            $(document).ready(function () {
                function getParams() {
                    var url = window.location.href;
                    var splitted = url.split("?");
                    if(splitted.length === 1) {
                        return {};
                    }
                    var paramList = decodeURIComponent(splitted[1]).split("&");
                    var params = {};
                    for(var i = 0; i < paramList.length; i++) {
                        var paramTuple = paramList[i].split("=");
                        if (paramTuple[0] == 'article') {
                            params[paramTuple[0]] = paramTuple[1];
                        }
                    }
                    return params;
                }
                $('#bt_Article_' + getParams()['article']).click();
            });
        </script>
        <script src="<?php echo(URL_JVS . 'mdb-4.5.3.min.js');?>"></script>
        <script src="<?php echo(URL_JVS . 'fontawesome-all.js');?>"></script>
        <script src="<?php echo(URL_JVS . 'tether.min.js');?>"></script>
    </body>
</html>
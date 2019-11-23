<?php
    // echo "<pre>".var_dump($_SERVER)."</pre>";

    if (session_status() !== PHP_SESSION_ACTIVE) {session_start();}
    if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/licencies')) {
        // Config finale
        require_once $_SERVER['DOCUMENT_ROOT'] . '/licencies/config_local.php';
        require_once $_SERVER['DOCUMENT_ROOT'] . '/licencies/constantes.php';
    } else {
        // Config Debug
        require_once $_SERVER['DOCUMENT_ROOT'] . '/config_local.php';
        require_once $_SERVER['DOCUMENT_ROOT'] . '/constantes.php';
    }
    
	require_once PATH_MODELS . 'model_class_entrainement.php';
    $objEntrainement  = new ClassEntrainement();
    if (!isset($_POST['entrainement'])) { $_POST['entrainement'] = 'now';}
    if (false) {
        echo "<h3> PHP List All Session Variables</h3>";
        var_dump($_SESSION);
        echo "<h3> PHP List All Post Variables</h3>";
        var_dump($_POST);
        echo "<h3> PHP List All Server Variables</h3>";
        var_dump($_SERVER);
        echo "<h3> PHP List All Get Variables</h3>";
        var_dump($_GET);
    }
    
    echo $objEntrainement->afficherConsigneCourante($_POST['userID']);
?>
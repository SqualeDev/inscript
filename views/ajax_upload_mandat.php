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
    
    $pathMandats = PATH_DOCS . 'mandats/';

    $allowedExts = array("pdf", "doc");
    $temp = explode(".", $_FILES["inputFile"]["name"]);
    $extension = end($temp);
    
    if ((($_FILES["inputFile"]["type"] == "application/msword")
    || ($_FILES["inputFile"]["type"] == "application/pdf"))
    && ($_FILES["inputFile"]["size"] < 2000000)
    && in_array($extension, $allowedExts)) {
        if ($_FILES["inputFile"]["error"] > 0) {
            echo "Return Code: " . $_FILES["inputFile"]["error"] . "<br/>>";
        } else {
            $filename = $_FILES["inputFile"]["name"];
            echo "Upload: " . $_FILES["inputFile"]["name"] . "<br/>";
            echo "Type: " . $_FILES["inputFile"]["type"] . "<br/>";
            echo "Size: " . ($_FILES["inputFile"]["size"] / 1024) . " kB<br/>";
            // echo "Temp file: " . $_FILES["inputFile"]["tmp_name"] . "<br/>";

            if (file_exists($pathMandats . $filename)) {
                echo "<strong>". $filename . " existe déjà.</strong>";
            } else {
                move_uploaded_file($_FILES["inputFile"]["tmp_name"], $pathMandats . $filename);
                echo "Stored in: " . $pathMandats . $filename;
            }
        }
    } else {
        echo "Invalid file";
    }
?>
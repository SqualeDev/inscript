<?php
	// REQUIRE MODELS
	require_once 'constantes.php';
	require_once PATH_MODELS . 'model_class_db.php';
	require_once PATH_MODELS . 'model_class_log.php';
	// require_once PATH_MODELS . 'model_class_user.php';
	// require_once PATH_MODELS . 'model_class_roles.php';
	
	// DEMARRER SESSION
	session_start();

	// LAYOUT HTML
	require_once PATH_VIEWS . 'view_layout.php';
?>
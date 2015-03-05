<?php
	require_once dirname(__FILE__) . "/config.php";
	require_once dirname(__FILE__) . "/session.php";

	if($_SESSION['login'] == FALSE) {
		header('HTTP/1.0 401 Unauthorized');
		die();
	}

	session_unset();
	session_destroy();
	header("Location: " . $setting['root_domain']);
	die();
?>
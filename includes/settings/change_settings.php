<?php
	require_once dirname(dirname(__FILE__)) . "/config.php";
	require_once dirname(dirname(__FILE__)) . "/session.php";
	require_once dirname(dirname(__FILE__)) . "/functions.php";

	if(!isset($_SESSION['username'])) {
		header("Location: " . $setting['root_domain']);
		die();
	}

	$query = 'UPDATE users SET TIMEZONE="' . htmlspecialchars($_POST['timezone']) . '" WHERE MD5="' . $_SESSION['md5'] . '"';
	mysqli_real_query($mysqli,$query);

	header("Location: " . $_SERVER['HTTP_REFERER']);
	exit;
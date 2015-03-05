<?php
	require_once dirname(__FILE__) . "/config.php";

	session_start();
	if(isset($_SESSION['username'])) {
		$query = 'SELECT USERNAME,TIMEZONE,MD5 FROM users WHERE USERNAME="' . $_SESSION['username'] . '"';
		$result = mysqli_query($mysqli,$query);
		$row = mysqli_fetch_array($result);
		if(!isset($row['USERNAME'])) {
			session_destroy();
		}
		else {
			$query = 'UPDATE users SET LAST_ACTIVE=' . time() . ' WHERE USERNAME="' . $row['USERNAME'] . '"';
			$result = mysqli_query($mysqli,$query);

			$_SESSION['md5'] = $row['MD5'];

			setcookie(session_name(),session_id(),time()+81600);
			date_default_timezone_set($row['TIMEZONE']);
		}
	} else {
		date_default_timezone_set("America/Chicago");
	}
?>
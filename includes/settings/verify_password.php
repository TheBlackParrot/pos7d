<?php
	require_once dirname(dirname(__FILE__)) . "/config.php";
	require_once dirname(dirname(__FILE__)) . "/session.php";
	require_once dirname(dirname(__FILE__)) . "/functions.php";

	if(!isset($_SESSION['username'])) {
		header("Location: " . $setting['root_domain']);
		die();
	}

	if(!isset($_GET['code'])) {
		die("Invalid code.");
	}

	$token = mysqli_real_escape_string($mysqli,stripslashes(htmlspecialchars($_GET['code'])));
	$query = 'SELECT * FROM verifications WHERE V_KEY="' . $token . '" AND REASON="CHANGE PWD"';
	$result = mysqli_query($mysqli,$query);

	if(mysqli_num_rows($result) == 1) {
		$row = mysqli_fetch_array($result);
		if(time() - $row['DATE_ADDED'] < 900) {
			$query = 'UPDATE users SET HASH="' . $row['V_DATA'] . '" WHERE MD5="' . $row['MD5'] . '"';
			mysqli_real_query($mysqli,$query);
			$query = 'DELETE FROM verifications WHERE V_KEY="' . $token . '" AND REASON="CHANGE PWD"';
			mysqli_real_query($mysqli,$query);
			die("Your password has been changed.");
		} else {
			$query = 'DELETE FROM verifications WHERE V_KEY="' . $token . '" AND REASON="CHANGE PWD"';
			mysqli_real_query($mysqli,$query);

			die("You must verify within 15 minutes of changing your password.");
		}
	} else {
		die("Invalid code.");
	}
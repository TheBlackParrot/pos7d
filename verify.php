<?php
	require_once "includes/config.php";

	if(isset($_SESSION['username'])) {
		header("Location: http://" . $_SESSION['loginrefer']);
		die();
	}

	if(!isset($_GET['code'])) {
		die("Invalid code.");
	}

	$token = mysqli_real_escape_string($mysqli,stripslashes(htmlspecialchars($_GET['code'])));
	$query = 'SELECT * FROM verifications WHERE V_KEY="' . $token . '"';
	$result = mysqli_query($mysqli,$query);

	if(mysqli_num_rows($result) == 1) {
		$row = mysqli_fetch_array($result);
		if(time() - $row['DATE_ADDED'] < 900) {
			$query = 'UPDATE users SET VERIFIED=1 WHERE MD5="' . $row['MD5'] . '"';
			mysqli_real_query($mysqli,$query);
			$query = 'DELETE FROM verifications WHERE V_KEY="' . $token . '"';
			mysqli_real_query($mysqli,$query);
			die("Your account has been verified; you may now login!");
		} else {
			$query = 'DELETE FROM verifications WHERE V_KEY="' . $token . '"';
			mysqli_real_query($mysqli,$query);
			$query = 'DELETE FROM users WHERE MD5="' . $row['MD5'] . '"';
			mysqli_real_query($mysqli,$query);

			die("You must verify within 15 minutes of registering. Please re-register");
		}
	} else {
		die("Invalid code.");
	}
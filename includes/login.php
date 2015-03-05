<?php
	require_once dirname(__FILE__) . "/config.php";

	if(isset($_SESSION['username'])) {
		header("Location: " . $setting['root_domain']);
		die();
	}

	$username = mysqli_real_escape_string($mysqli,stripslashes(htmlspecialchars($_POST['username'])));
	$password = mysqli_real_escape_string($mysqli,stripslashes(htmlspecialchars($_POST['password'])));
	if(strlen($username) >= 2 && strlen($username) < 64) {
		$query = 'SELECT * FROM users WHERE USERNAME="' . $username . '"';
		$result = mysqli_query($mysqli,$query);
		if(mysqli_num_rows($result) == 1) {
			$row = mysqli_fetch_array($result);
			if($row['VERIFIED'] == 0) {
				die("You must verify your account first. Please check your e-mail.");
			}
			if($row['HASH'] == hash("sha512",$password . "-:-" . $row['REGISTER_DATE'])) {
				session_start();
				$_SESSION['login'] = "1";
				$_SESSION['username'] = $username;
				//$_SESSION['user_id'] = $row['ID'];
				header("Location: " . $setting['root_domain']);
				exit;
			}
			else {
				header("Location: " . $setting['root_domain']);
				exit;
			}
		}
		else {
			header("Location: " . $setting['root_domain']);
			exit;
		}
	}
	else {
		header("Location: " . $setting['root_domain']);
		exit;
	}
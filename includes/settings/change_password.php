<?php
	require_once dirname(dirname(__FILE__)) . "/config.php";
	require_once dirname(dirname(__FILE__)) . "/functions.php";
	require_once dirname(dirname(__FILE__)) . "/session.php";

	if(!isset($_POST['password'])) {
		echo "No password was given.";
		die();
	}
	if(!isset($_SESSION['username'])) {
		header("Location: " . $setting['root_domain']);
		exit;
	}

	$password = mysqli_real_escape_string($mysqli,stripslashes(htmlspecialchars($_POST['password'])));
	$verify = mysqli_real_escape_string($mysqli,stripslashes(htmlspecialchars($_POST['verify'])));
	if($password != $verify)
		die("Passwords do not match, please try again.");

	// http://stackoverflow.com/a/13733588
	function crypto_rand_secure($min, $max) {
			$range = $max - $min;
			if ($range < 0) return $min; // not so random...
			$log = log($range, 2);
			$bytes = (int) ($log / 8) + 1; // length in bytes
			$bits = (int) $log + 1; // length in bits
			$filter = (int) (1 << $bits) - 1; // set all lower bits to 1
			do {
				$rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
				$rnd = $rnd & $filter; // discard irrelevant bits
			} while ($rnd >= $range);
			return $min + $rnd;
	}

	function getToken($length){
		$token = "";
		$codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$codeAlphabet.= "abcdefghijklmnopqrstuvwxyz";
		$codeAlphabet.= "0123456789";
		for($i=0;$i<$length;$i++){
			$token .= $codeAlphabet[crypto_rand_secure(0,strlen($codeAlphabet))];
		}
		return $token;
	}

	$token = getToken(32);

	$user_id = md5(htmlspecialchars($_SESSION['username']));

	$query = 'SELECT REGISTER_DATE,EMAIL FROM users WHERE MD5="' . $user_id . '"';
	$result = mysqli_query($mysqli,$query);
	$user_info = mysqli_fetch_array($result);

	$query = 'DELETE FROM verifications WHERE MD5="' . $user_id . '" AND REASON="CHANGE PWD"';
	mysqli_real_query($mysqli,$query);
	$query = 'INSERT INTO verifications (MD5,V_KEY,DATE_ADDED,REASON,V_DATA) VALUES ("' . $user_id . '", "' . $token . '", ' . time() . ', "CHANGE PWD", "' . hash("sha512",$password . "-:-" . $user_info['REGISTER_DATE']) . '")';
	mysqli_real_query($mysqli,$query);

	$email = $user_info['EMAIL'];
	$subject = 'pos7d password change verification';
	$message = "Please click this link to verify your new password.\r\n" . $setting['root_domain'] . "includes/settings/verify_password.php?code=" . $token;

	$headers   = array();
	$headers[] = "MIME-Version: 1.0";
	$headers[] = "Content-type: text/plain; charset=iso-8859-1";
	$headers[] = "From: no-reply@theblackparrot.us";
	$headers[] = "Reply-To: {$email}";
	$headers[] = "Subject: {$subject}";
	$headers[] = "X-Mailer: PHP/".phpversion();

	mail($email, $subject, $message, implode("\r\n", $headers));

	die("Please check your e-mail for a verification link.<br/>(This frontend here is currently being worked on, excuse the lack of style).");

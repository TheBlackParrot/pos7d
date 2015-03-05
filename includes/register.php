<?php
	require_once dirname(__FILE__) . "/config.php";

	$username = mysqli_real_escape_string($mysqli,stripslashes(htmlspecialchars($_POST['username'])));
	$password = mysqli_real_escape_string($mysqli,stripslashes(htmlspecialchars($_POST['password'])));
	$verify = mysqli_real_escape_string($mysqli,stripslashes(htmlspecialchars($_POST['verify'])));
	$bot_check = mysqli_real_escape_string($mysqli,stripslashes(strtolower(htmlspecialchars($_POST['bot_check']))));
	$email = mysqli_real_escape_string($mysqli,stripslashes(strtolower(htmlspecialchars($_POST['email']))));
	$dateadd = time();

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

	if($password != $verify)
		die("Passwords do not match, please try again.");
	if($bot_check != 151)
		die("This is needed to make sure you are human.");

	if(strlen($username) >= 2 && strlen($username) < 64) {
		if(strlen($password) >= 4) {
			$query = 'SELECT * FROM users WHERE USERNAME="' . $username . '"';
			$result = mysqli_query($mysqli,$query);

			if(mysqli_num_rows($result) <= 0) {
				$query = 'INSERT INTO users (USERNAME, HASH, MD5, VERIFIED, REGISTER_DATE, EMAIL) VALUES ("' . $username . '", "' . hash("sha512",$password . "-:-" . $dateadd) . '", "' . md5(htmlspecialchars($_POST['username'])) . '", 0, ' . $dateadd . ', "' . $email . '")';
				mysqli_real_query($mysqli,$query);

				$token = getToken(32);
				$query = 'INSERT INTO verifications (MD5,V_KEY,DATE_ADDED) VALUES ("' . md5(htmlspecialchars($_POST['username'])) . '", "' . $token . '", ' . time() . ')';
				mysqli_real_query($mysqli,$query);

				$subject = 'pos7d verification';
				$message = "Please click this link to verify your forum account.\r\n" . $setting['root_domain'] . "verify.php?code=" . $token;

				$headers   = array();
				$headers[] = "MIME-Version: 1.0";
				$headers[] = "Content-type: text/plain; charset=iso-8859-1";
				$headers[] = "From: no-reply@theblackparrot.us";
				$headers[] = "Reply-To: {$email}";
				$headers[] = "Subject: {$subject}";
				$headers[] = "X-Mailer: PHP/".phpversion();

				mail($email, $subject, $message, implode("\r\n", $headers));

				die("Please check your e-mail for a verification link.<br/>(This frontend here is currently being worked on, excuse the lack of style).");
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
?>
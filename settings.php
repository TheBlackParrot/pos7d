<?php
	$gen_time = -microtime(true);
	
	require_once "includes/config.php";
	require_once "includes/functions.php";
	require_once "includes/session.php";

	if(!isset($_SESSION['username'])) {
		header("Location: " . $setting['root_domain']);
		exit;
	}

	$user_id = htmlspecialchars($_SESSION['md5']);
	
	$query = 'SELECT USERNAME,TIMEZONE,REGISTER_DATE,LAST_ACTIVE,MD5 FROM users WHERE MD5="' . $user_id . '"';
	$result = mysqli_query($mysqli,$query);
	if(!mysqli_num_rows($result)) {
		die("Invalid ID");
	}
	$row = mysqli_fetch_array($result);

	//$md5_name = md5($row['USERNAME']);

	$name = $row['USERNAME'];
	if(!isset($bgc[$name])) {
		$bgc[$name] = "";
		for($i=0;$i<3;$i++) {
			$bgc[$name] .= str_pad(dechex(mt_rand(0,255)),2,'0',STR_PAD_LEFT);
		}
	}
	$av_str = "";
	if(is_file("images/av/" . $user_id . ".jpg")) {
		$av_str = '<img src="images/av/' . $user_id . '.jpg"/>';
	}
?>

<html lang="en">

<head>
	<link rel="stylesheet" type="text/css" href="css/reset.css"/>
	<link rel="stylesheet" type="text/css" href="css/main.css"/>
	<link rel="stylesheet" type="text/css" href="css/sections.css"/>
	<link rel="stylesheet" type="text/css" href="css/settings.css"/>
	<link rel="stylesheet" type="text/css" href="css/open-iconic.css"/>
</head>

<body>
	<div class="header">
		<?php include "includes/sections/header.php"; ?>
	</div>
	<div class="wrapper">
		<div class="breadcrumbs">
			<a href="<?php echo $setting['root_domain']; ?>"><span class="breadcrumb">Main</span></a>
		</div>
		<h1>General settings</h1>
		<form action="includes/settings/change_settings.php" method="post">
			<div class="settings_input_wrapper">
				Timezone
				<div class="settings_input">
					<select name="timezone" style="width: 256px;" required>
					<?php
						foreach(timezone_identifiers_list() as $timezone) {
							if($timezone == $row['TIMEZONE'])
								echo '<option value="' . $timezone . '" selected>' . $timezone . '</option>';
							else
								echo '<option value="' . $timezone . '">' . $timezone . '</option>';
						}
					?>
					</select>
				</div>
			</div>
			<!--<div class="settings_input_wrapper">
				Username
				<div class="settings_input"><input type="text" name="username" placeholder="Username" required></div>
			</div>-->
			<input type="submit" value="apply" class="button" style="float: left; margin: 0;"/>
		</form>
		<div class="tip">Timezones only affect what you see, times are not affected on the server.</div>
		<!-- [currently debating on allowing users to change email, commenting out for now]
		<h1>Change e-mail</h1>
		<form action="" method="post">
			<div class="settings_input_wrapper">
				E-Mail
				<div class="settings_input"><input type="text" name="email" placeholder="example@example.org" required></div>
			</div>
			<div class="settings_input_wrapper">
				Confirm E-Mail
				<div class="settings_input"><input type="text" name="confirm_email" placeholder="example@example.org" required></div>
			</div>
			<input type="submit" value="change e-mail" class="button" style="float: left; margin: 0;"/>
		</form>
		<div class="tip">An e-mail will be sent to your old address to verify your new e-mail.<br/>(Methods are currently being figured out for those who have no access to their previous e-mail address.)</div>
		-->
		<h1>Change password</h1>
		<form action="includes/settings/change_password.php" method="post">
			<div class="settings_input_wrapper">
				Password
				<div class="settings_input"><input type="password" name="password" required></div>
			</div>
			<div class="settings_input_wrapper">
				Confirm password
				<div class="settings_input"><input type="password" name="verify" required></div>
			</div>
			<input type="submit" value="change password" class="button" style="float: left; margin: 0;"/>
		</form>
		<div class="tip">An e-mail will be sent to your currently specified address to verify your new password.<br/>Your old password will still be functional until you have verified the change.</div>
		<h1>Change avatar</h1>
		<form name="avatar-change-form" action="includes/settings/change_avatar.php" method="POST" enctype="multipart/form-data">
		<img class="av" src="images/av/<?php echo md5(htmlspecialchars($_SESSION['username'])); ?>.jpg"/>
			<div class="settings_input_wrapper">
				<input type="file" name="userfile" style="margin-bottom: 16px;" required/><br/>
			</div>
			<input type="submit" value="upload" class="button" style="float: left; margin: 0;"/>
		</form><br/>
		<div class="tip">Avatars are resized to 100x100 JPG files at quality 97.<br/>Only images smaller than <?php echo ini_get("upload_max_filesize"); ?> are allowed.</div>
		<div class="footer">
			<?php
				echo '<span style="color: #777;">pos7d</span> is currently under development. Please expect bugs, and report them immediately.<br/>';
				$gen_time += microtime(true);
				echo "Page generated in " . number_format($gen_time,3) . " seconds";
			?>
		</div>
	</div>
</body>

</html>
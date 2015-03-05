<?php
	$gen_time = -microtime(true);
	
	require_once "includes/config.php";
	require_once "includes/functions.php";
	require_once "includes/session.php";

	if(!isset($_GET['id'])) {
		echo "No user ID was given.";
		die();
	} else {
		$user_id = htmlspecialchars($_GET['id']);
		
		$query = 'SELECT USERNAME,TIMEZONE,REGISTER_DATE,LAST_ACTIVE,MD5,RANK_LEVEL,POST_COUNT FROM users WHERE MD5="' . $user_id . '"';
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
	}
?>

<html lang="en">

<head>
	<link rel="stylesheet" type="text/css" href="css/reset.css"/>
	<link rel="stylesheet" type="text/css" href="css/main.css"/>
	<link rel="stylesheet" type="text/css" href="css/sections.css"/>
	<link rel="stylesheet" type="text/css" href="css/open-iconic.css"/>
	<style>
		.rank_bubble {
			margin: 0 !important;
		}
	</style>
</head>

<body>
	<div class="header">
		<?php include "includes/sections/header.php"; ?>
	</div>
	<div class="wrapper">
		<div class="breadcrumbs">
			<a href="<?php echo $setting['root_domain']; ?>"><span class="breadcrumb">Main</span></a>
		</div>
		<div class="post">
			<div class="av" style="background-color: #<?php echo $bgc[$name]; ?>;"><?php echo $av_str; ?></div>
			<h1><?php echo isUserActive($row['LAST_ACTIVE'],0) . $name; ?></h1>
			<table>
				<tr>
					<td>Registered since</td>
					<td><?php echo date('M. d, Y g:i A',$row['REGISTER_DATE']); ?></td>
				</tr>
				<tr>
					<td>Last Active</td>
					<td><?php echo date('M. d, Y g:i A',$row['LAST_ACTIVE']); ?></td>
				</tr>
				<tr>
					<td>Rank</td>
					<td><?php echo getUserRank($row['RANK_LEVEL']); ?></td>
				</tr>
				<tr>
					<td>Timezone</td>
					<td><?php echo $row['TIMEZONE']; ?></td>
				</tr>
				<tr>
					<td>Post Count</td>
					<td><?php echo $row['POST_COUNT']; ?></td>
				</tr>
			</table>
		</div>
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
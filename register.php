<?php
	$gen_time = -microtime(true);

	require_once "includes/config.php";
	require_once "includes/functions.php";

	$query = "SELECT * FROM categories";
	$result = mysqli_query($mysqli,$query);
	if(isset($_SESSION['username'])) {
		header("Location: " . $setting['root_domain']);
		exit;
	}
?>

<html lang="en">

<head>
	<link rel="stylesheet" type="text/css" href="css/reset.css"/>
	<link rel="stylesheet" type="text/css" href="css/main.css"/>
	<link rel="stylesheet" type="text/css" href="css/threads.css"/>
	<link rel="stylesheet" type="text/css" href="css/open-iconic.css"/>
	<script src="js/jquery.js"></script>
</head>

<body>
	<div class="header">
		<?php include "includes/sections/header.php"; ?>
	</div>
	<div class="wrapper">
		<div class="breadcrumbs">
			<a href="<?php echo $setting['root_domain']; ?>"><span class="breadcrumb">Main</span></a>
		</div>
		<form action="includes/register.php" method="post" style="line-height: 23px;">
			<div style="width: 400px;"><span style="text-align: left;">Username</span>
				<div style="float: right;"><input type="text" name="username" style="width: 256px;" placeholder="Username" required></div>
			</div>
			<div style="width: 400px;"><span style="text-align: left;">E-mail</span>
				<div style="float: right;"><input type="text" name="email" style="width: 256px;" placeholder="example@example.com" required></div>
			</div>
			<div style="width: 400px;"><span style="text-align: left;">Password</span>
				<div style="float: right;"><input type="password" name="password" style="width: 256px;" placeholder="Password" required></div>
			</div>
			<div style="width: 400px;"><span style="text-align: left;">Re-type Password</span>
				<div style="float: right;"><input type="password" name="verify" style="width: 256px;" placeholder="Password" required></div>
			</div>
			<div style="width: 400px;"><span style="text-align: left;">What is (3 x 4)<sup>2</sup> plus 7?</span>
				<div style="float: right;"><input type="text" name="bot_check" style="width: 256px;" placeholder="3 digit number here" required></div>
			</div><br/>
			<input type="submit" value="Register" class="button" style="float: left; margin: 0;"/>
		</form>
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
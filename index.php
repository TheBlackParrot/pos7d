<?php
	$gen_time = -microtime(true);

	require_once "includes/config.php";
	require_once "includes/functions.php";
	require_once "includes/session.php";

	$query = "SELECT * FROM categories";
	$result = mysqli_query($mysqli,$query);
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
	<!--
		use <span class="oi" data-glyph="comment-square"></span> as an icon
		color it red for new replies
		leave black for no new replies
	-->
	<div class="header">
		<?php include "includes/sections/header.php"; ?>
	</div>
	<div class="wrapper">
		<div class="breadcrumbs">
			<a href="<?php echo $setting['root_domain']; ?>"><span class="breadcrumb">Main</span></a>
		</div>
		<h1>General</h1>
		<div class="threads">
			<table>
				<tr>
					<td></td>
					<td>category</td>
					<td>last post by</td>
					<td>topics</td>
					<td>last post</td>
				</tr>
				<?php
					while($row = mysqli_fetch_array($result)) {
						$category_id = $row['MD5'];
						$db_filename = 'includes/db/' . $category_id . '.db';

						$db = new SQLite3($db_filename);

						$count = $db->querySingle('SELECT COUNT(MD5) FROM topics');
						if($count > 0) {
							$recent_thr_q = $db->query('SELECT MD5,LAST_POST FROM topics ORDER BY LAST_POST DESC LIMIT 1');
							$recent_thr = $recent_thr_q->fetchArray();
							$recent_info_q = $db->query('SELECT AUTHOR,POST_DATE FROM "' . $recent_thr['MD5'] . '" ORDER BY POST_DATE DESC LIMIT 1');
							$recent_info = $recent_info_q->fetchArray();
						}
						if($count > 0) {
							?>
								<tr>
									<td><span class="oi" data-glyph="comment-square"></span></td>
									<td><a href="threads.php?id=<?php echo $category_id; ?>"><?php echo $row['NAME']; ?></a></td>
									<td><a href="user.php?id=<?php echo md5(htmlspecialchars($recent_info['AUTHOR'])); ?>"><?php echo $recent_info['AUTHOR']; ?></a></td>
									<td><?php echo $count; ?></td>
									<td><?php echo date('M. d, Y g:i A',$recent_thr['LAST_POST']); ?></td>
								</tr>
							<?php
						} else {
							?>
								<tr>
									<td><span class="oi" data-glyph="comment-square"></span></td>
									<td><a href="threads.php?id=<?php echo $category_id; ?>"><?php echo $row['NAME']; ?></a></td>
									<td>-</td>
									<td><?php echo $count; ?></td>
									<td>-</td>
								</tr>
							<?php
						}
					}
				?>
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
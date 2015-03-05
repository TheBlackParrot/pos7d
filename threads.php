<?php
	$gen_time = -microtime(true);

	require_once "includes/config.php";
	require_once "includes/functions.php";
	require_once "includes/session.php";

	if(!isset($_GET['page'])) {
		$page = 1;
	} else {
		$page = htmlspecialchars($_GET['page']);
	}

	if(!isset($_GET['id'])) {
		echo "No category ID was given.";
		die();
	} else {
		$category_id = htmlspecialchars($_GET['id']);
		$db_filename = 'includes/db/' . $category_id . '.db';

		$db = new SQLite3($db_filename);

		$count = $db->querySingle('SELECT COUNT(*) FROM topics');
		$pages = ceil($count / 30);
		if($page > $pages || $page < $pages) {
			$page = 1;
		}
		$limit_str = (($page-1)*30) . ',' . $page*30;

		$thread_list = $db->query('SELECT * FROM topics ORDER BY LAST_POST DESC LIMIT ' . $limit_str);

		$category_name = getCategoryName($category_id);
	}
?>

<html lang="en">

<head>
	<link rel="stylesheet" type="text/css" href="css/reset.css"/>
	<link rel="stylesheet" type="text/css" href="css/main.css"/>
	<link rel="stylesheet" type="text/css" href="css/threads.css"/>
	<link rel="stylesheet" type="text/css" href="css/posts.css"/>
	<link rel="stylesheet" type="text/css" href="css/open-iconic.css"/>
	<script src="js/jquery.js"></script>
	<script>
		<?php echo 'var category_id = "' . $category_id . '";'; ?>
		$(document).ready(function(){
			$(".button[id='add']").on("click", function(){
				$(".submit_loader").load("includes/sections/new_thread.php?" + $.param({id: category_id}), function(){
					$(".button[id='add']").css("display","none");
				})
			})
		})
	</script>
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
			<a href="<?php echo $setting['root_domain']; ?>"><span class="breadcrumb">Main</span></a> > <a href="threads.php?id=<?php echo $category_id; ?>"><span class="breadcrumb"><?php echo $category_name; ?></span></a>
		</div>
		<h1><?php echo $category_name; ?></h1>
		<div class="threads">
			<table>
				<tr>
					<td></td>
					<td>thread</td>
					<td>author</td>
					<td>replies</td>
					<td>last post</td>
				</tr>
				<?php
					while($list_row = $thread_list->fetchArray(SQLITE3_BOTH)) {
						$count = $db->querySingle('SELECT COUNT(*) FROM "' . $list_row['MD5'] . '"');
						$session_thr_id = 'last_seen-' . $list_row['MD5'];
						$new_post = 'style="color: #333;"';
						if($list_row['LOCKED'] != 1) {
							if(isset($_SESSION['username'])) {
								if(isset($_SESSION[$session_thr_id])) {
									if($list_row['LAST_POST'] > $_SESSION[$session_thr_id]) {
										$new_post = 'style="color: #F44336;"';
									}
								} else {
									$new_post = 'style="color: #F44336;"';
								}
							} else {
								$new_post = 'style="color: #333;"';
							}
							$thr_icon = '<span class="oi" ' . $new_post . ' data-glyph="comment-square"></span>';
						} else {
							$thr_icon = '<span class="oi locked" data-glyph="lock-locked"></span>';
						}
						?>
						<tr>
							<td><?php echo $thr_icon; ?></td>
							<td><a href="<?php echo "posts.php?id=" . $category_id . "&thread=" . $list_row['MD5']; ?>"><?php echo $list_row['NAME']; ?></a></td>
							<td><?php echo '<a href="user.php?id=' . md5(htmlspecialchars($list_row['AUTHOR'])) . '">' . $list_row['AUTHOR']; ?></a></td>
							<td><?php echo $count; ?></td>
							<td><a href="<?php echo "posts.php?id=" . $category_id . "&thread=" . $list_row['MD5'] . "#post" . $count; ?>"><?php echo date('M. d, Y g:i A',$list_row['LAST_POST']); ?></td>
						</tr>
						<?php
					}
				?>
			</table>
		</div>
		<div class="buttons">
			<div class="pagination">
				<?php
					$pg_str = "threads.php?id=" . $category_id . "&page=";
					echo getPagination($page,$pages,$pg_str);
				?>
			</div>
			<?php if(isset($_SESSION['username'])) {
				?>
				<div class="button" id="add">add thread</div>
				<?php
			}
			?>
		</div>
		<div class="submit_loader">
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
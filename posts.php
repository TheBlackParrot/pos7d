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
	}
	if(!isset($_GET['thread'])) {
		echo "No thread was selected.";
		die();
	} else {
		$category_id = htmlspecialchars($_GET['id']);
		$thread_id = htmlspecialchars($_GET['thread']);

		$db_filename = 'includes/db/' . $category_id . '.db';

		$db = new SQLite3($db_filename);

		$count = $db->querySingle('SELECT COUNT(*) FROM "' . $thread_id . '"');
		$pages = ceil($count / 30);
		if($page > $pages || $page < $pages) {
			$page = 1;
		}
		$limit_str = (($page-1)*30) . ',' . $page*30;

		// LIMIT (($page-1)*30)+1, ($page*30)
		$post_list = $db->query('SELECT * FROM "' . $thread_id . '" LIMIT ' . $limit_str);
		$thread_owner = $db->querySingle('SELECT AUTHOR FROM topics WHERE MD5="' . $thread_id . '"');

		$is_locked = $db->querySingle('SELECT LOCKED FROM topics WHERE MD5="' . $thread_id . '"');

		$category_name = getCategoryName($category_id);
		$thread_title = getThreadName($category_id,$thread_id);
	}

	if(isset($_SESSION['username'])) {
		$session_thr_id = 'last_seen-' . $thread_id;
		$_SESSION[$session_thr_id] = time();
	}
?>

<html lang="en">

<head>
	<link rel="stylesheet" type="text/css" href="css/reset.css"/>
	<link rel="stylesheet" type="text/css" href="css/main.css"/>
	<link rel="stylesheet" type="text/css" href="css/posts.css"/>
	<link rel="stylesheet" type="text/css" href="css/open-iconic.css"/>
	<link rel="stylesheet" type="text/css" href="css/code.css"/>
	<script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/8.4/highlight.min.js"></script>
	<script src="js/jquery.js"></script>
	<script>
		<?php echo 'var category_id = "' . $category_id . '";'; ?>
		<?php echo 'var thread_id = "' . $thread_id . '";'; ?>
		$(document).ready(function(){
			$(".button[id='reply']").on("click", function(){
				$(".reply_loader").load("includes/sections/reply.php?" + $.param({id: category_id, thread: thread_id}), function(){
					$(".button[id='reply']").css("display","none");
				})
			})
			$(".button_left[id='lock']").on("click", function(){
				console.log("toggled");
				$.get("includes/sections/toggle_lock.php?" + $.param({id: category_id, thread: thread_id}), function(data){
					//$("body").append(data);
				})
			})
			$(".edit").on("click", function(){
				$(".reply_loader").load("includes/sections/edit.php?" + $.param({id: category_id, thread: thread_id, post_num: $(this).attr("pnum")}), function(){
					$(".button[id='reply']").css("display","none");
					location.hash = "#reply_box";
				})
			})
			$('.post pre code').each(function(i, block) {
				hljs.highlightBlock(block);

				var lines = $(this).text().split('\n').length - 1;
				var $numbering = $('<ul/>').addClass('pre-numbering');
				$(this).addClass('has-numbering').parent().append($numbering);
				for(i=1;i<=lines+1;i++){
					$numbering.append($('<li/>').text(i));
				}
				var width = $(this).parent().children("ul").width();
				$(this).css("margin-left",width + 8 + "px");
			});
		})
	</script>
</head>

<body>
	<div class="header">
		<?php include "includes/sections/header.php"; ?>
	</div>
	<div class="wrapper">
		<div class="breadcrumbs">
			<a href="<?php echo $setting['root_domain']; ?>"><span class="breadcrumb">Main</span></a> > <a href="threads.php?id=<?php echo $category_id; ?>"><span class="breadcrumb"><?php echo $category_name; ?></span></a> > <a href="posts.php?id=<?php echo $category_id  . "&thread=" . $thread_id; ?>"><span class="breadcrumb"><?php echo $thread_title; ?></span></a>
		</div>
		<h1 class="no_border"><?php echo $thread_title; ?></h1>
		<div class="posts">
			<?php
				while($content = $post_list->fetchArray(SQLITE3_BOTH)) {
					$name = $content['AUTHOR'];
					$md5_name = md5($content['AUTHOR']);
					$user_info = getUserInfo($md5_name);
					$av_str = "";
					if(!isset($bgc[$name])) {
						$bgc[$name] = "";
						for($i=0;$i<3;$i++) {
							$bgc[$name] .= str_pad(dechex(mt_rand(0,255)),2,'0',STR_PAD_LEFT);
						}
					}
					if(is_file("images/av/" . $md5_name . ".jpg")) {
						$av_str = '<img src="images/av/' . $md5_name . '.jpg"/>';
					}
				?>
				<div class="post" id="post<?php echo $content['ID']; ?>">
					<div class="av" style="background-color: #<?php echo $bgc[$name]; ?>;">
						<div class="av_wrapper">
							<?php echo $av_str; ?>
						</div>
						<div class="av_info">
							<span class="av_info_l">Posts</span>
							<span class="av_info_r"><?php echo number_format($user_info['POST_COUNT']); ?></span>
						</div>
					</div>
					<div class="info">
						posted by <?php echo isUserActive($user_info['LAST_ACTIVE'],0); ?> <a href="user.php?id=<?php echo md5(htmlspecialchars($content['AUTHOR'])); ?>"><?php echo $content['AUTHOR']; ?></a><?php echo getUserRank($user_info['RANK_LEVEL']); ?> at <?php echo date('M. d, Y g:i A',$content['POST_DATE']); ?>
						<?php
							if($content['EDIT_DATE'] != 0) {
							?>
								<em class="edit_info">last edited <?php echo date('M. d, Y g:i A',$content['EDIT_DATE']); ?></em>
							<?php
							}
						?>
						<div class="post_buttons">
							<?php if($content['AUTHOR'] == $_SESSION['username']) { ?>
								<a href="#reply_anchor" class="edit" pnum="<?php echo $content['ID']; ?>"><span class="oi" data-glyph="pencil"></span> edit</a>
							<?php } ?>
							<a href="<?php echo "posts.php?id=" . $category_id . "&thread=" . $thread_id . "&page=" . $page . "#post" . $content['ID']; ?>"><span class="oi" data-glyph="globe"></span> #<?php echo $content['ID']; ?></a>
						</div>
					</div>
					<div class="content">
						<?php
							echo parsePostData($content['CONTENT']);
						?>
					</div>
				</div>
				<?php
				}
			?>
		</div>
		<div class="buttons">
			<div class="pagination">
				<?php
					$pg_str = "posts.php?id=" . $category_id . "&thread=" . $thread_id . "&page=";
					echo getPagination($page,$pages,$pg_str);
				?>
			</div>
			<?php if(isset($_SESSION['username'])) {
				if($is_locked == 0) {
					?>
					<div class="button" id="reply">reply</div>
					<?php
				}
				if($thread_owner == $_SESSION['username']) {
					?>
						<div class="button_left" id="lock">toggle lock</div>
					<?php
				}
			}
			?>
		</div>
		<div class="reply_loader" id="reply_anchor">
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
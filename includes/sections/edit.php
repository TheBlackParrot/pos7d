<?php
	if(!isset($_GET['id'])) {
		echo "No category ID was given.";
		die();
	}
	if(!isset($_GET['thread'])) {
		echo "No thread was selected.";
		die();
	}
	if(!isset($_GET['post_num'])) {
		echo "No post was specified.";
		die();
	}

	require_once dirname(dirname(__FILE__)) . "/config.php";
	require_once dirname(dirname(__FILE__)) . "/functions.php";
	require_once dirname(dirname(__FILE__)) . "/session.php";

	$category_id = htmlspecialchars($_GET['id']);
	$thread_id = htmlspecialchars($_GET['thread']);
	$post_num = htmlspecialchars($_GET['post_num']);

	$db_filename = dirname(dirname(__FILE__)) . '/db' . '/' . $category_id . '.db';

	$db = new SQLite3($db_filename);

	$post_info_q = $db->query('SELECT AUTHOR,CONTENT,POST_DATE FROM "' . $thread_id . '" WHERE ID=' . $post_num);
	$post_info = $post_info_q->fetchArray();
	$is_locked = $db->querySingle('SELECT LOCKED FROM topics WHERE MD5="' . $thread_id . '"');

	if($post_info['AUTHOR'] != $_SESSION['username']) {
		die("Not the original author, nice try kiddo.");
	}
	if($is_locked) {
		die("Thread is locked, you cannot edit your posts.");
	}
?>

<div class="post_info">
	pos7d uses <a href="https://help.github.com/articles/github-flavored-markdown/">GitHub flavored Markdown</a> to format posts.<br/><em>Editing post #<?php echo $post_num . " posted on " . date('M. d, Y g:i A',$post_info['POST_DATE']); ?>
</div>
<form action="includes/submit_edit.php" method="POST" id="reply_message">
	<textarea id="reply_box" class="reply_box" name="reply_message" placeholder="Enter a message..." required><?php echo htmlspecialchars_decode($post_info['CONTENT']); ?></textarea>
	<input type="hidden" name="id" value="<?php echo $category_id; ?>">
	<input type="hidden" name="thread" value="<?php echo $thread_id; ?>">
	<input type="hidden" name="post_num" value="<?php echo $post_num; ?>">
</form>

<div class="buttons">
	<?php if(isset($_SESSION['username'])) {
		?>
		<div class="button" id="submit" onclick='document.getElementById("reply_message").submit();' style="margin-bottom: 32px;">edit post</div>
		<div class="button" id="preview" onclick='previewPost();' style="margin-bottom: 32px;">preview post</div>
		<?php
	}
	?>
</div>

<div class="preview_loader" style="margin-bottom: 32px;"></div>

<script>
	function previewPost() {
		var message_data = $(".reply_box").val();

		$.post("includes/sections/preview.php", {data: message_data})
		.done(function(data) {
			$(".preview_loader").html(data)
			$('.preview_loader .post pre code').each(function(i, block) {
				hljs.highlightBlock(block);

				var lines = $(this).text().split('\n').length - 1;
				var $numbering = $('<ul/>').addClass('pre-numbering');
				$(this).addClass('has-numbering').parent().append($numbering);
				for(i=1;i<=lines+1;i++){
					$numbering.append($('<li/>').text(i));
				}
			});
		})
	}
	$(document).ready(function(){
		previewPost();
	})
</script>
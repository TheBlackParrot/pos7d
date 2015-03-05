<?php
	if(!isset($_GET['id'])) {
		echo "No category ID was given.";
		die();
	}
	if(!isset($_GET['thread'])) {
		echo "No thread was selected.";
		die();
	}

	require_once dirname(dirname(__FILE__)) . "/config.php";
	require_once dirname(dirname(__FILE__)) . "/functions.php";
	require_once dirname(dirname(__FILE__)) . "/session.php";

	$category_id = htmlspecialchars($_GET['id']);
	$thread_id = htmlspecialchars($_GET['thread']);

	$db_filename = dirname(dirname(__FILE__)) . '/db' . '/' . $category_id . '.db';

	$db = new SQLite3($db_filename);

	$is_locked = $db->querySingle('SELECT LOCKED FROM topics WHERE MD5="' . $thread_id . '"');
	if($is_locked) {
		die("Thread is locked, you cannot reply.");
	}
?>

<div class="post_info">
	pos7d uses <a href="https://help.github.com/articles/github-flavored-markdown/">GitHub flavored Markdown</a> to format posts.
</div>
<form action="includes/submit_reply.php" method="POST" id="reply_message">
	<textarea id="reply_box" class="reply_box" name="reply_message" placeholder="Enter a message..." required></textarea>
	<input type="hidden" name="id" value="<?php echo htmlspecialchars($_GET['id']); ?>">
	<input type="hidden" name="thread" value="<?php echo htmlspecialchars($_GET['thread']); ?>">
</form>

<div class="buttons">
	<?php if(isset($_SESSION['username'])) {
		?>
		<div class="button" id="submit" onclick='document.getElementById("reply_message").submit();' style="margin-bottom: 32px;">submit reply</div>
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
				var width = $(this).parent().children("ul").width();
				$(this).css("margin-left",width + 8 + "px");
			});
		})
	}
</script>
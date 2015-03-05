<?php
	if(!isset($_GET['id'])) {
		echo "No category ID was given.";
		die();
	}

	require_once dirname(dirname(__FILE__)) . "/config.php";
	require_once dirname(dirname(__FILE__)) . "/functions.php";
	require_once dirname(dirname(__FILE__)) . "/session.php";

	if(!isset($_SESSION['username'])) {
		header("Location: " . $setting['root_domain']);
		exit;
	}
?>

<form action="includes/submit_thread.php" method="POST" id="new_thread">
	<input class="threadtitle_box" type="text" name="thread_name" style="width: 100%;" placeholder="Topic title" required>
pos7d uses <a href="https://help.github.com/articles/github-flavored-markdown/">GitHub flavored Markdown</a> to format posts.
	<textarea class="reply_box" name="message" placeholder="Enter a message..." required></textarea>
	<input type="hidden" name="id" value="<?php echo htmlspecialchars($_GET['id']); ?>">
</form>

<div class="preview_loader"></div>

<div class="buttons" style="padding-bottom: 64px;">
	<?php if(isset($_SESSION['username'])) {
		?>
		<div class="button" id="submit" onclick='document.getElementById("new_thread").submit();'>submit thread</div>
		<div class="button" id="preview" onclick='previewPost();' style="margin-bottom: 64px;">preview post</div>
		<?php
	}
	?>
</div>

<script>
	function previewPost() {
		var message_data = $(".reply_box").val();

		$.post("includes/sections/preview.php", {data: message_data})
		.done(function(data) {
			$(".preview_loader").html(data)
			$('.preview_loader .post pre code').each(function(i, block) {
				hljs.highlightBlock(block);
			});
		})
	}
</script>
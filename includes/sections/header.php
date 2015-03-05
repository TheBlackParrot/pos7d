<div class="header">
	<?php if(isset($_SESSION['username'])) {
		$md5_name = md5(htmlspecialchars($_SESSION['username']));
		$av_str = "";
		if(is_file("images/av/" . $md5_name . ".jpg")) {
			$av_str = '<img src="images/av/' . $md5_name . '.jpg"/>';
		}
		?>
		<div class="header_right">
			<a href="user.php?id=<?php echo $md5_name; ?>"><div class="header_user"><?php echo $av_str; ?> <?php echo $_SESSION['username']; ?></div></a>
		</div>
		<div class="header_right">
			<a href="includes/logout.php"><div class="header_link"><span class="oi" data-glyph="account-logout"></span> logout</div></a>
		</div>
		<div class="header_right">
			<a href="settings.php"><div class="header_link"><span class="oi" data-glyph="cog"></span></span> settings</div></a>
		</div>
		<div class="header_left">
			<a href="#"><div class="header_link"><span class="oi" data-glyph="envelope-closed"></span></div></a>
		</div>
		<div class="header_left">
				<a href="#"><div class="header_link"><span class="oi" data-glyph="book"></span></div></a>
		</div>
		<div class="header_left">
				<a href="#"><div class="header_link"><span class="oi" data-glyph="bell"></span></div></a>
		</div>
		<?php
	} else {
		?>
		<div class="header_right">
			<a href="login.php"><div class="header_link"><span class="oi" data-glyph="account-login"></span> login</div></a>
		</div>
		<div class="header_right">
			<a href="register.php"><div class="header_link"><span class="oi" data-glyph="person"></span> register</div></a>
		</div>
		<?php
	}
	?>
</div>
<script src="js/jquery.js"></script>
<script>
	$(document).ready(function(){
		$(".header_left a").on("click",function(){
			alert("These are currently placeholders, and do nothing.");
		})
	})
</script>
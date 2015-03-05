<?php
if(!isset($_POST['data'])) {
	die("No data");
}

require_once dirname(dirname(__FILE__)) . "/functions.php";

echo '<div class="post">' . parsePostData($_POST['data']) . '</div>';
//return "yes";
?>
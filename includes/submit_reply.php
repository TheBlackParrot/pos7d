<?php
	// making this easier to update as I did parts of the same function in 2 different scripts.
	// see: submit_thread.php
	require_once dirname(__FILE__) . "/config.php";
	require_once dirname(__FILE__) . "/functions.php";
	if(!isset($_POST['reply_message'])) {
		die("No message");
	}
	if($_POST['reply_message'] == "") {
		die("No message");
	}

	postMessage($_POST['id'],$_POST['thread'],$_POST['reply_message']);
?>
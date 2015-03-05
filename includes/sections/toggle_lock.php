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

	$thread_owner = $db->querySingle('SELECT AUTHOR FROM topics WHERE MD5="' . $thread_id . '"');

	if($thread_owner != $_SESSION['username']) {
		die("Not the original author, nice try kiddo.");
	} else {
		toggleLockedTopic($category_id,$thread_id);
		header("Location: " . $setting['root_domain'] . "threads.php?id=" . $category_id);
		exit;
	}
?>
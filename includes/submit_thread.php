<?php
//CREATE TABLE "md5(TOPIC_NAME)" (ID INT NOT NULL AUTO_INCREMENT, AUTHOR VARCHAR(64) DEFAULT "Anonymous", CONTENT TEXT DEFAULT NULL, POST_DATE INT(32) DEFAULT 0, EDIT_DATE INT(32) DEFAULT 0, FAVES INT(7) DEFAULT 0, FLAGGED INT(1) DEFAULT 0, PRIMARY KEY (ID))
//CREATE TABLE topics (MD5 VARCHAR(32) DEFAULT "NA", NAME VARCHAR(256) DEFAULT "Topic", AUTHOR VARCHAR(64) DEFAULT "Anonymous", INIT_DATE INT(32) DEFAULT 0, LOCKED INT(1) DEFAULT 0, STICKY INT(1) DEFAULT 0, LAST_POST INT(32) DEFAULT 0, PRIMARY KEY (MD5))
	require_once dirname(__FILE__) . "/config.php";
	require_once dirname(__FILE__) . "/functions.php";
	require_once dirname(__FILE__) . "/session.php";

	if(!isset($_SESSION['username'])) {
		header("Location: " . $setting['root_domain']);
		exit;
	} else {
		if(!isset($_POST['id'])) {
			die("No category ID");
		}
		if(!isset($_POST['thread_name'])) {
			die("No thread name");
		}
		if(!isset($_POST['message'])) {
			die("No message");
		}
		if($_POST['thread_name'] == "") {
			die("No thread name");
		}
		if($_POST['message'] == "") {
			die("No message");
		}
		$thread_name = htmlspecialchars($_POST['thread_name']);
		$md5_id = md5($thread_name);
		$category_id = htmlspecialchars($_POST['id']);

		$db_filename = 'db/' . $category_id . '.db';
		$db = new SQLite3($db_filename);

		$duplicate_check = $db->querySingle('SELECT COUNT(MD5) FROM topics WHERE MD5="' . $md5_id . '"');

		if($duplicate_check == 0) {
			$db->exec('CREATE TABLE "' . $md5_id . '" (ID INTEGER PRIMARY KEY AUTOINCREMENT, AUTHOR VARCHAR(64) DEFAULT "Anonymous", CONTENT TEXT DEFAULT NULL, POST_DATE INT(32) DEFAULT 0, EDIT_DATE INT(32) DEFAULT 0, FAVES INT(7) DEFAULT 0, FLAGGED INT(1) DEFAULT 0)');
			$db->exec('INSERT INTO topics (MD5,NAME,AUTHOR,INIT_DATE,LAST_POST) VALUES ("' . $md5_id . '", "' . SQLite3::escapeString($thread_name) . '", "' . SQLite3::escapeString($_SESSION['username']) . '", ' . time() . ', ' . time() . ')');
			postMessage($_POST['id'],$md5_id,$_POST['message']);
		} else {
			die("Thread already exists");
		}

		header("Location: " . $_SERVER['HTTP_REFERER']);
		exit;
	}
?>
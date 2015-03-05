<?php
	require_once dirname(__FILE__) . "/config.php";

	$query = 'CREATE TABLE users (ID INT NOT NULL AUTO_INCREMENT, USERNAME VARCHAR(64) DEFAULT "Anonymous", HASH VARCHAR(512) DEFAULT NULL, EMAIL VARCHAR(512) DEFAULT NULL, MD5 VARCHAR(32) DEFAULT 0, VERIFIED INT(1) DEFAULT 0, REGISTER_DATE INT(32) DEFAULT 0, LAST_ACTIVE INT(32) DEFAULT 0, TIMEZONE VARCHAR(256) DEFAULT "America/Chicago", POST_COUNT INT(16) DEFAULT 0, RANK_LEVEL INT(2) DEFAULT 0, MODERATING TEXT DEFAULT NULL, LAST_POSTED INT(32) DEFAULT 0, PRIMARY KEY (ID))';
	mysqli_real_query($mysqli,$query);

	$query = 'CREATE TABLE verifications (MD5 VARCHAR(32) DEFAULT NULL, V_KEY VARCHAR(32) DEFAULT NULL, DATE_ADDED INT(32) DEFAULT 0, PRIMARY KEY (V_KEY))';
	mysqli_real_query($mysqli,$query);

	$query = 'CREATE TABLE categories (ID INT NOT NULL AUTO_INCREMENT, NAME VARCHAR(256) DEFAULT "Category", DESCRIPTION TEXT DEFAULT NULL, CREATION_DATE INT(32) DEFAULT 0, MODERATORS TEXT DEFAULT NULL, HIDDEN INT(1) DEFAULT 0, MD5 VARCHAR(32) DEFAULT NULL, PRIMARY KEY (ID))';
	mysqli_real_query($mysqli,$query);

	$db_filename = dirname(__FILE__) . "/includes" . "/" . "db";
	if(!is_dir($db_filename)) {
		mkdir($db_filename);
	}

	// provided as a sample for right now until the admin backend is completed
	$category_name = "General Discussion";
	$category_id = md5(htmlspecialchars($category_name));
	$db_filename = 'db/' . $category_id . '.db';

	$query = 'INSERT INTO categories (NAME, CREATION_DATE, MD5) VALUES ("' . $category_name . '", ' . time() . ', "' . $category_id . '")';
	mysqli_real_query($mysqli,$query);

	$db = new SQLite3($db_filename);

	$db->exec('CREATE TABLE topics (MD5 VARCHAR(32) DEFAULT "NA", NAME VARCHAR(256) DEFAULT "Topic", AUTHOR VARCHAR(64) DEFAULT "Anonymous", INIT_DATE INT(32) DEFAULT 0, LOCKED INT(1) DEFAULT 0, STICKY INT(1) DEFAULT 0, LAST_POST INT(32) DEFAULT 0, PRIMARY KEY (MD5))');
?>
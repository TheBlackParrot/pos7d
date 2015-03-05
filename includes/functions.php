<?php

require_once dirname(__FILE__) . "/config.php";
require_once dirname(__FILE__) . "/session.php";

function getCategoryName($id) {
	require dirname(__FILE__) . "/config.php";

	$category_id = htmlspecialchars($id);
	$query = 'SELECT NAME FROM categories WHERE MD5="' . $category_id . '"';
	$result = mysqli_query($mysqli,$query);
	$row = mysqli_fetch_array($result);

	return $row['NAME'];
}

function getThreadName($id,$thread) {
	$category_id = htmlspecialchars($id);
	$thread_id = htmlspecialchars($thread);

	$db_filename = 'includes/db/' . $category_id . '.db';

	$db = new SQLite3($db_filename);

	//$temp = $db->query('SELECT NAME FROM topics WHERE MD5="' . $thread_id . '"');
	//$arr = $temp->fetchArray(SQLITE3_BOTH)

	return $db->querySingle('SELECT NAME FROM topics WHERE MD5="' . $thread_id . '" LIMIT 1');
}

function getThreadContents($id,$thread,$start,$end) {
	$category_id = htmlspecialchars($id);
	$thread_id = htmlspecialchars($thread);
	if(!isset($start)) {
		$start = 1;
	}
	if(!isset($end)) {
		$end = 30;
	}

	$db_filename = 'includes/db/' . $category_id . '.db';

	$db = new SQLite3($db_filename);

	$temp = $db->query('SELECT * FROM "' . $thread_id . '" LIMIT ' . $start . ',' . $end);

	return $temp->fetchArray(SQLITE3_BOTH);
}

function parsePostData($data) {
	require_once dirname(__FILE__) . "/parsedown.php";
	$parsedown = new Parsedown();

	$data = htmlspecialchars_decode($data);

	$search = array("<","\"","/");
	$replace = array("&lt;","&quot;","&#x2F;");

	return $parsedown->text(str_replace($search, $replace, $data));
}

function isUserActive($time, $enable_text) {
	$diff = time() - $time;
	$text = "";
	if($diff < 600) {
		if($enable_text) {
			$text = " Online";
		}
		return '<div class="circle" style="background-color: #4CAF50"></div>' . $text;
	} else {
		if($enable_text) {
			$text = " Offline";
		}
		return '<div class="circle" style="background-color: #aaa"></div>' . $text;
	}
}

function postMessage($category,$thread,$data) {
	require dirname(__FILE__) . "/config.php";

	if(!isset($_SESSION['username'])) {
		header("Location: " . $setting['root_domain']);
		exit;
	} else {
		if(!isset($category)) {
			die("No category ID");
		}
		if(!isset($thread)) {
			die("No thread ID");
		}
		if(!isset($data)) {
			die("No message");
		}
		$reply_message = htmlspecialchars($data);
		$category_id = htmlspecialchars($category);
		$thread_id = htmlspecialchars($thread);
		$db_filename = 'db/' . $category_id . '.db';

		$db = new SQLite3($db_filename);
		$is_locked = $db->querySingle('SELECT LOCKED FROM topics WHERE MD5="' . $thread_id . '"');
		if($is_locked) {
			die("Thread is locked, you cannot reply.");
		}

		$db->exec('INSERT INTO "' . $thread_id . '" (CONTENT,AUTHOR,POST_DATE) VALUES (\'' . SQLite3::escapeString(rtrim($reply_message)) . "',\"" . SQLite3::escapeString($_SESSION['username']) . '",' . time() . ')');
		$db->exec('UPDATE topics SET LAST_POST=' . time() . ' WHERE MD5="' . $thread_id . '"');
		$query = 'UPDATE users SET POST_COUNT=POST_COUNT+1 WHERE MD5="' . $_SESSION['md5'] . '"';
		mysqli_real_query($mysqli,$query);
		//echo 'INSERT INTO "' . $thread_id . '" (POST_ID,CONTENT,AUTHOR,POST_DATE) VALUES (' . $post_id . ",'" . $reply_message . "',\"" . $_SESSION['username'] . '",' . time() . ')';
		//die();

		header("Location: " . $_SERVER['HTTP_REFERER']);
		exit;
	}
}

function editMessage($category,$thread,$post_num,$data) {
	require dirname(__FILE__) . "/config.php";

	if(!isset($_SESSION['username'])) {
		header("Location: " . $setting['root_domain']);
		exit;
	} else {
		if(!isset($category)) {
			die("No category ID");
		}
		if(!isset($thread)) {
			die("No thread ID");
		}
		if(!isset($data)) {
			die("No message");
		}
		if(!isset($post_num)) {
			die("No post ID");
		}
		$reply_message = htmlspecialchars($data);
		$category_id = htmlspecialchars($category);
		$thread_id = htmlspecialchars($thread);
		$post_id = htmlspecialchars($post_num);
		$db_filename = 'db/' . $category_id . '.db';
		if(!is_file($db_filename)) {
			die("Category doesn't exist");
		}

		$db = new SQLite3($db_filename);

		$is_locked = $db->querySingle('SELECT LOCKED FROM topics WHERE MD5="' . $thread_id . '"');
		if($is_locked) {
			die("Thread is locked, you cannot edit your posts.");
		}

		$db->exec('UPDATE "' . $thread_id . '" SET CONTENT=\'' . SQLite3::escapeString(rtrim($reply_message)) . '\', EDIT_DATE=' . time() . ' WHERE ID=' . $post_id);

		header("Location: " . $_SERVER['HTTP_REFERER']);
		exit;
	}
}

function toggleLockedTopic($category,$thread) {
	require dirname(__FILE__) . "/config.php";

	if(!isset($_SESSION['username'])) {
		header("Location: " . $setting['root_domain']);
		exit;
	} else {
		if(!isset($category)) {
			die("No category ID");
		}
		if(!isset($thread)) {
			die("No thread ID");
		}
		$category_id = htmlspecialchars($category);
		$thread_id = htmlspecialchars($thread);

		$db_filename = dirname(__FILE__) . '/' . 'db/' . $category_id . '.db';
		if(!is_file($db_filename)) {
			die("Category doesn't exist");
		}

		$db = new SQLite3($db_filename);

		$is_locked = $db->querySingle('SELECT LOCKED FROM topics WHERE MD5="' . $thread_id . '"');
		if($is_locked == 1) {
			$new_locked = 0;
		} else {
			$new_locked = 1;
		}
		$db->exec('UPDATE topics SET LOCKED=' . $new_locked . ' WHERE MD5="' . $thread_id . '"');

		header("Location: " . $_SERVER['HTTP_REFERER']);
		exit;
	}
}

function getUserInfo($md5) {
	require dirname(__FILE__) . "/config.php";

	$user_id = htmlspecialchars($md5);
	$query = 'SELECT MD5,POST_COUNT,RANK_LEVEL,LAST_ACTIVE FROM users WHERE MD5="' . $user_id . '" LIMIT 1';
	$result = mysqli_query($mysqli,$query);
	$row = mysqli_fetch_array($result);

	return $row;
}

function getUserRank($rank) {
	switch ($rank) {
		case '1': // category moderator
			return '<div class="rank_bubble" style="background-color: #2196F3;">LOCAL MOD</div>';
			break;
		case '2': // global moderator
			return '<div class="rank_bubble" style="background-color: #4CAF50;">MOD</div>';
			break;
		case '3': // administrator
			return '<div class="rank_bubble" style="background-color: #F44336;">ADMIN</div>';
			break;
		case '4': // host
			return '<div class="rank_bubble" style="background-color: #9C27B0;">HOST</div>';
			break;
		
		default: // normal user
			break;
	}
}

function getPagination($page,$pages,$str) {
	if($pages > 7) {
		$min = $page - 3;
		if($min < 1) {
			$max_offset = ($page - 3)*-1;
			$min = 1;
		}
		$max = $page + 3;
		if($max > $pages) {
			$min_offset = $max - $pages;
			$max = $pages;
		}
		$min -= $min_offset;
		$max += $max_offset;
	} else {
		$min = 1;
		$max = $pages;
	}
	$return_str = "";
	for($i=$min;$i<=$max;$i++) {
		if($i == $page) {
			$return_str .= '<a href="' . $str . $i . '"><div class="page pg_selected">' . $i . '</div></a>';
		} else {
			$return_str .= '<a href="' . $str . $i . '"><div class="page">' . $i . '</div></a>';
		}
	}

	return $return_str;
}
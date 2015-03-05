<?php
	require_once dirname(dirname(__FILE__)) . "/config.php";
	require_once dirname(dirname(__FILE__)) . "/functions.php";
	require_once dirname(dirname(__FILE__)) . "/session.php";

	if(!isset($_SESSION['username'])) {
		header("Location: " . $setting['root_domain']);
		exit;
	}

	$name = $_FILES['userfile']['name'];
	$tmpfile = $_FILES['userfile']['tmp_name'];

	$finfo = finfo_open(FILEINFO_MIME_TYPE);
	foreach (glob($tmpfile) as $filename) {
		$type = finfo_file($finfo, $filename);
	}
	finfo_close($finfo);

	$allowed_types = array("image/png", "image/jpeg", "image/gif", "image/bmp", "image/tiff", "image/svg+xml", "image/svg");
	if(in_array($type, $allowed_types)) {
		$fname = dirname(dirname(dirname(__FILE__))) . "/images/av/" . md5(htmlspecialchars($_SESSION['username'])) . ".jpg";
		$image = new Imagick();
		$image->readImage($tmpfile);
		$image->setFormat("jpg");
		$image->setImageCompression(Imagick::COMPRESSION_JPEG);
		$image->setImageCompressionQuality(97);
		$image->thumbnailImage(100,100);
		$image->writeImage($fname);
		$image->clear();
	} else {
		die("not allowed");
	}

	header("Location: " . $_SERVER['HTTP_REFERER']);
	die();
?>
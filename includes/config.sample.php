<?php
// -- SQL --
// hostname to connect to
$sql['host'] = "localhost";
$sql['port'] = 3306;
// SQL credentials
$sql['user'] = "SQL_user";
$sql['pass'] = "hackme";
// database
$sql['db'] = "pos7d_db";
// defines the SQL connection
$mysqli = new mysqli($sql['host'], $sql['user'], $sql['pass'], $sql['db'], $sql['port']);

// -- pos7d --
// self-explanatory, use slash at the end
//     "http://sub.example.com/"
$setting['root_domain'] = "http://forum.example.com/";
?>
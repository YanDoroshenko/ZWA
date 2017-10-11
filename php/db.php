<?php

define('DBHOST', 'localhost');
define('DBUSER', 'dorosyan');
define('DBPASS', 'webove aplikace');
define('DBNAME', 'dorosyan');

$db = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);

if (!$db)
    die("Database connection failed : " . $db->error);
else
    $db->set_charset("utf8");
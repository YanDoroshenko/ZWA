<?php

// DB config
define('DBHOST', 'localhost');
define('DBUSER', 'dorosyan');
define('DBPASS', 'webove aplikace');
define('DBNAME', 'dorosyan');

// DB connection object
$db = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);

if (!$db)
    die("Database connection failed : " . $db->error);

<?php

define('DBHOST', 'localhost');
define('DBUSER', 'root');
define('DBPASS', 'L0v3_7h3,5m311.0f_n@p@1m,in.7h3_m0rning');
define('DBNAME', 'mysql');

$db = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);

if (!$db) {
    die("Database connection failed : " . $db->error);
}
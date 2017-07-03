<?php
ob_start();
include("header.php");
session_start();
require_once 'db.php';

// if session is not set this will redirect to login page
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}
// select loggedin users detail
$query = $db->prepare("SELECT id, login, name FROM t_user");
$query->execute();
$users = $query->get_result();

?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title>Users</title>
    </head>
    <body>

    <?php
    while ($user = $users->fetch_assoc())
        echo $user['id'] . " " . $user['login'] . " " . $user['name'] . "<br/>";
    ?>

    </body>
    </html>
<?php ob_end_flush(); ?>
<?php
ob_start();
session_start();
require_once 'db.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$query = $db->prepare("SELECT id, login, name FROM t_user");
$query->execute();
$users = $query->get_result();

?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>TITS - Users</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <link rel="icon" type="image/x-icon" href="../favicon.ico"/>
    </head>
    <body>

    <?php
    include("header.php");

    while ($user = $users->fetch_assoc())
        echo $user['id'] . " " . $user['login'] . " " . $user['name'] . "<br/>";
    ?>

    </body>
    </html>
<?php ob_end_flush(); ?>
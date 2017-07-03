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
$query = $db->prepare("SELECT id, login, name FROM t_user WHERE id = ?");
$query->bind_param("i", $_SESSION['user']);
$query->execute();
$userRow = $query->get_result()->fetch_assoc();
?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title>Welcome - <?php echo $userRow['name']; ?></title>
    </head>
    <body>

    <h1>Logged in</h1>

    </body>
    </html>
<?php ob_end_flush(); ?>
<?php
ob_start();
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
        <title>TITS - Home</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <link rel="icon" type="image/x-icon" href="../favicon.ico"/>
    </head>
    <body>
    <?php include("header.php"); ?>
    <h1>Temporal Issue Tracking System</h1>
    <h2>Welcome, <?php echo $userRow['name'] ?></h2>

    </body>
    </html>
<?php ob_end_flush(); ?>
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
$res = mysqli_query($db, "SELECT * FROM t_user WHERE id=" . $_SESSION['user']);
$userRow = mysqli_fetch_array($res);
?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title>Welcome - <?php echo $userRow['name']; ?></title>
    </head>
    <body>

    <a href="logout.php?logout">Logout</a>

    <h1>Logged in</h1>

    </body>
    </html>
<?php ob_end_flush(); ?>
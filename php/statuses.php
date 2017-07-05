<?php
ob_start();
include("header.php");
session_start();
require_once 'db.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$query = $db->prepare("SELECT id, title, description, icon_path, final, system FROM t_status");
if (!$query || !$query->execute()) {
    echo $query->error;
    echo $db->error;
}
else {
    $statuses = $query->get_result();
}

?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title>Statuses</title>
    </head>
    <body>

    <?php
    if (isset($statuses))
        while ($status = $statuses->fetch_assoc()) {
            if (isset($status['icon_path']))
                echo "<img src=\"" . $status['icon_path'] . "\" width=25pt/>";
            echo $status['title'] . " ";
            echo $status['description'] . " ";
            echo $status['final'] . " ";
            if (!$status['system'])
                echo "<a href=delete_status.php?id=" . $status['id'] . ">X</a>";
            echo "<br/>";
        }
    ?>

    <a href="new_status.php">New status</a>
    </body>
    </html>
<?php ob_end_flush(); ?>
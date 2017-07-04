<?php
ob_start();
include("header.php");
session_start();
require_once 'db.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$query = $db->prepare("SELECT t.id 'id', name, priority, s.title 'status' FROM t_task t LEFT JOIN t_status s ON t.status = s.id");
$query->execute();
$tasks = $query->get_result();

?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title>Tasks</title>
    </head>
    <body>

    <?php
    while ($task = $tasks->fetch_assoc()) {
        echo $task['id'] . " ";
        echo $task['name'] . " ";
        echo $task['priority'] . " ";
        echo $task['status'];
        echo "<br/>";
    }
    ?>

    <a href="new_task.php">New task</a>
    </body>
    </html>
<?php ob_end_flush(); ?>
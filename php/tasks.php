<?php
ob_start();
include("header.php");
session_start();
require_once 'db.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$query = $db->prepare("SELECT t.id 'id', t.name 'name', priority, s.title 'status', r.login 'reporter', a.login 'assignee' FROM t_task t JOIN t_status s ON t.status = s.id JOIN t_user r ON t.reporter = r.id LEFT JOIN t_user a ON t.assignee = a.id");
if (!$query || !$query->execute()) {
    echo $query->error;
    echo $db->error;
}
else {
    $tasks = $query->get_result();
}

?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title>Tasks</title>
    </head>
    <body>

    <?php
    if (isset($tasks))
        while ($task = $tasks->fetch_assoc()) {
            echo $task['id'] . " ";
            echo $task['name'] . " ";
            echo $task['priority'] . " ";
            echo $task['status'] . " ";
            echo $task['reporter'] . " ";
            echo $task['assignee'] . " ";
            echo "<br/>";
        }
    ?>

    <a href="new_task.php">New task</a>
    </body>
    </html>
<?php ob_end_flush(); ?>
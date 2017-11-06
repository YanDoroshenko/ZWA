<?php
ob_start();
session_start();
require_once 'db.php';

// Redirect unauthorized user to login
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}
// select authorized user's detail
$query = $db->prepare("SELECT id, login, name FROM t_user WHERE id = ?");
$query->bind_param("i", $_SESSION['user']);
$query->execute();
$userRow = $query->get_result()->fetch_assoc();

// select tasks the user is related to
$query = $db->prepare("SELECT t.id 'id', t.name 'name', t.priority 'priority', t.description 'description', t.deadline 'deadline', s.title 'status', s.final 'final', r.login 'reporter_l', r.name 'reporter_n', a.login 'assignee_l', a.name 'assignee_n', s.icon_path FROM t_task t JOIN t_status s ON t.status = s.id JOIN t_user r ON t.reporter = r.id LEFT JOIN t_user a ON t.assignee = a.id WHERE NOT final = 1 AND (r.id = ? OR a.id = ?) ORDER BY CASE WHEN deadline IS  NULL THEN ~0 / 11 * priority ELSE (11 - priority) * DATEDIFF(deadline, CURDATE()) END LIMIT 5");
date_default_timezone_set('Europe/Prague');
$query->bind_param("ss", $userRow["id"], $userRow["id"]);
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
        <title>TITS - Home</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <link rel="icon" type="image/x-icon" href="../favicon.ico"/>
        <link rel="stylesheet" type="text/css" href="../css/header.css">
        <link rel="stylesheet" type="text/css" href="../css/style.css">
        <link rel="stylesheet" type="text/css" href="../css/home.css">
        <link rel="stylesheet" type="text/css" href="../css/tasks.css">
    </head>
    <body>
    <?php include("header.php"); ?>
    <div id="content">
        <h1 id="title"><img id="logo-home" src="../img/favicon.png"/><span>Temporal Issue Tracking System</span></h1>
        <h2 id="welcome">Welcome, <?php echo '<span id="name">' . $userRow['name'] . '</span>' ?></h2>
        <h3 id="notice">Tasks below require your attention the most</h3>
<?php
if (isset($tasks))
    while ($task = $tasks->fetch_assoc()) {
        $task_str = "<a href=task_detail.php?id=" . $task['id'] . ">";
        $task_str .= '<article class="task">';
        $task_str .= "<div class=\"details\">";
        $task_str .= "<img class=\"status\" src=\"" . $task['icon_path']. "\"/>";
        $task_str .= "<h4 class=\"priority\">" . $task['priority'] . "</h4>";
        $task_str .= "<div class=\"main-details\">";
        $task_str .= "<h3 class=\"name\">" . $task['name'] . ":</h3>";
        $task_str .= "<span class=\"status\">" . $task['status'] . "</span>";
        if (!empty($task['deadline'])) {
            $task_str .= "<span class=\"deadline ";
            if (strtotime($task['deadline']) < time())
                $task_str .= "past";
            else 
                $task_str .= "future";
            $task_str .= "\">Deadline: " . date("d.m.Y", strtotime($task['deadline'])) . "</span>"; 
        }
        $task_str .= "<br/>";
        $task_str .= "<span class=\"user reporter\">Reporter: <h4>" . $task['reporter_n'] . "</h4> (".  $task['reporter_l']. ")</span>";
        $task_str .= "<br/>";
        if (!empty($task['assignee_l']))
            $task_str .= "<span class=\"user\"><span>Assignee: </span><h4>" . $task['assignee_n'] . "</h4> (".  $task['assignee_l']. ")</span>";
        $task_str .= "</div>";
        $task_str .= "</div>";
        if (!empty($task['description']))
            $task_str .= "<div class=\"description\"><p>" . $task['description'] . "</p></div>";
        $task_str .= "</article>";
        $task_str .= "</a>";
        echo $task_str;
    }
?>
    </div>
    </body>
    </html>
<?php ob_end_flush(); ?>

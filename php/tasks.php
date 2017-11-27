<?php
ob_start();
session_start();
require_once 'db.php';

// Redirect unauthorized user to login
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

// For filter to know where to go after filtering
$self = $_SERVER['PHP_SELF'];

// Initialize filter and pagination
if (isset($_POST['btn-filter']))
    $filter = '%' . $_POST['filter'] . '%';
else
    $filter = '%';

$page_size = 10;

// Count entries
$count_query = $db->prepare("SELECT count(*) FROM t_task WHERE name LIKE ? OR description LIKE ?");
$count_query->bind_param("ss", $filter, $filter);
if (!$count_query || !$count_query->execute()) {
    echo $query->error;
    echo $db->error;
}
else
    $count = $count_query->get_result()->fetch_array()[0];

if( isset($_GET{'page'} ) ) {
    $page = $_GET{'page'};
    if ($page <= 0)
        $page = 1;
    $offset = $page_size * ($page - 1);
}
else {
    $page = 1;
    $offset = 0;
}

// Entry counters for the page
$from = min($count, $offset + 1);
$to = min($count, $offset + $page_size);

// Find all the entries according to the filter and pagination
$query = $db->prepare("SELECT t.id 'id', t.name 'name', t.priority 'priority', t.description 'description', t.deadline 'deadline', s.title 'status', r.login 'reporter_l', r.name 'reporter_n', a.login 'assignee_l', a.name 'assignee_n', s.icon_path FROM t_task t JOIN t_status s ON t.status = s.id JOIN t_user r ON t.reporter = r.id LEFT JOIN t_user a ON t.assignee = a.id WHERE t.name LIKE ? OR t.description LIKE ? LIMIT $offset, $page_size");
$query->bind_param("ss", $filter, $filter);
if (!$query || !$query->execute()) {
    echo $query->error;
    echo $db->error;
}
else {
    $tasks = $query->get_result();
}

?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <title>TITS - Tasks</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <link rel="icon" type="image/x-icon" href="../favicon.ico"/>
        <link rel="stylesheet" type="text/css" href="../css/header.css"/>
        <link rel="stylesheet" type="text/css" href="../css/style.css"/>
        <link rel="stylesheet" type="text/css" href="../css/tasks.css"/>
        <link rel="stylesheet" type="text/css" href="../css/footer.css"/>
    </head>
    <body>

<?php
$context_action = '<a href="new_task.php">New task</a>';
include("header.php");
?>
<div id="content">
<?php
// Show all the tasks
if (isset($tasks))
    while ($task = $tasks->fetch_assoc()) {
        $link_html = "<a href='task_detail.php?id=" . $task['id'] . "'>";
        $task_str = $link_html;
        $task_str .= '<article class="task">';
        $task_str .= "<div class=\"details\">";
        $task_str .= "<img class=\"status\" src=\"" . str_replace(" ", "_", htmlspecialchars($task['icon_path'])) . "\" alt='status_icon'/>";
        $task_str .= "<h4 class=\"priority\">" . $task['priority'] . "</h4>";
        $task_str .= "<div class=\"main-details\">";
        $task_str .= "<h3 class=\"name\">" . htmlspecialchars($task['name']) . ":</h3>";
        $task_str .= "<span class=\"status\">" . htmlspecialchars($task['status']) . "</span>";
        if (!empty($task['deadline'])) {
            $task_str .= "<span class=\"deadline ";
            if (strtotime($task['deadline']) < time())
                $task_str .= "past";
            else
                $task_str .= "future";
            $task_str .= "\">Deadline: " . date("d.m.Y", strtotime($task['deadline'])) . "</span>";
        }
        $task_str .= "<br/>";
        $task_str .= "<span class=\"user reporter\">Reporter: <span class='user name'>" . htmlspecialchars($task['reporter_n']) . "</span> (".  htmlspecialchars($task['reporter_l']) . ")</span>";
        $task_str .= "<br/>";
        if (!empty($task['assignee_l']))
            $task_str .= "<span class=\"user\"><span>Assignee: </span><span class='user name'>" . htmlspecialchars($task['assignee_n']) . "</span> (".  htmlspecialchars($task['assignee_l']) . ")</span>";
        $task_str .= "</div>";
        $task_str .= "</div>";
        if (!empty($task['description']))
            $task_str .= "<div class=\"description\"><p>" . htmlspecialchars($task['description']) . "</p></div>";
        $task_str .= "</article>";
        $task_str .= "</a>";
        echo $task_str;
    }
?>
</div>
<footer>
<?php
//Pagination
if ($from > 1)
    echo "<a id=\"prev\" class=\"pagination\" href=\"tasks.php?page=" . intval($page - 1) . "\">&#x25C4; Previous page</a>";
if ($to < $count)
    echo "<a id=\"next\" class=\"pagination\" href=\"tasks.php?page=" . intval($page + 1) . "\">Next page &#x25BA;</a>";
echo "<h4 id=\"count\" class=\"pagination\">$from-$to/$count</h4>";
?>
</footer>
    </body>
    </html>
<?php ob_end_flush(); ?>

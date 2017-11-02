<?php
ob_start();
session_start();
require_once 'db.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

if (isset($_POST['btn-filter']))
    $filter = '%' . $_POST['filter'] . '%';
else
    $filter = '%';

$page_size = 5;

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

$from = min($count, $offset + 1);
$to = min($count, $offset + $page_size);

$query = $db->prepare("SELECT t.id 'id', t.name 'name', priority, s.title 'status', r.login 'reporter', a.login 'assignee' FROM t_task t JOIN t_status s ON t.status = s.id JOIN t_user r ON t.reporter = r.id LEFT JOIN t_user a ON t.assignee = a.id WHERE t.name LIKE ? OR t.description LIKE ? LIMIT $offset, $page_size");
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
    <html>
    <head>
        <title>TITS - Tasks</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <link rel="icon" type="image/x-icon" href="../favicon.ico"/>
    </head>
    <body>

<?php
include("header.php");
?>
    <a href="new_task.php">New task</a>
    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<label for="filter">Filter</label>
        <input type="text" name="filter" placeholder="Filter"
        value="<?php
if (isset($filter))
    echo str_replace("%", "", $filter); ?>"
        />
        <button type="submit" name="btn-filter">&#x1F50D;</button>
    </form>
<br/>
<?php
    if (isset($tasks))
        while ($task = $tasks->fetch_assoc()) {
            echo "<a href=task_detail.php?id=" . $task['id'] . ">" . $task['id'] . "</a> ";
            echo $task['name'] . " ";
            echo $task['priority'] . " ";
            echo $task['status'] . " ";
            echo $task['reporter'] . " ";
            echo $task['assignee'] . " ";
            echo "<br/>";
        }
if ($from > 1)
    echo "<a href=\"tasks.php?page=" . intval($page - 1) . "\">Previous page<a/>";
if ($to < $count)
    echo "<a href=\"tasks.php?page=" . intval($page + 1) . "\">Next page<a/>";
echo "$from-$to/$count";
?>

    </body>
    </html>
<?php ob_end_flush(); ?>

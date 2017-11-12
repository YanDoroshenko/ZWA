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

$count_query = $db->prepare("SELECT count(*) FROM t_status WHERE title LIKE ? OR description LIKE ?");
$count_query->bind_param("ss", $filter, $filter);
if (!$count_query || !$count_query->execute()) {
    echo $query->error;
    echo $db->error;
}
else
    $count = $count_query->get_result()->fetch_array()[0];

// Show the correct page
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

// Page counters
$from = min($count, $offset + 1);
$to = min($count, $offset + $page_size);

// Find all the entries according to the filter and pagination
$query = $db->prepare("SELECT id, title, description, icon_path, final, system FROM t_status WHERE title LIKE ? OR description LIKE ? LIMIT $offset, $page_size");
$query->bind_param("ss", $filter, $filter);
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
        <title>TITS - Statuses</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <link rel="icon" type="image/x-icon" href="../favicon.ico"/>
        <link rel="stylesheet" type="text/css" href="../css/header.css"/>
        <link rel="stylesheet" type="text/css" href="../css/style.css"/>
        <link rel="stylesheet" type="text/css" href="../css/statuses.css"/>
        <link rel="stylesheet" type="text/css" href="../css/footer.css"/>
    </head>
    <body>

<?php
$context_action = '<a href="new_status.php">New status</a>';
include("header.php");
echo '<div id="content">';
// Show all the statuses got from DB
if (isset($statuses))
    while ($status = $statuses->fetch_assoc()) {
        echo "<article>";
        if (isset($status['icon_path']))
            echo "<img src=\"" . $status['icon_path'] . "\" alt=\"" . $status['title'] . "\" width=25pt/>";
        echo "<div class=\"details\"/>";
        echo "<h3>" . $status['title'] . "</h3>";
        if ($status['final'])
            echo "<h4 class=\"final\">Final</h4>";
        echo "<br/>";
        echo "<h4>";
        if ($status['description'])
            echo $status['description'];
        else 
            echo "No description";
        echo "</h4>";
        echo "</div>";
        if (!$status['system'])
            echo "<a class=\"delete\" href=delete_status.php?id=" . $status['id'] . ">Delete</a>";
        echo "</article>";
    }
?>
</div>
<footer>
<?php
//Pagination
if ($from > 1)
    echo "<a id=\"prev\" class=\"pagination\" href=\"statuses.php?page=" . intval($page - 1) . "\">&#x25C4; Previous page<a/>";
if ($to < $count)
    echo "<a id=\"next\" class=\"pagination\" href=\"statuses.php?page=" . intval($page + 1) . "\">Next page &#x25BA;<a/>";
echo "<h4 id=\"count\" class=\"pagination\">$from-$to/$count</h4>";
?>
</footer>
    </body>
    </html>
<?php ob_end_flush(); ?>

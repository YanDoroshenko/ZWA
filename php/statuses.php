<?php
ob_start();
session_start();
require_once 'db.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$page_size = 5;

$count_query = $db->prepare("SELECT count(*) FROM t_status");
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

$from = $offset + 1;
$to = min($count, $offset + $page_size);

$query = $db->prepare("SELECT id, title, description, icon_path, final, system FROM t_status LIMIT $offset, $page_size");
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
    </head>
    <body>

<?php
include("header.php");
?>
    <a href="new_status.php">New status</a>
<br/>
<?php
if (isset($statuses))
    while ($status = $statuses->fetch_assoc()) {
	if (isset($status['icon_path']))
	    echo "<img src=\"" . $status['icon_path'] . "\" alt=\"" . $status['title'] . "\" width=25pt/>";
	echo $status['title'] . " ";
	echo $status['description'] . " ";
	echo $status['final'] . " ";
	if (!$status['system'])
	    echo "<a href=delete_status.php?id=" . $status['id'] . ">X</a>";
	echo "<br/>";
    }
if ($from > 1)
    echo "<a href=\"statuses.php?page=" . intval($page - 1) . "\">Previous page<a/>";
if ($to < $count)
    echo "<a href=\"statuses.php?page=" . intval($page + 1) . "\">Next page<a/>";
echo "$from-$to/$count";
?>

    </body>
    </html>
<?php ob_end_flush(); ?>

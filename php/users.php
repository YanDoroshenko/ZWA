<?php
ob_start();
session_start();
require_once 'db.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$page_size = 5;

$count_query = $db->prepare("SELECT count(*) FROM t_user");
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

$query = $db->prepare("SELECT id, login, name FROM t_user LIMIT $offset, $page_size");
$query->execute();
$users = $query->get_result();

?>
    <!DOCTYPE html>
    <html>
    <head>
	<title>TITS - Users</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<link rel="icon" type="image/x-icon" href="../favicon.ico"/>
    </head>
    <body>

<?php
include("header.php");

while ($user = $users->fetch_assoc())
    echo $user['id'] . " " . $user['login'] . " " . $user['name'] . "<br/>";
if ($from > 1)
    echo "<a href=\"users.php?page=" . intval($page - 1) . "\">Previous page<a/>";
if ($to < $count)
    echo "<a href=\"users.php?page=" . intval($page + 1) . "\">Next page<a/>";
echo "$from-$to/$count";
?>

    </body>
    </html>
<?php ob_end_flush(); ?>

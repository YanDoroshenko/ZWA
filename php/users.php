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

$page_size = 5;

// Count entries
$count_query = $db->prepare("SELECT count(*) FROM t_user WHERE login LIKE ? OR name LIKE ?");
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

// Select all the entries according to the filter and pagination
$query = $db->prepare("SELECT id, login, name FROM t_user WHERE login LIKE ? OR name LIKE ? LIMIT $offset, $page_size");
$query->bind_param("ss", $filter, $filter);
$query->execute();
$users = $query->get_result();
?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>TITS - Users</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <link rel="icon" type="image/x-icon" href="../favicon.ico"/>
        <link rel="stylesheet" type="text/css" href="../css/header.css"/>
        <link rel="stylesheet" type="text/css" href="../css/style.css"/>
        <link rel="stylesheet" type="text/css" href="../css/users.css"/>
        <link rel="stylesheet" type="text/css" href="../css/footer.css"/>
    </head>
    <body>
<?php
include("header.php");
?>
<div id="content">
<?php
    // Show all the users
    while ($user = $users->fetch_assoc()) {
        echo "<article>
            <img class='avatar' src='../img/avatar_blue.png'>
<div class='user-details'>
<h3>" . $user['name'] . "</h3>
<h4>" . $user['login'] . "</h4>
</div>
</article>
";
    }
?>
</div>
<footer>
<?php
//Pagination
if ($from > 1)
    echo "<a id=\"prev\" class=\"pagination\" href=\"users.php?page=" . intval($page - 1) . "\">&#x25C4; Previous page<a/>";
if ($to < $count)
    echo "<a id=\"next\" class=\"pagination\" href=\"users.php?page=" . intval($page + 1) . "\">Next page &#x25BA;<a/>";
echo "<h4 id=\"count\" class=\"pagination\">$from-$to/$count</h4>";
?>
</footer>
    </body>
    </html>
<?php ob_end_flush(); ?>

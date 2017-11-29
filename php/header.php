<!-- Common header -->
<?php
ob_start();
require_once 'db.php';

if (isset($_POST['change-style'])) {
    if (isset($_COOKIE["style"]) && $_COOKIE["style"] == "alt")
        setcookie("style", "main");
    else
        setcookie("style", "alt");
    header("Location: " . $_SERVER['PHP_SELF']);
}
$query = $db->prepare("SELECT id, login, name FROM t_user WHERE id = ?");
$query->bind_param("i", $_SESSION['user']);
$query->execute();
$user = $query->get_result()->fetch_assoc();
?>
<header>
<ul>
<li class="left menu edge">
     <a href="home.php"><img src="../img/favicon.png" alt="Logo"> Home</a>
</li>
    <li class="left menu">
        <a id="tasks" href="tasks.php">Tasks</a>
    </li>
    <li class="left menu">
        <a href="statuses.php">Statuses</a>
    </li>
    <li class="left menu">
        <a id="users" href="users.php">Users</a>
    </li>
    <li class="right menu edge">
        <a id="logout" href="logout.php?logout">Log Out</a>
    </li>
<li id="user-name" class="right">
<h3 title="Login: <?php echo $user['login']; ?>">
    <img id="avatar-header" src="../img/avatar_white.png" alt="user"/> <?php echo htmlspecialchars($user['name']) ?>
    </h3>
</li>
<li class="right menu">
    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<button type="submit" name="change-style">
<h3>
Style
</h3>
</button>
</form>
</li>
<?php
if (isset($filter))
    echo '
<li id="search" class="right">
<form method="post" action="' . $self . '">
<label for="filter">Filter</label>
        <input id="filter" type="text" name="filter" placeholder="Filter"
        value="' . str_replace("%", "", $filter) . '"
        />
        <button type="submit" name="btn-filter">&#x1F50D;</button>
    </form>
</li>';
if (isset($context_action))
    echo '
<li id="context" class="menu left">' .
    $context_action .
'</li>';
?>
</ul>
</header>
<br/>
<?php ob_end_flush(); ?>

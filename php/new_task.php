<?php
ob_start();
session_start();
require_once 'db.php';

// Redirect unauthorized user to login
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$error = false;

// Processing form
if (isset($_POST['btn-save'])) {

    define("INITIAL_STATUS", 1);

    $priority = $_POST['priority'];
    $name = $_POST['name'];
    $reporter = (int)$_SESSION['user'];

    // Validation
    if (empty($name)) {
        $error = true;
        echo "Please enter task name.";
    }

    if (empty($priority)) {
        $error = true;
        echo "Please select priority.";
    }

    // If OK, save task to DB
    if (!$error) {
        $sql = "INSERT INTO t_task(status, reporter";
        foreach ($_POST as $k => $v)
            if (!empty($v))
                $sql .= ", " . $k;
        $sql .= ") VALUES (" . INITIAL_STATUS . ", " . $reporter;
        foreach ($_POST as $k => $v)
            if (!empty($v))
                $sql .= ", ?";
        $sql .= ")";
        $query = $db->prepare($sql);
        $types = "";
        $values = [];
        foreach ($_POST as $k => $v)
            if (!empty($v)) {
                $types .= substr(gettype($k), 0, 1);
                $values[] = $v;
            }
        $query->bind_param($types, ...$values);

        // If OK proceed to tasks list
        if ($query->execute())
            header("Location: tasks.php");
        else {
            echo "Something went wrong:<br/>";
            echo $db->error . "<br/>";
            echo $query->error . "<br/>";
        }
    }
}
?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>TITS - New task</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <link rel="icon" type="image/x-icon" href="../favicon.ico"/>
        <link rel="stylesheet" type="text/css" href="../css/header.css"/>
        <link rel="stylesheet" type="text/css" href="../css/style.css"/>
        <link rel="stylesheet" type="text/css" href="../css/new_task.css"/>
    </head>
    <body>

    <?php include("header.php"); ?>

<div id="content">
    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>"
id="new_task">
<label for="priority">Priority</label>
<br/>
        <input
type="number" name="priority"
id="priority"
required="required"
min="1" max="10" title="Priority" value="5">
    <label class="feedback" id="priority-feedback"></label>
<br/>
<label for="deadline">Deadline</label>
<br/>
<input type="date" name="deadline" id="deadline"/>
<br/>
<label for="assignee">Assignee</label>
<br/>
        <select name="assignee" title="Assignee">
<?php
// List users
echo "<option value=''>None</option>";
$query = $db->prepare("SELECT id, login, name FROM t_user");
if ($query->execute()) {
    $assignees = $query->get_result();
    while ($assignee = $assignees->fetch_assoc()) {
        $displayName = $assignee['name'] ? $assignee['name'] . " (" . $assignee['login'] . ")" : $assignee['login'];
        echo "<option value=\"" . $assignee['id'] . "\"\">" . $displayName . "</option>";
    }
}
else {
    echo "Something went wrong:<br/>";
    echo $db->error . "<br/>";
    echo $query->error . "<br/>";
}
?>
        </select>
<br/>
<label for="name">Name</label>
<br/>
        <input
                type="text"
                name="name"
                id="name"
                required="required"
                placeholder="Task name"
                title="Name"/>
<label id="name-feedback"></label>
<br/>
<label for="description">Description</label>
<br/>
        <textarea rows="3" cols="23"
name="description"
                placeholder="Task description">
</textarea>
<br/>
        <button type="submit" name="btn-save">Create</button>
    </form>
</div>
    </body>
    <script src="../js/new_task.js"></script>
    </html>
<?php ob_end_flush(); ?>

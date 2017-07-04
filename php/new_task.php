<?php
ob_start();
include("header.php");
session_start();
require_once 'db.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$error = false;

if (isset($_POST['btn-save'])) {

    define("INITIAL_STATUS", 1);

    $priority = $_POST['priority'];
    $name = $_POST['name'];
    $reporter = (int)$_SESSION['user'];

    if (empty($name)) {
        $error = true;
        echo "Please enter task name.";
    }

    if (empty($priority)) {
        $error = true;
        echo "Please select priority.";
    }

    if (!$error) {
        foreach ($_POST as $k => $v)
            if (!empty($v))
                echo $k . " => " . $v;
        $sql = "INSERT INTO t_task(status, reporter";
        foreach ($_POST as $k => $v)
            if (!empty($v))
                $sql .= ", " . $k;
        $sql .= ") VALUES (" . INITIAL_STATUS . ", " . $reporter;
        foreach ($_POST as $k => $v)
            if (!empty($v))
                $sql .= ", ?";
        $sql .= ")";
        echo $sql;
        $query = $db->prepare($sql);
        $types = "";
        $values = [];
        foreach ($_POST as $k => $v)
            if (!empty($v)) {
                $types .= substr(gettype($k), 0, 1);
                $values[] = $v;
            }
        $query->bind_param($types, ...$values);
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
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title>New task</title>
    </head>
    <body>

    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <input type="number" name="priority" min="1" max="10"
               value="<?php
               if (isset($task))
                   echo $task['priority'];
               else
                   echo 5;
               ?>"
               title="Priority">
        <select name="assignee" title="Assignee">
            <?php
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
        <input
                type="text"
                name="name"
                placeholder="Task name"
                value="<?php
                if (isset($task))
                    echo $task['name'];
                ?>"
                title="Name"/>
        <input
                type="text"
                name="description"
                placeholder="Task description"
                value="<?php
                if (isset($task))
                    echo $task['description'];
                ?>"
                title="Name"/>
        <button type="submit" name="btn-save">Save task</button>
    </form>
    </body>
    </html>
<?php ob_end_flush(); ?>
<?php
ob_start();
include("header.php");
session_start();
require_once 'db.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

if (isset($taskId)) {
    $query = $db->prepare("SELECT t.name 'name', t.priority 'priority', s.title 'status', r.login 'reporter', a.login 'assignee' FROM t_task t JOIN t_status s ON t.status = s.id JOIN t_user r ON t.reporter = r.id LEFT JOIN t_user a ON t.assignee = a.id WHERE t.id = ?");
    $query->bind_param("i", $taskId);
    $query->execute();
    $task = $query->get_result()->fetch_assoc();
}
else {
    $task = ['id' => "", 'name' => "", 'status' => ""];
}

$error = false;

if (isset($_POST['btn-save'])) {

    $name = $_POST['name'];

    $status = $_POST['status'];

    if (empty($name)) {
        $error = true;
        echo "Please enter task name.";
    }

    if (empty($status)) {
        $error = true;
        echo "Please select status.";
    }

    if (!$error) {
        if (isset($taskId)) {
            $query = $db->prepare("UPDATE t_task SET name = ?, status = ? WHERE id = ?");
            $query->bind_param("sii", $name, $status, $taskId);
        }
        else {
            $query = $db->prepare("INSERT INTO t_task(name, status, reporter) VALUES (?, ?, 1)");
            $query->bind_param("si", $name, $status);
        }
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
        <title>Users</title>
    </head>
    <body>

    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <input type="text" name="name" value="<?php echo $task['name'] ?>" title="Name"/>
        <select name="status" title="Status">
            <option value="1">New</option>
            <option value="2">In progress</option>
            <option value="3">Complete</option>
        </select>
        <button type="submit" name="btn-save">Save task</button>
    </form>
    </body>
    </html>
<?php ob_end_flush(); ?>
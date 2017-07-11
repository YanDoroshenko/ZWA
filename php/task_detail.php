<?php
ob_start();
include("header.php");
session_start();
require_once 'db.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$id = $_GET['id'];

$action_query = $db->prepare("SELECT t.id 'id', t.name 'name', priority, t.description 'description', s.id 'status_id', s.title 'status', s.icon_path 'icon', r.id 'reporter_id',  r.login 'reporter', a.id 'assignee_id', a.login 'assignee' FROM t_task t JOIN t_status s ON t.status = s.id JOIN t_user r ON t.reporter = r.id LEFT JOIN t_user a ON t.assignee = a.id WHERE t.id = ?");
$action_query->bind_param("i", $id);
if (!$action_query || !$action_query->execute()) {
    echo $action_query->error;
    echo $db->error;
}
else {
    $task = $action_query->get_result()->fetch_assoc();
}

if (isset($task) && isset($_POST['btn-save'])) {
    if (
        $_POST['priority'] != $task['priority'] ||
        $_POST['status'] != $task['status_id'] ||
        $_POST['assignee'] != $task['assignee_id']
    ) {
        $sql_task = "UPDATE t_task SET ";
        $task_types = "";
        $task_values = [];
        $sql_action = "INSERT INTO t_action (task, actor";
        if ($_POST['priority'] != $task['priority']) {
            $sql_task .= "priority = ?";
            $task_types .= "i";
            $task_values[] = $_POST['priority'];
            $sql_action .= ", source_priority, target_priority";
        }
        if ($_POST['status'] != $task['status_id']) {
            if (!empty($task_values))
                $sql_task .= ", ";
            $sql_task .= "status = ?";
            $task_types .= "i";
            $task_values[] = $_POST['status'];
            $sql_action .= ", source_status, target_status";
        }
        if ($_POST['assignee'] != $task['assignee_id']) {
            if (!empty($task_values))
                $sql_task .= ", ";
            if ($_POST['assignee'] === '')
                $sql_task .= "assignee = NULL";
            else {
                $sql_task .= "assignee = ?";
                $task_types .= "i";
                $task_values[] = $_POST['assignee'];
            }
            $sql_action .= ", assignee";
        }
        if ($_POST['comment']) {
            $sql_action .= ", description";
        }
        $task_types .= "i";
        $task_values[] = $id;
        $sql_task .= " WHERE id = ?";
        $sql_action .= ") VALUES (?, ?";
        $action_types = "ii";
        $action_values = [$task['id'], $_SESSION['user']];
        if ($_POST['priority'] != $task['priority']) {
            $sql_action .= ", ?, ?";
            $action_types .= "ii";
            $action_values[] = $task['priority'];
            $action_values[] = $_POST['priority'];
        }
        if ($_POST['status'] != $task['status_id']) {
            $sql_action .= ", ?, ?";
            $action_types .= "ii";
            $action_values[] = $task['status_id'];
            $action_values[] = $_POST['status'];
        }
        if ($_POST['assignee'] != $task['assignee_id']) {
            $sql_action .= ", ?";
            $action_types .= "i";
            $new_assignee = $_POST['assignee'];
            $action_values[] = $new_assignee === '' ? NULL : $new_assignee;
        }
        if ($_POST['comment']) {
            $sql_action .= ", ?";
            $action_types .= "s";
            $action_values[] = $_POST['comment'];
        }
        $sql_action .= ")";
        $task_query = $db->prepare($sql_task);
        $action_query = $db->prepare($sql_action);
        if (!$task_query || !$action_query) {
            echo $sql_task;
            echo "Malformed query:<br/>";
            echo $db->error . "<br/>";
            if (!$task_query)
                echo $action_query->error . "<br/>";
            if (!$action_query)
                echo $task_query->error . "<br/>";
        }
        else {
            $task_query->bind_param($task_types, ...$task_values);
            $action_query->bind_param($action_types, ...$action_values);
            if ($task_query->execute() && $action_query->execute())
                header("Location: task_detail.php?id=" . $id);
            else {
                echo "DB error:<br/>";
                echo $db->error . "<br/>";
                echo $task_query->error . "<br/>";
                echo $action_query->error . "<br/>";
            }
        }
    }
    elseif ($_POST['comment']) {
        $sql_action = "INSERT INTO t_action (task, actor, description) VALUES (?, ?, ?)";
        $action_query = $db->prepare($sql_action);

        if (!$action_query) {
            echo "Malformed query:<br/>";
            echo $db->error . "<br/>";
            echo $action_query->error . "<br/>";
        }
        else {
            $action_query->bind_param("iis", $task['id'], $_SESSION['user'], $_POST['comment']);
            if ($action_query->execute())
                header("Location: task_detail.php?id=" . $id);
            else {
                echo "DB error:<br/>";
                echo $db->error . "<br/>";
                echo $action_query->error . "<br/>";
            }
        }
    }
}

?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title>Task detail</title>
    </head>
    <body>

    <form method="post" action="<?php echo $_SERVER['PHP_SELF'] . "?id=" . $id; ?>">

        <?php
        if (isset($task)) {
            echo $task['id'] . " ";
            echo $task['name'] . " ";
            if ($_SESSION['user'] == $task['reporter_id']) {
                echo "<input type=\"number\" name=\"priority\" min=\"1\" max=\"10\" title=\"Priority\" value=\"" . $task['priority'] . "\">";
            }
            else
                echo $task['priority'] . " ";
            if ($_SESSION['user'] == $task['reporter_id']) {
                echo '<select name="status" title="Status">';
                $action_query = $db->prepare("SELECT id, title, icon_path FROM t_status");
                if ($action_query->execute()) {
                    $statuses = $action_query->get_result();
                    while ($status = $statuses->fetch_assoc()) {
                        echo "<option value=\"" . $status['id'] . ($status['id'] == $task['status_id'] ? "\" selected>" : "\">") . $status['title'] . "</option>";
                    }
                }
                else {
                    echo "Something went wrong:<br/>";
                    echo $db->error . "<br/>";
                    echo $action_query->error . "<br/>";
                }
                echo "</select >";
            }
            else
                echo $task['status'] . " ";
            echo '<img src="' . $task['icon'] . '" width=15pt/>';
            echo $task['reporter'];
            if ($_SESSION['user'] == $task['reporter_id']) {
                echo '<select name="assignee" title="Assignee">';
                echo "<option value=''>None</option>";
                $action_query = $db->prepare("SELECT id, login, name FROM t_user");
                if ($action_query->execute()) {
                    $statuses = $action_query->get_result();
                    while ($status = $statuses->fetch_assoc()) {
                        $displayName = $status['name'] ? $status['name'] . " (" . $status['login'] . ")" : $status['login'];
                        echo "<option value=\"" . $status['id'] . ($status['id'] == $task['assignee_id'] ? "\" selected>" : "\">") . $displayName . "</option>";
                    }
                }
                else {
                    echo "Something went wrong:<br/>";
                    echo $db->error . "<br/>";
                    echo $action_query->error . "<br/>";
                }
                echo "</select >";
            }
            else
                echo $task['assignee'] . " ";
            echo $task['description'] . " ";
            echo "<br/>";
        }
        ?>
        <input
                type="text"
                name="comment"
                placeholder="Your commentary"
                title="Comment"/>
        <br/>
        <button type="submit" name="btn-save">Save task</button>
    </form>
    </body>
    </html>
<?php ob_end_flush(); ?>
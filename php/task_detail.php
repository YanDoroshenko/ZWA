<?php
ob_start();
session_start();
require_once 'db.php';

// Redirect unauthorized user to login
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$id = $_GET['id'];

// Find taks in DB
$task_query = $db->prepare("SELECT t.id 'id', t.name 'name', t.priority 'priority', t.deadline 'deadline', t.description 'description', s.id 'status_id', s.title 'status', s.icon_path 'icon', s.final 'final', r.id 'reporter_id',  r.login 'reporter', r.name 'reporter_name', a.id 'assignee_id', a.login 'assignee' , a.name 'assignee_name' FROM t_task t JOIN t_status s ON t.status = s.id JOIN t_user r ON t.reporter = r.id LEFT JOIN t_user a ON t.assignee = a.id WHERE t.id = ?");
$task_query->bind_param("i", $id);
if (!$task_query || !$task_query->execute())
    echo '<label class="incorrect feedback">Error: ' . $db->error .'</label>';
else
    $task = $task_query->get_result()->fetch_assoc();

// Process the form submit
if (isset($task) && isset($_POST['btn-save'])) {
    if (
        $_POST['priority'] != $task['priority'] ||
        isset($_POST['status']) && $_POST['status'] != $task['status_id'] ||
        $_POST['assignee'] != $task['assignee_id'] ||
        strtotime($_POST['deadline']) != strtotime($task['deadline'])
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
        if (strtotime($_POST['deadline']) != strtotime($task['deadline'])) {
            if (!empty($task_values))
                $sql_task .= ", ";
            if (empty($_POST['deadline']))
                $sql_task .= "deadline = NULL, ";
            else {
                $sql_task .= "deadline = ?";
                $task_types .= "s";
                $task_values[] = date("Y-m-d", strtotime($_POST['deadline']));
            }
        }
        if (isset($_POST['status']) && $_POST['status'] != $task['status_id']) {
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
                $sql_task .= "assignee = NULL, ";
            else {
                $sql_task .= "assignee = ?";
                $task_types .= "i";
                $task_values[] = $_POST['assignee'];
            }
            $sql_action .= ", assignee";
        }
        if ($_POST['comment'] || strtotime($_POST['deadline']) != strtotime($task['deadline']) || $_POST['assignee'] === '' && $task['assignee_id'])
            $sql_action .= ", description";
        $task_types .= "i";
        $task_values[] = $id;
        $sql_task = rtrim($sql_task, " ");
        $sql_task = rtrim($sql_task, ",");
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
        if (isset($_POST['status']) && $_POST['status'] != $task['status_id']) {
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
            $comment = $_POST['comment'] . "<br/>";
            if (strtotime($_POST['deadline']) != strtotime($task['deadline']))
                if (empty($_POST['deadline']))
                    $comment .= "Deadline removed.<br/>";
                else if (empty($task['deadline']))
                    $comment .= "Deadline set to ". $_POST['deadline'] . ".<br/>";
                else
                    $comment .= "Deadline changed from " . date("Y-m-d", strtotime($task['deadline'])) . " to " . $_POST['deadline'] . ".<br/>";
            if ($task['assignee_id'] != $_POST['assignee'] && $_POST['assignee'] === '')
                $comment .= "Task unassigned.";
            $action_values[] = $comment;
        }
        else if (strtotime($_POST['deadline']) != strtotime($task['deadline']) || $task['assignee'] != $_POST['assignee'] && $_POST['assignee'] === '') {
            $sql_action .= ", ?";
            $action_types .= "s";
            $comment = "";
            if (strtotime($_POST['deadline']) != strtotime($task['deadline']))
                if (empty($_POST['deadline']))
                    $comment .= "Deadline removed.<br/>";
                else if (empty($task['deadline']))
                    $comment .= "Deadline set to ". $_POST['deadline'] . ".<br/>";
                else
                    $comment .= "Deadline changed from " . date("Y-m-d", strtotime($task['deadline'])) . " to " . $_POST['deadline'] . ".<br/>";
            if ($task['assignee_id'] != $_POST['assignee'] && $_POST['assignee'] === '')
                $comment .= "Task unassigned.";
            $action_values[] = $comment;
        }
        $sql_action .= ")";
        $sql_task = preg_replace("/, *( *,)+/i", " , ", $sql_task);
        $sql_action = preg_replace("/, *( *,)+/i", " , ", $sql_action);
        $task_query = $db->prepare($sql_task);
        $action_query = $db->prepare($sql_action);
        if (!$task_query || !$action_query)
            echo '<label class="incorrect feedback">Error: ' . $db-error .'</label>';
        else {
            $task_query->bind_param($task_types, ...$task_values);
            $action_query->bind_param($action_types, ...$action_values);
            if ($task_query->execute() && $action_query->execute())
                header("Location: task_detail.php?id=" . $id);
            else
                echo '<label class="incorrect feedback">Error: ' . $db->error .'</label>';
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
        <title>TITS - Task <?php echo $id ?></title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <link rel="icon" type="image/x-icon" href="../favicon.ico"/>
        <link rel="stylesheet" type="text/css" href="../css/header.css"/>
        <link rel="stylesheet" type="text/css" href="../css/style.css"/>
        <link rel="stylesheet" type="text/css" href="../css/task_detail.css"/>
    </head>
    <body>

    <?php include("header.php"); ?>
<div id="content">
    <form id="task-detail" method="post" action="<?php echo $_SERVER['PHP_SELF'] . "?id=" . $id; ?>">

<?php
// Show task details
if (isset($task)) {
    echo "<div class=\"info\" id=\"first\"><br/>";
    echo '<img class="task-status-icon" src="' . $task['icon'] . '" alt=""/><br/>';
    echo "<label for=\"priority\">Priority</label>";
    if ($_SESSION['user'] == $task['reporter_id'])
        echo "<input type=\"number\" name=\"priority\" id=\"priority\" min=\"1\" max=\"10\" title=\"Priority\" value=\"" . $task['priority'] . "\">";
    else
        echo "<h3 id=\"priority\">" . $task['priority'] . "</h3>";
    echo '<label for="status">Status</label>';
    if (($_SESSION['user'] == $task['reporter_id'] || $_SESSION['user'] == $task['assignee_id']) && $task['final'] != 1) {
        echo '<select id="status" name="status" title="Status">';
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
        echo "</select><br/>";
    }
    else
        echo "<h3 id=\"status\">" . $task['status'] . "</h3>";
    echo "</div>";
    echo "<div class=\"info\"  id=\"second\">";
    echo '<label for="task-name">Task name</label>';
    echo "<h2 class=\"task-name\">" . $task['name'] . "</h2>";
    echo "<h5 id=\"task_description\">" . $task['description'] . "</h5>";
    echo "</div>";
    echo "<div class=\"info";
    if (!empty($task['deadline']))
        echo " tall";
    echo "\" id=\"third\">";
    if ($_SESSION['user'] == $task['reporter_id']) {
        echo '<label for="deadline">Deadline</label>';
        $input_str ='<input name="deadline" type="date" id="deadline" ';
        if (!empty($task['deadline']))
            $input_str .= 'value="' . date("Y-m-d", strtotime($task['deadline'])) . '"';
        $input_str .= "/><br/>";
        echo $input_str;
    }
    else if (!empty($task['deadline']))
        echo "<span>" . $task['deadline'] . "</span><br/>";
    echo "<h4 class=\"user\">Reporter</h4><br/><h3>" . $task['reporter_name'] . "</h3><span>(" . $task['reporter'] . ")</span><br/><br/>";
    if ($_SESSION['user'] == $task['reporter_id'] || !empty($task['assignee']))
        echo "<h4 class=\"user\">Assignee</h4><br/>";
    if ($_SESSION['user'] == $task['reporter_id']) {
        echo '<select id="assignee" name="assignee" title="Assignee">';
        echo "<option value=''>None</option>";
        $query = $db->prepare("SELECT id, login, name FROM t_user");
        if ($query->execute()) {
            $assignees = $query->get_result();
            while ($user = $assignees->fetch_assoc()) {
                $displayName = $user['name'] ? $user['name'] . " (" . $user['login'] . ")" : $user['login'];
                echo "<option value=\"" . $user['id'] . ($user['id'] == $task['assignee_id'] ? "\" selected>" : "\">") . $displayName . "</option>";
            }
        }
        else {
            echo "Something went wrong:<br/>";
            echo $db->error . "<br/>";
            echo $action_query->error . "<br/>";
        }
        echo "</select>";
    }
    else if ($_SESSION['user'] == $task['reporter_id'] || !empty($task['assignee']))
        echo "<h3>" . $task['assignee_name'] . "</h3><span>(" . $task['assignee'] . ")</span>";
    echo "</div>";
}
?>
<div class="info" id="fourth">
    <label for="comment">Comment</label>
    <textarea
                rows=4
                id="comment"
                name="comment"
                placeholder="Your commentary"
                title="Comment"></textarea>
    <br/>
    <button id="save" name="btn-save">&#x1F5AB;Save task</button>
</div>
</form>

<?php
// Show the history of the task
$actions_sql = "SELECT source_priority, target_priority,  u1.login actor_l, u1.name actor_n, u2.login assignee_l, u2.name assignee_n , ss.title source_t, ts.title target_t,timepoint, a.description FROM t_action a LEFT JOIN t_user u1 ON a.actor = u1.id LEFT JOIN t_user u2 ON a.assignee = u2.id LEFT JOIN t_status ss ON a.source_status = ss.id LEFT JOIN t_status ts ON a.target_status = ts.id WHERE task = ? ORDER BY timepoint DESC";
$actions_query = $db->prepare($actions_sql);
echo $db->error;
$actions_query->bind_param("i", $id);
if (!$actions_query || !$actions_query->execute()) {
    echo $db->error;
}
else {
    $actions = $actions_query->get_result();
    while ($action = $actions->fetch_assoc()) {
        $source_priority = $action["source_priority"];
        $target_priority = $action["target_priority"];
        $source_status = $action["source_t"];
        $target_status = $action["target_t"];
        $actor_l = $action["actor_l"];
        $actor_n = $action["actor_n"];
        $assignee_l = $action["assignee_l"];
        $assignee_n = $action["assignee_n"];
        $timepoint = $action["timepoint"];
        $description = $action["description"];

        $action_str = "<article class=\"action\"><h4 class=\"author\">$actor_n ($actor_l)</h4> on <h5>$timepoint</h5>";
        if (isset($source_status) && isset($target_status))
            $action_str .= "<p>Status change: from $source_status to $target_status</p>";
        if (isset($source_priority) && isset($target_priority))
            $action_str .= "<p>Priority change: from $source_priority to $target_priority</p>";
        if (isset($assignee_l) && isset($assignee_n))
            $action_str .= "<p>Assigned $assignee_n($assignee_l)</p>";
        if (isset($description))
            $action_str .= "<p>Comment: $description</p>";

        echo $action_str . "</article>";
    }
}
?>
</div>
    </body>
    </html>
<?php ob_end_flush(); ?>

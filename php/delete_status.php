<?php
session_start();
require_once 'db.php';

// Redirect unauthorized user to login
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$id = $_GET['id'];

// Preliminary checks
$checkTasks = $db->prepare("SELECT id FROM t_task WHERE status = ?");
$checkTasks->bind_param("i", $id);
$checkSystem = $db->prepare("SELECT id FROM t_status WHERE id = ? AND system = TRUE");
$checkSystem->bind_param("i", $id);

if ($checkTasks->num_rows)
    echo "Dependent tasks found";
elseif ($checkSystem->num_rows)
    echo "System status can't be deleted";
else {

    // Delete status
    $query = $db->prepare("DELETE FROM t_status WHERE id = ?");
    $query->bind_param("i", $id);
    if (!$query || !$query->execute()) {
        echo $query->error;
        echo $db->error;
    }
    else
        header("Location: statuses.php");
}
?>

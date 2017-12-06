<?php
require_once 'db.php';
if (isset($_GET["login"])) {
    $query = $db->prepare("SELECT COUNT(*) FROM t_user WHERE login = ?");
    $query->bind_param("s", $_GET["login"]);
    if ($query->execute()) {
        if ($query->get_result()->fetch_array()[0] != 0)
            echo "taken";
        else
            echo "free";
    }
}
?>

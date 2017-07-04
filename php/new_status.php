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

    $title = $_POST['title'];
    $description = $_POST['description'];


    if (empty($title)) {
        $error = true;
        echo "Please enter status title.";
    }

    if (!$error) {
        $sql = "INSERT INTO t_status(title";
        echo $title;
        if (isset($description))
            $sql .= ", description";
        if (isset($_POST['final']))
            $sql .= ", final";
        $sql .= ") VALUES (?";
        if (isset($description))
            $sql .= ", ?";
        if (isset($_POST['final']))
            $sql .= ", ?";
        $sql .= ")";
        $query = $db->prepare($sql);
        $types = "s";
        $values = [$title];
        if (isset($description)) {
            $types .= "s";
            $values[] = $description;
        }
        if (isset($_POST['final'])) {
            $types .= "b";
            $values[] = true;
        }
        $query->bind_param($types, ...$values);
        if ($query->execute())
            header("Location: statuses.php");
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
        <title>New status</title>
    </head>
    <body>

    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <input
                type="text"
                name="title"
                placeholder="Status title"
                title="Name"/>
        <input
                type="text"
                name="description"
                placeholder="Status description"
                title="Name"/>
        <input
                type="checkbox"
                name="final"
                title="Task can't be modified after this status is assigned">
        <button type="submit" name="btn-save">Save status</button>
    </form>
    </body>
    </html>
<?php ob_end_flush(); ?>
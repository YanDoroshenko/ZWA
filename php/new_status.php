<?php
ob_start();
session_start(); require_once 'db.php';

// Redirect unauthorized user to login
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$error = false;

// Process form
if (isset($_POST['btn-save'])) {

    $title = $_POST['title'];
    $description = $_POST['description'];


    // Validation
    if (empty($title)) {
        $error = true;
        echo '<label id="overall" class="incorrect feedback">Please enter title</label>';
    }

    if (isset($_POST['final']) && $_POST['final'] == 'y')
        $isFinal = true;

    // Icon processing
    if (!empty($_FILES["iconUpload"]["name"])) {
        if (is_uploaded_file($_FILES["iconUpload"]["tmp_name"])) {
            foreach ($_FILES["iconUpload"] as $FILE) {
                echo $FILE;
            };
            echo "<br/>";
            $source_file = $_FILES["iconUpload"]["name"];
            $upload_dir = "uploads/";
            $target_file = $upload_dir . basename($source_file);
            $imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);
            $target_file = $upload_dir . strtolower($title) . "." . $imageFileType;

            if (getimagesize($_FILES["iconUpload"]["tmp_name"]) === false)
                $error = true;
        }
        else {
            $error = true;
            echo '<label id="overall" class="incorrect feedback">File was not uploaded. The maximum allowed size might have been exceeded</label>';
        }
    }
    // If OK save status to DB
    if (!$error) {
        $sql = "INSERT INTO t_status(title";
        if (isset($description))
            $sql .= ", description";
        if (isset($isFinal)) {
            echo "FINAL SET";
            $sql .= ", final";
        }
        if (isset($target_file))
            $sql .= ", icon_path";
        $sql .= ") VALUES (?";
        if (isset($description))
            $sql .= ", ?";
        if (isset($isFinal))
            $sql .= ", ?";
        if (isset($target_file))
            $sql .= ", ?";
        $sql .= ")";
        $query = $db->prepare($sql);
        $types = "s";
        $values = [$title];
        if (isset($description)) {
            $types .= "s";
            $values[] = $description;
        }
        if (isset($isFinal)) {
            $types .= "i";
            $values[] = $isFinal;
        }
        if (isset($target_file)) {
            if (move_uploaded_file($_FILES["iconUpload"]["tmp_name"], $target_file)) {
                $types .= "s";
                $values[] = $target_file;
            }
            else {
                echo "Icon can not be uploaded";
                $query->close();
                exit();
            }
        }
        $query->bind_param($types, ...$values);

        // If OK proceed to status list
        if ($query->execute()) {
            header("Location: statuses.php");
        }
        else
            echo '<label id="overall" class="incorrect feedback">Error: ' . $db->error .'</label>';
    }
}
?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>TITS - New status</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <link rel="icon" type="image/x-icon" href="../favicon.ico"/>
        <link rel="stylesheet" type="text/css" href="../css/header.css"/>
        <link rel="stylesheet" type="text/css" href="../css/style.css"/>
        <link rel="stylesheet" type="text/css" href="../css/new_status.css"/>
    </head>
    <body>

    <?php include("header.php"); ?>
<div id="content">
    <form method="post"
        action="<?php echo $_SERVER['PHP_SELF']; ?>"
        id="new_status"
        enctype="multipart/form-data">
        <label for="title">Title</label>
<br/>
        <input
                required="required"
                type="text"
                name="title"
                id="title"
                placeholder="Status title"
                title="Name"/>
<label id="title-feedback"></label>
<br/>
<label for="title">Description</label>
<br/>
        <textarea rows="5" columns="23"
                name="description"
                placeholder="Status description"
                title="Description"></textarea>
<br/>
<label for="title">Final</label>
        <input type="checkbox"
               name="final"
               title="Task can't be modified after this status is assigned"
               value="y">
<br/>
<label for="iconUpload">Icon</label>
<br/>
        <input id="iconUpload" type="file" name="iconUpload">
        <label id="iconUpload-feedback"></label>
<br/>
        <button type="submit" name="btn-save">Create</button>
    </form>
</div>
    </body>
    <script src="../js/new_status.js"></script>
    </html>
<?php ob_end_flush(); ?>

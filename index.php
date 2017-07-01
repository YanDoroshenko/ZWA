<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
</head>
<body>
Before
<br/>
<?php
$db = mysqli_connect("localhost", "root", "L0v3_7h3,5m311.0f_n@p@1m,in.7h3_m0rning", "mysql");
$query = mysqli_query($db, "SELECT * FROM t_task JOIN t_user u1 ON t_task.assignee = u1.id JOIN t_user u2 ON t_task.reporter = u2.id");
foreach (mysqli_fetch_all($query) as $result) {
    foreach ($result as $column)
        echo $column . " ";
    echo "<br/>";
}
mysqli_close($db);
?>
After
</body>
</html>
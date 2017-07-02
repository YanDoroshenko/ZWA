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
$query = mysqli_query($db, "SELECT t.id 'id', t.name 'task', t.status 'status', u2.name 'reporter', u1.name 'assignee' FROM t_task t LEFT JOIN t_user u1 ON t.assignee = u1.id JOIN t_user u2 ON t.reporter = u2.id");
while ($result = mysqli_fetch_assoc($query))
    echo $result["id"] . " " . $result['task'] . " " . $result['status'] . " " . $result['reporter'] . " " . $result['assignee'] . "<br/>";
if (defined(mysqli_error($db)))
    echo mysqli_error($db) . "<br/>";
mysqli_close($db);
?>
After
</body>
</html>
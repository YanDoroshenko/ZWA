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
$query = mysqli_query($db, "SELECT id, name, country FROM test_users");
foreach (mysqli_fetch_all($query) as $result)
    echo $result[0] . " " . $result[1] . " " . $result[2] . "<br/>";
mysqli_close($db);
?>
After
</body>
</html>
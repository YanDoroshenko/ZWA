<?php
ob_start();
session_start();
if (isset($_SESSION['user']) != "") {
    header("Location: home.php");
}
include_once 'db.php';

$error = false;

if (isset($_POST['btn-signup'])) {

    $name = $_POST['name'];

    $login = $_POST['login'];

    $password = $_POST['password'];


    $query = $db->prepare("SELECT login FROM t_user WHERE login = ?");
    $query->bind_param("s", $login);
    $query->execute();
    $result = $query->get_result();
    $count = mysqli_num_rows($result);
    if ($count != 0) {
        $error = true;
        echo "Provided login is already in use.";
    }

    if (empty($password)) {
        $error = true;
        echo "Please enter password.";
    }

    $hashedPassword = hash('sha512', $password, $login);

    if (!$error) {

        $query = $db->prepare("INSERT INTO t_user(login, password_hash, name) VALUES(?, ?, ?)");
        $query->bind_param("sss", $login, $hashedPassword, $name);

        if ($query->execute()) {
            echo "Successfully registered, you may login now";
            unset($name);
            unset($login);
            unset($password);
        }
        else {
            echo $query->error . "<br/>";
            echo $db->error . "<br/>";
            echo "Something went wrong, try again later...";
        }
    }
}
?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title>Sign up</title>
    </head>
    <body>
    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <h2>Sign Up</h2>

        <input type="text" name="name" placeholder="Name"
               value="<?php
               if (isset($name))
                   echo $name ?>"/>

        <input type="text" name="login" placeholder="Login"
               value="<?php
               if (isset($login))
                   echo $login ?>"/>

        <input type="password" name="password" placeholder=" Password"/>

        <button type="submit" name="btn-signup">Sign Up</button>

        <a href="login.php">Log in</a>

    </form>
    </body>
    </html>
<?php ob_end_flush(); ?>
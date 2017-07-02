<?php
ob_start();
session_start();
if (isset($_SESSION['user']) != "") {
    header("Location: home.php");
}
include_once 'db.php';

$error = false;

if (isset($_POST['btn-signup'])) {

    // clean user inputs to prevent sql injections
    $name = trim($_POST['name']);
    $name = strip_tags($name);
    $name = htmlspecialchars($name);

    $login = trim($_POST['email']);
    $login = strip_tags($login);
    $login = htmlspecialchars($login);

    $pass = trim($_POST['pass']);
    $pass = strip_tags($pass);
    $pass = htmlspecialchars($pass);


    // check email exist or not
    $query = "SELECT login FROM t_user WHERE login='$login'";
    $result = mysqli_query($db, $query);
    $count = mysqli_num_rows($result);
    if ($count != 0) {
        $error = true;
        echo "Provided Email is already in use.";
    }
    // password validation
    if (empty($pass)) {
        $error = true;
        echo "Please enter password.";
    }

    // password encrypt using SHA512();
    $password = hash('sha512', $pass, $login);

    // if there's no error, continue to signup
    if (!$error) {

        $query = "INSERT INTO t_user(name,login,password_hash) VALUES('$name','$login','$password')";
        $res = mysqli_query($db, $query);

        if ($res) {
            echo "Successfully registered, you may login now";
            unset($name);
            unset($login);
            unset($pass);
        }
        else {
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
    <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
        <h2>Sign Up</h2>

        <input type="text" name="name" placeholder="Name"
               value="<?php
               if (isset($name))
                   echo $name ?>"/>

        <input type="text" name="email" placeholder="Login"
               value="<?php
               if (isset($login))
                   echo $login ?>"/>

        <input type="password" name="pass" placeholder=" Password"/>

        <button type="submit" name="btn-signup">Sign Up</button>

        <a href="login.php">Log in</a>

    </form>
    </body>
    </html>
<?php ob_end_flush(); ?>
<?php
ob_start();
session_start();
require_once 'db.php';

// it will never let you open index(login) page if session is set
if (isset($_SESSION['user'])) {
    header("Location: home.php");
    exit;
}

$error = false;

if (isset($_POST['btn-login'])) {

    // prevent sql injections/ clear user invalid inputs
    $login = $_POST['login'];

    $password = $_POST['password'];

    if (empty($login)) {
        $error = true;
        echo "Please enter your login.";
    }

    if (empty($password)) {
        $error = true;
        echo "Please enter your password.";
    }

    // if there's no error, continue to login
    if (!$error) {

        $hashedPassword = hash('sha512', $password, $login); // password hashing using SHA256

        $res = mysqli_query($db, "SELECT id FROM t_user WHERE login='$login' AND password_hash = '$hashedPassword'");
        $row = mysqli_fetch_assoc($res);
        $count = mysqli_num_rows($res); // if uname/pass correct it returns must be 1 row

        if ($count == 1) {
            $_SESSION['user'] = $row['id'];
            header("Location: home.php");
        }
        else {
            echo "Incorrect Credentials, Try again...";
        }

    }

}
?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title>Log in</title>
    </head>
    <body>
    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <h2>Log in</h2>

        <input type="text" name="login" placeholder="Login"
               value="<?php
               if (isset($login))
                   echo $login; ?>"
        />
        <input type="password" name="password" placeholder="Password"/>
        <button type="submit" name="btn-login">Log in</button>

        <a href="signup.php">Sign Up</a>
    </form>
    </body>
    </html>
<?php ob_end_flush(); ?>
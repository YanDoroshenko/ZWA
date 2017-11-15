<?php
ob_start();
session_start();

// If authorized proceed to home
if (isset($_SESSION['user']) != "") {
    header("Location: home.php");
}
include_once 'db.php';

$error = false;

// Process form
if (isset($_POST['btn-signup'])) {

    $name = $_POST['name'];

    $login = $_POST['login'];

    $password1 = $_POST['password1'];
    $password2 = $_POST['password2'];

    // Validation
    if (empty($password1)) {
        $error = true;
        echo "Please enter password.";
    }
    else if (empty($password2)) {
        echo "Please confirm password!";
        $error = true;
    }
    else if ($password1 != $password2) {
        echo "Password confirmation does not match";
        $error = true;
    }
    else {
        $query = $db->prepare("SELECT login FROM t_user WHERE login = ?");
        $query->bind_param("s", $login);
        $query->execute();
        $result = $query->get_result();
        $count = mysqli_num_rows($result);
        if ($count != 0) {
            $error = true;
                echo '<label id="overall" class="incorrect feedback">Login ' . htmlspecialchars($login) .' is already taken</label>';
        }

        $hashedPassword = password_hash($password1, PASSWORD_DEFAULT, ['salt' => 'kjihgfedcba' . $login . 'abcdefghijk']);

        // If OK, save user to DB
        if (!$error) {

            $query = $db->prepare("INSERT INTO t_user(login, password_hash, name) VALUES(?, ?, ?)");
            $query->bind_param("sss", $login, $hashedPassword, $name);

            if ($query->execute()) {
                echo '<label id="overall" class="correct feedback">Successfully registered, you may login now</label>';
                unset($name);
                unset($login);
                unset($password1);
                unset($password2);
            }
            else {
                echo '<label id="overall" class="incorrect feedback">Something went wrong with the database: ' . $db->error . '</label>';
            }
        }
    }
}
?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>TITS - Signup</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <link rel="icon" type="image/x-icon" href="../favicon.ico"/>
        <link rel="stylesheet" type="text/css" href="../css/style.css"/>
        <link rel="stylesheet" type="text/css" href="../css/signup.css"/>
    </head>
    <body>
<header>
<img id="logo" src="../img/favicon.png" alt="logo"/>
         <span>
            Temporal Issue Tracking System
        </span>
        <a href="login.php">&#x26BF; Log in</a>
</header>
<div id="content">
    <form method="post"
          action="<?php echo $_SERVER['PHP_SELF']; ?>"
          id="signup">

<label for="name">Name</label>
<br/>
        <input type="text" name="name" id="name" placeholder="Name"
        value="<?php
if (isset($name))
    echo htmlspecialchars($name) ?>"/>
<br/>

<label for="login">Login</label>
<br/>
        <input  type="text"
                name="login"
                id="login"
                required="required"
                pattern="^[a-zA-Z]+[a-zA-Z0-9]*$"
                placeholder="Login"
                value="<?php
    if (isset($login))
        echo $login ?>"/>
        <label class="feedback" id="login-feedback"></label>
<br/>

<label for="password2">Password</label>
<br/>
        <input type="password"
               name="password1"
               id="password1"
               required="required"
               placeholder=" Password"/>
        <label class="feedback" id="password1-feedback"></label>
<br/>

<label for="password2">Password confirmation</label>
<br/>
        <input type="password"
               name="password2"
               id="password2"
               required="required"
               placeholder="Password confirmation"/>
        <label class="feedback" id="password2-feedback"></label>

<br/>
        <button type="submit" name="btn-signup">Sign Up</button>

    </form>
</div>
<script src="../js/signup.js"></script>
    </body>
    </html>
<?php ob_end_flush(); ?>

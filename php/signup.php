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
            echo "Provided login is already in use.";
        }

        $hashedPassword = password_hash($password1, PASSWORD_DEFAULT, ['salt' => 'kjihgfedcba' . $login . 'abcdefghijk']);

        // If OK, save user to DB
        if (!$error) {

            $query = $db->prepare("INSERT INTO t_user(login, password_hash, name) VALUES(?, ?, ?)");
            $query->bind_param("sss", $login, $hashedPassword, $name);

            if ($query->execute()) {
                echo "Successfully registered, you may login now";
                unset($name);
                unset($login);
                unset($password1);
                unset($password2);
            }
            else {
                echo $query->error . "<br/>";
                echo $db->error . "<br/>";
                echo "Something went wrong, try again later...";
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
<link rel="stylesheet" type="text/css" href="../css/style.css">
    </head>
    <body>
    <form method="post" 
          action="<?php echo $_SERVER['PHP_SELF']; ?>"
          id="signup">
        <h2>Sign Up</h2>
<br/>
        <a href="login.php">Log in</a>
<br/>

<label for="name">Name</label>
        <input type="text" name="name" placeholder="Name"
        value="<?php
if (isset($name))
    echo $name ?>"/>
<br/>

<label for="name">Name</label>
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
        <input type="password" 
               name="password1"  
               id="password1"  
               required="required"
               placeholder=" Password"/>
        <label class="feedback" id="password1-feedback"></label>
<br/>

<label for="password2">Password confirmation</label>
        <input type="password" 
               name="password2"  
               id="password2"
               required="required"
               placeholder="Password confirmation"/>
        <label class="feedback" id="password2-feedback"></label>
<br/>

        <button type="submit" name="btn-signup">Sign Up</button>
<br/>

    </form>
    </body>
<script src="../js/signup.js"></script>
    </html>
<?php ob_end_flush(); ?>

<?php
ob_start();
session_start();
require_once 'db.php';

// If authorized, proceed to home
if (isset($_SESSION['user'])) {
    header("Location: home.php");
    exit;
}

$error = false;

// Form processing
if (isset($_POST['btn-login'])) {

    $login = $_POST['login'];

    $password = $_POST['password'];

    // Validation
    if (empty($login)) {
        $error = true;
        echo '<label id="overall" class="incorrect feedback">Entered credentials are invalid, try again</label>';
    }

    if (empty($password)) {
        $error = true;
        echo '<label id="overall" class="incorrect feedback">Entered credentials are invalid, try again</label>';
    }

    // if there's no error, continue to login
    if (!$error) {

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT, ['salt' => 'kjihgfedcba' . $login . 'abcdefghijk']);

        // Check the credentials with the database
        $query = mysqli_prepare($db, "SELECT id FROM t_user WHERE login = ? AND password_hash = ?");
        $query->bind_param("ss", $login, $hashedPassword);
        $query->execute();
        $result = $query->get_result();
        $row = mysqli_fetch_assoc($result);
        $count = mysqli_num_rows($result);

        // If OK proceed to home
        if ($count == 1) {
            $_SESSION['user'] = $row['id'];
            header("Location: home.php");
        }
        else {
            echo '<label id="overall" class="incorrect feedback">Entered credentials are invalid, try again</label>';
        }

    }

}
?>
    <!DOCTYPE html>
    <html lang="en">
    <head><title>TITS - Login</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <link rel="icon" type="image/x-icon" href="../favicon.ico"/>
        <link rel="stylesheet" type="text/css" href="../css/style.css"/>
        <link rel="stylesheet" type="text/css" href="../css/signup.css"/>
        <link rel="stylesheet" type="text/css" href="../css/login.css"/>
    </head>
    <body>
<header>
<img id="logo" src="../img/favicon.png" alt="logo"/>
         <span>
            Temporal Issue Tracking System
        </span>
        <a href="signup.php">&#x26BF; Sign Up</a>
</header>
<div id="content">
    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<label for="login">Login</label>
<br/>
        <input type="text" name="login" id="login" placeholder="Enter your login" required="required"
            autofocus="autofocus"
            value="<?php
if (isset($login))
    echo htmlspecialchars($login); ?>"
        />
<label class="feedback" id="login-feedback"></label>
<br/>
<label for="password">Password</label>
<br/>
        <input type="password" name="password" id="password" placeholder="Enter your password" required="required"/>
<label class="feedback" id="password-feedback"></label>
<br/>
        <button type="submit" name="btn-login">Log in</button>
    </form>
</div>
<script src="../js/login.js"></script>
    </body>
    </html>
<?php ob_end_flush(); ?>

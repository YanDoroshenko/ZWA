<?php
ob_start();
session_start();
require_once 'db.php';

if (isset($_SESSION['user'])) {
    header("Location: home.php");
    exit;
}

$error = false;

if (isset($_POST['btn-login'])) {

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

	$hashedPassword = password_hash($password, PASSWORD_DEFAULT, ['salt' => 'kjihgfedcba' . $login . 'abcdefghijk']);

	$query = mysqli_prepare($db, "SELECT id FROM t_user WHERE login = ? AND password_hash = ?");
	$query->bind_param("ss", $login, $hashedPassword);
	$query->execute();
	$result = $query->get_result();
	$row = mysqli_fetch_assoc($result);
	$count = mysqli_num_rows($result);

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
    <head><title>TITS - Login</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<link rel="icon" type="image/x-icon" href="../favicon.ico"/>
    </head>
    <body>
    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	 <h1>
	    Temporal Issue Tracking System
	</h1>
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

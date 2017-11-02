<?php
session_start();

// Redirect unauthorized user to login
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
}

// Log authorized user out
if (isset($_GET['logout'])) {
    unset($_SESSION['user']);
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit;
}

<?php
session_start(); // STARTING THE SESSION

// UNSETTING UNWANTED SESSION VARIABLES
unset($_SESSION['logged_in']);  // REMOVES LOGIN STATUS
unset($_SESSION['username']);  // REMOVES USERNAME

// REDIRECTING TO SIGN IN PAGE AFTER SIGN OUT
header("Location: signin.php");
exit();
session_write_close();
?>

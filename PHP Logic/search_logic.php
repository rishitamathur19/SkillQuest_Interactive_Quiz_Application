<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../signin.php");
    exit();
}

include('../PHP Logic/db_config.php');

// INITIALIZING SEARCH QUERY TO BLANK
$search_query = "";

// CHECKING IF SEARCH QUERY IS BEING PASSED
if (isset($_POST['search_query'])) {
    // SANITIZING SEARCH QUERY TO PREVENT SQL INJECTION
    $search_query = mysqli_real_escape_string($conn, $_POST['search_query']);
    // CONVERTING SEARCH QUERY TO LOWERCASE TO MAKE IT CASE-INSESITIVE
    $search_query = strtolower($search_query);
    // CONVERTING THE FIRST LETTER OF EACH WORD IN SEARCH QUERY CAPITAL
    $search_query = ucwords($search_query);
    // Perform the redirect to quiz.php with the category set to the search query
    // REDIRECTING TO 'quiz.php' WITH CATEGORY NAME IN SEARCH QUERY
    header("Location: ../templates/quiz.php?category=" . $search_query);
    exit; // Ensure no further code is executed
} else {
    header("Location: " . $_SERVER['HTTP_REFERER']);
}
session_write_close();
mysqli_close($conn);
?>
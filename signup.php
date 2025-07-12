<!-- PHP CODE TO PROCESS THE FORM AFTER SUBMISSION -->
<?php
// INCLUDING DB CONFIG FILE (CONTAINS DATABASE CONNECTION INFORMATION)
include('PHP Logic/db_config.php');
session_start(); // STARTING THE SESSION

$error_message = ""; // INITIALIZING ERROR MESSAGE VARIABLE

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // GET FORM INPUTS AND PREVENTS FROM SQL INJECTION
    $username = mysqli_real_escape_string($conn, $_POST['uname']);
    $password = mysqli_real_escape_string($conn, $_POST['pswd']);
    $confirm_password = mysqli_real_escape_string($conn, $_POST['cpswd']);

    // CHECK IF USERNAME ALREADY EXISTS
    $sql = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        // ERROR MESSAGE FOR USERNAME ALREADY EXISTS
        $error_message = "Username already taken. Please choose another.";
    } else {
        // CHECK IF PASSWORD AND CONFIRM PASSWORD MATCH
        if ($password !== $confirm_password) {
            // ERROR MESSAGE FOR PASSORD DID NOT MATCH
            $error_message = "Passwords do not match.";
        } else {
            // HASHING THE PASSWORD USING 'password_hash()' FUNCTION
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            // QUERY TO INSERT NEW USER INTO THE DATABASE
            $insert_sql = "INSERT INTO `users` (`username`, `password`) VALUES ('$username', '$hashed_password')";
            if (mysqli_query($conn, $insert_sql)) {
                // REDIRECTING TO SIGN IN PAGE AFTER SUCCESSFULL REGISTERATION
                header("Location: signin.php");
                exit();
            } else {
                // ERROR MESSAGE FOR UNSUCCESSFULL REGISTERATION
                $error_message = "Something went wrong. Please try again.";
            }
        }
    }

    // CLOSING THE DATABSE CONNECTION
    mysqli_close($conn);
    session_write_close(); // ENDS SESSION FOR CURRENT REQUEST WITHOUT DESTROYING SESSION DATA
}

// DISPLAYING ERROR MESSAGE FOR UNSUCCESSFULL REGISTERATION
if (!empty($error_message)) {
    echo "
    <div class='alert alert-danger alert-dismissible fade show' role='alert' style='position: fixed; top: 0; left: 0; right: 0; z-index: 1050;'>
    $error_message
    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
    </div>";
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SkillQuest | Sign Up</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <!-- FAVICON  -->
    <link rel="icon" type="image/x-icon" href="images/favicon.png">
    <style>
        html,
        body {
            height: 100%;
            margin: 0;
            background: linear-gradient(135deg, #06597a 0%, #02a8b5 100%);
            color: white;
            font-family: 'Poppins', sans-serif;
        }

        .card {
            background: linear-gradient(135deg, #06597a 100%, #02a8b5 0%);
            border: #02a8b5;
        }

        .btn-light:hover {
            color: #000;
        }

        .back-btn {
            position: absolute;
            top: 20px;
            left: 20px;
            background-color: transparent;
            border: none;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }

        .back-btn:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <!-- BACK TO INDEX BUTTON -->
    <button class="back-btn link-offset-2" onclick="window.location.href='index.php'">← Back to Home</button>

    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="card shadow-lg rounded-4 p-4" style="max-width: 400px; width: 100%;">
            <!-- LOGO AT THE TOP-LEFT CORNER -->
            <div class="text-center mb-4">
                <img src="images/logo.png" alt="SkillQuest Logo" class="img-fluid" style="max-width: 180px;">
            </div>
            <h2 class="text-center mb-3 text-white">Sign Up to SkillQuest</h2>

            <!-- SIGN UP FORM -->
            <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <div class="mb-2">
                    <label for="text" class="form-label text-white">Username</label>
                    <input type="text" class="form-control" id="uname" name="uname" placeholder="Enter Username" required>
                </div>
                <div class="mb-2">
                    <label for="password" class="form-label text-white">Password</label>
                    <input type="password" class="form-control" id="pswd" name="pswd" placeholder="Enter password" required>
                </div>
                <div class="mb-2">
                    <label for="password" class="form-label text-white">Confirm Password</label>
                    <input type="password" class="form-control" id="cpswd" name="cpswd" placeholder="Confirm password" required>
                </div>
                <div class="d-grid">
                    <button type="submit"
                        class="btn btn-light btn-lg text-info-emphasis fw-semibold rounded-pill mt-3 mb-1">Sign
                        Up</button>
                </div>
            </form>

            <!-- LINK TO SIGN IN -->
            <p class="text-center text-white">Have an existing account? <a href="signin.php" class="sign-in-link link-light link-offset-3 text-white link-underline link-underline-opacity-0 link-underline-opacity-75-hover">Sign In</a></p>

            <!-- FOOTER -->
            <footer class="mt-2 text-center text-white-50">
                <p class="h6">SkillQuest - Knock The Answers</p>
            </footer>
        </div>
    </div>

    <script src="/docs/5.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</body>

</html>
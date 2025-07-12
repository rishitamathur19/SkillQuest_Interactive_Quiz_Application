<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../signin.php");
    exit();
}
$username = $_SESSION['username'];
include('../PHP Logic/db_config.php');

// FETCHING USER ID BASED ON USERNAME
$query = "SELECT `id` FROM `users` WHERE `username` = '$username'";
$result = mysqli_query($conn, $query);
$user_id = null;
if (!mysqli_num_rows($result) == 0) {
    $row = mysqli_fetch_assoc($result);
    $user_id = $row['id'];
}

// FETCHING TOTAL QUIZZES ATTEMPTED BY USER
$total_quizzes_attempted_query = "SELECT COUNT(*) as total_attempted FROM `quiz_results` WHERE `user_id` = $user_id";
$total_quizzes_attempted_result = mysqli_query($conn, $total_quizzes_attempted_query);
$total_quizzes_attempted_row = mysqli_fetch_assoc($total_quizzes_attempted_result);
$total_attempted = $total_quizzes_attempted_row['total_attempted'];

// FETCHING TOTAL NO OF QUIZZES AVAILABLE
$total_quizzes_available_query = "SELECT COUNT(*) as total_quizzes FROM `mcqs`";
$total_quizzes_available_result = mysqli_query($conn, $total_quizzes_available_query);
$total_quizzes_available_row = mysqli_fetch_assoc($total_quizzes_available_result);
$total_available = $total_quizzes_available_row['total_quizzes'];

// Calculate the progress (percentage)
if ($total_available > 0) {
    $progress = ($total_attempted / $total_available) * 100;
} else {
    $progress = 0;
}

// FOR DISPLAYING DAILY QUIZ RESULTS
$daily_results_query = "SELECT * FROM `daily_quiz_results` WHERE `user_id` = $user_id ORDER BY `quiz_date` DESC";
$results_daily = mysqli_query($conn, $daily_results_query);

// FOR DISPLAYING CATEGORY-WISE QUIZ RESULTS
$category_results_query = "SELECT * FROM `quiz_results` WHERE `user_id` = $user_id ORDER BY `quiz_date` DESC";
$results_category = mysqli_query($conn, $category_results_query);

// FOR DISPLAYING TOTAL NO OF CATEGORY-WISE QUIZ TAKEN
$category_query = "
    SELECT `category`, COUNT(*) AS total_rows
    FROM `quiz_results`
    WHERE `user_id` = $user_id
    GROUP BY `category`
    ORDER BY `category` DESC";
$category_result = mysqli_query($conn, $category_query);
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SkillQuest | Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- EXTERNAL CSS  -->
    <link rel="stylesheet" href="../templates/style.css">
    <!-- FAVICON  -->
    <link rel="icon" type="image/x-icon" href="../images/favicon.png">
</head>

<body>
    <!-- Navbar STARTS -->
    <nav class="navbar nav-underline navbar-expand-lg" style="background-color: #06597a;" data-bs-theme="dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="../index.php">
                <img src="../images/logo.png" alt="logo" width="150" height="42">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link mx-2" href="../templates/home.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link mx-2" href="../templates/categories.php">Categories</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link mx-2" href="../templates/quiz.php">Quiz</a>
                    </li>
                </ul>
                <!-- PROFILE SECTION STARTS -->
                <div
                    class="dropdown d-flex flex-lg-column flex-md-row flex-sm-row align-items-lg-center align-items-md-start align-items-sm-start ms-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="white"
                        class="bi bi-person-circle" viewBox="0 0 16 16">
                        <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0" />
                        <path fill-rule="evenodd"
                            d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1" />
                    </svg>
                    <button class="nav-link active me-auto mb-2 mb-lg-0 px-2 text-white btn btn-link" type="button"
                        id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        Profile
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="profileDropdown">
                        <li><a class="dropdown-item" href="../templates/profile.php">View Profile</a></li>
                        <li><a class="dropdown-item" href="../signout.php">Sign Out</a></li>
                    </ul>
                </div>
                <!-- PROFILE SECTION ENDS -->
                <!-- SEARCH SECTION ENDS -->
                <form class="d-flex ms-2 mt-2 mt-lg-0" role="search" action="../PHP Logic/search_logic.php" method="POST">
                    <input class="form-control me-2" type="text" name="search_query" placeholder="Search" aria-label="Search" data-bs-theme="light">
                    <button class="btn searchbtn fw-semibold" type="submit">Search</button>
                </form>
                <!-- SEARCH SECTION ENDS -->
            </div>
        </div>
    </nav>
    <!-- NAVBAR ENDS-->

    <!-- PROFILE PAGE STARTS -->
    <div class="container mt-4">

        <?php
        echo "<h3 class='text-primary-emphasis text-center mb-4'>Welcome, " . htmlspecialchars($username) . "!</h3>";
        ?>

        <!-- Progress Bar Section -->
        <?php
        echo "<h5 class='text-primary-emphasis mb-3 ms-2'>Track Your Progress</h5>";
        ?>
        <div class="container mt-4">
            <div class="progress">
                <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $progress; ?>%" aria-valuenow="<?php echo $progress; ?>" aria-valuemin="0" aria-valuemax="100">
                </div>
                <span class="text-primary-emphasis">
                    <?php echo round($progress, 2); ?>% Completed
                </span>
            </div>
        </div>
        <br>

        <?php
        echo "<h5 class='text-primary-emphasis mb-3 ms-2'>Quiz Streaks</h5>";
        ?>
        <ol class="list-group">
            <li class="list-group-item d-flex justify-content-between align-items-start">
                <div class="ms-2 me-auto p-1">
                    <div class="fw-bold">Daily Quiz</div>
                </div>
                <span class="badge text-bg-primary rounded-pill">
                    <?php
                    echo mysqli_num_rows($results_daily)
                    ?>
                </span>
            </li>
            <div class="accordion" id="accordionExample">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingOne">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                            <div class="ms-2 me-auto">
                                <div class="fw-bold">Category-Wise Quiz</div>
                            </div>
                        </button>
                    </h2>
                    <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                            <ol class="list-group list-group-numbered">
                                <?php
                                if (mysqli_num_rows($category_result) > 0) {
                                    while ($category_row = mysqli_fetch_assoc($category_result)) {
                                        $category_name = $category_row['category'];
                                        $total_rows = $category_row['total_rows'];
                                ?>
                                        <li class="list-group-item border-0 d-flex justify-content-between align-items-start">
                                            <div class="ms-2 me-auto">
                                                <div class="fw-semibold"><?php echo $category_name ?></div>
                                            </div>
                                            <span class="badge text-bg-primary rounded-pill">
                                                <?php
                                                echo $total_rows;
                                                ?>
                                            </span>
                                        </li>
                                <?php
                                    }
                                } else {
                                    echo "<h6 class='ms-4'>No Quiz Taken Yet</h6>";
                                }
                                ?>
                        </div>
                    </div>
                </div>
            </div>

        </ol>
        <br>

        <?php
        echo "<h5 class='text-primary-emphasis mb-3 ms-2'>View Results</h5>";
        ?>

        <div class="card text-center">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs">
                    <li class="nav-item">
                        <a class="nav-link text-primary-emphasis active" id="daily-tab" data-bs-toggle="tab" href="#daily-quiz">Daily Quiz Results</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-primary-emphasis" id="category-tab" data-bs-toggle="tab" href="#category-quiz">Category-Based Quiz Results</a>
                    </li>
                </ul>
            </div>

            <div class="card-body">
                <div class="tab-content">

                    <!-- DAILY QUIZ RESULTS TAB -->
                    <div class="tab-pane fade show active" id="daily-quiz">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th scope="col">Sr. No.</th>
                                        <th scope="col">Quiz Date</th>
                                        <th scope="col">Correct Answers</th>
                                        <th scope="col">Attempted Questions</th>
                                        <th scope="col">Total Questions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $d_counter = 1;
                                    if (mysqli_num_rows($results_daily) > 0) {
                                        while ($result = mysqli_fetch_assoc($results_daily)) {
                                            echo "<tr>";
                                            echo "<th scope='row'>" . $d_counter++ . "</th>";
                                            echo "<td>" . $result['quiz_date'] . "</td>";
                                            echo "<td>" . $result['correct_answers'] . "</td>";
                                            echo "<td>" . $result['attempted_questions'] . "</td>";
                                            echo "<td>" . $result['total_questions'] . "</td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='5' class='text-center'>No Daily Quiz Attempted Yet</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- CATEGORY-WISE QUIZ RESULTS TAB -->
                    <div class="tab-pane fade" id="category-quiz">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th scope="col">Sr. No.</th>
                                        <th scope="col">Quiz Date</th>
                                        <th scope="col">Category</th>
                                        <th scope="col">Correct Answers</th>
                                        <th scope="col">Attempted Questions</th>
                                        <th scope="col">Total Questions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $cat_counter = 1;
                                    if (mysqli_num_rows($results_category) > 0) {
                                        while ($result = mysqli_fetch_assoc($results_category)) {
                                            echo "<tr>";
                                            echo "<th scope='row'>" . $cat_counter++ . "</th>";
                                            echo "<td>" . $result['quiz_date'] . "</td>";
                                            echo "<td>" . $result['category'] . "</td>";
                                            echo "<td>" . $result['correct_answers'] . "</td>";
                                            echo "<td>" . $result['attempted_questions'] . "</td>";
                                            echo "<td>" . $result['total_questions'] . "</td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='6' class='text-center'>No Quiz Attempted Yet</td></tr>";
                                    }
                                    ?>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- PROFILE PAGE ENDS -->

    <!-- FOOTER SECTION STARTS -->
    <footer class="mt-4 mb-4 text-center text-info-emphasis">
        <p class="h5">SkillQuest - Knock The Answers</p>
    </footer>
    <!-- FOOTER SECTION ENDS -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</body>

</html>
<?php
session_write_close();
mysqli_close($conn);
?>
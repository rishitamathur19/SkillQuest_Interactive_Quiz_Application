<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
  header("Location: ../signin.php");
  exit();
}
$username = $_SESSION['username'];
date_default_timezone_set('Asia/Kolkata');

include('../PHP Logic/db_config.php');

// GETS THE CATEGORY FROM URL
$selected_category = isset($_GET['category']) ? $_GET['category'] : null;
$cat_id = null; // INITIALIZING CATEGORY ID TO NULL
$quiz_query = "";  // INITIALIZING QUIZ QUERY TO BLANK

// FETCHING CATEGORY ID COREESPONDING TO SELECTED CATEGORY
if ($selected_category) {
  urldecode($selected_category);
  $category_query = "SELECT `id` FROM `categories` WHERE `name` = '$selected_category'";
  $category_result = mysqli_query($conn, $category_query);

  // CHECKING IF RESULT WAS FOUND
  if ($category_result && mysqli_num_rows($category_result) > 0) {
    $category_row = mysqli_fetch_assoc($category_result); // FETCHING CATEGORY ID
    $cat_id = $category_row['id'];  // STORING CATEGORY ID
  }
}

// FETCHING QUESTIONS BASED ON CATEGORY ID
if ($cat_id) {
  $quiz_query = "SELECT * FROM `mcqs` WHERE `category_id` = '$cat_id'";
}

// CHECKING IF '$quiz_query' IS SET OR NOT
if (!empty($quiz_query)) {
  $quiz_result = mysqli_query($conn, $quiz_query);
  // FETCHING QUESTIONS
  if ($quiz_result) {
    if (!mysqli_num_rows($quiz_result) == 0) {
      // STORING QUESTIONS IN ARRAY
      $questions = [];
      while ($row = mysqli_fetch_assoc($quiz_result)) {
        $questions[] = $row;
      }
      shuffle($questions); // SHUFFLING THE QUESTIONS
    }
  } else {
    // IF QUERY IS NOT EXECUTED DISPLAYING ERROR
    $error_message = "Error Loading Questions. Please Try Again\n" . mysqli_error($conn);
  }
}
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SkillQuest | Quiz</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <!-- EXTERNAL CSS -->
  <link rel="stylesheet" href="../templates/style.css">
  <link rel="stylesheet" href="../css/quiz_style.css">
  <!-- FAVICON -->
  <link rel="icon" type="image/x-icon" href="../images/favicon.png">
</head>

<body>
  <!-- Navbar STARTS -->
  <nav class="navbar fixed-top nav-underline navbar-expand-lg" style="background-color: #06597a;" data-bs-theme="dark">
    <div class="container-fluid">
      <a class="navbar-brand" href="../index.php">
        <img src="../images/logo.png" alt="logo" width="150" height="42">
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
        aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
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
            <a class="nav-link active mx-2" href="../templates/quiz.php">Quiz</a>
          </li>
        </ul>
        <!-- PROFILE SECTION STARTS -->
        <div
          class="dropdown d-flex flex-lg-column flex-md-row flex-sm-row align-items-lg-center align-items-md-start align-items-sm-start ms-2">
          <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="white" class="bi bi-person-circle"
            viewBox="0 0 16 16">
            <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0" />
            <path fill-rule="evenodd"
              d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1" />
          </svg>
          <button class="nav-link me-auto mb-2 mb-lg-0 px-2 text-white btn btn-link" type="button" id="profileDropdown"
            data-bs-toggle="dropdown" aria-expanded="false">
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
  <!-- NAVBAR ENDS -->

  <!-- MAIN CONTAINER STARTS -->
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-3 border-end">
        <!-- SIDEBAR FOR LARGE SCREENS -->
        <div class="d-none d-lg-block flex-shrink-0 p-3" id="sidebar">
          <a href="/" class="d-flex align-items-center pb-3 mb-1 link-body-emphasis text-decoration-none border-bottom">
            <span class="fs-5 fw-semibold">Start Quizzing</span>
          </a>
          <ul class="list-unstyled ps-0">
            <li>
              <button class="btn btn-toggle d-inline-flex align-items-center rounded border-0 collapsed"
                data-bs-toggle="collapse" data-bs-target="#prog-collapse" aria-expanded="true">PROGRAMMING</button>
              <div class="collapse show" id="prog-collapse">
                <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                  <li><a href="?category=C" class="link-body-emphasis d-inline-flex text-decoration-none rounded">C
                      Programming</a>
                  </li>
                  <li><a href="?category=CPP" class="link-body-emphasis d-inline-flex text-decoration-none rounded">C++
                      Programming</a></li>
                  <li><a href="?category=PHP" class="link-body-emphasis d-inline-flex text-decoration-none rounded">PHP</a></li>
                  <li><a href="?category=Python" class="link-body-emphasis d-inline-flex text-decoration-none rounded">Python
                      Programming</a></li>
                  <li><a href="?category=Java" class="link-body-emphasis d-inline-flex text-decoration-none rounded">Java</a></li>
                </ul>
              </div>
            </li>
            <li>
              <button class="btn btn-toggle d-inline-flex align-items-center rounded border-0 collapsed"
                data-bs-toggle="collapse" data-bs-target="#db-collapse" aria-expanded="false">
                DATABASE
              </button>
              <div class="collapse" id="db-collapse">
                <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                  <li><a href="?category=SQL" class="link-body-emphasis d-inline-flex text-decoration-none rounded">SQL</a>
                  </li>
                  <li><a href="?category=MongoDb" class="link-body-emphasis d-inline-flex text-decoration-none rounded">MongoDb</a></li>
                </ul>
              </div>
            </li>
            <li>
              <button class="btn btn-toggle d-inline-flex align-items-center rounded border-0 collapsed"
                data-bs-toggle="collapse" data-bs-target="#apti-collapse" aria-expanded="false">
                APTITUDE & REASONING
              </button>
              <div class="collapse" id="apti-collapse">
                <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                  <li><a href="?category=Aptitude" class="link-body-emphasis d-inline-flex text-decoration-none rounded">Aptitude</a>
                  </li>
                  <li><a href="?category=Logical%20Reasoning" class="link-body-emphasis d-inline-flex text-decoration-none rounded">Logical
                      Reasoning</a>
                  </li>
                </ul>
              </div>
            </li>
            <li>
              <button class="btn btn-toggle d-inline-flex align-items-center rounded border-0 collapsed"
                data-bs-toggle="collapse" data-bs-target="#verbal-collapse" aria-expanded="false">
                VERBAL ABILITY
              </button>
              <div class="collapse" id="verbal-collapse">
                <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                  <li><a href="?category=Verbal%20Ability" class="link-body-emphasis d-inline-flex text-decoration-none rounded">Verbal
                      Ability</a></li>
                  <li><a href="?category=Verbal%20Reasoning" class="link-body-emphasis d-inline-flex text-decoration-none rounded">Verbal
                      Reasoning</a>
                  </li>
                </ul>
              </div>
            </li>
          </ul>
        </div>
        </ul>
      </div>

      <!-- COLLAPSIBLE SIDEBAR FOR SMALL SCREENS -->
      <div class="d-lg-none">
        <button class="btn text-info-emphasis" data-bs-toggle="offcanvas" data-bs-target="#offcanvasSidebar"
          aria-controls="offcanvasSidebar">☰ Menu</button>

        <!-- OFFCANVAS SIDEBAR -->
        <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasSidebar"
          aria-labelledby="offcanvasSidebarLabel">
          <div class="offcanvas-header">
            <h5 id="offcanvasSidebarLabel">Start Quizzing</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
          </div>
          <div class="offcanvas-body">
            <ul class="list-unstyled">
              <li class="mb-1">
                <button class="btn btn-toggle d-inline-flex align-items-center rounded border-0 collapsed"
                  data-bs-toggle="collapse" data-bs-target="#prog-collapse-mobile"
                  aria-expanded="true">PROGRAMMING</button>
                <div class="collapse show" id="prog-collapse-mobile">
                  <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                    <li><a href="?category=C" class="link-body-emphasis d-inline-flex text-decoration-none rounded">C
                        Programming</a>
                    </li>
                    <li><a href="?category=CPP" class="link-body-emphasis d-inline-flex text-decoration-none rounded">C++
                        Programming</a></li>
                    <li><a href="?category=PHP" class="link-body-emphasis d-inline-flex text-decoration-none rounded">PHP</a></li>
                    <li><a href="?category=Python" class="link-body-emphasis d-inline-flex text-decoration-none rounded">Python
                        Programming</a></li>
                    <li><a href="?category=Java" class="link-body-emphasis d-inline-flex text-decoration-none rounded">Java</a></li>
                  </ul>
                </div>
              </li>
              <li class="mb-1">
                <button class="btn btn-toggle d-inline-flex align-items-center rounded border-0 collapsed"
                  data-bs-toggle="collapse" data-bs-target="#db-collapse-mobile" aria-expanded="false">
                  DATABASE
                </button>
                <div class="collapse" id="db-collapse-mobile">
                  <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                    <li><a href="?category=SQL" class="link-body-emphasis d-inline-flex text-decoration-none rounded">SQL</a>
                    </li>
                    <li><a href="?category=MongoDb" class="link-body-emphasis d-inline-flex text-decoration-none rounded">MongoDb</a></li>
                  </ul>
                </div>
              </li>
              <li class="mb-1">
                <button class="btn btn-toggle d-inline-flex align-items-center rounded border-0 collapsed"
                  data-bs-toggle="collapse" data-bs-target="#apti-collapse-mobile" aria-expanded="false">
                  AAPTITUDE AND REASONING
                </button>
                <div class="collapse" id="apti-collapse-mobile">
                  <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                    <li><a href="?category=Aptitude" class="link-body-emphasis d-inline-flex text-decoration-none rounded">Aptitude</a>
                    </li>
                    <li><a href="?category=Logical%20Reasoning" class="link-body-emphasis d-inline-flex text-decoration-none rounded">Logical
                        Reasoning</a>
                    </li>
                  </ul>
                </div>
              </li>
              <li class="mb-1">
                <button class="btn btn-toggle d-inline-flex align-items-center rounded border-0 collapsed"
                  data-bs-toggle="collapse" data-bs-target="#verbal-collapse-mobile" aria-expanded="false">
                  VERBAL ABILITY
                </button>
                <div class="collapse" id="verbal-collapse-mobile">
                  <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                    <li><a href="?category=Verbal%20Ability" class="link-body-emphasis d-inline-flex text-decoration-none rounded">Verbal
                        Ability</a></li>
                    <li><a href="?category=Verbal%20Reasoning" class="link-body-emphasis d-inline-flex text-decoration-none rounded">Verbal
                        Reasoning</a>
                    </li>
                  </ul>
                </div>
              </li>
            </ul>
          </div>
          </ul>
        </div>
      </div>

      <!-- QUIZ CONTAINER STARTS -->
      <div class="col">
        <!-- DIV FOR DISPLAYING ERROR -->
        <?php if (isset($error_message)): ?>
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo $error_message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        <?php else: ?>
          <!-- SCROLL CONTAINER STARTS -->
          <div class="container-fluid mt-3 scroll-container">
            <?php
            if (!isset($selected_category)) {
              echo "<div class='quiz-rules d-flex flex-column justify-content-center align-items-center h-75 text-primary-emphasis'>
                <h3>Welcome to the SkillQuest</h3>
                <p>Please read the rules before starting:</p>
                <ul>
                    <li>Choose a category to start the quiz.</li>
                    <li>You have 20 minutes to complete the quiz.</li>
                    <li>Each question has multiple-choice answers.</li>
                    <li>No negative marking for incorrect answers.</li>
                    </ul>
                    <h5>Good luck!</h5> </div>";
            } else {
            ?>
              <!-- DYNAMICALLY POPULATING QUIZ QUESTIONS -->
              <div class="text-center mt-1">
                <!-- DISPLAYING CATEGORY -->
                <h2>
                  <span class="badge text-bg-light text-primary-emphasis border">
                    <?php echo $selected_category ?>
                  </span>
                </h2>
              </div>
              <div class="container-fluid d-flex justify-content-between align-items-center">
                <!-- DISPLAYING START TEST BUTTON -->
                <div class="text-start">
                  <button id="starttestbtn" class="btn btn-primary-emphasis btn-md border rounded-pill px-4 my-2">Start Test</button>
                </div>
                <!-- DISPLAYING TIMER -->
                <div id="timerdisplay" class="text-end">
                  <h4 class="my-3">Timer: <span id="timer">20:00</span></h4>
                </div>
              </div>
              <form id="quiz-form" action="../templates/quiz_result.php" method="POST">
                <!-- HIDDEN FIELD FOR PASSING 'category' TO THE 'quiz_result' PAGE-->
                <input type="hidden" name="category" value="<?php echo htmlspecialchars($selected_category); ?>">
                <?php foreach ($questions as $i => $question): ?>
                  <div class="quiz-questions ps-lg-4 mb-3">
                    <h5>Question <?php echo $i + 1; ?>: <?php echo htmlspecialchars($question['question']); ?></h5>
                    <div class="form-check">
                      <input type="radio" class="form-check-input" name="question_<?php echo $question['id']; ?>" value="a" id="q<?php echo $question['id']; ?>_A">
                      <label class="form-check-label" for="q<?php echo $question['id']; ?>_A">
                        <?php echo htmlspecialchars($question['option_a']); ?>
                      </label>
                    </div>
                    <div class="form-check">
                      <input type="radio" class="form-check-input" name="question_<?php echo $question['id']; ?>" value="b" id="q<?php echo $question['id']; ?>_B">
                      <label class="form-check-label" for="q<?php echo $question['id']; ?>_B">
                        <?php echo htmlspecialchars($question['option_b']); ?>
                      </label>
                    </div>
                    <div class="form-check">
                      <input type="radio" class="form-check-input" name="question_<?php echo $question['id']; ?>" value="c" id="q<?php echo $question['id']; ?>_C">
                      <label class="form-check-label" for="q<?php echo $question['id']; ?>_C">
                        <?php echo htmlspecialchars($question['option_c']); ?>
                      </label>
                    </div>
                    <div class="form-check">
                      <input type="radio" class="form-check-input" name="question_<?php echo $question['id']; ?>" value="d" id="q<?php echo $question['id']; ?>_D">
                      <label class="form-check-label" for="q<?php echo $question['id']; ?>_D">
                        <?php echo htmlspecialchars($question['option_d']); ?>
                      </label>
                    </div>
                  </div>
                <?php endforeach; ?>
                <!-- SUBMIT BUTTON -->
                <button class="btn btn-success btn-md border rounded-pill px-4 my-2" id="submitbtn" name="submit" data-bs-toggle="modal" data-bs-target="#resultModal">Submit Quiz</button>
              </form>
            <?php } ?>
          <?php endif; ?>
          </div>
          <!-- QUIZ QUESTIONS ENDS HERE -->
      </div>
      <!-- QUIZ CONTAINER ENDS -->
    </div>
  </div>
  <!-- MAIN CONTAINER ENDS -->

  <!-- FOOTER SECTION STARTS -->
  <footer class="mt-4 mb-4 text-center text-info-emphasis">
    <p class="h5">SkillQuest - Knock The Answers</p>
  </footer>
  <!-- FOOTER SECTION ENDS -->

  <!-- INTERNAL JAVASCRIPT -->
  <script>
    // INITIAL TIMER SETTINGS
    let timeRemaining = 19 * 60 + 59; // 20 MINUTES
    let timerInterval;

    // START TEST BUTTON FUNCTIONALITY
    document.getElementById('starttestbtn').addEventListener('click', function() {
      document.getElementById('starttestbtn').style.display = 'none'; // HIDES START TEST BUTTON
      startTimer(); // STARTING THE TIMER
    });

    // TIMER FUNCTIONALITY
    function startTimer() {
      timerInterval = setInterval(function() {
        // CALCULATING MINUTES AND SECONDS
        let minutes = Math.floor(timeRemaining / 60);
        let seconds = timeRemaining % 60;

        // FORMATTING TIME - ADDING PREFIX ZERO WHERE NEEDED
        document.getElementById('timer').innerText = `${minutes}:${seconds < 10 ? '0' + seconds : seconds}`;
        // DECREASING REMAINING TIME
        timeRemaining--;

        // SUBMIT THE QUIZ UPON TIME COMPLETION
        if (timeRemaining < 0) {
          clearInterval(timerInterval);
          document.getElementById('quiz-form').submit();
        }
      }, 1000);
    }
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"></script>
</body>

</html>
<?
session_write_close();
mysqli_close($conn);
?>
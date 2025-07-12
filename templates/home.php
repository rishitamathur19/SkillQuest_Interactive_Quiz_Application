<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
  header("Location: ../signin.php");
  exit();
}
$username = $_SESSION['username'];
date_default_timezone_set('Asia/Kolkata');

include('../PHP Logic/db_config.php');

// FETCHING USER ID BASED ON USERNAME
$query = "SELECT `id` FROM `users` WHERE `username` = '$username'";
$result = mysqli_query($conn, $query);
$user_id = null;
if (!mysqli_num_rows($result) == 0) {
  $row = mysqli_fetch_assoc($result);
  $user_id = $row['id'];
}

// CHECKING IF QUIZ HAS BEEN SUBMITTED IN LAST 24 hours
$date_query = "SELECT `quiz_date` FROM `daily_quiz_results` WHERE `user_id` = '$user_id' ORDER BY `quiz_date` DESC LIMIT 1";
$date_result = mysqli_query($conn, $date_query);
if ($date_result && mysqli_num_rows($date_result) > 0) {
  $row = mysqli_fetch_assoc($date_result);
  $quiz_date = $row['quiz_date'];
  $current_time = time();
  $time_difference = $current_time - strtotime($quiz_date);
  if ($time_difference < 86400) {
    // Quiz completed within the last 24 hours, show result
    $show_quiz = false;
  } else {
    // More than 24 hours passed, allow quiz submission
    $show_quiz = true;
  }
} else {
  // User has never completed the quiz, so allow quiz submission
  $show_quiz = true;
}


// FETCHING QUIZ QUESTIONS 
$quiz_query = "SELECT * FROM `mcqs`";
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
      // FETCHING ONLY 5 QUESTIONS FOR DAILY QUIZ
      $questions_to_display = array_slice($questions, 0, 5);
    }
  }
}

// CALCULATING QUIZ RESULT
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // STORES THE TIMESTAMP WHEN THE QUIZ WAS SUBMITTED
  $_SESSION['quiz_completed_at'] = time();
  $quiz_time = date("H:i:s", $_SESSION['quiz_completed_at']);
  $quiz_date = date("Y-m-d");
  // Initialize variables
  $correct_answers = 0;
  $incorrect_answers = 0;
  $attempted_questions = 0;
  $correct = 0;
  $incorrect = 0;
  $attempted = 0;

  // LOOPING THROUGH SUBMITTED ANSWERS
  foreach ($_POST as $key => $user_answer) {
    if (strpos($key, 'question_') === 0) {
      $question_id = str_replace('question_', '', $key);

      // FETCHING CORRECT ANSWERS FROM DATABASE
      $quiz_query = "SELECT correct_answer FROM mcqs WHERE id = $question_id";
      $quiz_result = mysqli_query($conn, $quiz_query);

      if ($quiz_result && mysqli_num_rows($quiz_result) > 0) {
        $question = mysqli_fetch_assoc($quiz_result);
        $correct_answer = $question['correct_answer'];

        // COUNTING ATTEMPTED QUESTIONS
        if (!empty($user_answer)) {
          $attempted_questions++;

          // COMPARING USER'S ANSWER AND CORRECT ANSWER
          if ($user_answer === $correct_answer) {
            $correct_answers++;
          } else {
            $incorrect_answers++;
          }
        }
      }
    }
  }
  // INSERTING DAILY QUIZ RESULTS INTO DATABASE
  $insert_query = "INSERT INTO `daily_quiz_results` (`user_id`, `quiz_date`, `correct_answers`, `incorrect_answers`, `attempted_questions`, `total_questions`) 
           VALUES ('$user_id', '$quiz_date', '$correct_answers', '$incorrect_answers', '$attempted_questions', 5)";
  mysqli_query($conn, $insert_query);
  header("Location: ../templates/home.php");
}

// FETCHING JUST INSERTED DAILY QUIZ RESULT TO DISPLAY FOR 24 HOURS
$select_query = "SELECT * FROM `daily_quiz_results` WHERE `user_id` = $user_id ORDER BY `quiz_date` DESC LIMIT 1";
$result = mysqli_query($conn, $select_query);
if ($result) {
  $quiz_result = mysqli_fetch_assoc($result);
  if ($quiz_result) {
    $correct = $quiz_result['correct_answers'];
    $incorrect = $quiz_result['incorrect_answers'];
    $attempted = $quiz_result['attempted_questions'];
  }
}
?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SkillQuest | Home</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
  <!-- EXTERNAL CSS  -->
  <link rel="stylesheet" href="../templates/style.css">
  <!-- FAVICON  -->
  <link rel="icon" type="image/x-icon" href="../images/favicon.png">
  <style>
    body {
      padding-top: calc(3.5rem + 10px);
    }

    /* QUIZ OPTION LABELS */
    .form-check-label {
      display: flex;
      align-items: center;
    }

    /* CIRCULAR PROGRESS BAR */
    .circle-background,
    .circle-progress {
      transform: rotate(-90deg);
      transform-origin: 50% 50%;
    }

    .progress-bar-container {
      position: relative;
      display: inline-block;
      width: 150px;
      height: 150px;
    }

    .circle-text {
      position: absolute;
      top: 50%;
      left: 58%;
      transform: translate(-50%, -50%);
      font-size: 18px;
      font-weight: bold;
      color: #28a745;
    }

    @media (max-width: 600px) {
      .circle-text {
        font-size: 24px;
      }
    }

    @media (min-width: 601px) {
      .circle-text {
        font-size: 30px;
      }
    }

    /*CUSTOM CARDS */
    .creator-card {
      perspective: 100px;
      transition: transform 0.3s ease-in-out;
    }

    .creator-card:hover {
      transform: rotateY(10deg) scale(1.05);
      box-shadow: 0px 10px 20px rgba(0, 0, 0, 0.5);
    }
  </style>
</head>

<body>
  <!-- NAVBAR STARTS -->
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
            <a class="nav-link active mx-2" href="../templates/home.php">Home</a>
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
  <!-- NAVBAR ENDS-->

  <div id="alert-container"><!-- CONTAINER FOR ALERTS--></div>

  <!-- DAILY QUIZ CONTAINER STARTS-->
  <div class="container mt-4">
    <h2 class="ms-4 text-info-emphasis">Daily Quiz</h2>
    <!-- Daily Quiz Block STARTS -->
    <div class="bg-body-tertiary border p-5 rounded-3 mt-3">
      <!-- QUIZ WILL BE DISPLAYED HERE -->
      <div class="container" id="quiz">

        <?php if ($show_quiz): ?>

          <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
            <?php foreach ($questions_to_display as $i => $question): ?>
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
            <div class="d-flex justify-content-center">
              <button class="btn btn-success btn-md border rounded-pill px-4 my-2" name="submit" id="submitbtn">Submit</button>
            </div>
          </form>

        <?php else: ?>

          <!-- RESULT CONTAINER STARTS -->
          <div class="row d-flex justify-content-center text-center">
            <div class="progress-bar-container">
              <svg width="150" height="150" viewBox="0 0 150 150" xmlns="http://www.w3.org/2000/svg">
                <circle class="circle-background" cx="75" cy="75" r="70" stroke="#ddd" stroke-width="10" fill="none"></circle>
                <circle class="circle-progress" cx="75" cy="75" r="70" stroke="#28a745" stroke-width="10" fill="none" stroke-dasharray="440" stroke-dashoffset="440"></circle>
              </svg>
              <div class="circle-text" id="correct-answers-text"></div>
            </div>
            <h4 class="mt-4">Attempted Questions: <?php echo  $attempted; ?></h4>
            <h4 class="mt-1">Correct Answers: <?php echo "<span class='text-success'>" . $correct;
                                              "</span>" ?></h4>
            <h4 class="mt-1">Incorrect Answers: <?php echo "<span class='text-danger'>" . $incorrect;
                                                "</span>" ?></h4>
          </div>
        <?php endif; ?>
        <!-- RESULT CONTAINER ENDS -->
      </div>
    </div>
  </div>
  <!-- Daily Quiz Block ENDS -->
  </div>
  <!-- DAILY QUIZ CONTAINER ENDS-->
  <br>

  <!-- EXPLORE MORE CONTAINER STARTS -->
  <div class="container mt-4">
    <h2 class="ms-4 text-info-emphasis mb-4">Explore More</h2>
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4 text-center d-flex justify-content-center">
      <div class="col d-flex justify-content-center">
        <button class="card creator-card p-2" onclick="window.location.href='quiz.php?category=C'" style="width: 15rem; height: 14rem;">
          <img src="../images/categories/c.png" class="card-img-top w-75 mx-auto" alt="C">
          <div class="card-body">
            <h5 class="card-title">C</h5>
          </div>
        </button>
      </div>
      <div class="col d-flex justify-content-center">
        <button class="card creator-card p-2" onclick="window.location.href='quiz.php?category=CPP'" style="width: 15rem; height: 14rem;">
          <img src="../images/categories/CPP.png" class="card-img-top w-75 mx-auto" alt="CPP">
          <div class="card-body">
            <h5 class="card-title">C++</h5>
          </div>
        </button>
      </div>
      <div class="col d-flex justify-content-center">
        <button class="card creator-card p-2 py-5" onclick="window.location.href='quiz.php?category=PHP'" style="width: 15rem; height: 14rem;">
          <img src="../images/categories/php.png" class="card-img-top w-75 mx-auto" alt="PHP">
          <div class="card-body">
            <h5 class="card-title">PHP</h5>
          </div>
        </button>
      </div>
      <div class="col d-flex justify-content-center">
        <button class="card creator-card p-2" onclick="window.location.href='quiz.php?category=Python'" style="width: 15rem; height: 14rem;">
          <img src="../images/categories/python.png" class="card-img-top w-75 mx-auto" alt="Python">
          <div class="card-body">
            <h5 class="card-title">Python</h5>
          </div>
        </button>
      </div>
      <div class="col d-flex justify-content-center">
        <button class="card creator-card p-3 py-5" onclick="window.location.href='quiz.php?category=Java'" style="width: 15rem; height: 14rem;">
          <img src="../images/categories/java.png" class="card-img-top w-75 mx-auto" alt="Java">
          <div class="card-body">
            <h5 class="card-title">Java</h5>
          </div>
        </button>
      </div>
      <div class="col d-flex justify-content-center">
        <button class="card creator-card p-2 py-5" onclick="window.location.href='quiz.php?category=SQL'" style="width: 15rem; height: 14rem;">
          <img src="../images/categories/sql.png" class="card-img-top w-75 mx-auto" alt="SQL">
          <div class="card-body">
            <h5 class="card-title">SQL</h5>
          </div>
        </button>
      </div>
      <div class="col d-flex justify-content-center">
        <button class="card creator-card p-3 py-5" onclick="window.location.href='quiz.php?category=MongoDb'" style="width: 15rem; height: 14rem;">
          <img src="../images/categories/mongodb.png" class="card-img-top w-100 mx-auto" alt="MongoDb">
          <div class="card-body">
            <h5 class="card-title">MongoDB</h5>
          </div>
        </button>
      </div>
      <div class="col d-flex justify-content-center">
        <button class="card creator-card p-2 py-4" onclick="window.location.href='quiz.php?category=Aptitude'" style="width: 15rem; height: 14rem;">
          <img src="../images/categories/aptitude.png" class="card-img-top w-50 mx-auto" alt="Aptitude">
          <div class="card-body">
            <h5 class="card-title">Aptitude</h5>
          </div>
        </button>
      </div>
      <div class="col d-flex justify-content-center">
        <button class="card creator-card p-2 py-4" onclick="window.location.href='quiz.php?category=Logical%20Reasoning'" style="width: 15rem; height: 14rem;">
          <img src="../images/categories/reasoning.png" class="card-img-top w-50 mx-auto" alt="Logical Reasoning">
          <div class="card-body">
            <h5 class="card-title">Logical Reasoning</h5>
          </div>
        </button>
      </div>
      <div class="col d-flex justify-content-center">
        <button class="card creator-card p-2 py-5" onclick="window.location.href='quiz.php?category=Verbal%20Ability'" style="width: 15rem; height: 14rem;">
          <img src="../images/categories/verbal.png" class="card-img-top w-50 mx-auto" alt="Verbal Ability">
          <div class="card-body">
            <h5 class="card-title">Verbal Ability</h5>
          </div>
        </button>
      </div>
      <div class="col d-flex justify-content-center">
        <button class="card creator-card p-2 py-4" onclick="window.location.href='quiz.php?category=Verbal%20Reasoning'" style="width: 15rem; height: 14rem;">
          <img src="../images/categories/verbalreason.png" class="card-img-top w-50 mx-auto" alt="SQL">
          <div class="card-body">
            <h5 class="card-title">Verbal Reasoning</h5>
          </div>
        </button>
      </div>
    </div>
  </div>
  <br>
  <!-- EXPLORE MORE CONTAINER ENDS -->

  <!-- ABOUT CONTAINER STARTS -->
  <div class="container">
    <section class="pt-3 text-center container">
      <div class="row py-lg-3">
        <div class="col-lg-10 col-md-10 mx-auto">
          <h1 class="ms-4 text-info-emphasis mb-2 fw-normal">About SkillQuest</h1>
          <p class="lead text-body-secondary d-flex justify-content-evenly">SkillQuest is a simple, interactive quiz app designed to test your knowledge across various categories. Challenge yourself with multiple-choice questions, track your progress, and have fun learning new things. Perfect for anyone looking to improve their skills in an engaging way!</p>
          <p>
            <a href="../templates/categories.php" class="btn btn-primary my-2">View Categories</a>
            <a href="../templates/quiz.php" class="btn btn-secondary my-2">Start Quizzing</a>
          </p>
        </div>
      </div>
    </section>
  </div>
  <!-- ABOUT CONTAINER ENDS -->

  <!-- FOOTER SECTION STARTS -->
  <footer class="mt-1 mb-4 text-center text-info-emphasis">
    <p class="h5">SkillQuest - Knock The Answers</p>
  </footer>
  <!-- FOOTER SECTION ENDS -->

  <!-- INTERNAL JS -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const correctAnswers = <?php echo $correct; ?>;
      const totalQuestions = <?php echo count($questions_to_display); ?>;

      // CALCULAYING STROKE-DASHOFFSET BASED ON NO. OF CORRECT ANSWERS
      const percentage = (correctAnswers / totalQuestions) * 100;
      const dashOffset = 440 - (440 * percentage) / 100;

      // SELECTING THE CIRCULAR PROGRESS BAR AND CORRECT ANSWERS DIV
      const circleProgress = document.querySelector('.circle-progress');
      const correctAnswersText = document.getElementById('correct-answers-text');

      // UPDATING THE TEXT IN THE CIRCLE
      correctAnswersText.textContent = `${correctAnswers}/${totalQuestions}`;

      // RESET THE CIRCLE STROKE-DASHOFFSET TO FULL CIRCLE, SO THE ANIMATION STARTS AGAIN
      circleProgress.style.strokeDashoffset = 440;

      // APPLYING THE ANIMATION AFTER A SMALL DELAY
      setTimeout(function() {
        circleProgress.style.transition = 'stroke-dashoffset 2s ease';
        circleProgress.style.strokeDashoffset = dashOffset;
      }, 100);
    });
  </script>

  <!-- EXTERNAL JS -->
  <script src="/js/home-script.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"></script>
</body>

</html>
<?php
session_write_close();
mysqli_close($conn);
?>
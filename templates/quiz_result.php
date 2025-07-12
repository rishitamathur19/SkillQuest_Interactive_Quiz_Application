<?php
error_reporting(E_ERROR | E_PARSE);

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

// FETCHING QUIZ RESULTS
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // GET THE CATEGORY VALUE FROM THE FORM SUBMISSION
  $category = $_POST['category'];
  // SANITIZING INPUT TO AVOID SECURITY RISKS
  $category = htmlspecialchars($category);

  // STORES THE TIMESTAMP WHEN THE QUIZ WAS COMPLETED
  $_SESSION['quiz_completed_at'] = time();
  $quiz_time = date("H:i:s", $_SESSION['quiz_completed_at']);
  $quiz_date = date("Y-m-d");

  // INITIALIZING VARIABLES
  $correct_answers = 0;
  $incorrect_answers = 0;
  $attempted_questions = 0;

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
  // INSERTING QUIZ RESULTS INTO DATABASE
  $insert_query = "INSERT INTO `quiz_results` (`user_id`, `quiz_date`, `category`, `correct_answers`, `incorrect_answers`, `attempted_questions`, `total_questions`) 
VALUES ('$user_id', '$quiz_date', '$category', '$correct_answers', '$incorrect_answers', '$attempted_questions', 10)";
  mysqli_query($conn, $insert_query);
}

$correct = 0;
$incorrect = 0;
$attempted = 0;
// FOR DISPLAYING QUIZ RESULTS
$category_results_query = "SELECT * FROM `quiz_results` WHERE `user_id` = $user_id ORDER BY `id` DESC LIMIT 1";
$results_category = mysqli_query($conn, $category_results_query);

if ($results_category && mysqli_num_rows($results_category) > 0) {
  // FETCHING LAST RESULT AND DISPLAYING
  $row = mysqli_fetch_assoc($results_category);

  $category =  $row['category'];
  $quiz_date =  $row['quiz_date'];
  $correct =  $row['correct_answers'];
  $incorrect = $row['incorrect_answers'];
  $attempted = $row['attempted_questions'];
  $total = $row['total_questions'];
} else {
  // IF QUERY FAILS, SHOW AN ERROR MESSAGE
  echo "Error fetching results: " . mysqli_error($conn);
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
    /* BACK TO QUIZ BUTTON */
    .back-btn {
      position: absolute;
      top: 20px;
      left: 20px;
      border: none;
      background-color: transparent;
      font-size: 18px;
      font-weight: bold;
      text-decoration: underline;
      cursor: pointer;
    }

    .back-btn:hover {
      font-size: 20px;
      font-weight: bold;
      text-decoration: underline;
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
  </style>
</head>

<body>
  <!-- BACK TO QUIZ BUTTON -->
  <button class="back-btn text-primary-emphasis px-3 link-offset-2" onclick="window.location.href='../templates/quiz.php'">← Back to Quiz</button>
  <br><br>
  <div id="alert-container"><!-- CONTAINER FOR ALERTS--></div>

  <!-- DAILY QUIZ CONTAINER STARTS-->
  <div class="container mt-4">
    <h2 class="ms-4 text-info-emphasis"> <?php echo $category ?> - QUIZ RESULTS</h2>
    <!-- Daily Quiz Block STARTS -->
    <div class="bg-body-tertiary border p-5 rounded-3 mt-3">
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
      <!-- RESULT CONTAINER ENDS -->
    </div>
  </div>
  </div>
  <!-- Daily Quiz Block ENDS -->
  </div>
  <!-- DAILY QUIZ CONTAINER ENDS-->
  <br>

  <!-- DISPLAYING CORRECT ANSWERS WITH QUESTIONS STARTS -->
  <div class="container">
    <h2 class="ms-4 text-info-emphasis"> TRACK YOUR ANSWERS</h2>
    <br>
    <?php
    // FETCHING CATEGORY ID COREESPONDING TO SELECTED CATEGORY
    $cat_id = null; // INITIALIZING CATEGORY ID TO NULL
    if ($category) {
      urldecode($category);
      $category_query = "SELECT `id` FROM `categories` WHERE `name` = '$category'";
      $category_result = mysqli_query($conn, $category_query);

      // CHECKING IF RESULT WAS FOUND
      if ($category_result && mysqli_num_rows($category_result) > 0) {
        $category_row = mysqli_fetch_assoc($category_result); // FETCHING CATEGORY ID
        $cat_id = $category_row['id'];  // STORING CATEGORY ID
      }
    }
    // FETCHING QUESTIONS TO DISPLAY
    $questions_query = "SELECT * FROM `mcqs` WHERE `category_id` = '$cat_id'";
    $questions_result = mysqli_query($conn, $questions_query);

    if ($questions_result) {
      while ($question = mysqli_fetch_assoc($questions_result)) {
        $question_id = $question['id'];
        $correct_answer = $question['correct_answer'];
        $user_answer = $_POST['question_' . $question_id] ?? null; // TO GET USER'S ANSWER

        // DISPLAYING THE QUESTIONS
        echo "<div class='quiz-questions ps-lg-4 mb-3'>";
        echo "<h5>Question: " . htmlspecialchars($question['question']) . "</h5>";

        // LOOPING THROUGH OPTIONS
        $options = ['a', 'b', 'c', 'd'];
        foreach ($options as $option) {
          $option_label = strtoupper($option); // 'A', 'B', 'C', 'D'
          $option_text = $question['option_' . $option];

          // CHECKING IF OPTION IS CORRECT OR INCORRECT
          $change_color = '';
          if ($correct_answer === $option && $user_answer === $option) {
            $change_color = 'color: green; font-weight: bolder;'; // CORRECT ANSWER
          } elseif ($user_answer === $option) {
            $change_color = 'color: red; font-weight: bolder;'; // INCORRECT ANSWERS
          } elseif ($correct_answer === $option) {
            $change_color = 'color: green; font-weight: bolder;'; // UNATTEMPTED CORRECT ANSWERS
          }

          echo "<div class='form-check' style='$change_color'>
                        <input type='radio' class='form-check-input' name='question_$question_id' value='$option' id='q{$question_id}_$option' disabled>
                        <label class='form-check-label' for='q{$question_id}_$option'>
                          $option_label: " . htmlspecialchars($option_text) . "
                        </label>
                      </div>";
        }
        echo "</div>";
      }
      echo "<div class='display'>
      <h5 class='fw-bold'> Note: </h5>
      <ul>
        <li> <h6>Green for Correct Answer of both Attempted and Unattempted Questions.</h6> </li>
        <li> <h6>Red for Incorrect Answer.</h6> </li>
        </ul>
      </div>";
    } else {
      echo "Error fetching questions: " . mysqli_error($conn);
    }
    ?>
  </div>
  <br>
  <!-- DISPLAYING QUESTIONS WITH OPTIONS ENDS -->

  <!-- DISPLAYING CORRECT ANSWERS WITH QUESTIONS ENDS -->

  <!-- FOOTER SECTION STARTS -->
  <footer class="mt-1 mb-4 text-center text-info-emphasis">
    <p class="h5">SkillQuest - Knock The Answers</p>
  </footer>
  <!-- FOOTER SECTION ENDS -->

  <!-- INTERNAL JS -->
  <script>
    // PROGRESS BAR FUNCTIONALITY
    document.addEventListener('DOMContentLoaded', function() {
      const correctAnswers = <?php echo $correct; ?>;
      const totalQuestions = 10;

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
// REDIRECTIONG AFTER FORM SUBMISSION TO AVAOID RESUBMISSION ON PAGE RELOAD
$_SESSION['quiz_completed'] = true;
header("Location: ../templates/quiz_result.php"); // REDIRECTING TO PAGE AGAIN
exit();
?>
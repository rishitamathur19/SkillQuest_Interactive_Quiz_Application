<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
  header("Location: ../signin.php");
  exit();
}
?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SkillQuest | Categories</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <!-- EXTERNAL CSS  -->
  <link rel="stylesheet" href="../templates/style.css">
  <link rel="stylesheet" href="../css/categories_style.css">
  <!-- FAVICON  -->
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
            <a class="nav-link active mx-2" href="../templates/categories.php">Categories</a>
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
  <br><br><br><br> <!-- TO STOP CONTENT OVERLAPPING WITH NAVBAR(TEMPORARTY FIX) -->
  <!-- CATEGORIES PAGE CONTENT START -->
  <div class="text-center">
    <h2><span class="badge text-bg-light text-primary-emphasis border">Browse Categories</span></h2>
  </div>
  <!-- CATEGORY-1 PROGRAMMING -->
  <div class="container accordion my-4" id="accordionExample">
    <div class="accordion-item">
      <h2 class="accordion-header">
        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne"
          aria-expanded="true" aria-controls="collapseOne">
          PROGRAMMING
        </button>
      </h2>
      <div id="collapseOne" class="accordion-collapse collapse show" data-bs-parent="#accordionExample">
        <div class="accordion-body">
          <div class="row mb-3">
            <div class="col-sm-4 mb-3 mb-sm-0">
              <a href="quiz.php?category=C" class="text-decoration-none">
                <div class="card c">
                  <div class="card-body">
                    <h5 class="card-title">C Programming</h5>
                  </div>
                </div>
              </a>
            </div>
            <div class="col-sm-4">
              <a href="quiz.php?category=CPP" class="text-decoration-none">
                <div class="card cpp">
                  <div class="card-body">
                    <h5 class="card-title">C++ Programming</h5>
                  </div>
                </div>
              </a>
            </div>
            <div class="col-sm-4">
              <a href="quiz.php?category=PHP" class="text-decoration-none">
                <div class="card php">
                  <div class="card-body">
                    <h5 class="card-title">PHP</h5>
                  </div>
                </div>
              </a>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-4 mb-3 mb-sm-0">
              <a href="quiz.php?category=Python" class="text-decoration-none">
                <div class="card python">
                  <div class="card-body">
                    <h5 class="card-title">Python Programming</h5>
                  </div>
                </div>
              </a>
            </div>
            <div class="col-sm-4">
              <a href="quiz.php?category=Java" class="text-decoration-none">
                <div class="card java">
                  <div class="card-body">
                    <h5 class="card-title">JAVA</h5>
                  </div>
                </div>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- CATEGORY-2 DATABSE -->
    <div class="accordion-item">
      <h2 class="accordion-header">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo"
          aria-expanded="false" aria-controls="collapseTwo">
          DATABASE
        </button>
      </h2>
      <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
        <div class="accordion-body">
          <div class="row mb-3">
            <div class="col-sm-4 mb-3 mb-sm-0">
              <a href="quiz.php?category=SQL" class="text-decoration-none">
                <div class="card sql">
                  <div class="card-body">
                    <h5 class="card-title">SQL</h5>
                  </div>
                </div>
              </a>
            </div>
            <div class="col-sm-4">
              <a href="quiz.php?category=MongoDB" class="text-decoration-none">
                <div class="card mongodb">
                  <div class="card-body">
                    <h5 class="card-title">MongoDB</h5>
                  </div>
                </div>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- CATEGORY-3 APTITUDE -->
    <div class="accordion-item">
      <h2 class="accordion-header">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
          data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
          APTITUDE AND REASONING
        </button>
      </h2>
      <div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
        <div class="accordion-body">
          <div class="row">
            <div class="col-sm-4 mb-3 mb-sm-0">
              <a href="quiz.php?category=Aptitude" class="text-decoration-none">
                <div class="card aptitude">
                  <div class="card-body">
                    <h5 class="card-title">Aptitude</h5>
                  </div>
                </div>
              </a>
            </div>
            <div class="col-sm-4">
              <a href="quiz.php?category=Logical%20Reasoning" class="text-decoration-none">
                <div class="card reasoning">
                  <div class="card-body">
                    <h5 class="card-title">Logical Reasoning</h5>
                  </div>
                </div>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- CATEGORY-4 VERBAL ABILITY -->
    <div class="accordion-item">
      <h2 class="accordion-header">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
          data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
          VERBAL ABILITY
        </button>
      </h2>
      <div id="collapseFour" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
        <div class="accordion-body">
          <div class="row mb-3">
            <div class="col-sm-4 mb-3 mb-sm-0">
              <a href="quiz.php?category=Verbal%20Ability" class="text-decoration-none">
                <div class="card verbal">
                  <div class="card-body">
                    <h5 class="card-title">Verbal Ability</h5>
                  </div>
                </div>
              </a>
            </div>
            <div class="col-sm-4">
              <a href="quiz.php?category=Verbal%20Reasoning" class="text-decoration-none">
                <div class="card verbalreason">
                  <div class="card-body">
                    <h5 class="card-title">Verbal Reasoning</h5>
                  </div>
                </div>
              </a>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- CATEGORIES PAGE CONTENT ENDS -->


  <!-- FOOTER SECTION STARTS -->
  <footer class="mt-4 mb-4 text-center text-info-emphasis">
    <p class="h5">SkillQuest - Knock The Answers</p>
  </footer>
  <!-- FOOTER SECTION STARTS -->

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"></script>
</body>

</html>
<?
session_write_close();
?>
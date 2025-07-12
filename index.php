<?php
session_start(); // STARTING THE SESSION

// UNSETTING UNWANTED SESSION VARIABLES
unset($_SESSION['logged_in']);  // REMOVES LOGIN STATUS
unset($_SESSION['username']);  // REMOVES USERNAME
session_write_close();
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SkillQuest | Interactive Quiz App</title>
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

        .cover-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
            text-align: center;
            flex-direction: column;
        }

        .logo {
            width: 250px;
            height: 70px;
        }

        h1 {
            font-size: 4rem;
            font-weight: 600;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        .lead {
            font-size: 1.6rem;
            font-weight: 400;
            margin-bottom: 30px;
            line-height: 1.8;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);
        }

        .btn-light {
            background-color: #fff;
            color: #06597a;
            font-weight: 600;
            padding: 16px 36px;
            font-size: 1.3rem;
            border-radius: 50px;
            text-transform: uppercase;
            border: none;
            transition: all 0.3s ease;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .btn-light:hover {
            background-color: #02a8b5;
            color: #fff;
            transform: translateY(-5px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.3);
        }

        footer {
            margin-top: auto;
            font-size: 1rem;
            opacity: 0.5;
            text-align: center;
        }

        @media (max-width: 768px) {
            h1 {
                font-size: 2.5rem;
            }

            .lead {
                font-size: 1.3rem;
            }

            .btn-light {
                padding: 14px 30px;
                font-size: 1.1rem;
            }
        }
    </style>
</head>

<body>
    <div class="cover-container p-3 mx-auto">
        <header class="mb-auto">
            <img src="images/logo.png" alt="SkillQuestLogo" class="logo">
        </header>

        <main class="px-3">
            <h1>Welcome to SkillQuest!</h1>
            <p class="lead">SkillQuest is an interactive quiz app that helps you learn new skills, track your progress,
                and challenge yourself with fun and engaging quizzes.</p>
            <p class="lead">
                <a href="signin.php" class="btn btn-lg btn-light">Start Quizzing</a>
            </p>
        </main>

        <footer>
            <p class="h6">SkillQuest - Knock The Answers</p>
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"></script>
</body>

</html>
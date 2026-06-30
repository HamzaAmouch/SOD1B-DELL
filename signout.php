<?php
session_start();

// Controle of er op een knop is gedrukt
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Gebruiker wil niet uitloggen
    if (isset($_POST['cancel'])) {
        header("Location: index.php");
        exit();
    }

    // Gebruiker bevestigt uitloggen
    if (isset($_POST['logout'])) {

        // Verwijder de belangrijkste sessievariabelen
        unset($_SESSION['user_id']);
        unset($_SESSION['rol']);

        // Leeg de volledige sessie
        $_SESSION = array();

        // Vernietig de sessie
        session_destroy();

        // Terug naar homepage
        header("Location: index.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Uitloggen</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin-top: 100px;
        }

        .btn {
            padding: 10px 20px;
            margin: 10px;
            cursor: pointer;
        }

        .cancel {
            background-color: #ccc;
        }

        .logout {
            background-color: #d9534f;
            color: white;
        }
    </style>
</head>
<body>

    <h2>Weet u zeker dat u zich wilt afmelden?</h2>

    <form method="post">
        <button type="submit" name="cancel" class="btn cancel">
            Breek af
        </button>

        <button type="submit" name="logout" class="btn logout">
            Uitloggen
        </button>
    </form>

</body>
</html>
``
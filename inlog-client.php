<?php
session_start();
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Inloggen klant</title>
    <link rel="stylesheet" type="text/css" href="company.css">
</head>
<body>
    <?php include 'nav.html'; ?>
    <main>
        <h1>Inloggen klant</h1>
        <form action="inlog-client-exec.php" method="post" class="login-form">
            <div>
                <label for="email">E-mailadres klant*</label><br>
                <input type="email" id="email" name="email" required>
            </div>
            <div>
                <label for="password">Wachtwoord*</label><br>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="button-row">
                <button type="button" onclick="window.location.href='index.php'">Breek af</button>
                <button type="submit">Log in</button>
            </div>
        </form>
    </main>
</body>
</html>

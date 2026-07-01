<?php
session_start();

if (isset($_SESSION["benJeErAl"]) && $_SESSION["benJeErAl"] && isset($_SESSION["SoortToegang"]) && $_SESSION["SoortToegang"] === "Beheer") {
    header("Location: index.php");
    exit();
}

$message = "";
if (isset($_SESSION["login_error"])) {
    $message = $_SESSION["login_error"];
    unset($_SESSION["login_error"]);
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Beheerder inloggen</title>
    <link rel="stylesheet" type="text/css" href="company.css">
</head>
<body>
    <?php include "nav.html"; ?>

    <main>
        <h2>Inloggen als beheerder</h2>

        <?php if ($message !== "") : ?>
            <p style="color: red; font-weight: bold;"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>

        <form action="inlog-admin-exec.php" method="post">
            <p>
                <label for="email">Email-adres</label><br>
                <input type="email" id="email" name="email" required>
            </p>

            <p>
                <label for="pswrd">Wachtwoord</label><br>
                <input type="password" id="pswrd" name="pswrd" required>
            </p>

            <p>
                <button type="submit" name="cancel" value="1">Breek af</button>
                <button type="submit" name="login" value="1">Log in</button>
            </p>
        </form>
    </main>
</body>
</html>

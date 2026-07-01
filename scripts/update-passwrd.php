<?php
session_start();

if (!isset($_SESSION["benJeErAl"]) || $_SESSION["benJeErAl"] !== true || ($_SESSION["SoortToegang"] ?? "") !== "Klant") {
    echo "<h2>Deze functie is alleen toegankelijk voor een ingelogde klant.</h2>";
    echo "<p><a href='../index.php'>Terug naar home</a></p>";
    exit;
}

require_once "../dbconnect.php";

$showForm = true;
$errorMessage = "";
$successMessage = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["cancel"])) {
        header("Location: ../index.php");
        exit;
    }

    if (!isset($_POST["step"]) || $_POST["step"] !== "2") {
        header("Location: update-passwrd.php");
        exit;
    }

    $clientId = (int)($_SESSION["welkNummerIsDit"] ?? 0);
    $currentPassword = $_POST["current_password"] ?? "";
    $newPassword = $_POST["new_password"] ?? "";
    $repeatPassword = $_POST["repeat_password"] ?? "";

    try {
        $selectQuery = $db->prepare("SELECT pswrd FROM client WHERE id = :clientid");
        $selectQuery->bindValue(":clientid", $clientId, PDO::PARAM_INT);
        $selectQuery->execute();

        if ($selectQuery->rowCount() !== 1) {
            $errorMessage = "De klantgegevens konden niet worden gevonden.";
        } else {
            $clientData = $selectQuery->fetch(PDO::FETCH_ASSOC);

            if (!password_verify($currentPassword, $clientData["pswrd"])) {
                $errorMessage = "Het huidige wachtwoord is onjuist.";
            } elseif ($newPassword !== $repeatPassword) {
                $errorMessage = "De twee nieuwe wachtwoorden komen niet overeen.";
            } else {
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

                $updateQuery = $db->prepare("UPDATE client SET pswrd = :newpassword WHERE id = :clientid");
                $updateQuery->bindValue(":newpassword", $hashedPassword);
                $updateQuery->bindValue(":clientid", $clientId, PDO::PARAM_INT);
                $updateQuery->execute();

                $successMessage = "Uw wachtwoord is gewijzigd.";
                $showForm = false;
            }
        }
    } catch (PDOException $e) {
        die("Fout bij het wijzigen van het wachtwoord: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../company.css">
    <title>Wachtwoord wijzigen</title>
</head>
<body>
    <h1>Wachtwoord wijzigen</h1>

    <?php if (!empty($errorMessage)): ?>
        <p><strong><?php echo htmlspecialchars($errorMessage); ?></strong></p>
    <?php endif; ?>

    <?php if (!empty($successMessage)): ?>
        <p><strong><?php echo htmlspecialchars($successMessage); ?></strong></p>
        <p><a href="../index.php">Terug naar home</a></p>
    <?php endif; ?>

    <?php if ($showForm): ?>
        <form method="post" action="update-passwrd.php">
            <input type="hidden" name="step" value="2">
            <p>
                <label for="current_password">Huidig wachtwoord:</label><br>
                <input type="password" id="current_password" name="current_password" required>
            </p>
            <p>
                <label for="new_password">Nieuw wachtwoord:</label><br>
                <input type="password" id="new_password" name="new_password" required>
            </p>
            <p>
                <label for="repeat_password">Herhaal nieuw wachtwoord:</label><br>
                <input type="password" id="repeat_password" name="repeat_password" required>
            </p>
            <p>
                <button type="submit" name="submit" value="wijzigen">Wijzig wachtwoord</button>
                <button type="submit" name="cancel" value="annuleren">Annuleren</button>
            </p>
        </form>
    <?php endif; ?>
</body>
</html>
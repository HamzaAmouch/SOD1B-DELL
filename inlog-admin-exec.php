<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit();
}

if (isset($_POST["cancel"])) {
    header("Location: index.php");
    exit();
}

if (!isset($_POST["login"]) || !isset($_POST["email"]) || !isset($_POST["pswrd"])) {
    $_SESSION["login_error"] = "Combinatie van email-adres en wachtwoord is niet correct";
    header("Location: inlog-admin.php");
    exit();
}

$email = trim($_POST["email"]);
$password = trim($_POST["pswrd"]);

if ($email === "" || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
    $_SESSION["login_error"] = "Combinatie van email-adres en wachtwoord is niet correct";
    header("Location: inlog-admin.php");
    exit();
}

require_once "dbconnect.php";

try {
    $stmt = $db->prepare("SELECT id, first_name, last_name, isadmin, pswrd FROM client WHERE email = :email");
    $stmt->bindValue(":email", $email);
    $stmt->execute();

    if ($stmt->rowCount() !== 1) {
        $_SESSION["login_error"] = "Combinatie van email-adres en wachtwoord is niet correct";
        header("Location: inlog-admin.php");
        exit();
    }

    $client = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!isset($client["isadmin"]) || strtoupper($client["isadmin"]) !== "J") {
        $_SESSION["login_error"] = "Combinatie van email-adres en wachtwoord is niet correct";
        header("Location: inlog-admin.php");
        exit();
    }

    if (!isset($client["pswrd"]) || !password_verify($password, $client["pswrd"])) {
        $_SESSION["login_error"] = "Combinatie van email-adres en wachtwoord is niet correct";
        header("Location: inlog-admin.php");
        exit();
    }

    $_SESSION["benJeErAl"] = true;
    $_SESSION["welkNummerIsDit"] = (int)$client["id"];
    $_SESSION["wieBenJeDan"] = trim($client["first_name"] . " " . $client["last_name"]);
    $_SESSION["SoortToegang"] = "Beheer";

    $_SESSION["login_success"] = "welkom " . $_SESSION["wieBenJeDan"] . ", inloggen als beheerder is gelukt";
    header("Location: index.php");
    exit();
} catch (PDOException $e) {
    $_SESSION["login_error"] = "Combinatie van email-adres en wachtwoord is niet correct";
    header("Location: inlog-admin.php");
    exit();
}

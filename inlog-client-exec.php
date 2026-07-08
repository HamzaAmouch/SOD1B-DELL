<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

require_once 'dbconnect.php';

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

function invalidCredentials() {
    header('Refresh: 4; url=index.php');
    header('Content-Type: text/html; charset=utf-8');
    echo '<!DOCTYPE html><html lang="nl"><head><meta charset="UTF-8"><title>Inloggen</title>';
    echo '<link rel="stylesheet" type="text/css" href="company.css"></head><body>';
    include 'nav.html';
    echo '<main><h1>Combinatie van email-adres en wachtwoord is niet correct</h1>';
    echo '<p>U wordt teruggestuurd naar de homepage.</p>';
    echo '</main></body></html>';
    exit;
}

if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
    invalidCredentials();
}

try {
    $stmt = $db->prepare('SELECT id, first_name, last_name, pswrd FROM client WHERE email = :email');
    $stmt->bindValue(':email', $email);
    $stmt->execute();
    $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($clients) !== 1) {
        invalidCredentials();
    }

    $client = $clients[0];
    $hash = $client['pswrd'] ?? '';

    if (!password_verify($password, $hash)) {
        invalidCredentials();
    }

    $_SESSION['benJeErAl'] = true;
    $_SESSION['welkNummerIsDit'] = $client['id'];
    $_SESSION['wieBenJeDan'] = $client['first_name'] . ' ' . $client['last_name'];
    $_SESSION['SoortToegang'] = 'Klant';

    header('Refresh: 3; url=index.php');
    header('Content-Type: text/html; charset=utf-8');
    echo '<!DOCTYPE html><html lang="nl"><head><meta charset="UTF-8"><title>Inloggen gelukt</title>';
    echo '<link rel="stylesheet" type="text/css" href="company.css"></head><body>';
    include 'nav.html';
    echo '<main><h1>welkom , inloggen is gelukt</h1>';
    echo '<p>U wordt teruggestuurd naar de homepage.</p>';
    echo '</main></body></html>';
    exit;
} catch (PDOException $e) {
    header('Content-Type: text/html; charset=utf-8');
    echo '<!DOCTYPE html><html lang="nl"><head><meta charset="UTF-8"><title>Fout</title></head><body>';
    echo '<main><h1>Er is een fout opgetreden.</h1><p>' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . '</p>';
    echo '<p><a href="index.php">Terug naar homepage</a></p></main></body></html>';
    exit;
}

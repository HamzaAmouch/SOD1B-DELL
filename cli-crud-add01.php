<?php
session_start();
require_once 'dbconnect.php';

function clean($v){ return trim($v ?? ''); }

$first_name = clean($_POST['first_name'] ?? '');
$last_name  = clean($_POST['last_name'] ?? '');
$email      = clean($_POST['email'] ?? '');
$adress     = clean($_POST['adress'] ?? '');
$zipcode    = clean($_POST['zipcode'] ?? '');
$city       = clean($_POST['city'] ?? '');
$state      = clean($_POST['state'] ?? '');
$country    = clean($_POST['country'] ?? '');
$telephone  = clean($_POST['telephone'] ?? '');
$pswrd1     = $_POST['pswrd1'] ?? '';
$pswrd2     = $_POST['pswrd2'] ?? '';

$errors = [];

// validations
if($first_name === '' || !preg_match('/^[\p{L} ]+$/u', $first_name)) $errors[] = 'Ongeldige voornaam';
if($last_name === '' || !preg_match('/^[\p{L} ]+$/u', $last_name)) $errors[] = 'Ongeldige achternaam';
if($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Ongeldig e-mailadres';
if($adress === '') $errors[] = 'Adres is verplicht';
if($zipcode === '') $errors[] = 'Postcode is verplicht';
if($city === '' || !preg_match('/^[\p{L} ]+$/u', $city)) $errors[] = 'Ongeldige woonplaats';
if($country === '') $errors[] = 'Land is verplicht';
if($pswrd1 === '' || $pswrd2 === '') $errors[] = 'Vul beide wachtwoordvelden in';
if($pswrd1 !== $pswrd2) $errors[] = 'Wachtwoorden komen niet overeen';
if($telephone !== '' && !preg_match('/^[0-9\s]+$/', $telephone)) $errors[] = 'Ongeldig telefoonnummer';

// unique email
if(empty($errors)){
    try{
        $stmt = $db->prepare('SELECT id FROM client WHERE email = :email LIMIT 1');
        $stmt->bindValue(':email', $email);
        $stmt->execute();
        if($stmt->fetch()) $errors[] = 'Dit e-mailadres is al geregistreerd';
    }catch(PDOException $e){
        $errors[] = 'Databasefout: '.$e->getMessage();
    }
}

if(!empty($errors)){
    $_SESSION['errors'] = $errors;
    $_SESSION['old'] = [
        'first_name'=>$first_name,'last_name'=>$last_name,'email'=>$email,'adress'=>$adress,
        'zipcode'=>$zipcode,'city'=>$city,'state'=>$state,'country'=>$country,'telephone'=>$telephone
    ];
    header('Location: cli-crud-add.php');
    exit;
}

// success -> store pending data in session and show confirmation
$_SESSION['pending_registration'] = [
    'first_name'=>$first_name,'last_name'=>$last_name,'email'=>$email,'adress'=>$adress,
    'zipcode'=>$zipcode,'city'=>$city,'state'=>$state,'country'=>$country,'telephone'=>$telephone,'pswrd'=>$pswrd1
];

?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="company.css">
    <title>Bevestig registratie</title>
</head>
<body>
    <h1>Bevestig uw gegevens</h1>
    <p>Controleer uw gegevens en bevestig om de registratie te voltooien. Wachtwoord wordt niet getoond.</p>

    <ul>
        <li>First name: <?=htmlspecialchars($first_name)?></li>
        <li>Last name: <?=htmlspecialchars($last_name)?></li>
        <li>Email: <?=htmlspecialchars($email)?></li>
        <li>Adress: <?=htmlspecialchars($adress)?></li>
        <li>Zipcode: <?=htmlspecialchars($zipcode)?></li>
        <li>City: <?=htmlspecialchars($city)?></li>
        <li>State: <?=htmlspecialchars($state)?></li>
        <li>Country: <?=htmlspecialchars($country)?></li>
        <li>Telephone: <?=htmlspecialchars($telephone)?></li>
    </ul>

    <form action="cli-crud-adding.php" method="post" style="display:inline">
        <button type="submit">Bevestigen</button>
    </form>
    <form action="cli-crud-add.php" method="get" style="display:inline">
        <button type="submit">Annuleren</button>
    </form>
</body>
</html>

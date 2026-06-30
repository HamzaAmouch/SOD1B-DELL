<?php
session_start();
require_once 'dbconnect.php';

if(empty($_SESSION['pending_registration'])){
    header('Location: cli-crud-add.php');
    exit;
}

$data = $_SESSION['pending_registration'];

try{
    $hashed = password_hash($data['pswrd'], PASSWORD_DEFAULT);

    $stmt = $db->prepare('INSERT INTO client
        (first_name,last_name,email,adress,zipcode,city,state,country,telephone,pswrd,isadmin)
        VALUES
        (:first_name,:last_name,:email,:adress,:zipcode,:city,:state,:country,:telephone,:pswrd,:isadmin)');

    $stmt->bindValue(':first_name', $data['first_name']);
    $stmt->bindValue(':last_name', $data['last_name']);
    $stmt->bindValue(':email', $data['email']);
    $stmt->bindValue(':adress', $data['adress']);
    $stmt->bindValue(':zipcode', $data['zipcode']);
    $stmt->bindValue(':city', $data['city']);
    $stmt->bindValue(':state', $data['state']);
    $stmt->bindValue(':country', $data['country']);
    $stmt->bindValue(':telephone', $data['telephone']);
    $stmt->bindValue(':pswrd', $hashed);
    $stmt->bindValue(':isadmin', 'N');

    $stmt->execute();

    unset($_SESSION['pending_registration']);

}catch(PDOException $e){
    die('Fout bij opslaan: '.$e->getMessage());
}

?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="company.css">
    <title>Registratie voltooid</title>
</head>
<body>
    <h1>Welkom, <?=htmlspecialchars($data['first_name'])?>!</h1>
    <p>Uw registratie is succesvol. U kunt nu inloggen.</p>
    <p><a href="index.php">Terug naar start</a></p>
</body>
</html>

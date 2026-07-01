<?php
session_start();

if (!isset($_SESSION["benJeErAl"]) || $_SESSION["benJeErAl"] !== true || ($_SESSION["SoortToegang"] ?? "") !== "Beheer") {
    echo "<h2>Deze functie is alleen toegankelijk voor een ingelogde beheerder.</h2>";
    echo "<p><a href='index.php'>Terug naar home</a></p>";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_POST["productid"]) || ($_POST["source"] ?? "") !== "pro-crud-upd01a.php") {
    header("Location: pro-crud-upd01.php");
    exit;
}

require_once "dbconnect.php";

$productId = (int)$_POST["productid"];

try {
    $selectQuery = $db->prepare("SELECT isactive FROM product WHERE ID = :productid");
    $selectQuery->bindValue(":productid", $productId, PDO::PARAM_INT);
    $selectQuery->execute();

    if ($selectQuery->rowCount() !== 1) {
        header("Location: pro-crud-upd01.php");
        exit;
    }

    $currentProduct = $selectQuery->fetch(PDO::FETCH_ASSOC);
    $newStatus = ($currentProduct["isactive"] === "J") ? "N" : "J";

    $updateQuery = $db->prepare("UPDATE product SET isactive = :newstatus WHERE ID = :productid");
    $updateQuery->bindValue(":newstatus", $newStatus);
    $updateQuery->bindValue(":productid", $productId, PDO::PARAM_INT);
    $updateQuery->execute();
} catch (PDOException $e) {
    die("Fout bij het wijzigen van de productstatus: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productstatus gewijzigd</title>
    <link rel="stylesheet" type="text/css" href="company.css">
</head>
<body>
    <?php include "nav.html"; ?>

    <h1>Productstatus gewijzigd</h1>
    <p>De status van het product is bijgewerkt.</p>
    <p><a href="pro-crud-upd01.php">Terug naar het overzicht</a></p>
</body>
</html>

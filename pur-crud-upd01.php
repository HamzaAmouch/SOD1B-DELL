<?php
session_start();
require_once "dbconnect.php";

if (!isset($_SESSION["benJeErAl"]) || !$_SESSION["benJeErAl"] || !isset($_SESSION["SoortToegang"]) || $_SESSION["SoortToegang"] !== "Klant") {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['purchaselineid'], $_POST['quantity'])) {
    header("Location: pur-crud-upd.php");
    exit();
}

$purchaselineId = (int)$_POST['purchaselineid'];
$newQuantity = (int)$_POST['quantity'];
$clientId = (int)$_SESSION["welkNummerIsDit"];
$errorMessage = '';
$orderLine = null;

if ($newQuantity < 1) {
    $errorMessage = 'Het aantal moet minimaal 1 zijn.';
}

try {
    $query = $db->prepare(
        "SELECT pl.ID AS purchaselineID,
                p.ID AS purchaseID,
                prod.productname,
                pl.quantity AS currentQuantity,
                pl.price
         FROM purchaseline pl
         JOIN purchase p ON p.ID = pl.purchaseid
         JOIN product prod ON prod.ID = pl.productid
         WHERE pl.ID = :purchaselineid
           AND p.clientid = :clientid
           AND p.delivered = 0"
    );
    $query->bindValue(':purchaselineid', $purchaselineId, PDO::PARAM_INT);
    $query->bindValue(':clientid', $clientId, PDO::PARAM_INT);
    $query->execute();
    $orderLine = $query->fetch(PDO::FETCH_ASSOC);

    if (!$orderLine) {
        $errorMessage = 'Deze bestelling kan niet gewijzigd worden.';
    }
} catch (PDOException $e) {
    $errorMessage = 'Fout bij ophalen bestelregel: ' . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Bevestig wijziging bestelling</title>
    <link rel="stylesheet" type="text/css" href="company.css">
</head>
<body>
    <?php include "nav.html"; ?>
    <main class="centering">
        <header class="spacebelowabove">
            <h1>Bevestig wijziging</h1>
        </header>
        <?php if (!empty($errorMessage)): ?>
            <p class="error"><?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?></p>
            <p><a href="pur-crud-upd.php">Terug</a></p>
        <?php else: ?>
            <p>Je wijzigt de bestelling van het volgende product:</p>
            <ul>
                <li>Bestelnummer: <?= htmlspecialchars($orderLine['purchaseID'], ENT_QUOTES, 'UTF-8') ?></li>
                <li>Productnaam: <?= htmlspecialchars($orderLine['productname'], ENT_QUOTES, 'UTF-8') ?></li>
                <li>Prijs: &euro; <?= number_format($orderLine['price'], 2, ',', '.') ?></li>
                <li>Huidig aantal: <?= htmlspecialchars($orderLine['currentQuantity'], ENT_QUOTES, 'UTF-8') ?></li>
                <li>Nieuw aantal: <?= htmlspecialchars($newQuantity, ENT_QUOTES, 'UTF-8') ?></li>
            </ul>
            <form action="pur-crud-update.php" method="post">
                <input type="hidden" name="purchaselineid" value="<?= htmlspecialchars($orderLine['purchaselineID'], ENT_QUOTES, 'UTF-8') ?>">
                <input type="hidden" name="quantity" value="<?= htmlspecialchars($newQuantity, ENT_QUOTES, 'UTF-8') ?>">
                <button type="submit">Bevestigen</button>
            </form>
            <p><a href="pur-crud-upd.php">Afbreken</a></p>
        <?php endif; ?>
    </main>
</body>
</html>
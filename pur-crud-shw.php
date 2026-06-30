<?php
session_start();
require_once "dbconnect.php";

if (!isset($_SESSION["benJeErAl"]) || !$_SESSION["benJeErAl"] || !isset($_SESSION["SoortToegang"]) || $_SESSION["SoortToegang"] !== "Klant") {
    header("Location: index.php");
    exit();
}

$clientId = (int)$_SESSION["welkNummerIsDit"];
$orders = [];
$errorMessage = '';

try {
    $query = $db->prepare(
        "SELECT p.ID AS purchaseID,
                p.purchasedate,
                p.delivered,
                pl.ID AS purchaselineID,
                prod.productname,
                pl.price,
                pl.quantity
         FROM purchase p
         JOIN purchaseline pl ON pl.purchaseid = p.ID
         JOIN product prod ON prod.ID = pl.productid
         WHERE p.clientid = :clientid
         ORDER BY p.ID, pl.ID"
    );
    $query->bindValue(':clientid', $clientId, PDO::PARAM_INT);
    $query->execute();
    $orders = $query->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $errorMessage = 'Fout bij ophalen bestellingen: ' . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Mijn bestellingen</title>
    <link rel="stylesheet" type="text/css" href="company.css">
</head>
<body>
    <?php include "nav.html"; ?>
    <main class="centering">
        <header class="spacebelowabove">
            <h1>Mijn bestellingen</h1>
        </header>
        <?php if (!empty($errorMessage)): ?>
            <p class="error"><?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?></p>
        <?php elseif (empty($orders)): ?>
            <p>Je hebt nog geen bestellingen.</p>
        <?php else: ?>
            <table class="tabledisp">
                <thead>
                    <tr>
                        <th>Bestelnummer</th>
                        <th>Besteldatum</th>
                        <th>Afgeleverd</th>
                        <th>Productnaam</th>
                        <th>Prijs</th>
                        <th>Aantal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><?= htmlspecialchars($order['purchaseID'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($order['purchasedate'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= $order['delivered'] ? 'Ja' : 'Nee' ?></td>
                            <td><?= htmlspecialchars($order['productname'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td>&euro; <?= number_format($order['price'], 2, ',', '.') ?></td>
                            <td><?= htmlspecialchars($order['quantity'], ENT_QUOTES, 'UTF-8') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </main>
</body>
</html>
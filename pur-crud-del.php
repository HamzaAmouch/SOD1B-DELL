<?php
session_start();
require_once "dbconnect.php";

if (!isset($_SESSION["benJeErAl"]) || !$_SESSION["benJeErAl"] || !isset($_SESSION["SoortToegang"]) || $_SESSION["SoortToegang"] !== "Beheer") {
    header("Location: index.php");
    exit();
}

$orders = [];
$errorMessage = '';

try {
    $query = $db->prepare(
        "SELECT p.ID AS purchaseID,
                cl.last_name,
                p.purchasedate,
                pl.ID AS purchaselineID,
                prod.productname,
                pl.quantity
         FROM purchase p
         JOIN client cl ON cl.id = p.clientid
         JOIN purchaseline pl ON pl.purchaseid = p.ID
         JOIN product prod ON prod.ID = pl.productid
         WHERE p.delivered = 0
         ORDER BY p.ID, pl.ID"
    );
    $query->execute();
    $orders = $query->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $errorMessage = 'Fout bij ophalen te verwijderen bestellingen: ' . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Bestellingen verwijderen</title>
    <link rel="stylesheet" type="text/css" href="company.css">
</head>
<body>
    <?php include "nav.html"; ?>
    <main class="centering">
        <header class="spacebelowabove">
            <h1>Bestellingen beheren</h1>
        </header>
        <?php if (!empty($errorMessage)): ?>
            <p class="error"><?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?></p>
        <?php elseif (empty($orders)): ?>
            <p>Er zijn geen niet-afgeleverde bestellingen om te verwijderen.</p>
        <?php else: ?>
            <table class="tabledisp">
                <thead>
                    <tr>
                        <th>Bestelnummer</th>
                        <th>Achternaam klant</th>
                        <th>Besteldatum</th>
                        <th>Regelnummer</th>
                        <th>Productnaam</th>
                        <th>Aantal</th>
                        <th>Actie</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><?= htmlspecialchars($order['purchaseID'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($order['last_name'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($order['purchasedate'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($order['purchaselineID'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($order['productname'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($order['quantity'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td>
                                <form action="pur-crud-delete.php" method="post" style="display:inline-block; margin-right: 0.5rem;">
                                    <input type="hidden" name="action" value="regel">
                                    <input type="hidden" name="purchaseid" value="<?= htmlspecialchars($order['purchaseID'], ENT_QUOTES, 'UTF-8') ?>">
                                    <input type="hidden" name="purchaselineid" value="<?= htmlspecialchars($order['purchaselineID'], ENT_QUOTES, 'UTF-8') ?>">
                                    <button type="submit">Regel</button>
                                </form>
                                <form action="pur-crud-delete.php" method="post" style="display:inline-block;">
                                    <input type="hidden" name="action" value="aankoop">
                                    <input type="hidden" name="purchaseid" value="<?= htmlspecialchars($order['purchaseID'], ENT_QUOTES, 'UTF-8') ?>">
                                    <button type="submit">Aankoop</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </main>
</body>
</html>
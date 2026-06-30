<?php
session_start();
require_once "dbconnect.php";

if (!isset($_SESSION["benJeErAl"]) || !$_SESSION["benJeErAl"] || !isset($_SESSION["SoortToegang"]) || $_SESSION["SoortToegang"] !== "Klant") {
    header("Location: index.php");
    exit();
}

$clientId = (int)$_SESSION["welkNummerIsDit"];
$lines = [];
$errorMessage = '';

try {
    $query = $db->prepare(
        "SELECT pl.ID AS purchaselineID,
                p.ID AS purchaseID,
                prod.productname,
                pl.quantity,
                pl.price
         FROM purchaseline pl
         JOIN purchase p ON p.ID = pl.purchaseid
         JOIN product prod ON prod.ID = pl.productid
         WHERE p.clientid = :clientid
           AND p.delivered = 0
         ORDER BY p.ID, pl.ID"
    );
    $query->bindValue(':clientid', $clientId, PDO::PARAM_INT);
    $query->execute();
    $lines = $query->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $errorMessage = 'Fout bij ophalen bestellingen om te wijzigen: ' . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Bestelling wijzigen</title>
    <link rel="stylesheet" type="text/css" href="company.css">
</head>
<body>
    <?php include "nav.html"; ?>
    <main class="centering">
        <header class="spacebelowabove">
            <h1>Bestelling wijzigen</h1>
        </header>
        <?php if (!empty($errorMessage)): ?>
            <p class="error"><?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?></p>
        <?php elseif (empty($lines)): ?>
            <p>Er zijn geen niet-afgeleverde bestellingen om te wijzigen.</p>
        <?php else: ?>
            <table class="tabledisp">
                <thead>
                    <tr>
                        <th>Bestelnummer</th>
                        <th>Productnaam</th>
                        <th>Prijs</th>
                        <th>Aantal</th>
                        <th>Actie</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($lines as $line): ?>
                        <tr>
                            <td><?= htmlspecialchars($line['purchaseID'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($line['productname'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td>&euro; <?= number_format($line['price'], 2, ',', '.') ?></td>
                            <td colspan="2">
                                <form action="pur-crud-upd01.php" method="post">
                                    <input type="hidden" name="purchaselineid" value="<?= htmlspecialchars($line['purchaselineID'], ENT_QUOTES, 'UTF-8') ?>">
                                    <input type="number" name="quantity" min="1" value="<?= htmlspecialchars($line['quantity'], ENT_QUOTES, 'UTF-8') ?>" required>
                                    <button type="submit">Opslaan</button>
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
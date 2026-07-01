<?php
session_start();

require_once "dbconnect.php";

try {
    $query = $db->prepare(
        "SELECT p.ID, c.first_name, c.last_name, c.city, p.purchasedate, p.delivered, COALESCE(SUM(pl.price * pl.quantity), 0) AS totaalprijs " .
        "FROM purchase AS p " .
        "JOIN client AS c ON p.clientid = c.id " .
        "LEFT JOIN purchaseline AS pl ON p.ID = pl.purchaseid " .
        "GROUP BY p.ID, c.first_name, c.last_name, c.city, p.purchasedate, p.delivered " .
        "ORDER BY p.ID"
    );
    $query->execute();
    $purchases = $query->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Fout bij het ophalen van de aankooprapportage: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alle aankopen met hun totale waarde</title>
    <link rel="stylesheet" type="text/css" href="company.css">
</head>
<body>
    <?php include "nav.html"; ?>

    <h1>Alle aankopen met hun totale waarde</h1>

    <table>
        <thead>
            <tr>
                <th>purchase.ID</th>
                <th>client.first_name</th>
                <th>client.last_name</th>
                <th>client.city</th>
                <th>purchasedate</th>
                <th>delivered</th>
                <th>totaal-prijs</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($purchases as $purchase): ?>
                <tr>
                    <td><?php echo htmlspecialchars($purchase["ID"]); ?></td>
                    <td><?php echo htmlspecialchars($purchase["first_name"]); ?></td>
                    <td><?php echo htmlspecialchars($purchase["last_name"]); ?></td>
                    <td><?php echo htmlspecialchars($purchase["city"]); ?></td>
                    <td><?php echo htmlspecialchars($purchase["purchasedate"]); ?></td>
                    <td><?php echo ($purchase["delivered"] == 1) ? "Ja" : "Nee"; ?></td>
                    <td><?php echo number_format((float)$purchase["totaalprijs"], 2, ',', '.'); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>

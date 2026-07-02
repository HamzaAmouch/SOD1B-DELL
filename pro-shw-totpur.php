<?php
session_start();

require_once "dbconnect.php";

try {
    $query = $db->prepare(
        "SELECT p.ID, p.productname, p.price, s.company AS suppliercompany, COALESCE(SUM(pl.price * pl.quantity), 0) AS totaalverkopen " .
        "FROM product AS p " .
        "LEFT JOIN supplier AS s ON p.supplierid = s.ID " .
        "LEFT JOIN purchaseline AS pl ON p.ID = pl.productid " .
        "GROUP BY p.ID, p.productname, p.price, s.company " .
        "ORDER BY p.ID"
    );
    $query->execute();
    $products = $query->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Fout bij het ophalen van de rapportage: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alle producten met waarde van aankopen</title>
    <link rel="stylesheet" type="text/css" href="company.css">
</head>
<body>
    <?php include "nav.html"; ?>

    <h1>Alle producten met de waarde van diens aankopen</h1>

    <?php if (count($products) > 0): ?>
        <div class="centerflex">
            <table class="tabledisp2">
                <thead>
                    <tr>
                        <th>Product ID</th>
                        <th>Productnaam</th>
                        <th>Prijs</th>
                        <th>Leverancier</th>
                        <th>Totale waarde aankopen</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($product["ID"]); ?></td>
                            <td><?php echo htmlspecialchars($product["productname"]); ?></td>
                            <td><?php echo htmlspecialchars($product["price"]); ?></td>
                            <td><?php echo htmlspecialchars($product["suppliercompany"]); ?></td>
                            <td><?php echo number_format((float)$product["totaalverkopen"], 2, ',', '.'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p>Er zijn geen producten gevonden.</p>
    <?php endif; ?>
</body>
</html>

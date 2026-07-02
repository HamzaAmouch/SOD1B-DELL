<?php
session_start();

if (!isset($_SESSION["benJeErAl"]) || $_SESSION["benJeErAl"] !== true || ($_SESSION["SoortToegang"] ?? "") !== "Beheer") {
    echo "<h2>Deze pagina is alleen toegankelijk voor een ingelogde beheerder.</h2>";
    echo "<p><a href='index.php'>Terug naar home</a></p>";
    exit;
}

require_once "dbconnect.php";

try {
    $query = $db->prepare(
        "SELECT p.ID, p.productname, c.name AS categoryname, s.company AS suppliercompany, p.price " .
        "FROM product AS p " .
        "JOIN category AS c ON p.categoryid = c.ID " .
        "JOIN supplier AS s ON p.supplierid = s.ID " .
        "WHERE p.isactive = 'N' " .
        "ORDER BY p.ID"
    );
    $query->execute();
    $products = $query->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Fout bij het ophalen van inactieve producten: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inactieve producten</title>
    <link rel="stylesheet" type="text/css" href="company.css">
</head>
<body>
    <?php include "nav.html"; ?>

    <h1>Overzicht inactieve producten</h1>

    <?php if (count($products) > 0): ?>
        <div class="centerflex">
            <table class="tabledisp2">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Productnaam</th>
                        <th>Categorie</th>
                        <th>Leverancier</th>
                        <th>Prijs</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($product['ID']); ?></td>
                            <td><?php echo htmlspecialchars($product['productname']); ?></td>
                            <td><?php echo htmlspecialchars($product['categoryname']); ?></td>
                            <td><?php echo htmlspecialchars($product['suppliercompany']); ?></td>
                            <td><?php echo htmlspecialchars($product['price']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p>Er zijn geen inactieve producten.</p>
    <?php endif; ?>
</body>
</html>

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
        "SELECT p.ID, p.productname, p.price, c.name AS categoryname, s.company AS suppliercompany, p.isactive " .
        "FROM product AS p " .
        "JOIN category AS c ON p.categoryid = c.ID " .
        "JOIN supplier AS s ON p.supplierid = s.ID " .
        "ORDER BY p.ID"
    );
    $query->execute();
    $products = $query->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Fout bij het ophalen van producten: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Onderhoud producten</title>
    <link rel="stylesheet" type="text/css" href="company.css">
</head>
<body>
    <?php include "nav.html"; ?>

    <h1>Onderhoud producten</h1>

    <?php if (isset($_SESSION['product_add_success'])): ?>
        <p style="color:green;"><strong><?php echo htmlspecialchars($_SESSION['product_add_success']); ?></strong></p>
        <?php unset($_SESSION['product_add_success']); ?>
    <?php endif; ?>

    <form action="pro-crud-add.php" method="get">
        <button type="submit">Product toevoegen</button>
    </form>

    <?php if (count($products) > 0): ?>
        <div class="centerflex">
            <table class="tabledisp2">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Productnaam</th>
                        <th>Prijs</th>
                        <th>Categorie</th>
                        <th>Leverancier</th>
                        <th>Status</th>
                        <th>Acties</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($product['ID']); ?></td>
                            <td><?php echo htmlspecialchars($product['productname']); ?></td>
                            <td><?php echo htmlspecialchars($product['price']); ?></td>
                            <td><?php echo htmlspecialchars($product['categoryname']); ?></td>
                            <td><?php echo htmlspecialchars($product['suppliercompany']); ?></td>
                            <td><?php echo ($product['isactive'] === 'J') ? 'Actief' : 'Niet actief'; ?></td>
                            <td>
                                <form action="pro-crud-upd.php" method="post" style="display:inline;">
                                    <input type="hidden" name="productid" value="<?php echo (int)$product['ID']; ?>">
                                    <button type="submit" name="submt-sel-prod-upd">Wijzigen</button>
                                </form>
                                <form action="pro-crud-del.php" method="post" style="display:inline; margin-left:8px;">
                                    <input type="hidden" name="productid" value="<?php echo (int)$product['ID']; ?>">
                                    <button type="submit" name="submt-sel-prod-del">Verwijderen</button>
                                </form>
                            </td>
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

<?php
session_start();

if (!isset($_SESSION["benJeErAl"]) || $_SESSION["benJeErAl"] !== true || ($_SESSION["SoortToegang"] ?? "") !== "Beheer") {
    echo "<h2>Deze functie is alleen toegankelijk voor een ingelogde beheerder.</h2>";
    echo "<p><a href='index.php'>Terug naar home</a></p>";
    exit;
}

require_once "dbconnect.php";

try {
    $query = $db->prepare(
        "SELECT p.ID, p.productname, p.ingredients, p.allergens, c.name AS categoryname, s.company AS suppliercompany, p.price, p.isactive " .
        "FROM product AS p " .
        "JOIN category AS c ON p.categoryid = c.ID " .
        "JOIN supplier AS s ON p.supplierid = s.ID " .
        "ORDER BY p.ID"
    );
    $query->execute();
    $products = $query->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Fout bij het ophalen van de producten: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inactiveren product</title>
    <link rel="stylesheet" type="text/css" href="company.css">
</head>
<body>
    <?php include "nav.html"; ?>

    <h1>Inactiveren product</h1>

    <form method="post" action="pro-crud-upd01a.php">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Productnaam</th>
                    <th>Ingrediënten</th>
                    <th>Allergenen</th>
                    <th>Categorie</th>
                    <th>Leverancier</th>
                    <th>Prijs</th>
                    <th>Status</th>
                    <th>Actie</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($product["ID"]); ?></td>
                        <td><?php echo htmlspecialchars($product["productname"]); ?></td>
                        <td><?php echo htmlspecialchars($product["ingredients"]); ?></td>
                        <td><?php echo htmlspecialchars($product["allergens"]); ?></td>
                        <td><?php echo htmlspecialchars($product["categoryname"]); ?></td>
                        <td><?php echo htmlspecialchars($product["suppliercompany"]); ?></td>
                        <td><?php echo htmlspecialchars($product["price"]); ?></td>
                        <td><?php echo ($product["isactive"] === "N") ? "Niet actief" : "Actief"; ?></td>
                        <td>
                            <input type="hidden" name="productid" value="<?php echo (int)$product["ID"]; ?>">
                            <button type="submit">(de)Activeren</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </form>
</body>
</html>

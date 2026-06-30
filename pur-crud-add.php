<?php
session_start();
require_once "dbconnect.php";

// Alleen ingelogde klanten mogen bestellen
if (!isset($_SESSION["benJeErAl"]) || !$_SESSION["benJeErAl"] || !isset($_SESSION["SoortToegang"]) || $_SESSION["SoortToegang"] !== "Klant") {
    header("Location: index.php");
    exit();
}

$products = [];
try {
    $query = $db->prepare(
        "SELECT p.ID, p.productname, c.name AS categoryname, p.price
         FROM product p
         JOIN category c ON p.categoryid = c.ID
         WHERE p.isactive = 'J'
         ORDER BY p.ID"
    );
    $query->execute();
    $products = $query->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $errorMessage = 'Fout bij ophalen producten: ' . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Productoverzicht bestellen</title>
    <link rel="stylesheet" type="text/css" href="company.css">
</head>
<body>
    <?php include "nav.html"; ?>
    <header class="spacebelowabove">
        <h1>Productoverzicht voor bestellen</h1>
    </header>
    <main class="centering">
        <h2 class="spacebelowabove">LET OP: je kan maar één product tegelijk bestellen</h2>

        <?php if (!empty($errorMessage)): ?>
            <p class="error"><?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?></p>
        <?php elseif (empty($products)): ?>
            <p>Er zijn geen actieve producten om te bestellen.</p>
        <?php else: ?>
            <table class="tabledisp">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Productnaam</th>
                        <th>Categorie</th>
                        <th>Prijs</th>
                        <th>Aantal</th>
                        <th>Actie</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td><?= htmlspecialchars($product['ID'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($product['productname'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($product['categoryname'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td>&euro; <?= number_format($product['price'], 2, ',', '.') ?></td>
                            <td colspan="2">
                                <form action="pur-crud-adding.php" method="post">
                                    <input type="hidden" name="productid" value="<?= htmlspecialchars($product['ID'], ENT_QUOTES, 'UTF-8') ?>">
                                    <input type="hidden" name="price" value="<?= htmlspecialchars($product['price'], ENT_QUOTES, 'UTF-8') ?>">
                                    <input type="number" name="quantity" min="1" value="1" required>
                                    <button type="submit">Bestellen</button>
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

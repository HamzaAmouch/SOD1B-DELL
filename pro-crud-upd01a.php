<?php
session_start();

if (!isset($_SESSION["benJeErAl"]) || $_SESSION["benJeErAl"] !== true || ($_SESSION["SoortToegang"] ?? "") !== "Beheer") {
    echo "<h2>Deze functie is alleen toegankelijk voor een ingelogde beheerder.</h2>";
    echo "<p><a href='index.php'>Terug naar home</a></p>";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_POST["productid"])) {
    header("Location: pro-crud-upd01.php");
    exit;
}

require_once "dbconnect.php";

$productId = (int)$_POST["productid"];

try {
    $query = $db->prepare(
        "SELECT p.ID, p.productname, p.ingredients, p.allergens, c.name AS categoryname, s.company AS suppliercompany, p.price, p.isactive " .
        "FROM product AS p " .
        "JOIN category AS c ON p.categoryid = c.ID " .
        "JOIN supplier AS s ON p.supplierid = s.ID " .
        "WHERE p.ID = :productid"
    );
    $query->bindValue(":productid", $productId, PDO::PARAM_INT);
    $query->execute();

    if ($query->rowCount() !== 1) {
        header("Location: pro-crud-upd01.php");
        exit;
    }

    $product = $query->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Fout bij het ophalen van het product: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productstatus wijzigen</title>
    <link rel="stylesheet" type="text/css" href="company.css">
</head>
<body>
    <?php include "nav.html"; ?>

    <h1><?php echo ($product["isactive"] === "N") ? "(opnieuw) Activeren van product" : "De-activeren van product"; ?></h1>

    <p><strong>ID:</strong> <?php echo htmlspecialchars($product["ID"]); ?></p>
    <p><strong>Productnaam:</strong> <?php echo htmlspecialchars($product["productname"]); ?></p>
    <p><strong>Ingrediënten:</strong> <?php echo htmlspecialchars($product["ingredients"]); ?></p>
    <p><strong>Allergenen:</strong> <?php echo htmlspecialchars($product["allergens"]); ?></p>
    <p><strong>Categorie:</strong> <?php echo htmlspecialchars($product["categoryname"]); ?></p>
    <p><strong>Leverancier:</strong> <?php echo htmlspecialchars($product["suppliercompany"]); ?></p>
    <p><strong>Prijs:</strong> <?php echo htmlspecialchars($product["price"]); ?></p>

    <p>
        <?php echo ($product["isactive"] === "N") ? "Wilt u dit bovenstaande product opnieuw activeren ?" : "Wilt u dit bovenstaande product de-activeren ?"; ?>
    </p>

    <form method="post" action="pro-crud-update01.php">
        <input type="hidden" name="productid" value="<?php echo (int)$product["ID"]; ?>">
        <input type="hidden" name="source" value="pro-crud-upd01a.php">
        <button type="submit" formaction="pro-crud-upd01.php">Breek af</button>
        <button type="submit"><?php echo ($product["isactive"] === "N") ? "Activeer" : "De-activeer"; ?></button>
    </form>
</body>
</html>

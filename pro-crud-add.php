<?php
session_start();

if (!isset($_SESSION["benJeErAl"]) || $_SESSION["benJeErAl"] !== true || ($_SESSION["SoortToegang"] ?? "") !== "Beheer") {
    echo "<h2>Deze pagina is alleen toegankelijk voor een ingelogde beheerder.</h2>";
    echo "<p><a href='index.php'>Terug naar home</a></p>";
    exit;
}

require_once "dbconnect.php";

$errors = [];
$values = [
    'productname' => '',
    'ingredients' => '',
    'allergens' => '',
    'price' => '',
    'categoryid' => '',
    'supplierid' => ''
];

if (isset($_SESSION['product_add_errors'])) {
    $errors = $_SESSION['product_add_errors'];
    unset($_SESSION['product_add_errors']);
}

if (isset($_SESSION['product_add_values'])) {
    $values = $_SESSION['product_add_values'];
    unset($_SESSION['product_add_values']);
}

$categories = [];
$suppliers = [];

try {
    $catQuery = $db->prepare('SELECT ID, name FROM category ORDER BY name');
    $catQuery->execute();
    $categories = $catQuery->fetchAll(PDO::FETCH_ASSOC);

    $supQuery = $db->prepare('SELECT ID, company FROM supplier ORDER BY company');
    $supQuery->execute();
    $suppliers = $supQuery->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $errors[] = 'Fout bij het ophalen van categorieën of leveranciers: ' . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product toevoegen</title>
    <link rel="stylesheet" type="text/css" href="company.css">
</head>
<body>
    <?php include "nav.html"; ?>

    <h1>Product toevoegen</h1>

    <?php if (!empty($errors)): ?>
        <div style="color:red; margin-bottom:12px;">
            <strong>Controleer de invoer:</strong>
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="pro-crud-adding.php" method="post">
        <div>
            <label for="productname">Productnaam:</label><br>
            <input type="text" id="productname" name="productname" value="<?php echo htmlspecialchars($values['productname']); ?>" required pattern="[A-Za-zÀ-ÖØ-öø-ÿ ]+" title="Alleen letters en spaties zijn toegestaan.">
        </div>

        <div style="margin-top:10px;">
            <label for="ingredients">Ingrediënten:</label><br>
            <input type="text" id="ingredients" name="ingredients" value="<?php echo htmlspecialchars($values['ingredients']); ?>" pattern="[A-Za-z0-9À-ÖØ-öø-ÿ ]*" title="Alleen letters, cijfers en spaties zijn toegestaan.">
        </div>

        <div style="margin-top:10px;">
            <label for="allergens">Allergenen:</label><br>
            <input type="text" id="allergens" name="allergens" value="<?php echo htmlspecialchars($values['allergens']); ?>" pattern="[A-Za-z0-9À-ÖØ-öø-ÿ ]*" title="Alleen letters, cijfers en spaties zijn toegestaan.">
        </div>

        <div style="margin-top:10px;">
            <label for="price">Prijs:</label><br>
            <input type="text" id="price" name="price" value="<?php echo htmlspecialchars($values['price']); ?>" required pattern="\d+,\d{2}" title="Gebruik het formaat 12,34">
        </div>

        <div style="margin-top:10px;">
            <label for="categoryid">Categorie:</label><br>
            <select id="categoryid" name="categoryid" required>
                <option value="">-- Kies een categorie --</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?php echo (int)$category['ID']; ?>" <?php echo ((string)$values['categoryid'] === (string)$category['ID']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($category['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div style="margin-top:10px;">
            <label for="supplierid">Leverancier:</label><br>
            <select id="supplierid" name="supplierid" required>
                <option value="">-- Kies een leverancier --</option>
                <?php foreach ($suppliers as $supplier): ?>
                    <option value="<?php echo (int)$supplier['ID']; ?>" <?php echo ((string)$values['supplierid'] === (string)$supplier['ID']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($supplier['company']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div style="margin-top:15px;">
            <button type="button" onclick="window.location.href='pro-crud-get.php';">Breek af</button>
            <button type="submit">Sla op</button>
        </div>
    </form>
</body>
</html>
<?php
require_once("database.php"); // pas eventueel aan naar jouw databasebestand

$sql = "
    SELECT
        category.ID,
        category.name,
        AVG(product.price) AS gem_prijs
    FROM category
    LEFT JOIN product
        ON category.ID = product.categoryid
    GROUP BY category.ID, category.name
    ORDER BY category.name
";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Alle categorieën met gemiddelde prijs</title>
    <style>
        table {
            border-collapse: collapse;
            width: 60%;
        }

        th, td {
            border: 1px solid black;
            padding: 8px;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>

<h1>Alle categorieën met gemiddelde prijs</h1>

<table>
    <tr>
        <th>category.ID</th>
        <th>category.name</th>
        <th>gem-prijs</th>
    </tr>

    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
        <tr>
            <td><?= $row['ID']; ?></td>
            <td><?= $row['name']; ?></td>
            <td>€ <?= number_format($row['gem_prijs'], 2, ',', '.'); ?></td>
        </tr>
    <?php } ?>

</table>

</body>
</html>
``
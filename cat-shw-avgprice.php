<?php
require_once("dbconnect.php");

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

$rows = [];
try {
    $stmt = $db->query($sql);
    if ($stmt === false) {
        throw new Exception('Query mislukt');
    }
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    echo '<p style="color:red;">Fout bij query: '.htmlspecialchars($e->getMessage()).'</p>';
}

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

    <?php foreach ($rows as $row) { ?>
        <tr>
            <td><?= htmlspecialchars($row['ID']); ?></td>
            <td><?= htmlspecialchars($row['name']); ?></td>
            <td><?php
                if ($row['gem_prijs'] !== null) {
                    echo '€ ' . number_format((float)$row['gem_prijs'], 2, ',', '.');
                } else {
                    echo '-';
                }
            ?></td>
        </tr>
    <?php } ?>

</table>

</body>
</html>
``
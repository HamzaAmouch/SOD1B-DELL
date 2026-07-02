<?php
require_once("database.php"); // pas aan naar jouw databasebestand

$sql = "
    SELECT
        purchase.ID,
        client.first_name,
        client.last_name,
        client.city,
        purchase.purchasedate,
        purchase.delivered
    FROM purchase
    INNER JOIN client
        ON purchase.clientid = client.ID
    ORDER BY purchase.purchasedate
";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Alle aankopen</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }

        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>

<h1>Alle aankopen</h1>

<table>
    <tr>
        <th>purchase.ID</th>
        <th>client.first_name</th>
        <th>client.last_name</th>
        <th>client.city</th>
        <th>purchasedate</th>
        <th>delivered</th>
    </tr>

    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
        <tr>
            <td><?= $row['ID']; ?></td>
            <td><?= htmlspecialchars($row['first_name']); ?></td>
            <td><?= htmlspecialchars($row['last_name']); ?></td>
            <td><?= htmlspecialchars($row['city']); ?></td>
            <td><?= $row['purchasedate']; ?></td>
            <td><?= $row['delivered']; ?></td>
        </tr>
    <?php } ?>

</table>

</body>
</html>
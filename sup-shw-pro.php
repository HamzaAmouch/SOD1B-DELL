<?php
session_start();

require_once "dbconnect.php";

try {
    $query = $db->prepare(
        "SELECT s.ID, s.company, s.adress, s.city, p.productname, p.price " .
        "FROM supplier AS s " .
        "LEFT JOIN product AS p ON s.ID = p.supplierid " .
        "ORDER BY s.ID, p.productname"
    );
    $query->execute();
    $suppliers = $query->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Fout bij het ophalen van de leverancierrapportage: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alle leveranciers met hun producten</title>
    <link rel="stylesheet" type="text/css" href="company.css">
</head>
<body>
    <?php include "nav.html"; ?>

    <h1>Alle leveranciers met hun producten</h1>

    <table>
        <thead>
            <tr>
                <th>supplier.ID</th>
                <th>supplier.company</th>
                <th>supplier.adress</th>
                <th>suppliers.city</th>
                <th>product.productname</th>
                <th>product.price</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($suppliers as $supplier): ?>
                <tr>
                    <td><?php echo htmlspecialchars($supplier["ID"]); ?></td>
                    <td><?php echo htmlspecialchars($supplier["company"]); ?></td>
                    <td><?php echo htmlspecialchars($supplier["adress"]); ?></td>
                    <td><?php echo htmlspecialchars($supplier["city"]); ?></td>
                    <td><?php echo htmlspecialchars($supplier["productname"]); ?></td>
                    <td><?php echo htmlspecialchars($supplier["price"]); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>

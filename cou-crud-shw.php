<?php
require_once "dbconnect.php";

try {
    $query = $db->prepare("SELECT idcountry, name FROM country ORDER BY name");
    $query->execute();
    $countries = $query->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Fout bij het ophalen van landen: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alle landen</title>
    <link rel="stylesheet" type="text/css" href="company.css">
</head>
<body>
    <?php include "nav.html"; ?>

    <h1>Overzicht van alle landen</h1>

    <?php if (count($countries) > 0): ?>
        <div class="centerflex">
            <table class="tabledisp2">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Naam</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($countries as $country): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($country['idcountry']); ?></td>
                            <td><?php echo htmlspecialchars($country['name']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p>Er zijn geen landen in de database gevonden.</p>
    <?php endif; ?>
</body>
</html>

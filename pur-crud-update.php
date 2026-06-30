<?php
session_start();
require_once "dbconnect.php";

if (!isset($_SESSION["benJeErAl"]) || !$_SESSION["benJeErAl"] || !isset($_SESSION["SoortToegang"]) || $_SESSION["SoortToegang"] !== "Klant") {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['purchaselineid'], $_POST['quantity'])) {
    header("Location: pur-crud-upd.php");
    exit();
}

$purchaselineId = (int)$_POST['purchaselineid'];
$newQuantity = (int)$_POST['quantity'];
$clientId = (int)$_SESSION["welkNummerIsDit"];
$errorMessage = '';
$successMessage = '';

if ($newQuantity < 1) {
    $errorMessage = 'Het aantal moet minimaal 1 zijn.';
}

try {
    $stmt = $db->prepare(
        "SELECT pl.ID
         FROM purchaseline pl
         JOIN purchase p ON p.ID = pl.purchaseid
         WHERE pl.ID = :purchaselineid
           AND p.clientid = :clientid
           AND p.delivered = 0"
    );
    $stmt->bindValue(':purchaselineid', $purchaselineId, PDO::PARAM_INT);
    $stmt->bindValue(':clientid', $clientId, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() === 0) {
        $errorMessage = 'Deze bestelling kan niet gewijzigd worden.';
    }

    if (empty($errorMessage)) {
        $update = $db->prepare("UPDATE purchaseline SET quantity = :quantity WHERE ID = :purchaselineid");
        $update->bindValue(':quantity', $newQuantity, PDO::PARAM_INT);
        $update->bindValue(':purchaselineid', $purchaselineId, PDO::PARAM_INT);
        $update->execute();
        $successMessage = 'De wijziging is opgeslagen.';
    }
} catch (PDOException $e) {
    $errorMessage = 'Fout bij opslaan wijziging: ' . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Wijziging opgeslagen</title>
    <link rel="stylesheet" type="text/css" href="company.css">
</head>
<body>
    <?php include "nav.html"; ?>
    <main class="centering">
        <header class="spacebelowabove">
            <h1>Wijziging opgeslagen</h1>
        </header>
        <?php if (!empty($errorMessage)): ?>
            <p class="error"><?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?></p>
        <?php else: ?>
            <p class="success"><?= htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>
        <p><a href="pur-crud-upd.php">Terug naar wijzigen</a></p>
    </main>
</body>
</html>
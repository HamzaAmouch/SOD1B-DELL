<?php
session_start();
require_once "dbconnect.php";

if (!isset($_SESSION["benJeErAl"]) || !$_SESSION["benJeErAl"] || !isset($_SESSION["SoortToegang"]) || $_SESSION["SoortToegang"] !== "Klant") {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['productid'], $_POST['quantity'], $_POST['price'])) {
    header("Location: pur-crud-add.php");
    exit();
}

$clientId = (int)$_SESSION["welkNummerIsDit"];
$productId = (int)$_POST['productid'];
$quantity = (int)$_POST['quantity'];
$price = (float)$_POST['price'];
$errorMessage = '';
$successMessage = '';

if ($quantity < 1) {
    $errorMessage = 'Het aantal moet minimaal 1 zijn.';
}

try {
    $stmt = $db->prepare("SELECT ID, price, isactive FROM product WHERE ID = :productid");
    $stmt->bindValue(':productid', $productId, PDO::PARAM_INT);
    $stmt->execute();
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product || $product['isactive'] !== 'J') {
        $errorMessage = 'Dit product kan niet besteld worden.';
    } else {
        $price = (float)$product['price'];
    }

    if (empty($errorMessage)) {
        if (isset($_SESSION['purchaseID'])) {
            $checkPurchase = $db->prepare("SELECT ID FROM purchase WHERE ID = :purchaseid AND clientid = :clientid AND delivered = 0");
            $checkPurchase->bindValue(':purchaseid', $_SESSION['purchaseID'], PDO::PARAM_INT);
            $checkPurchase->bindValue(':clientid', $clientId, PDO::PARAM_INT);
            $checkPurchase->execute();
            if ($checkPurchase->rowCount() === 0) {
                unset($_SESSION['purchaseID']);
            }
        }

        if (!isset($_SESSION['purchaseID'])) {
            $insertPurchase = $db->prepare("INSERT INTO purchase (clientid, purchasedate, delivered) VALUES (:clientid, :purchasedate, 0)");
            $insertPurchase->bindValue(':clientid', $clientId, PDO::PARAM_INT);
            $insertPurchase->bindValue(':purchasedate', date('Y-m-d'));
            $insertPurchase->execute();
            $_SESSION['purchaseID'] = (int)$db->lastInsertId();
        }

        $insertLine = $db->prepare("INSERT INTO purchaseline (purchaseid, productid, price, quantity) VALUES (:purchaseid, :productid, :price, :quantity)");
        $insertLine->bindValue(':purchaseid', $_SESSION['purchaseID'], PDO::PARAM_INT);
        $insertLine->bindValue(':productid', $productId, PDO::PARAM_INT);
        $insertLine->bindValue(':price', $price);
        $insertLine->bindValue(':quantity', $quantity, PDO::PARAM_INT);
        $insertLine->execute();

        $successMessage = 'Bestelling is opgeslagen. Je kan een nieuw product aan de bestelling toevoegen.';
    }
} catch (PDOException $e) {
    $errorMessage = 'Fout bij verwerken van bestelling: ' . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Bevestiging bestelling</title>
    <link rel="stylesheet" type="text/css" href="company.css">
</head>
<body>
    <?php include "nav.html"; ?>
    <main class="centering">
        <header class="spacebelowabove">
            <h1>Bestelling verwerken</h1>
        </header>
        <?php if (!empty($errorMessage)): ?>
            <p class="error"><?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?></p>
            <p><a href="pur-crud-add.php">Terug naar productoverzicht</a></p>
        <?php else: ?>
            <p class="success"><?= htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8') ?></p>
            <p><a href="pur-crud-add.php">Terug naar productoverzicht</a></p>
        <?php endif; ?>
    </main>
</body>
</html>
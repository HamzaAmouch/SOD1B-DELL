<?php
session_start();
require_once "dbconnect.php";

if (!isset($_SESSION["benJeErAl"]) || !$_SESSION["benJeErAl"] || !isset($_SESSION["SoortToegang"]) || $_SESSION["SoortToegang"] !== "Beheer") {
    header("Location: index.php");
    exit();
}

$action = $_POST['action'] ?? '';
$purchaseId = isset($_POST['purchaseid']) ? (int)$_POST['purchaseid'] : 0;
$purchaselineId = isset($_POST['purchaselineid']) ? (int)$_POST['purchaselineid'] : 0;
$message = '';
$errorMessage = '';
$showWarning = false;

try {
    if ($action === 'regel' && $purchaselineId > 0 && $purchaseId > 0) {
        $query = $db->prepare("SELECT p.ID AS purchaseID, COUNT(pl.ID) AS lineCount FROM purchase p JOIN purchaseline pl ON pl.purchaseid = p.ID WHERE p.ID = :purchaseid AND pl.ID = :purchaselineid AND p.delivered = 0 GROUP BY p.ID");
        $query->bindValue(':purchaseid', $purchaseId, PDO::PARAM_INT);
        $query->bindValue(':purchaselineid', $purchaselineId, PDO::PARAM_INT);
        $query->execute();
        $row = $query->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            $errorMessage = 'Deze bestellingsregel kan niet worden verwijderd.';
        } elseif ((int)$row['lineCount'] === 1) {
            $showWarning = true;
        } else {
            $delete = $db->prepare("DELETE FROM purchaseline WHERE ID = :purchaselineid AND purchaseid = :purchaseid");
            $delete->bindValue(':purchaselineid', $purchaselineId, PDO::PARAM_INT);
            $delete->bindValue(':purchaseid', $purchaseId, PDO::PARAM_INT);
            $delete->execute();
            $message = 'De bestellingsregel is verwijderd.';
        }
    } elseif ($action === 'aankoop' && $purchaseId > 0) {
        $deleteLines = $db->prepare("DELETE FROM purchaseline WHERE purchaseid = :purchaseid");
        $deleteLines->bindValue(':purchaseid', $purchaseId, PDO::PARAM_INT);
        $deleteLines->execute();

        $deletePurchase = $db->prepare("DELETE FROM purchase WHERE ID = :purchaseid");
        $deletePurchase->bindValue(':purchaseid', $purchaseId, PDO::PARAM_INT);
        $deletePurchase->execute();

        $message = 'De aankoop is verwijderd.';
    } else {
        $errorMessage = 'Onbekende bewerking.';
    }
} catch (PDOException $e) {
    $errorMessage = 'Fout bij verwijderen: ' . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Verwijder bestelling</title>
    <link rel="stylesheet" type="text/css" href="company.css">
</head>
<body>
    <?php include "nav.html"; ?>
    <main class="centering">
        <header class="spacebelowabove">
            <h1>Verwijder bestelling</h1>
        </header>

        <?php if (!empty($errorMessage)): ?>
            <p class="error"><?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?></p>
            <p><a href="pur-crud-del.php">Terug naar verwijderoverzicht</a></p>
        <?php elseif ($showWarning): ?>
            <p class="warning">Laatste product bij deze aankoop. Wilt u het verwijderen afbreken of wilt u de hele aankoop verwijderen?</p>
            <form action="pur-crud-del.php" method="post" style="display:inline-block; margin-right: 1rem;">
                <button type="submit">Afbreken</button>
            </form>
            <form action="pur-crud-delete.php" method="post" style="display:inline-block;">
                <input type="hidden" name="action" value="aankoop">
                <input type="hidden" name="purchaseid" value="<?= htmlspecialchars($purchaseId, ENT_QUOTES, 'UTF-8') ?>">
                <button type="submit">Verwijder aankoop</button>
            </form>
        <?php else: ?>
            <p class="success"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></p>
            <p><a href="pur-crud-del.php">Terug naar verwijderoverzicht</a></p>
        <?php endif; ?>
    </main>
</body>
</html>
<?php
session_start();

if (!isset($_SESSION["benJeErAl"]) || $_SESSION["benJeErAl"] !== true || ($_SESSION["SoortToegang"] ?? "") !== "Beheer") {
    echo "<h2>Deze pagina is alleen toegankelijk voor een ingelogde beheerder.</h2>";
    echo "<p><a href='index.php'>Terug naar home</a></p>";
    exit;
}

require_once "dbconnect.php";

function test_input($value)
{
    $value = trim($value);
    $value = stripslashes($value);
    return $value;
}

$errors = [];
$values = [
    'productname' => '',
    'ingredients' => '',
    'allergens' => '',
    'price' => '',
    'categoryid' => '',
    'supplierid' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $values['productname'] = test_input($_POST['productname'] ?? '');
    $values['ingredients'] = test_input($_POST['ingredients'] ?? '');
    $values['allergens'] = test_input($_POST['allergens'] ?? '');
    $values['price'] = test_input($_POST['price'] ?? '');
    $values['categoryid'] = test_input($_POST['categoryid'] ?? '');
    $values['supplierid'] = test_input($_POST['supplierid'] ?? '');

    if ($values['productname'] === '') {
        $errors[] = 'Productnaam is verplicht.';
    } elseif (!preg_match('/^[A-Za-zÀ-ÖØ-öø-ÿ ]+$/u', $values['productname'])) {
        $errors[] = 'Productnaam mag alleen letters en spaties bevatten.';
    }

    if ($values['ingredients'] !== '' && !preg_match('/^[A-Za-z0-9À-ÖØ-öø-ÿ ]+$/u', $values['ingredients'])) {
        $errors[] = 'Ingrediënten mogen alleen letters, cijfers en spaties bevatten.';
    }

    if ($values['allergens'] !== '' && !preg_match('/^[A-Za-z0-9À-ÖØ-öø-ÿ ]+$/u', $values['allergens'])) {
        $errors[] = 'Allergenen mogen alleen letters, cijfers en spaties bevatten.';
    }

    if ($values['price'] === '') {
        $errors[] = 'Prijs is verplicht.';
    } elseif (!preg_match('/^\d+,\d{2}$/', $values['price'])) {
        $errors[] = 'Prijs moet in het formaat 12,34 zijn.';
    }

    if ($values['categoryid'] === '' || !ctype_digit($values['categoryid'])) {
        $errors[] = 'Selecteer een categorie.';
    }

    if ($values['supplierid'] === '' || !ctype_digit($values['supplierid'])) {
        $errors[] = 'Selecteer een leverancier.';
    }

    if (empty($errors)) {
        try {
            $categoryCheck = $db->prepare('SELECT ID FROM category WHERE ID = :id');
            $categoryCheck->execute([':id' => (int)$values['categoryid']]);
            if ($categoryCheck->rowCount() === 0) {
                $errors[] = 'De geselecteerde categorie bestaat niet.';
            }

            $supplierCheck = $db->prepare('SELECT ID FROM supplier WHERE ID = :id');
            $supplierCheck->execute([':id' => (int)$values['supplierid']]);
            if ($supplierCheck->rowCount() === 0) {
                $errors[] = 'De geselecteerde leverancier bestaat niet.';
            }
        } catch (PDOException $e) {
            $errors[] = 'Fout bij het controleren van categorie of leverancier: ' . $e->getMessage();
        }
    }

    if (empty($errors)) {
        try {
            $priceForDb = str_replace(',', '.', $values['price']);
            $insertQuery = $db->prepare(
                'INSERT INTO product (productname, ingredients, allergens, price, categoryid, supplierid, isactive) ' .
                'VALUES (:productname, :ingredients, :allergens, :price, :categoryid, :supplierid, :isactive)'
            );
            $insertQuery->execute([
                ':productname' => $values['productname'],
                ':ingredients' => $values['ingredients'],
                ':allergens' => $values['allergens'],
                ':price' => $priceForDb,
                ':categoryid' => (int)$values['categoryid'],
                ':supplierid' => (int)$values['supplierid'],
                ':isactive' => 'J'
            ]);

            $_SESSION['product_add_success'] = 'Product is succesvol toegevoegd.';
            header('Location: pro-crud-get.php');
            exit;
        } catch (PDOException $e) {
            $errors[] = 'Fout bij het opslaan van het product: ' . $e->getMessage();
        }
    }
}

$_SESSION['product_add_errors'] = $errors;
$_SESSION['product_add_values'] = $values;
header('Location: pro-crud-add.php');
exit;

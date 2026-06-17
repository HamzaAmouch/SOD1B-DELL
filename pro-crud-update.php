<!DOCTYPE html>
<html lang="nl"> 
<head>
	 <meta charset="UTF-8">
	 <title>Product wijzigen</title>
	 <link rel="stylesheet" type="text/css" href="company.css">  
</head>

<body>
    <?php
        // controleren of de gebruiker afkomt van het product update scherm
        if (!isset($_POST["pro_applyupdate"]) )
        {
            header("Refresh: 4, url=pro-crud-get.php");
            echo "<h2>Je bent hier niet op de juiste manier gekomen!</h2>";
            exit();
        }

        // Formulierveld ophalen naar een variabele en gelijk opschonen bij invoer product id
        $pro_pk = test_input($_POST["pro_pk"]);
 
        // Het geselecteerde id moet wel nummeriek zijn
        if (!is_numeric($pro_pk))
        {
            header("Refresh: 9, url=pro-crud-get.php");
            echo "<h2>Je moet een nummer opgeven!!</h2>";
            exit();
        }

        // Controleren of de opgegeven Primary Key nog hetzelfde is (voorkomen van hacken)
        session_start();
        if (!isset($_SESSION["update_pro_pk"]) || $_SESSION["update_pro_pk"] <> $pro_pk)
        {
            header("Refresh: 4, url=index.php");
            echo "<h2>HACKER HACKER HACKER</h2>";
            echo "Je hebt geprobeerd de werking van het programma te wijzigen!";
            exit();
        }
        
        // Haal de overige formulier velden binnen
        $pro_name = test_input($_POST["pro_name"]);
        $pro_ingredients = test_input($_POST["pro_ingredients"]);
        $pro_allergens = test_input($_POST["pro_allergens"]);
        $pro_price = test_input($_POST["pro_price"]);
        $pro_category = test_input($_POST["pro_category"]);
        $pro_supplier = test_input($_POST["pro_supplier"]);
        $pro_isactive = test_input($_POST["pro_isactive"]);

        // Check all input to verify content
        if (empty($pro_name) || !check_alfabet($pro_name))
        {
            header("Refresh: 4, url=pro-crud-upd.php");
            echo "<h2>De productnaam moet ingevuld zijn (met alleen letters en spaties)</h2>";
            exit();
        }

        if (!empty($pro_ingredients) && !check_alfanum($pro_ingredients))
        {
            header("Refresh: 4, url=pro-crud-upd.php");
            echo "<h2>Ingrediënten mogen alleen letters, cijfers en spaties bevatten</h2>";
            exit();
        }

        if (!empty($pro_allergens) && !check_alfanum($pro_allergens))
        {
            header("Refresh: 4, url=pro-crud-upd.php");
            echo "<h2>Allergenen mogen alleen letters, cijfers en spaties bevatten</h2>";
            exit();
        }

        // Validate price format (12,34)
        if (empty($pro_price) || !preg_match("/^[0-9]+,[0-9]{2}$/",$pro_price))
        {
            header("Refresh: 4, url=pro-crud-upd.php");
            echo "<h2>De prijs moet in het formaat 12,34 worden ingevuld</h2>";
            exit();
        }
        
        // Convert price format from 12,34 to 12.34 for database
        $pro_price = str_replace(',', '.', $pro_price);

        if (!is_numeric($pro_category))
        {
            header("Refresh: 4, url=pro-crud-upd.php");
            echo "<h2>Je moet een geldige categorie selecteren!</h2>";
            exit();
        }

        if (!is_numeric($pro_supplier))
        {
            header("Refresh: 4, url=pro-crud-upd.php");
            echo "<h2>Je moet een geldige leverancier selecteren!</h2>";
            exit();
        }

        if ($pro_isactive != 'J' && $pro_isactive != 'N')
        {
            header("Refresh: 4, url=pro-crud-upd.php");
            echo "<h2>Ongeldige status!</h2>";
            exit();
        }

        // Controleer of categorie en leverancier bestaan
        require_once "dbconnect.php";
        try 
        {
            $sQuery = "SELECT * FROM category WHERE ID = :pro_category";
            $oStmt = $db->prepare($sQuery);
            $oStmt->bindValue(":pro_category", $pro_category);
            $oStmt->execute();

            if ($oStmt->rowCount() <> 1) 
            {
                header("Refresh: 4, url=pro-crud-upd.php");
                echo "<h2>De opgegeven categorie bestaat niet!</h2>";
                exit();
            }

            $sQuery = "SELECT * FROM supplier WHERE ID = :pro_supplier";
            $oStmt = $db->prepare($sQuery);
            $oStmt->bindValue(":pro_supplier", $pro_supplier);
            $oStmt->execute();

            if ($oStmt->rowCount() <> 1) 
            {
                header("Refresh: 4, url=pro-crud-upd.php");
                echo "<h2>De opgegeven leverancier bestaat niet!</h2>";
                exit();
            }
        } catch (PDOException $e) 
        {
            $sMsg = '<p> 
                        Regelnummer: ' . $e->getLine() . '<br /> 
                        Bestand: ' . $e->getFile() . '<br /> 
                        Foutmelding: ' . $e->getMessage() . ' 
                    </p>';
    
            trigger_error($sMsg);
            die();
        }

        // Pas na alle controles bouw je de header (met navigatie) op.

        try 
        {
            $sQuery = "UPDATE `product` SET `productname`= :pro_name,
                                           `ingredients`= :pro_ingredients,
                                           `allergens`= :pro_allergens,
                                           `price`= :pro_price,
                                           `categoryid`= :pro_category,
                                           `supplierid`= :pro_supplier,
                                           `isactive`= :pro_isactive
                                        WHERE ID = :pro_pk";
            $oStmt = $db->prepare($sQuery);
            $oStmt->bindValue(":pro_name", $pro_name);
            $oStmt->bindValue(":pro_ingredients", $pro_ingredients);
            $oStmt->bindValue(":pro_allergens", $pro_allergens);
            $oStmt->bindValue(":pro_price", $pro_price);
            $oStmt->bindValue(":pro_category", $pro_category);
            $oStmt->bindValue(":pro_supplier", $pro_supplier);
            $oStmt->bindValue(":pro_isactive", $pro_isactive);
            $oStmt->bindValue(":pro_pk", $pro_pk);
            $oStmt->execute();

            header("Refresh: 2, url=pro-crud-get.php");
            echo "<header class='spacebelowabove'>";
            echo "<h1>Product wijzigen</h1>";
            // hieronder wordt het menu opgehaald. -->
                include "nav.html";
    	    echo "</header>";

            echo "<h2>Het product is gewijzigd in de database!</h2>";

        } catch (PDOException $e) 
        {
            $sMsg = '<p> 
                        Regelnummer: ' . $e->getLine() . '<br /> 
                        Bestand: ' . $e->getFile() . '<br /> 
                        Foutmelding: ' . $e->getMessage() . ' 
                    </p>';
    
            trigger_error($sMsg);
            die();
        }

    ?>
    
    <?php
    // Hier komen alle functies te staan

    // test_input zorgt voor het opschonen van een veld in een formulier.
    function test_input($inpData)
    {
        $inpData = trim($inpData);
        $inpData = stripslashes($inpData);
        $inpData = htmlspecialchars($inpData);
        return $inpData;
    }

        // check_alfanum controleert of de input alleen uit letters, cijfers of spaties bestaat
        function check_alfanum($inpData)
        {
            if (preg_match("/^[a-zA-Z0-9-' ]*$/",$inpData)) 
            {
                return true;
            }
            else
            {
                return false;
            }
        }

        // check_alfabet controleert of de input alleen uit letters en spaties bestaat.
        function check_alfabet($inpData)
        {
            if (preg_match("/^[a-zA-Z-' ]*$/",$inpData)) 
            {
                return true;
            }
            else
            {
                return false;
            }
        }

    ?>    

</body>
</html>
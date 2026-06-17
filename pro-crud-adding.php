<!DOCTYPE html>
<html lang="nl"> 
<head>
	 <meta charset="UTF-8">
	 <title>Product toevoegen</title>
	 <link rel="stylesheet" type="text/css" href="company.css">  
</head>

<body>
    <?php
        session_start();
        // controleren of de gebruiker afkomt van het product add scherm
        if (!isset($_POST["pro_applyinsert"]) )
        {
            header("Refresh: 4, url=pro-crud-get.php");
            echo "<h2>Je bent hier niet op de juiste manier gekomen!</h2>";
            exit();
        }

        // Haal alle formulier velden binnen
        $pro_name = test_input($_POST["pro_name"]);
        $pro_ingredients = test_input($_POST["pro_ingredients"]);
        $pro_allergens = test_input($_POST["pro_allergens"]);
        $pro_price = test_input($_POST["pro_price"]);
        $pro_category = test_input($_POST["pro_category"]);
        $pro_supplier = test_input($_POST["pro_supplier"]);

        // Set SESSION variable to mark checking of input
        $_SESSION["chk_pro_insert"] = true;

        // Check all input to verify content
        if (empty($pro_name) || !check_alfabet($pro_name))
        {
            header("Refresh: 4, url=pro-crud-add.php");
            echo "<h2>De productnaam moet ingevuld zijn (met alleen letters en spaties)</h2>";
            exit();
        }

        if (!empty($pro_ingredients) && !check_alfanum($pro_ingredients))
        {
            header("Refresh: 4, url=pro-crud-add.php");
            echo "<h2>Ingrediënten mogen alleen letters, cijfers en spaties bevatten</h2>";
            exit();
        }

        if (!empty($pro_allergens) && !check_alfanum($pro_allergens))
        {
            header("Refresh: 4, url=pro-crud-add.php");
            echo "<h2>Allergenen mogen alleen letters, cijfers en spaties bevatten</h2>";
            exit();
        }

        // Validate price format (12,34)
        if (empty($pro_price) || !preg_match("/^[0-9]+,[0-9]{2}$/",$pro_price))
        {
            header("Refresh: 4, url=pro-crud-add.php");
            echo "<h2>De prijs moet in het formaat 12,34 worden ingevuld</h2>";
            exit();
        }
        
        // Convert price format from 12,34 to 12.34 for database
        $pro_price = str_replace(',', '.', $pro_price);

        if (!is_numeric($pro_category))
        {
            header("Refresh: 4, url=pro-crud-add.php");
            echo "<h2>Je moet een geldige categorie selecteren!</h2>";
            exit();
        }

        if (!is_numeric($pro_supplier))
        {
            header("Refresh: 4, url=pro-crud-add.php");
            echo "<h2>Je moet een geldige leverancier selecteren!</h2>";
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
                header("Refresh: 4, url=pro-crud-add.php");
                echo "<h2>De opgegeven categorie bestaat niet!</h2>";
                exit();
            }

            $sQuery = "SELECT * FROM supplier WHERE ID = :pro_supplier";
            $oStmt = $db->prepare($sQuery);
            $oStmt->bindValue(":pro_supplier", $pro_supplier);
            $oStmt->execute();

            if ($oStmt->rowCount() <> 1) 
            {
                header("Refresh: 4, url=pro-crud-add.php");
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
        // checking complete, release SESSION variable
        unset($_SESSION["chk_pro_insert"]);

        // Pas na alle controles bouw je de header (met navigatie) op.

        try 
        {
            $sQuery = "INSERT INTO `product`(`productname`, `ingredients`, `allergens`, `price`, 
                                            `categoryid`, `supplierid`, `isactive`) 
                                VALUES (:pro_name, :pro_ingredients, :pro_allergens, :pro_price,
                                        :pro_category, :pro_supplier, 'J')";
            $oStmt = $db->prepare($sQuery);
            $oStmt->bindValue(":pro_name", $pro_name);
            $oStmt->bindValue(":pro_ingredients", $pro_ingredients);
            $oStmt->bindValue(":pro_allergens", $pro_allergens);
            $oStmt->bindValue(":pro_price", $pro_price);
            $oStmt->bindValue(":pro_category", $pro_category);
            $oStmt->bindValue(":pro_supplier", $pro_supplier);
            $oStmt->execute();

            header("Refresh: 2, url=pro-crud-get.php");
            echo "<header class='spacebelowabove'>";
            echo "<h1>Product toevoegen</h1>";
            // hieronder wordt het menu opgehaald. -->
                include "nav.html";
    	    echo "</header>";

            echo "<h2>Het product is toegevoegd aan de database!</h2>";

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
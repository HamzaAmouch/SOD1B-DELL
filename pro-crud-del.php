<!DOCTYPE html>
<html lang="nl"> 
<head>
	 <meta charset="UTF-8">
	 <title>Verwijder product</title>
	 <link rel="stylesheet" type="text/css" href="company.css">  
</head>

<body>
    <?php
        // controleren of de gebruiker afkomt van het product selectie scherm
        if (!isset($_POST["submt-sel-pro-del"]) )
        {
            header("Refresh: 4, url=pro-crud-get.php");
            echo "<h2>Je bent hier niet op de juiste manier gekomen!</h2>";
            exit();
        }

        // Formulierveld ophalen naar een variabele en gelijk opschonen bij invoer product id
        $pro_pk = test_input($_POST["sel-pro-pk"]);
        $return_prog = "pro-crud-get.php";
 
        // Het geselecteerde id moet wel nummeriek zijn
        if (!is_numeric($pro_pk))
        {
            header("Refresh: 4, url=pro-crud-get.php");
            echo "<h2>Je moet een nummer opgeven!!</h2>";
            exit();
        }

        // Controleren of de opgegeven Primary Key daadwerkelijk aanwezig is in de database
        require_once "dbconnect.php";

        try 
        {
            $sQuery = "SELECT * FROM product WHERE ID = :pro_pk";
            $oStmt = $db->prepare($sQuery);
            $oStmt->bindValue(":pro_pk", $pro_pk);
            $oStmt->execute();

            if ($oStmt->rowCount() <> 1) 
            {
                header("Refresh: 4, url=pro-crud-get.php");
                echo "<h2>Het opgegeven productnummer bestaat niet!</h2>";
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

        // Check if product is used in any undelivered purchase
        try 
        {
            $sQuery = "SELECT COUNT(*) as cnt FROM purchaseline pl
                       JOIN purchase p ON pl.purchaseid = p.ID
                       WHERE pl.productid = :pro_pk AND p.isdelivered = 'N'";
            $oStmt = $db->prepare($sQuery);
            $oStmt->bindValue(":pro_pk", $pro_pk);
            $oStmt->execute();
            
            $aResult = $oStmt->fetch(PDO::FETCH_ASSOC);
            
            if ($aResult['cnt'] > 0) 
            {
                header("Refresh: 4, url=pro-crud-get.php");
                echo "<h2>Dit product kan niet verwijderd worden!</h2>";
                echo "<p>Het product is nog gebruikt in niet-afgeleverde bestellingen.</p>";
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

        // Sla de gekozen product Primary Key op in SESSION om te controleren of deze ongewijzigd blijft
        session_start();
        $_SESSION["delete_pro_pk"] = $pro_pk;

        // Zet standaard header op de pagina.
        echo "<header class='spacebelowabove'>";
		echo "<h1>Verwijder product</h1>";
		// hieronder wordt het menu opgehaald. -->
			include "nav.html";
	    echo "</header>";

        // Haal nu de gegevens op van het éne record dat is gevonden.
        $dataProduct = $oStmt->fetch(PDO::FETCH_ASSOC);
        
   ?>
    <main class="centering">
        <h2 class="spacebelowabove">Verwijderen product</h2>
        <form action="pro-crud-delete.php" method="post" class="tabledisp">
            <input type="text" name="pro_pk" readonly value="<?php echo $pro_pk; ?>" >

            <fieldset class="tbodyflex">
                <label for="pro_name">Productnaam : </label>
                <input type="text" name="pro_name" readonly value="<?php echo htmlspecialchars($dataProduct["productname"]); ?>" >
            </fieldset>
            <fieldset class="tbodyflex">
                <label for="pro_ingredients">Ingrediënten : </label>
                <textarea rows="3" cols="50" name="pro_ingredients" readonly><?php echo htmlspecialchars($dataProduct["ingredients"]); ?></textarea>
            </fieldset>
            <fieldset class="tbodyflex">
                <label for="pro_allergens">Allergenen : </label>
                <textarea rows="3" cols="50" name="pro_allergens" readonly><?php echo htmlspecialchars($dataProduct["allergens"]); ?></textarea>
            </fieldset>
            <fieldset class="tbodyflex">
                <label for="pro_price">Prijs : </label>
                <input type="text" name="pro_price" readonly value="€ <?php echo number_format($dataProduct["price"], 2, ',', '.'); ?>" >
            </fieldset>
            <fieldset class="tbodyflex, spacebelowabove">
                <button type="submit" formaction="pro-crud-get.php">Breek af</button>&nbsp;&nbsp;
                <input type="submit" value="Verwijder" name="pro_applydelete">
            </fieldset>
        </form>
    </main>

    
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

    ?>    

</body>
</html>
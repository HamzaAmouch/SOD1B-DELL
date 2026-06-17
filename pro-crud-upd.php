<!DOCTYPE html>
<html lang="nl"> 
<head>
	 <meta charset="UTF-8">
	 <title>Product wijzigen</title>
	 <link rel="stylesheet" type="text/css" href="company.css">  
</head>

<body>
    <?php
        // controleren of de gebruiker afkomt van het product selectie scherm
        if (!isset($_POST["submt-sel-pro-upd"]) )
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

        // Sla de gekozen product Primary Key op in SESSION om te controleren of deze ongewijzigd blijft
        session_start();
        $_SESSION["update_pro_pk"] = $pro_pk;

        // Zet standaard header op de pagina.
        echo "<header class='spacebelowabove'>";
		echo "<h1>Product wijzigen</h1>";
		// hieronder wordt het menu opgehaald. -->
			include "nav.html";
	    echo "</header>";

        // Haal nu de gegevens op van het éne record dat is gevonden.
        $dataProduct = $oStmt->fetch(PDO::FETCH_ASSOC);
        
   ?>
    <main class="centering">
        <h2 class="spacebelowabove">Wijzigen product</h2>
        <form action="pro-crud-update.php" method="post" class="tabledisp">
            <input type="text" name="pro_pk" readonly value="<?php echo $pro_pk; ?>" >

            <fieldset class="tbodyflex">
                <label for="pro_name">Productnaam : </label>
                <input type="text" name="pro_name" required value="<?php echo htmlspecialchars($dataProduct["productname"]); ?>" >
            </fieldset>
            <fieldset class="tbodyflex">
                <label for="pro_ingredients">Ingrediënten : </label>
                <textarea rows="3" cols="50" name="pro_ingredients"><?php echo htmlspecialchars($dataProduct["ingredients"]); ?></textarea>
            </fieldset>
            <fieldset class="tbodyflex">
                <label for="pro_allergens">Allergenen : </label>
                <textarea rows="3" cols="50" name="pro_allergens"><?php echo htmlspecialchars($dataProduct["allergens"]); ?></textarea>
            </fieldset>
            <fieldset class="tbodyflex">
                <label for="pro_price">Prijs (12,34) : </label>
                <input type="text" name="pro_price" required value="<?php echo number_format($dataProduct["price"], 2, ',', ''); ?>" pattern="[0-9]+,[0-9]{2}">
            </fieldset>
            <fieldset class="tbodyflex">
                <label for="pro_category">Categorie : </label>
                <select name="pro_category" required>
                    <?php
                        try {
                            $sQuery2 = "SELECT * FROM category ORDER BY name";
                            $oStmt2 = $db->prepare($sQuery2);
                            $oStmt2->execute();

                                while ($aCategory = $oStmt2->fetch(PDO::FETCH_ASSOC)) {
                                    if ($dataProduct["categoryid"] == $aCategory['ID'])
                                    {
                                        echo '<option value="'.$aCategory['ID'].'" selected>'.$aCategory['name'].'</option>';
                                    }
                                    else
                                    {
                                        echo '<option value="'.$aCategory['ID'].'">'.$aCategory['name'].'</option>';
                                    }
                                }
                        } catch (PDOException $e) {
                            $sMsg = '<p> 
                                        Regelnummer: ' . $e->getLine() . '<br /> 
                                        Bestand: ' . $e->getFile() . '<br /> 
                                        Foutmelding: ' . $e->getMessage() . ' 
                                    </p>';

                            trigger_error($sMsg);
                        }
                    ?>
                </select>
            </fieldset>
            <fieldset class="tbodyflex">
                <label for="pro_supplier">Leverancier : </label>
                <select name="pro_supplier" required>
                    <?php
                        try {
                            $sQuery3 = "SELECT * FROM supplier ORDER BY company";
                            $oStmt3 = $db->prepare($sQuery3);
                            $oStmt3->execute();

                                while ($aSupplier = $oStmt3->fetch(PDO::FETCH_ASSOC)) {
                                    if ($dataProduct["supplierid"] == $aSupplier['ID'])
                                    {
                                        echo '<option value="'.$aSupplier['ID'].'" selected>'.$aSupplier['company'].'</option>';
                                    }
                                    else
                                    {
                                        echo '<option value="'.$aSupplier['ID'].'">'.$aSupplier['company'].'</option>';
                                    }
                                }
                        } catch (PDOException $e) {
                            $sMsg = '<p> 
                                        Regelnummer: ' . $e->getLine() . '<br /> 
                                        Bestand: ' . $e->getFile() . '<br /> 
                                        Foutmelding: ' . $e->getMessage() . ' 
                                    </p>';

                            trigger_error($sMsg);
                        }
                    ?>
                </select>
            </fieldset>
            <fieldset class="tbodyflex">
                <label for="pro_isactive">Status : </label>
                <select name="pro_isactive" required>
                    <?php
                        if ($dataProduct["isactive"] == "J")
                        {
                            echo '<option value="J" selected>Actief</option>';
                            echo '<option value="N">Inactief</option>';
                        }
                        else
                        {
                            echo '<option value="J">Actief</option>';
                            echo '<option value="N" selected>Inactief</option>';
                        }
                    ?>
                </select>
            </fieldset>
            <fieldset class="tbodyflex, spacebelowabove">
                <button type="submit" formaction="pro-crud-get.php">Breek af</button>&nbsp;&nbsp;
                <input type="submit" value="Opslaan" name="pro_applyupdate">
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
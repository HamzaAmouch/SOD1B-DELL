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
        
        // Check if user is admin
        if (!isset($_SESSION['isadmin']) || $_SESSION['isadmin'] != 'J') {
            header("Refresh: 4, url=index.php");
            echo "<h2>Geen toegang! Alleen beheerders kunnen producten toevoegen.</h2>";
            exit();
        }
        
        // controleren of de gebruiker afkomt van het product selectie scherm
        if (!isset($_POST["submt-sel-pro-add"]))
        {
            if ((isset($_SESSION["chk_pro_insert"]) && $_SESSION["chk_pro_insert"]))
            {
                header("Refresh: 4, url=pro-crud-get.php");
                echo "<h2>Je bent hier niet op de juiste manier gekomen!</h2>";
                exit();
            }
        }
        // Zet standaard header op de pagina.
        echo "<header class='spacebelowabove'>";
		echo "<h1>Product toevoegen</h1>";
		// hieronder wordt het menu opgehaald. -->
			include "nav.html";
	    echo "</header>";

        // formulier om gegevens voor nieuw product op te halen.
   ?>
   
    <main class="centering">
        <h2 class="spacebelowabove">Toevoegen product</h2>
        <form action="pro-crud-adding.php" method="post" class="tabledisp">

            <fieldset class="tbodyflex">
                <label for="pro_name">Productnaam : </label>
                <input type="text" name="pro_name" required >
            </fieldset>
            <fieldset class="tbodyflex">
                <label for="pro_ingredients">Ingrediënten : </label>
                <textarea rows="3" cols="50" name="pro_ingredients"></textarea>
            </fieldset>
            <fieldset class="tbodyflex">
                <label for="pro_allergens">Allergenen : </label>
                <textarea rows="3" cols="50" name="pro_allergens"></textarea>
            </fieldset>
            <fieldset class="tbodyflex">
                <label for="pro_price">Prijs (12,34) : </label>
                <input type="text" name="pro_price" required pattern="[0-9]+,[0-9]{2}">
            </fieldset>
            <fieldset class="tbodyflex">
                <label for="pro_category">Categorie : </label>
                <select name="pro_category" required>
                    <?php
                        require_once "dbconnect.php";
                        try {
                            $sQuery2 = "SELECT * FROM category ORDER BY name";
                            $oStmt2 = $db->prepare($sQuery2);
                            $oStmt2->execute();

                                while ($aCategory = $oStmt2->fetch(PDO::FETCH_ASSOC)) {
                                    echo '<option value="'.$aCategory['ID'].'">'.$aCategory['name'].'</option>';
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
                                    echo '<option value="'.$aSupplier['ID'].'">'.$aSupplier['company'].'</option>';
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
            <fieldset class="tbodyflex, spacebelowabove">
                <button type="submit" formaction="pro-crud-get.php">Breek af</button>&nbsp;&nbsp;
                <input type="submit" value="Sla op" name="pro_applyinsert">
            </fieldset>
        </form>
    </main>

</body>
</html>
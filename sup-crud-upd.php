<!DOCTYPE html>
<html lang="nl"> 
<head>
	 <meta charset="UTF-8">
	 <title>Wijzig leverancier</title>
	 <link rel="stylesheet" type="text/css" href="company.css">  
</head>

<body>
    <?php
        // controleren of de gebruiker afkomt van het leverancier selectie scherm
        // Dat weet je doordat hij dan daar de submit knop heeft ingedrukt
        if (!isset($_POST["submt-sel-supp-upd"]) )
        {
            header("Refresh: 4, url=sup-crud-get.php");
            echo "<h2>Je bent hier niet op de juiste manier gekomen!</h2>";
            exit();
        }

        // Formulierveld ophalen naar een variabele en gelijk opschonen bij invoer lev.nr
        $supp_pk = test_input($_POST["sel-supp-pk"]);
        $return_prog = "sup-crud-get.php";
 
        // Het geselecteerde id moet wel nummeriek zijn
        if (!is_numeric($supp_pk))
        {
            header("Refresh: 4, url=sup-crud-get.php");
            echo "<h2>Je moet een nummer opgeven!!</h2>";
            exit();
        }

        // Controleren of de opgegeven Primary Key daadwerkelijk aanwezig is in de database
        // Hiervoor doe je een SELECT op de gewenste tabel.

        // Pas na alle controles bouw je de header (met navigatie) op.

//        include "header.php";
        require_once "dbconnect.php";

        try 
        {
            $sQuery = "SELECT * FROM supplier WHERE id = :supp_pk";
            $oStmt = $db->prepare($sQuery);
            $oStmt->bindValue(":supp_pk", $supp_pk);
            $oStmt->execute();

            /* Wanneer er twee of meer records gevonden worden, is er iets fout in de database. De primary key moet
               uniek zijn.
               Wanneer er géén record gevonden worden, bestaat de opgegeven primary key niet.
            */
            if ($oStmt->rowCount() <> 1) 
            {
                header("Refresh: 4, url=sup-crud-get.php");
                echo "<h2>Het opgegeven leveranciersnummer bestaat niet!</h2>";
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

        // Sla de gekozen supplier Primary Key op in SESSION om te controleren of deze ongewijzigd blijft
        session_start();
        $_SESSION["update_supp_pk"] = $supp_pk;

        // Zet standaard header op de pagina.
        echo "<header class='spacebelowabove'>";
		echo "<h1>Wijzig leverancier</h1>";
		// hieronder wordt het menu opgehaald. -->
			include "nav.html";
	    echo "</header>";

        // Haal nu de gegevens op van het éne record dat is gevonden.
        $dataSupplier = $oStmt->fetch(PDO::FETCH_ASSOC);
        
        /* alle gegevens van de gewenste supplier staan nu in de named array $dataSupplier.
           Je stopt nu het werken met PHP om het formulier gewoon in HTML te kunnen tonen.

           Elk veld in het formulier wordt gevuld met gegevens uit de $dataSupplier. 
           Dat gebeurt telkens met een klein stukje PHP waarin de value van het veld met "echo" wordt gevuld.

           LET OP: bij elke value staat ook een dubbele aanhalingsteken openen, dat na het stukje PHP wordt
           afgesloten. Dat is noodzakelijk omdat anders de input bij de eerste spatie wordt afgebroken. Zonder
           de aanhalingstekens zou van een bedrijfsnaam 'Monster Transport' alleen het 'Monster' te zien zijn.
        */
   ?>
    <main class="centering">
        <h2 class="spacebelowabove">Wijzigen leverancier</h2>
        <form action="sup-crud-update.php" method="post" class="tabledisp">
            <input type="text" name="supp_pk" readonly value="<?php echo $supp_pk; ?>" >

            <fieldset class="tbodyflex">
                <label for="supp_company">Naam leverancier : </label>
                <input type="text" name="supp_company" required value="<?php echo $dataSupplier["company"]; ?>" >
            </fieldset>
            <fieldset class="tbodyflex">
                <label for="supp_streetaddress">Adres leverancier : </label>
                <input type="text" name="supp_streetaddress" value="<?php echo $dataSupplier["adress"]; ?>" >
            </fieldset>
            <fieldset class="tbodyflex">
                <label for="supp_streetnr">Adres leverancier : </label>
                <input type="text" name="supp_streetnr" value="<?php echo $dataSupplier["streetnr"]; ?>" >
            </fieldset>
            <fieldset class="tbodyflex">
                <label for="supp_zipcode">Postcode : </label>
                <input type="text" name="supp_zipcode" value="<?php echo $dataSupplier["zipcode"]; ?>" >
            </fieldset>
            <fieldset class="tbodyflex">
                <label for="supp_city">Vestigingsplaats : </label>
                <input type="text" name="supp_city" required value="<?php echo $dataSupplier["city"]; ?>" >
            </fieldset>
            <fieldset class="tbodyflex">
                <label for="supp_state">Provincie : </label>
                <input type="text" name="supp_state" required value="<?php echo $dataSupplier["state"]; ?>" >
            </fieldset>
            <fieldset class="tbodyflex">
                <label for="supp_country">Land : </label>
                <select name="supp_country" required>
                    <?php
                        require_once "dbconnect.php";
                        try {
                            $sQuery2 = "SELECT * FROM country";
                            $oStmt2 = $db->prepare($sQuery2);
                            $oStmt2->execute();

                                while ($aCountry = $oStmt2->fetch(PDO::FETCH_ASSOC)) {
                                    if ($dataSupplier["countryid"] == $aCountry['idcountry'])
                                    {
                                        echo '<option value="'.$aCountry['idcountry'].'" selected>'.$aCountry['name'].'</option>';
                                    }
                                    else
                                    {
                                        echo '<option value="'.$aCountry['idcountry'].'">'.$aCountry['name'].'</option>';
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
                <label for="supp_teleph">Telefoon : </label>
                <input type="text" name="supp_teleph" value="<?php echo $dataSupplier["telephone"]; ?>" >
            </fieldset>
            <fieldset class="tbodyflex">
                <label for="supp_domain">Website : </label>
                <textarea rows="10" cols="50" name="supp_domain" ><?php echo $dataSupplier["website"]; ?></textarea>
            </fieldset>
        <!-- Een "reset" knop (leeg maken van het formulier) heeft hieronder geen zin. Dan zijn alle gegevens
        van de leverancier verdwenen. Het is daarom beter om terug te keren naar het eerste formulier. daar
        kan de gebruiker de gegevens opnieuw opvragen. 
        Bij het openen van het programma is het aanleverende programma vastgelegd in variabele $return_prog -->
            <fieldset class="tbodyflex, spacebelowabove">
                <button type="submit" formaction="<?php echo $return_prog; ?>">Breek af</button>&nbsp;&nbsp;
                <input type="submit" value="Verwerk" name="supp_applyupdate">
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
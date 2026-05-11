<!DOCTYPE html>
<html lang="nl"> 
<head>
	 <meta charset="UTF-8">
	 <title>Wijzig leverancier</title>
	 <link rel="stylesheet" type="text/css" href="company.css">  
</head>

<body>
    <?php
        session_start();
        // controleren of de gebruiker afkomt van het leverancier selectie scherm
        // Dat weet je doordat hij dan daar de submit knop heeft ingedrukt
        // OF doordat de session variabele in het adding programma is aangemaakt en op "true" gezet
        if (!isset($_POST["submt-sel-supp-add"]))
        {
            echo "Niet van submt-sel-supp-add <br>";
            if ((isset($_SESSION["chk_supp_insert"]) && $_SESSION["chk_supp_insert"]))
            {
                echo "Bestaat session:X".isset($_SESSION["chk_supp_insert"]);
                header("Refresh: 4, url=sup-crud-get.php");
                echo "<h2>Je bent hier niet op de juiste manier gekomen!</h2>";
                exit();
            }
        }
        // Zet standaard header op de pagina.
        echo "<header class='spacebelowabove'>";
		echo "<h1>Wijzig leverancier</h1>";
		// hieronder wordt het menu opgehaald. -->
			include "nav.html";
	    echo "</header>";

        // formulier om gegevens voor nieuwe leverancier op te halen.
   ?>
   
    <main class="centering">
        <h2 class="spacebelowabove">Toevoegen leverancier</h2>
        <form action="sup-crud-adding.php" method="post" class="tabledisp">

            <fieldset class="tbodyflex">
                <label for="supp_company">Naam leverancier : </label>
                <input type="text" name="supp_company" required >
            </fieldset>
            <fieldset class="tbodyflex">
                <label for="supp_streetaddress">Adres leverancier : </label>
                <input type="text" name="supp_streetaddress">
            </fieldset>
            <fieldset class="tbodyflex">
                <label for="supp_streetnr">Huisnummer leverancier : </label>
                <input type="text" name="supp_streetnr" >
            </fieldset>
            <fieldset class="tbodyflex">
                <label for="supp_zipcode">Postcode : </label>
                <input type="text" name="supp_zipcode">
            </fieldset>
            <fieldset class="tbodyflex">
                <label for="supp_city">Vestigingsplaats : </label>
                <input type="text" name="supp_city" required >
            </fieldset>
            <fieldset class="tbodyflex">
                <label for="supp_state">Provincie : </label>
                <input type="text" name="supp_state" required>
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
                                    echo '<option value="'.$aCountry['idcountry'].'">'.$aCountry['name'].'</option>';
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
                <input type="text" name="supp_teleph">
            </fieldset>
            <fieldset class="tbodyflex">
                <label for="supp_domain">Website : </label>
                <textarea rows="10" cols="50" name="supp_domain" ></textarea>
            </fieldset>
        <!-- Een "reset" knop (leeg maken van het formulier) heeft hieronder geen zin. Dan zijn alle gegevens
        van de leverancier verdwenen. Het is daarom beter om terug te keren naar het eerste formulier. daar
        kan de gebruiker de gegevens opnieuw opvragen. 
        Bij het openen van het programma is het aanleverende programma vastgelegd in variabele $return_prog -->
            <fieldset class="tbodyflex, spacebelowabove">
                <button type="submit" formaction="sup-crud-get.php">Breek af</button>&nbsp;&nbsp;
                <input type="submit" value="Verwerk" name="supp_applyinsert">
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
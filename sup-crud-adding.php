<!DOCTYPE html>
<html lang="nl"> 
<head>
	 <meta charset="UTF-8">
	 <title>Leverancier toevoegen</title>
	 <link rel="stylesheet" type="text/css" href="company.css">  
</head>

<body>
    <?php
        session_start();
        // controleren of de gebruiker afkomt van het leverancier selectie scherm
        // Dat weet je doordat hij dan daar de submit knop heeft ingedrukt
        if (!isset($_POST["supp_applyinsert"]) )
        {
            header("Refresh: 4, url=sup-crud-get.php");
            echo "<h2>Je bent hier niet op de juiste manier gekomen!</h2>";
            exit();
        }

        // Haal alle formulier velden binnen
        $supp_company = test_input($_POST["supp_company"]);
        $supp_streetaddress = test_input($_POST["supp_streetaddress"]);
        $supp_streetnr = test_input($_POST["supp_streetnr"]);
        $supp_zipcode = test_input($_POST["supp_zipcode"]);
        $supp_city = test_input($_POST["supp_city"]);
        $supp_state = test_input($_POST["supp_state"]);
        $supp_country = test_input($_POST["supp_country"]);
        $supp_teleph = test_input($_POST["supp_teleph"]);
        $supp_domain = test_input($_POST["supp_domain"]);

        // Set SESSION variable to mark checking of input (for return to previous program)
        $_SESSION["chk_supp_insert"] = true;

        // Check all input to verify content
        if (empty($supp_company) || !check_alfabet($supp_company))
        {
            header("Refresh: 4, url=sup-crud-add.php");
            echo "<h2>De leveranciersnaam moet ingevuld zijn (met alleen letters en spaties)</h2>";
            exit();
        }

        if (empty($supp_streetaddress) || !check_alfanum($supp_streetaddress))
        {
            header("Refresh: 4, url=sup-crud-add.php");
            echo "<h2>Het vestigingsadres moet ingevuld zijn (met alleen letters, cijfers en spaties)</h2>";
            exit();
        }

        if (empty($supp_streetnr) || !is_numeric($supp_streetnr))
        {
            header("Refresh: 4, url=sup-crud-add.php");
            echo "<h2>Het huisnummer moet ingevuld zijn (met alleen cijfers)</h2>";
            exit();
        }

        if (empty($supp_zipcode) || !check_alfanum($supp_zipcode))
        {
            header("Refresh: 4, url=sup-crud-add.php");
            echo "<h2>Het huisnummer moet ingevuld zijn (met alleen letters, cijfers en spaties)</h2>";
            exit();
        }

        if (empty($supp_city) || !check_alfabet($supp_city))
        {
            header("Refresh: 4, url=sup-crud-add.php");
            echo "<h2>De vestigingsplaats moet ingevuld zijn (met alleen letters en spaties)</h2>";
            exit();
        }

        if (!check_alfanum($supp_state))
        {
            header("Refresh: 4, url=sup-crud-add.php");
            echo "<h2>De provincie mag alleen letters, cijfers en spaties bevatten</h2>";
            exit();
        }

        // Controleer of er een geldig land is ingevuld
        require_once "dbconnect.php";
        try 
        {
            $sQuery = "SELECT * FROM country WHERE idcountry = :supp_country";
            $oStmt = $db->prepare($sQuery);
            $oStmt->bindValue(":supp_country", $supp_country);
            $oStmt->execute();

            if ($oStmt->rowCount() <> 1) 
            {
                header("Refresh: 4, url=sup-crud-get.php");
                echo "<h2>Het opgegeven landnummer bestaat niet!</h2>";
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
        unset($_SESSION["chk_supp_insert"]);

        // Pas na alle controles bouw je de header (met navigatie) op.

        try 
        {
            $sQuery = "INSERT INTO `supplier`(`company`, `adress`, `streetnr`, 
                                              `zipcode`, `city`, `state`, `countryid`, 
                                              `telephone`, `website`) 
                                VALUES (:supp_company, :supp_streetaddress, :supp_streetnr,
                                        :supp_zipcode, :supp_city, :supp_state, :supp_country,
                                        :supp_teleph, :supp_domain)";
            $oStmt = $db->prepare($sQuery);
            $oStmt->bindValue(":supp_company", $supp_company);
            $oStmt->bindValue(":supp_streetaddress", $supp_streetaddress);
            $oStmt->bindValue(":supp_streetnr", $supp_streetnr);
            $oStmt->bindValue(":supp_zipcode", $supp_zipcode);
            $oStmt->bindValue(":supp_city", $supp_city);
            $oStmt->bindValue(":supp_state", $supp_state);
            $oStmt->bindValue(":supp_country", $supp_country);
            $oStmt->bindValue(":supp_teleph", $supp_teleph);
            $oStmt->bindValue(":supp_domain", $supp_domain);
            $oStmt->execute();

            header("Refresh: 2, url=sup-crud-get.php");
            echo "<header class='spacebelowabove'>";
            echo "<h1>Toevoegen leverancier</h1>";
            // hieronder wordt het menu opgehaald. -->
                include "nav.html";
    	    echo "</header>";

            echo "<h2>De gegevens zijn toegevoegd aan de database!</h2>";
//            exit();

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
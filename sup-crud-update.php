<!DOCTYPE html>
<html lang="nl"> 
<head>
	 <meta charset="UTF-8">
	 <title>Leverancier wijzigen</title>
	 <link rel="stylesheet" type="text/css" href="company.css">  
</head>

<body>
    <?php
        // controleren of de gebruiker afkomt van het leverancier selectie scherm
        // Dat weet je doordat hij dan daar de submit knop heeft ingedrukt
        if (!isset($_POST["supp_applyupdate"]) )
        {
            header("Refresh: 4, url=sup-crud-get.php");
            echo "<h2>Je bent hier niet op de juiste manier gekomen!</h2>";
            exit();
        }

        // Formulierveld ophalen naar een variabele en gelijk opschonen bij invoer lev.nr
        $supp_pk = test_input($_POST["supp_pk"]);
 
        // Het geselecteerde id moet wel nummeriek zijn
        if (!is_numeric($supp_pk))
        {
            header("Refresh: 9, url=sup-crud-get.php");
            echo "<h2>Je moet een nummer opgeven!!</h2>";
            exit();
        }

        // Controleren of de opgegeven Primary Key nog hetzelfde is (voorkomen van hacken)
        // In het vorig programma is de PK opgeslagen in SESSION.
        session_start();
        if (!isset($_SESSION["update_supp_pk"]) || $_SESSION["update_supp_pk"] <> $supp_pk)
        {
            header("Refresh: 4, url=index.php");
            echo "<h2>HACKER HACKER HACKER</h2>";
            echo "Je hebt geprobeerd de werking van het programma te wijzigen!";
            exit();
        }
        
        // Haal de overige formulier velden binnen
        $supp_company = test_input($_POST["supp_company"]);
        $supp_streetaddress = test_input($_POST["supp_streetaddress"]);
        $supp_streetnr = test_input($_POST["supp_streetnr"]);
        $supp_zipcode = test_input($_POST["supp_zipcode"]);
        $supp_city = test_input($_POST["supp_city"]);
        $supp_state = test_input($_POST["supp_state"]);
        $supp_country = test_input($_POST["supp_country"]);
        $supp_teleph = test_input($_POST["supp_teleph"]);
        $supp_domain = test_input($_POST["supp_domain"]);

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


        // Pas na alle controles bouw je de header (met navigatie) op.

        try 
        {
            $sQuery = "UPDATE `supplier` SET `company`= :supp_company,
                                             `adress`= :supp_streetaddress,
                                             `streetnr`= :supp_streetnr,
                                             `zipcode`= :supp_zipcode,
                                             `city`= :supp_city,
                                             `state`= :supp_state,
                                             `countryid`= :supp_country,
                                             `telephone`= :supp_teleph,
                                             `website`= :supp_domain 
                                        WHERE id = :supp_pk";
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
            $oStmt->bindValue(":supp_pk", $supp_pk);
            $oStmt->execute();

            header("Refresh: 2, url=sup-crud-get.php");
            echo "<header class='spacebelowabove'>";
            echo "<h1>Wijzig leverancier</h1>";
            // hieronder wordt het menu opgehaald. -->
                include "nav.html";
    	    echo "</header>";

            echo "<h2>De gegevens zijn gewijzigd in de database!</h2>";
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

    ?>    

</body>
</html>
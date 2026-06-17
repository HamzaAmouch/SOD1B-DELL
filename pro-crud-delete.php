<!DOCTYPE html>
<html lang="nl"> 
<head>
	 <meta charset="UTF-8">
	 <title>Product verwijderen</title>
	 <link rel="stylesheet" type="text/css" href="company.css">  
</head>

<body>
    <?php
        // controleren of de gebruiker afkomt van het product delete scherm
        if (!isset($_POST["pro_applydelete"]) )
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
            header("Refresh: 4, url=pro-crud-get.php");
            echo "<h2>Je moet een nummer opgeven!!</h2>";
            exit();
        }

        // Controleren of de opgegeven Primary Key nog hetzelfde is (voorkomen van hacken)
        session_start();
        if (!isset($_SESSION["delete_pro_pk"]) || $_SESSION["delete_pro_pk"] <> $pro_pk)
        {
            header("Refresh: 4, url=index.php");
            echo "<h2>HACKER HACKER HACKER</h2>";
            echo "Je hebt geprobeerd de werking van het programma te wijzigen!";
            exit();
        }
        
        // Pas na alle controles bouw je de header (met navigatie) op.

        try 
        {
            require_once "dbconnect.php";
            $sQuery = "DELETE FROM `product` WHERE ID = :pro_pk";
            $oStmt = $db->prepare($sQuery);
            $oStmt->bindValue(":pro_pk", $pro_pk);
            $oStmt->execute();

            header("Refresh: 2, url=pro-crud-get.php");
            echo "<header class='spacebelowabove'>";
            echo "<h1>Product verwijderen</h1>";
            // hieronder wordt het menu opgehaald. -->
                include "nav.html";
    	    echo "</header>";

            echo "<h2>Het product is verwijderd uit de database!</h2>";

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
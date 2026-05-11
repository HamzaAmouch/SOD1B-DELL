<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>bestellingen aanmaken</title>
    <link rel="stylesheet" type="text/css" href="company.css">  
</head>
<body>
    <header>
		<h1>Vullen bestellingen - bestelregels</h1>
	</header>
 
	<!-- Initialisatie van variabelen -->
    <section>
    <?php 
        require_once("../dbconnect.php");
        $currentday = date('Y-m-d');
        $startingday = date('Y-m-d', strtotime($currentday.' - 1899 days'));
        $loopdate = $startingday;
        $maxpurlines = 5;
        $nextLoopDay = array(1, 0, 10, 2, 1, 3, 0, 1, 0, 1, 6, 0, 2, 4, 1, 3, 0, 4, 1, 0, 1, 2, 3, 6, 3, 0, 4, 0, 1, 5, 1);
        $totnrpurchases = 0;
        $totnrpurchaselines =0;
        $mostlyDelivered = array(1, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 1, 1, 1);

// Alle klanten id's ophalen en in array zetten om willekeurige klanten te kunnen kiezen
        try 
		{  
			$query = $db->prepare("SELECT id FROM client"); 
			$query->execute();	
			if($query->rowCount()>0) 
			{
				$totnrclients = $query->rowCount();
                $indexnrclients = 0;
                $result=$query->fetchAll(PDO::FETCH_ASSOC);
                foreach($result as $rij) {
                    $allclients[$indexnrclients]=$rij["id"];
                    $indexnrclients++;
                }
			} else {
                echo "GEEN KLANTEN GEVONDEN !!!!";
                die();
            }

            $randomclient = $allclients[array_rand($allclients)];
        }

        catch(PDOException $e) 
		{ 
			$sMsg = '<p> 
					Regelnummer: '.$e->getLine().'<br /> 
					Bestand: '.$e->getFile().'<br /> 
					Foutmelding: '.$e->getMessage().' 
				</p>'; 
			 
			trigger_error($sMsg); 
		} 

        // alle product id's ophalen en in array plaatsen om willekeurig product te kunnen kiezen.
        try 
		{  
			$query = $db->prepare("SELECT id FROM product"); 
			$query->execute();	
			if($query->rowCount()>0) 
			{
					$result=$query->fetchAll(PDO::FETCH_ASSOC);
                    $totnrproducts = $query->rowCount();
                    $indexnrproducts = 0;
                    foreach($result as $rij) {
                        $allproducts[$indexnrproducts]=$rij["id"];
                        $indexnrproducts++;
                    }
    		} else {
                echo "GEEN PRODUCTEN GEVONDEN !!";
                die();
            }
        }

        catch(PDOException $e) 
		{ 
			$sMsg = '<p> 
					Regelnummer: '.$e->getLine().'<br /> 
					Bestand: '.$e->getFile().'<br /> 
					Foutmelding: '.$e->getMessage().' 
				</p>'; 
			 
			trigger_error($sMsg); 
		} 

        if ($totnrclients = 0 || $totnrproducts = 0)
        {
            echo "RowCount geeft verkeerd resultaat <br>";
            echo "Totaal aantal klanten: ". $totnrclients ."<br>";
            echo "Totaal aantal producten: " . $totnrproducts . "<br>";
            die();
        }

        ?>
    </section>
    
    <main>

        <?php
            while ($loopdate <= $currentday) {
                // velden vullen voor het purchase record
                $randomclient = $allclients[array_rand($allclients)];
                $purchaseday = $loopdate;
                $randomDelivered = $mostlyDelivered[array_rand($mostlyDelivered)];
                // nu zijn alle velden van purchase gevuld en kan de INSERT plaats vinden
                $query = $db->prepare("INSERT INTO purchase
                                            (purchasedate, clientid, delivered) 
                                VALUES (:purchaseday, :randomclient, :randomdelivered)");
                $query->bindValue(':purchaseday', $purchaseday);
                $query->bindValue(':randomclient', $randomclient);
                $query->bindValue(':randomdelivered', $randomDelivered);
                $query->execute();
                $totnrpurchases++;

                // Nu opslaan welke purchase-id zojuist is aangemaakt en bepalen hoeveel purchaselines er komen.
                $purchaserecentid = $db->lastInsertId()*1;

                // echo "Toegevoegde purchase: $purchaserecentid <br>";

                // Als de lastInsertId niet correct werkt, hier alsnog het laatste record ophalen
                if ($purchaserecentid == 0)
                {
                    $querymax = $dp->prepare("SELECT MAX(id) AS maxid FROM purchase");
                    $querymax->execute();
                    $resultmax=$querymax->fetch(PDO::FETCH_ASSOC);
                    $purchaserecentid = $resultmax["maxid"] * 1;
                    echo "Aangepaste recente purchaseid: $purchaserecentid <br>";
                }

                // Eerst random bepalen hoeveel aankoopregels toegevoegd gaan worden
                $nrpurchaselines = random_int(1,$maxpurlines);

                // Nu de purchase lines gaan toevoegen
                for ($i=0; $i < $nrpurchaselines; $i++) {
                    // Random een productnummer kiezen om toe te voegen aan de bestelregel
                    $productid = $allproducts[array_rand($allproducts)];

                    // De gegevens van het random gekozen product ophalen
                    try {
                        $selectprod = $db->prepare("SELECT * FROM product WHERE id = $productid");
                        $selectprod->execute();
                        $paidprice = 0;
                        if($selectprod->rowCount()==1) 
                        {
                                $result=$selectprod->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($result as $selectedprod) {
                                    $paidprice = $selectedprod["price"] * 1;
                                }
                        } else {
                            echo "Iets ging vreselijk fout. Aantal producten niet gelijk 1.";
                            die();
                        }

                        // Afhankelijk van de prijs het aantal gekochte producten bepalen
                        if ($paidprice > 1000) {
                            $qty = 1;
                        } else {
                            $qty = random_int(1, 10);
                        }
                    }
            
                    catch(PDOException $e) 
                    { 
                        $sMsg = '<p> 
                                Regelnummer: '.$e->getLine().'<br /> 
                                Bestand: '.$e->getFile().'<br /> 
                                Foutmelding: '.$e->getMessage().' 
                            </p>'; 
                         
                        trigger_error($sMsg); 
                    } 

                    // echo "Toevoegen purchaseline nr $totnrpurchaselines voor purchase $purchaserecentid <br>";

                    // Alle gegevens voor een purchaseline record zijn bekend. We kunnen invoegen in de db
                    $query = $db->prepare("INSERT INTO purchaseline 
                                        (purchaseid, 
                                        productid, 
                                        price, 
                                        quantity) 
                                    VALUES ($purchaserecentid,
                                            $productid,
                                            $paidprice,
                                            $qty)"); 
                    $query->execute();
                    $totnrpurchaselines++;

                    // Einde loop voor toevoegen van aankoopregels
                }

                // ophogen van de purchasedatum in loop met random aantal dagen
                $loopdate = date('Y-m-d', strtotime($loopdate.' + '.$nextLoopDay[array_rand($nextLoopDay)].' days'));

            }
            echo "<br><br><br><p>In totaal zijn weggeschreven:</p>";
            echo "<p>Aantal aankopen (purchase): $totnrpurchases</p>";
            echo "<p>Aantal aankoop regels (purchaseline): $totnrpurchaselines</p>";
        ?>
    </main>	
    
</body>
</html>
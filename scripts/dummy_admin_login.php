<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dummy login klant</title>
</head>
<body>
    <?php
      require_once("..\\dbconnect.php");
      session_start();

      // Alle klanten id's ophalen en in array zetten om willekeurige klanten te kunnen kiezen
      try 
      {  
        $query = $db->prepare("SELECT id FROM client WHERE isadmin = 'J' "); 
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

      // Select random client for login
      $randomclient = $allclients[array_rand($allclients)];
      // Retrieve info of randomly selected client
      try 
      {  
        $query = $db->prepare("SELECT * FROM client WHERE id = :randomclient ");
        $query->bindValue(":randomclient", $randomclient);
        $query->execute();
        if($query->rowCount() == 1) 
        {
          $dataClient = $query->fetch(PDO::FETCH_ASSOC);
          $_SESSION["benJeErAl"] = true;
          $_SESSION["welkNummerIsDit"] = $randomclient;
          $_SESSION["wieBenJeDan"] = $dataClient["first_name"]." ".$dataClient["last_name"];
          $_SESSION["SoortToegang"] = "Beheer";
        } else {
            echo "<h1>INLOGGEN MISLUKT !!!!</h1>";
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
      
    ?>
</body>
</html>
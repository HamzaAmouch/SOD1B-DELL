<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="..\company.css">
        <title>Update empty passwords</title>
    </head>
    <body>
        <?php
            $defaulPw = "w8woord";
            $hashedpw = password_hash($defaulPw, PASSWORD_DEFAULT);

            #1 verbinding met database
            require "../dbconnect.php";

            #2 Verwijderen gegevens in de database
            try 
            {
                $updPaswrd = $db->prepare("UPDATE client SET pswrd = :clpwd  WHERE id <> 0");
                $updPaswrd->bindValue(":clpwd", $hashedpw);
                $updPaswrd->execute();

                header('refresh:5; url=../index.php'); 
                echo "<h2>Alle lege wachtwoorden zijn gezet naar de opgegeven default</h2>";
#                exit(); 
            }
            catch(PDOException $e) 
            {
                die("Fout bij verbinden met database: ".$e->getMessage());
            };

        ?>
    </body>
</html>
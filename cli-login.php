<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>klant login</title>
    <link rel="stylesheet" href="company.css">
</head>
<body>
    <?php
        session_start();
        if(!isset($_POST["cliFrmBtn"]))
            {
                header("Refresh: 4; url=index.php");
                include "nav.html";
                echo "<h2>Foutieve manier naar dit programma!!</h2>";
                echo "<p>Je keert terug naar het menu</p>";
                exit;
            };
        
        /*-- controleer de formvelden op de juiste invoer */
        $usrid = testinput($_POST["cliFrmEml"]);
        $usrpw = testinput($_POST["cliFrmPw"]);

        /*-- Open de database */
        require_once "dbconnect.php";

        $chkInlog = $db->prepare("SELECT * FROM client WHERE email = :usrid");
        $chkInlog->bindValue(":usrid", $usrid);
        $chkInlog->execute();
        $chkRow = $chkInlog->fetch(PDO::FETCH_ASSOC);
        
        if(!$chkRow || !password_verify($usrpw, $chkRow["passwrd"]))
            {
                header("Refresh: 4; url=index.php");
                include "nav.html";
                echo "<h2>Foutieve combinatie van email en wachtwoord!</h2>";
                echo "<p>Je keert terug naar het menu</p>";
                exit;
            };

        include "nav.html";
    ?>

        <p>&nbsp;</p>
        <h2>Klant login</h2>
        <form action="cli-login.php" method="post">
            <fieldset>
                <label for="cliFrmEml">User-id (email)</label>
                <input type="email" name="cliFrmEml" >
            </fieldset>
            <fieldset>
                <label for="cliFrmPw">Wachtwoord</label>
                <input type="text" name="cliFrmPw">
            </fieldset>
            <fieldset>
                <input type="submit" value="Afbreken" formaction="index.php" >
                <input type="submit" value="Inloggen" name="cliFrmBtn">
            </fieldset>

        </form>
    ?>

    <?php
        function testinput($formfield)
        {
            return $formfield;
        };

        $item = $stmt->fetch(PDO::FETCH_ASSOC);
    ?>
</body>

</html>
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
</body>
</html>
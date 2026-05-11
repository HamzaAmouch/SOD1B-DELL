<!DOCTYPE html>
<html lang="nl"> 
<head>
	 <meta charset="UTF-8">
	 <title>Selecteer leverancier</title>
	 <link rel="stylesheet" type="text/css" href="company.css">  
</head>

<body>
	<header class="spaceabovebelow">
		<h1>Welkom bij de Bread Company</h1>
		<!-- hieronder wordt het menu opgehaald. -->
		<?php
			session_start(); 
			include "nav.html";
		?>
	</header>
	<div class="centerflex, spaceabovebelow">
		<?php
			require_once "dbconnect.php";
			try {
				$sQuery = "SELECT * FROM supplier";
				$oStmt = $db->prepare($sQuery);
				$oStmt->execute();
				?>

				<p>&nbsp;</p>
				<h2 class='centercell'>Onderhoud leveranciers</h2>
				<p>&nbsp;</p>

				<?php
				if ($oStmt->rowCount() > 0) {
					echo '<div class="flexverticalcenter">';

					// eerst een formulier met een knop om een nieuwe leverancier toe te voegen.
					echo '<form action="sup-crud-add.php" method="post">';
					echo '  <label for="submt-sel-supp-add">Toevoegen leverancier &nbsp; </label>';
					echo '  <input type="submit" value="Voeg toe" name="submt-sel-supp-add" >';
					echo '</form>';
					echo '<p class="spacebelowabove">&nbsp;&nbsp;&nbsp;&nbsp;</p>';

					// hierna in tabelvorm het overzicht van alle leveranciers
					echo '<table class="tabledisp2">';
					echo '<thead>';
					echo '<td>Lev.nr.</td>';
					echo '<td>Lev.naam</td>';
					echo '<td>Adres</td>';
					echo '<td>Huisnr</td>';
					echo '<td>Woonplaats</td>';
					echo '<td>Actieknop</td>';
					echo '</thead>';
					while ($aRow = $oStmt->fetch(PDO::FETCH_ASSOC)) {
						echo '<tr><form action="sup-crud-upd.php" method="POST">';
						echo '<td><input type="number" readonly name="sel-supp-pk" value="' . $aRow['ID'] . '"></td>';
						echo '<td>' . $aRow['company'] . '</td>';
						echo '<td>' . $aRow['adress'] . '</td>';
						echo '<td>' . $aRow['streetnr'] . '</td>';
						echo '<td>' . $aRow['city'] . '</td>';
						echo '<td><input type="submit" value="Wijzig" name="submt-sel-supp-upd">.&nbsp;&nbsp;';
						echo '<input type="submit" value="Verwijder" name="submt-sel-supp-del" formaction="sup-crud-del.php"></td>';
						echo '</form></tr>';
					}
					echo '</table></div>';
				} else {
					echo 'Helaas, geen gegevens bekend';
				}
			} catch (PDOException $e) {
				$sMsg = '<p> 
							Regelnummer: ' . $e->getLine() . '<br /> 
							Bestand: ' . $e->getFile() . '<br /> 
							Foutmelding: ' . $e->getMessage() . ' 
						</p>';

				trigger_error($sMsg);
			}
			$db = null;
		?>
	</div>

</body>
</html>
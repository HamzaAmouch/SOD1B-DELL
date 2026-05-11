<!DOCTYPE html>
<html lang="nl"> 
<head>
	 <meta charset="UTF-8">
	 <title>Bread Company</title>
	 <link rel="stylesheet" type="text/css" href="company.css">  
</head>

<body>
	<header>
		<h1>Welkom bij de Bread Company</h1>
		<!-- hieronder wordt het menu opgehaald. -->
		<?php
			session_start(); 
			include "nav.html";
		?>
	</header>
 	<?php
	require_once "dbconnect.php";
	try {
		$sQuery = "SELECT * FROM supplier";
		$oStmt = $db->prepare($sQuery);
		$oStmt->execute();

		echo "<p>&nbsp;</p><h2 class='centercell'>Overzicht leveranciers</h2><p>&nbsp;</p>";
		if ($oStmt->rowCount() > 0) {
			echo '<div class="centerflex"><table class="tabledisp2">';
			echo '<thead>';
			echo '<td>Lev.nr.</td>';
			echo '<td>Lev.naam</td>';
			echo '<td>Adres</td>';
			echo '<td>Huisnr</td>';
			echo '<td>Postcode</td>';
			echo '<td>Woonplaats</td>';
			echo '</thead>';
			while ($aRow = $oStmt->fetch(PDO::FETCH_ASSOC)) {
				echo '<tr>';
				echo '<td>' . $aRow['ID'] . '</td>';
				echo '<td>' . $aRow['company'] . '</td>';
				echo '<td>' . $aRow['adress'] . '</td>';
				echo '<td>' . $aRow['streetnr'] . '</td>';
				echo '<td>' . $aRow['zipcode'] . '</td>';
				echo '<td>' . $aRow['city'] . '</td>';
				echo '</tr>';
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


</body>
</html>
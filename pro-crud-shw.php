<!DOCTYPE html>
<html lang="nl"> 
<head>
	 <meta charset="UTF-8">
	 <title>Producten overzicht</title>
	 <link rel="stylesheet" type="text/css" href="company.css">  
</head>

<body>
	<header class="spaceabovebelow">
		<h1>Welkom bij de Bread Company</h1>
		<?php
			session_start(); 
			include "nav.html";
		?>
	</header>
	<div class="centerflex, spaceabovebelow">
		<?php
			require_once "dbconnect.php";
			try {
				// Toon alleen actieve producten met hun categorie
				$sQuery = "SELECT p.ID, p.productname, p.allergens, p.price, c.name as category_name
							FROM product p
							JOIN category c ON p.categoryid = c.ID
							WHERE p.isactive = 'J'
							ORDER BY p.productname";
				$oStmt = $db->prepare($sQuery);
				$oStmt->execute();
				?>

				<p>&nbsp;</p>
				<h2 class='centercell'>Actieve producten</h2>
				<p>&nbsp;</p>

				<?php
				if ($oStmt->rowCount() > 0) {
					echo '<div class="flexverticalcenter">';

					// Toon overzicht van actieve producten in tabelvorm
					echo '<table class="tabledisp2">';
					echo '<thead>';
					echo '<td>Productnaam</td>';
					echo '<td>Allergenen</td>';
					echo '<td>Categorie</td>';
					echo '<td>Prijs</td>';
					echo '</thead>';
					while ($aRow = $oStmt->fetch(PDO::FETCH_ASSOC)) {
						echo '<tr>';
						echo '<td>' . htmlspecialchars($aRow['productname']) . '</td>';
						echo '<td>' . htmlspecialchars($aRow['allergens']) . '</td>';
						echo '<td>' . htmlspecialchars($aRow['category_name']) . '</td>';
						echo '<td>€ ' . number_format($aRow['price'], 2, ',', '.') . '</td>';
						echo '</tr>';
					}
					echo '</table></div>';
				} else {
					echo '<p>Geen actieve producten beschikbaar</p>';
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
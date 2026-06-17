<!DOCTYPE html>
<html lang="nl"> 
<head>
	 <meta charset="UTF-8">
	 <title>Inactieve producten overzicht</title>
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
			
			// Check if user is admin
			if (!isset($_SESSION['isadmin']) || $_SESSION['isadmin'] != 'J') {
				header("Refresh: 4, url=index.php");
				echo "<h2>Geen toegang! Alleen beheerders kunnen deze pagina zien.</h2>";
				exit();
			}
			
			try {
				// Toon alleen inactieve producten met hun leverancier
				$sQuery = "SELECT p.ID, p.productname, c.name as category_name, s.company as supplier_name, p.price
							FROM product p
							JOIN category c ON p.categoryid = c.ID
							JOIN supplier s ON p.supplierid = s.ID
							WHERE p.isactive = 'N'
							ORDER BY p.productname";
				$oStmt = $db->prepare($sQuery);
				$oStmt->execute();
				?>

				<p>&nbsp;</p>
				<h2 class='centercell'>Inactieve producten (Beheerder)</h2>
				<p>&nbsp;</p>

				<?php
				if ($oStmt->rowCount() > 0) {
					echo '<div class="flexverticalcenter">';

					// Toon overzicht van inactieve producten in tabelvorm
					echo '<table class="tabledisp2">';
					echo '<thead>';
					echo '<td>ID</td>';
					echo '<td>Productnaam</td>';
					echo '<td>Categorie</td>';
					echo '<td>Leverancier</td>';
					echo '<td>Prijs</td>';
					echo '</thead>';
					while ($aRow = $oStmt->fetch(PDO::FETCH_ASSOC)) {
						echo '<tr>';
						echo '<td>' . $aRow['ID'] . '</td>';
						echo '<td>' . htmlspecialchars($aRow['productname']) . '</td>';
						echo '<td>' . htmlspecialchars($aRow['category_name']) . '</td>';
						echo '<td>' . htmlspecialchars($aRow['supplier_name']) . '</td>';
						echo '<td>€ ' . number_format($aRow['price'], 2, ',', '.') . '</td>';
						echo '</tr>';
					}
					echo '</table></div>';
				} else {
					echo '<p>Geen inactieve producten beschikbaar</p>';
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

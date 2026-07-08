<?php
session_start();
require_once 'dbconnect.php';

$errors = $_SESSION['errors'] ?? [];
$old = $_SESSION['old'] ?? [];
unset($_SESSION['errors'], $_SESSION['old']);
?>
<!DOCTYPE html>
<html lang="nl">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="company.css">
	<title>Registreren</title>
</head>
<body>
	<h1>Registreren</h1>

	<?php if(!empty($errors)): ?>
		<div style="color:red;">
			<ul>
			<?php foreach($errors as $err): ?>
				<li><?=htmlspecialchars($err)?></li>
			<?php endforeach; ?>
			</ul>
		</div>
	<?php endif; ?>

	<form action="cli-crud-add01.php" method="post">
		<label>First name*: <input type="text" name="first_name" required pattern="[\p{L} ]+" title="Alleen letters en spaties" value="<?=htmlspecialchars($old['first_name'] ?? '')?>"></label><br>
		<label>Last name*: <input type="text" name="last_name" required pattern="[\p{L} ]+" title="Alleen letters en spaties" value="<?=htmlspecialchars($old['last_name'] ?? '')?>"></label><br>
		<label>Email*: <input type="email" name="email" required value="<?=htmlspecialchars($old['email'] ?? '')?>"></label><br>
		<label>Adress*: <input type="text" name="adress" required value="<?=htmlspecialchars($old['adress'] ?? '')?>"></label><br>
		<label>Zipcode*: <input type="text" name="zipcode" required value="<?=htmlspecialchars($old['zipcode'] ?? '')?>"></label><br>
		<label>City*: <input type="text" name="city" required pattern="[\p{L} ]+" title="Alleen letters en spaties" value="<?=htmlspecialchars($old['city'] ?? '')?>"></label><br>
		<label>State: <input type="text" name="state" pattern="[\p{L} ]*" title="Alleen letters en spaties" value="<?=htmlspecialchars($old['state'] ?? '')?>"></label><br>
		<label>Country*: 
			<select name="country" required>
				<option value="">--select--</option>
				<?php
				$countries = ['Netherlands','Belgium','Germany','France','United Kingdom','United States','Other'];
				foreach($countries as $c):
					$sel = (isset($old['country']) && $old['country']==$c) ? 'selected' : '';
					echo "<option value=\"".htmlspecialchars($c)."\" $sel>".htmlspecialchars($c)."</option>";
				endforeach;
				?>
			</select>
		</label><br>
		<label>Telephone: <input type="text" name="telephone" pattern="[0-9\s]*" title="Cijfers en spaties" value="<?=htmlspecialchars($old['telephone'] ?? '')?>"></label><br>
		<label>Wachtwoord*: <input type="password" name="pswrd1" required></label><br>
		<label>Wachtwoord (herhaal)*: <input type="password" name="pswrd2" required></label><br>

		<button type="submit">Registreren</button>
	</form>
</body>
</html>
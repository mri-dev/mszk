<? require "head.php"; ?>
<h2>Tisztelt <?=$nev?>!</h2>
<p>Köszönjük, hogy érdeklődik szolgáltatásaink iránt! Sikeresen fogadtuk ajánlatkérését, melynek feldolgozását hamarosan megkezdjük és kiajánljuk partnereinkek!</p>
<div><h3>Konfiguráció</h3></div>

<?php if (!empty($new_user_id)): ?>
<div><h3>Új fiókja elkészült! Belépési adatok:</h3></div>
<?php endif; ?>
<? require "footer.php"; ?>

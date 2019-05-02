<? require "head.php"; ?>
<h1>Tisztelt <?=$form['replyToName']?>!</h1>
<br>
<strong style="font-size: 14px; color: black;">Tárgy / Esemény: <u>
<?php switch ($msgData['tipus']) {
	case 'recall':
		 	echo 'Ingyenes visszahívás kérése.';
	break;
	case 'ajanlat':
		 	echo 'Ingyenes ajánlatkérés.';
	break;
	case 'requesttermprice':
		 	echo 'Termék ár kérés.';
	break;
} ?></u></strong>
<br><br>
<?php if ($msgData[uzenet] != ''): ?>
<strong style="color: black;">Ön a következő üzenetet írta:</strong><br><br>
<div style="padding: 0 0 0 15px; border-left: 3px solid #ffa99b;">
	<em>
		<?=$msgData[uzenet]?>
	</em>
</div>
<br><br>
<?php endif; ?>
<strong style="color:black;">Válaszüzenet:</strong><br>
<div style="padding: 0 0 0 15px; border-left: 3px solid #82d082;"><?=$form['replyMsg']?></div>
<br>
<div>
	Üdvözlettel,<br/>
	<strong><?=$settings['page_title']?></strong>
</div>
<? require "footer.php"; ?>

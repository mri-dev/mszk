<? require "head.php"; ?>
<h1>Tisztelt <?=$name?>!</h1>
<br>
<h3>Sikeresen lezárult egy megrendelés, ahol az Ön ajándló partnerkódját használtak, ezért</h3>
Önnek jóváírtunk virtuális egyenlegén <strong><?=$cash?> Ft</strong> összeget, amit weboldalunkon keresztül használhat fel megrendeléseinél.<br><br>

A partnerkód tranzakciókat az alábbi linken érheti el bejelentkezés után: <br>
<a href="http://<?=str_replace('http://','',$settings['page_url'])?>/user/referring"><strong>http://<?=str_replace('http://','',$settings['page_url'])?>/user/referring</strong></a>
<br><br><br>
<div>
	Üdvözlettel,<br/>
	<?=$settings['page_title']?>
</div>
<? require "footer.php"; ?>
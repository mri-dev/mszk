<? require "head.php"; ?>

<h2>Tisztelt <?=$data['neve']?>!</h2>
<br>
Örömmel értesítjük, hogy <strong>aktiváltuk 25%-os kedvezményre jogosító <u>"A Jövő bajnokainak / Arena Water Card"</u> kártyáját!</strong> Minden vásárlása után 25% kedvezményben részesül a(z) <?=$settings['page_title']?> weboldalon keresztül vásárolt termékekből! 
<br><br>
REGISZTRÁLT KÁRTYA ADATOK: <br>
Kártya száma: <strong><?=$data['kartya_szam']?></strong> <br>
Egyesület: <strong><?=$data['egyesulet']?></strong>
<br><br>
A kedvezmény részleteiről bővebben olvashat az alábbi linken: <br>
<a href="<?=$settings['domain']?>/p/arena_water_card"><?=$settings['domain']?>/p/arena_water_card</a>
<br><br>
Jó vásárlást kívánunk!
<? require "footer.php"; ?>
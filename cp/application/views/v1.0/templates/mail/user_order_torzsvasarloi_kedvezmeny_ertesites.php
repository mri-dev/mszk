<? require "head.php"; ?>

<h2>Tisztelt <?=$nev?>!</h2>
<br>
Örömmel értesítjük, hogy elérte <strong>törzsvásárlói kedvezményünk minimális határát</strong>, így további megrendeléseiért <strong><?=$kedvezmeny?>%-kal kevesebbet kell fizetnie!</strong>
<br><br>
A kedvezmények részelteiről bővebben <a href="http://<?=str_replace(array('http://','www.'),'',$settings['page_url'])?>/p/torzsvasarloi_kedvezmeny">itt olvashat</a>! 
<br>
<br>
Jó vásárlást kívánunk!
<? require "footer.php"; ?>
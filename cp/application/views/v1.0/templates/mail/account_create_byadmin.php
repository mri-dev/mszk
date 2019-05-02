<? require "head.php"; ?>

<h2>Tisztelt <?=$nev?>!</h2>
<br>
A(z) <strong><?=$settings['page_title']?></strong> (<?=$settings['page_url']?>) weboldal adminisztrátorai sikeresen regisztrálták Önt a rendszerbe. 
<br><br>
Belépéshez használja a következő linket: <br>
<strong><a href="<?=$settings['page_url']?>/user/belepes"><?=$settings['page_url']?>/user/belepes</a></strong><br>
<br>
Az Ön jelszava: <strong><?=$jelszo?></strong><br>
--<br>
<em>Jelszavát bármikor lecserélheti a <a href="<?=$settings['page_url']?>/user/jelszocsere"><strong><?=$settings['page_url']?>/user/jelszocsere</strong></a> linkre kattintva.</em>

<? require "footer.php"; ?>
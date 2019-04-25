	<footer>
		<div class="footer-inner">
			<div class="pw">
				<div class="logo">
					<a href="/"><img src="<?=IMG?>logo-white.svg" alt="<?=$this->settings['page_title']?>"></a>
				</div>
				<div class="navigator">
					<ul>
						<li class="impresszum"><a href="/impresszum"><?=__('Impresszum')?></a></li>
						<li class="aszf"><a href="/aszf"><?=__('Általános Szerződési Feltételek')?></a></li>
						<li class="adatv"><a href="/adatvedelem"><?=__('Adatvédelmi Tájékoztató')?></a></li>
					</ul>
					<ul>
						<li class="kapcsolat"><a href="/kapcsolat"><?=__('Kapcsolat')?></a></li>
						<li class="ceginfo"><a href="/ceginfo"><?=__('Céginfo')?></a></li>
						<li class="rolunk"><a href="/rolunk"><?=__('Rólunk')?></a></li>
					</ul>
				</div>
			</div>
		</div>
		<div class="copyright">
			<div class="pw">
				&copy; Copyright <?=date('Y')?>. <span class="page_title"><?=$this->settings['page_title']?></span> <span class="poweredby">Powered by <a target="_blank" href="https://www.web-pro.hu">WEBPRO Solutions</a> </span>
			</div>
		</div>
	</footer>
</body>
</html>

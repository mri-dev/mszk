<!-- Register module -->
<div id="login">
  <div class="row justify-content-md-center">
    <div class="logo col-md-12 center">
      <a href="<?=HOMEDOMAIN?>"><img src="<?=IMG?>logo-white.svg" alt="<?=$this->settings['page_title']?>"></a>
    </div>
  </div>
  <br>
  <? if($this->err){ echo $this->bmsg; } ?>
  <br>
	<div class="row justify-content-md-center login-box-holder">
    <div class="col-md-12">
      <h3><?=__('Fiók aktiválás')?></h3>
    </div>
    <div class="bg col-md-12">
      <div class="activation-status">
        <? if ($this->err): ?>
          <i class="fas fa-check-circle fa-5x"></i>
          <h2><?=$this->msg?></h2>
          <br>
          <a href="/belepes" class="btn btn-danger btn-md"><?=__('Tovább a bejelentkezéshez')?></a>
        <? else: ?>
          <i class="far fa-check-circle fa-5x"></i>
          <h2><?=__('Sikeresen aktiválta regisztrációját!')?></h2>
          <div class="sub"><?=__('Most már bejelentkezhet fiójába az alábbi gombra kattintva.')?></div>
          <br>
          <a href="/belepes" class="btn btn-danger btn-md"><?=__('Tovább a bejelentkezéshez')?></a>
        <? endif; ?>
      </div>
    </div>
  </div>
  <div class="copyright row justify-content-md-center ">
    <div class="col-md-12 center">
      Copyright <?=date('Y')?> &copy; <strong><?=$this->settings['page_title']?></strong> <span class="devby">Powered by <a target="_blank" href="https://www.web-pro.hu">WEBPRO Solutions</a></span>
    </div>
  </div>
</div>

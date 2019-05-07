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
      asdsa
    </div>
  </div>
  <div class="copyright row justify-content-md-center ">
    <div class="col-md-12 center">
      Copyright <?=date('Y')?> &copy; <strong><?=$this->settings['page_title']?></strong> <span class="devby">Powered by <a target="_blank" href="https://www.web-pro.hu">WEBPRO Solutions</a></span>
    </div>
  </div>
</div>

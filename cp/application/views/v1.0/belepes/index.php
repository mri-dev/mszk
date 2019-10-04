<!-- Login module -->
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
      <h3><?=__('Ügyfélkapu')?></h3>
    </div>
    <div class="bg col-md-12">
      <form action="" method="post">
        <input type="hidden" name="return" value="<?=$_GET['return']?>">
        <div class="input-group">
	        <span class="input-group-prepend"><span class="input-group-text"><i class="fas fa-user-tie"></i></span></span>
				  <input type="text" class="form-control" name="email" value="<?=(isset($_GET['email'])) ? $_GET['email']:''?>" placeholder="<?=__('E-mail cím')?>">
				</div>
        <br>
        <div class="input-group">
          <span class="input-group-prepend"><span class="input-group-text"><i class="fa fa-lock"></i></span></span>
      	  <input type="password" class="form-control" name="pw" placeholder="<?=__('Jelszó')?>">
      	</div>
        <br>
        <div class="row align-items-center">
          <div class="col-md-4 reset-password order-md-2 order-lg-1">
            <a href="/jelszo"><?=__('Elfelejtett jelszó')?></a>
          </div>
          <div class="col-md-8 order-md-1 order-lg-2 right">
            <a href="/regisztracio"><?=__('Regisztráció')?></a>
            <button name="login" class="btn btn-danger"><?=__('Bejelentkezés')?> <i class="fa fa-arrow-circle-right"></i></button>
          </div>
        </div>
      </form>
    </div>
  </div>
  <div class="copyright row justify-content-md-center ">
    <div class="col-md-12 center">
      Copyright <?=date('Y')?> &copy; <strong><?=$this->settings['page_title']?></strong> <span class="devby">Powered by <a target="_blank" href="https://www.web-pro.hu">WEBPRO Solutions</a></span>
    </div>
  </div>
</div>
<!--/Login module -->

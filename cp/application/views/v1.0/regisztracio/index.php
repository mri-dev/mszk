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
      <h3><?=__('Fiók regisztráció')?></h3>
    </div>
    <div class="bg col-md-12">
      <form action="" method="post">
        <input type="hidden" name="group" value="user">
        <div class="input-group">
	        <span class="input-group-prepend"><span class="input-group-text"><i class="fas fa-user-tie"></i></span></span>
				  <input type="text" class="form-control<?=($this->code && $this->code == '1001')?' is-invalid':''?>" name="nev" placeholder="<?=__('Az Ön neve')?>" value="<?=$_POST['nev']?>">
				</div>
        <br>
        <div class="input-group">
	        <span class="input-group-prepend"><span class="input-group-text"><i class="fas fa-globe"></i></span></span>
				  <input type="text" class="form-control<?=($this->code && $this->code == '1002')?' is-invalid':''?>" name="email" placeholder="<?=__('E-mail cím')?>" value="<?=$_POST['email']?>">
				</div>
        <br>
        <div class="input-group">
          <span class="input-group-prepend"><span class="input-group-text"><i class="fa fa-lock"></i></span></span>
      	  <input type="password" class="form-control<?=($this->code && ($this->code == '1003' || $this->code == '1034'))?' is-invalid':''?>" name="pw" placeholder="<?=__('Jelszó')?>">
      	</div>
        <br>
        <div class="input-group">
          <span class="input-group-prepend"><span class="input-group-text"><i class="fa fa-lock"></i></span></span>
      	  <input type="password" class="form-control<?=($this->code && ($this->code == '1004' || $this->code == '1034'))?' is-invalid':''?>" name="pw2" placeholder="<?=__('Jelszó újra')?>">
      	</div>
        <br>
        <div class="aszf">
          <input type="checkbox" name="aszf" id="aszf_ok" value="1" class="ccb"> <label for="aszf_ok"><?=sprintf(__('Elolvastam és elfogadom az <a target="_blank" href="%s">Általános Szerződési Feltételek</a>et és az <a target="_blank" href="%s">Adatvédelmi Tájékoztató</a>t!'), HOMEDOMAIN.'aszf',  HOMEDOMAIN.'adatvedelem')?></label>
      	</div>
        <br>
        <div class="row align-items-center">
          <div class="col-md-6 reset-password order-md-2 order-lg-1">
            <a href="/"><?=__('vissza a bejelentkezéshez')?></a>
          </div>
          <div class="col-md-6 order-md-1 order-lg-2 right">
            <button name="register" class="btn btn-danger"><?=__('Regisztráció')?> <i class="fa fa-arrow-circle-right"></i></button>
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

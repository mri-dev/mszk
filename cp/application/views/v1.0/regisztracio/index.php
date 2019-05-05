<div class="register-header">
  <div class="row align-items-center">
    <div class="col-md-2 logo">
      <a href="<?=DOMAIN?>"><img src="<?=IMG?>logo-white.svg" alt=""></a>
    </div>
    <div class="col">
      <h1><?php echo __('Partner regisztráció'); ?></h1>
    </div>
    <div class="col backurl right">
      <a href="<?=HOMEDOMAIN?>"><?=__('vissza')?></a>
    </div>
  </div>
</div>
<div class="inside-content-holder">
  <div class="row">
    <div class="col-md-4">
      <div class="row">
        <div class="col-md-12">
          <h2><?=__('Fiók adatok')?></h2>
          <div class="inp">
            <label for=""><?=__('E-mail cím')?></label>
            <input type="text" class="form-control" name="email" value="">
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <div class="inp">
            <label for=""><?=__('Jelszó')?></label>
            <input type="password" class="form-control" name="pw" value="">
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <div class="inp">
            <label for=""><?=__('Jelszó újra')?></label>
            <input type="password" class="form-control" name="pw2" value="">
          </div>
        </div>
      </div>  
    </div>
    <div class="col-md-8">
      <h2><?=__('Cég adatok')?></h2>
    </div>
  </div>
</div>

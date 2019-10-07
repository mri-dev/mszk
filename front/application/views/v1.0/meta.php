<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes" />
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<!-- STYLES -->
<link rel="icon" href="<?=IMG?>icons/favicon.ico" type="image/x-icon">
<link rel="stylesheet" href="//use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
<?=$this->addStyle('master.min', false, true)?>
<?=$this->addStyle('bootstrap4/bootstrap.min', false, true)?>
<link rel="stylesheet" type="text/css" href="<?=DOMAIN?>public/base/base.css" />
<?=$this->addStyle('media', false, false, true)?>
<!-- JS's -->
<?php $this->switchJSAsync('defer'); ?>
<?=$this->addJS('jquery/jquery-n-ui.pack.min', false, true)?>
<?=$this->addJS('angularjs/angular.pack.1.5.5.min', false, true)?>
<!-- Angular Material Library -->
<?=$this->addJS('angularjs/angular.material.pack.min', false, true)?>
<?=$this->addJS('bootstrap4/bootstrap.min', false, true)?>
<?=$this->addJS('app', false, false, true)?>

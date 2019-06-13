<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/html4"
      xmlns:og="http://ogp.me/ns#"
      xmlns:fb="http://www.facebook.com/2008/fbml" lang="hu-HU">
<head>
	<title><?=strip_tags($this->title)?></title>
    <?=$this->addMeta('robots','index,folow')?>
    <?=$this->SEOSERVICE?>
   	<? $this->render('meta'); ?>
    <?php if ($this->_USER): ?>
      <script type="text/javascript">
      	$(function(){
  			var slideMenu 	= $('#content .slideMenu');
  			var closeNum 	= slideMenu.width() - 75;
  			var isSlideOut 	= getMenuState();
  			var prePressed = false;

  			$(document).keyup(function(e){
  				var key = e.keyCode;
  				if(key === 17){
  					prePressed = false;
  				}
  			});

  			$(document).keydown(function(e){
  				var key = e.keyCode;
  				var keyUrl = new Array();
  					keyUrl[49] = '/'; keyUrl[97] = '/';
  					keyUrl[50] = '/termekek'; keyUrl[98] = '/termekek';
  					keyUrl[51] = '/reklamfal'; keyUrl[99] = '/reklamfal';
  					keyUrl[52] = '/menu'; keyUrl[100] = '/menu';
  					keyUrl[53] = '/oldalak'; keyUrl[101] = '/oldalak';
  					keyUrl[54] = '/kategoriak'; keyUrl[102] = '/kategoriak';
  					keyUrl[55] = '/markak'; keyUrl[103] = '/markak';
  				if(key === 17){
  					prePressed = true;
  				}
  				if(typeof keyUrl[key] !== 'undefined'){
  					if(prePressed){
  						//document.location.href=keyUrl[key];
  					}
  				}
  			});

  			if(isSlideOut){
  				slideMenu.css({
  					'left' : '0px'
  				});
  				$('.ct, footer').css({
  					'paddingLeft' : '280px'
  				});
          $('#top').css({
  					'paddingLeft' : '280px'
  				});
  			}else{
  				slideMenu.css({
  					'left' : '-'+closeNum+'px'
  				});
  				$('.ct, footer').css({
  					'paddingLeft' : '75px'
  				});
          $('#top').css({
  					'paddingLeft' : '75px'
  				});
  			}

  			$('.slideMenuToggle').click(function(){
  				if(isSlideOut){
  					isSlideOut = false;
  					slideMenu.animate({
  						'left' : '-'+closeNum+'px'
  					},200);
  					$('.ct, footer').animate({
  						'paddingLeft' : '75px'
  					},200);
            $('#top').animate({
  						'paddingLeft' : '75px'
  					},200);
  					saveState('closed');
  				}else{
  					isSlideOut = true;
  					slideMenu.animate({
  						'left' : '0px'
  					},200);
  					$('.ct, footer').animate({
  						'paddingLeft' : '280px'
  					},200);
            $('#top').animate({
  						'paddingLeft' : '280px'
  					},200);
  					saveState('opened');
  				}
  			});
  		})

  		function saveState(state){
  			if(typeof(Storage) !== "undefined") {
  				if(state == 'opened'){
  					localStorage.setItem("slideMenuOpened", "1");
  				}else if(state == 'closed'){
  					localStorage.setItem("slideMenuOpened", "0");
  				}
  			}
  		}

  		function getMenuState(){
  			var state =  localStorage.getItem("slideMenuOpened");

  			if(typeof(state) === null){
  				return false;
  			}else{
  				if(state == "1") return true; else return false;
  			}
  		}
      </script>
    <?php endif; ?>
</head>
<body class="<?=$this->bodyclass?> <? if($this->_USER): ?>logged-in user-group-<?=$this->_USERDATA['data']['user_group']?><? endif; ?>" ng-app="App">

<? if($this->_USER): ?>
<div id="top">
	<div class="control-bar">
    <div class="d-flex __justify-content-between align-items-center">
      <div class="message-alerts">
        <div class="d-flex align-items-center">
          <div class="ico">
            <div class="has-msg"><i class="far fa-dot-circle"></i></div>
            <a href="/ertesitesek"><i class="far fa-bell"></i></a>
          </div>
          <div class="alert-message"><?=end(explode(" ", $this->_USERDATA['data']['nev']))?>, <?=sprintf(__('%d db olvasatlan értesítése van!'), 0)?> <a href="/ertesitesek"><?=__('Megnézem')?></a>  </div>
        </div>
      </div>
      <div class="user-group">
        <span><?=$this->_USERDATA['data']['user_group_name']?></span>
      </div>
      <div class="user-block">
        <a href="/profil"><i class="far fa-user-circle"></i> <?=$this->_USERDATA['data']['nev']?> <i class="fas fa-angle-down"></i></a>
      </div>
    </div>
  </div>
  <div class="title-bar">
    <div class="d-flex justify-content-between align-items-center">
      <div class="page-title">
        <?php echo $this->_PAGETITLE; ?>
      </div>
      <div class="actions">
        <div class="d-flex align-items-center">
          <?php echo $this->_PAGEPAGINATION; ?>
          <div class="logout">
            <a href="/home/exit"><?=__('Kijelentkezés')?></a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<? endif; ?>

<div id="content">
<? if($this->_USER): ?>
  <div class="slideMenu">
    <div class="headerlogo">
  	  <div class="slideMenuToggle" title="Kinyit/Becsuk"><i class="fas fa-bars"></i></div>
      <img src="<?=IMG?>logo-white.svg" alt="<?=$this->settings['page_title']?>">
    </div>
    <div class="clr"></div>
 		<div class="menu">
      	<ul>
          	<li class="<?=($this->gets[0] == 'home')?'on':''?>"><a href="/" title="Dashboard"><span class="ni">1</span><i class="fas fa-tachometer-alt"></i> <?=__('Gépház')?></a></li>
            <?php if (false): ?>
              <li class="<?=($this->gets[0] == 'emails')?'on':''?>"><a href="/emails" title="<?=__('E-mail sablonok')?>"><span class="ni">8</span><i class="fa fa-envelope"></i> <?=__('E-mail sablonok')?></a></li>
            <?php endif; ?>

            <li class="div"></li>
            <!-- MODULS-->
            <?php if ( !empty($this->modules) ): ?>
            <li class="div"></li>
            <?php foreach ($this->modules as $module): ?>
            <li class="<?=($this->gets[0] == $module['menu_slug'])?'on':''?>"><a href="/<?=$module['menu_slug']?>" title="<?=$module['menu_title']?>"><span class="ni"><?=$module['ID']?></span><i class="fa fa-<?=$module['faico']?>"></i> <?=$module['menu_title']?></a></li>
            <?php endforeach; ?>
            <?php endif; ?>
            <!-- End of MODULS-->
            <li class="has-more <?=($this->gets[0] == 'ajanlatkeresek')?'on':''?>"><a href="/ajanlatkeresek" title="<?=__('Árajánlat kérések')?>"><span class="ni">8</span><i class="fas fa-file-import"></i> <?=__('Árajánlat kérések')?> <? if($this->badges['offers']['all']['total']!=0): ?><span class="badge badge-primary"><?=$this->badges['offers']['all']['total']?></span><? endif; ?></a></li>
            <?php if ($this->gets[0] == 'ajanlatkeresek'): ?>
              <?php if ($this->_USERDATA['data']['user_group'] == 'superadmin' || $this->_USERDATA['data']['user_group'] == 'admin'): ?>
                <li class="sub <?=($this->gets[0] == 'ajanlatkeresek' && $this->gets[1] == 'feldolgozatlan')?'on':''?>"><a href="/ajanlatkeresek/feldolgozatlan" title="<?=__('Feldolgozatlan')?>"><span class="ni">8</span><i class="fas fa-minus"></i> <?=__('Feldolgozatlan')?></a></li>
              <?php endif; ?>
              <?php if ($this->_USERDATA['data']['user_group'] == 'superadmin' || $this->_USERDATA['data']['user_group'] == 'admin'): ?>
                <li class="sub <?=($this->gets[0] == 'ajanlatkeresek' && $this->gets[1] == 'feldolgozott')?'on':''?>"><a href="/ajanlatkeresek/feldolgozott" title="<?=__('Feldolgozott')?>"><span class="ni">8</span><i class="fas fa-plus"></i> <?=__('Feldolgozott')?></a></li>
              <?php endif; ?>
              <?php if ($this->_USERDATA['data']['user_group'] != 'superadmin' && $this->_USERDATA['data']['user_group'] != 'admin'): ?>
                <li class="sub <?=($this->gets[0] == 'ajanlatkeresek' && $this->gets[1] == 'osszes')?'on':''?>"><a href="/ajanlatkeresek/osszes" title="<?=__('Összes')?>"><span class="ni">8</span><i class="fas fa-minus"></i> <?=__('Összes')?><? if($this->badges['offers']['all']['total']!=0): ?><span class="badge badge-primary"><?=$this->badges['offers']['all']['total']?></span><? endif; ?></a></li>
                <li class="sub <?=($this->gets[0] == 'ajanlatkeresek' && $this->gets[1] == 'fuggoben')?'on':''?>"><a href="/ajanlatkeresek/fuggoben" title="<?=__('Függőben')?>"><span class="ni">8</span><i class="fas fa-question"></i> <?=__('Függőben')?><? if($this->badges['offers']['inprogress']['total']!=0): ?><span class="badge badge-primary"><?=$this->badges['offers']['inprogress']['total']?></span><? endif; ?></a></li>
                <li class="sub <?=($this->gets[0] == 'ajanlatkeresek' && $this->gets[1] == 'ajanlat_elkuldve')?'on':''?>"><a href="/ajanlatkeresek/ajanlat_elkuldve" title="<?=__('Feldolgozott')?>"><span class="ni">8</span><i class="fas fa-plus"></i> <?=__('Feldolgozott')?><? if($this->badges['offers']['progressed']['total']!=0): ?><span class="badge badge-primary"><?=$this->badges['offers']['progressed']['total']?></span><? endif; ?></a></li>
                <li class="sub <?=($this->gets[0] == 'ajanlatkeresek' && $this->gets[1] == 'elfogadott')?'on':''?>"><a href="/ajanlatkeresek/elfogadott" title="<?=__('Elfogadott')?>"><span class="ni">8</span><i class="fas fa-check"></i> <?=__('Elfogadott')?><? if($this->badges['offers']['accepted']['total']!=0): ?><span class="badge badge-primary"><?=$this->badges['offers']['accepted']['total']?></span><? endif; ?></a></li>

              <?php endif; ?>
            <?php endif; ?>
            <li class="has-more <?=($this->gets[0] == 'projektek')?'on':''?>"><a href="/projektek" title="<?=__('Projektek')?>"><span class="ni">8</span><i class="far fa-lightbulb"></i> <?=__('Projektek')?> <? if($this->badges['projects']['inprogress']!=0): ?><span class="badge badge-danger"><?=$this->badges['projects']['inprogress']?></span><? endif; ?></a></li>
            <?php if ($this->gets[0] == 'projektek'): ?>
              <li class="sub <?=($this->gets[0] == 'projektek' && $this->gets[1] == 'aktualis')?'on':''?>"><a href="/projektek/aktualis" title="<?=__('Aktív projektek')?>"><span class="ni">8</span><i class="far fa-folder-open"></i> <?=__('Aktív projektek')?> <? if($this->badges['projects']['inprogress']!=0): ?><span class="badge badge-primary"><?=$this->badges['projects']['inprogress']?></span><? endif; ?></a></li>
              <li class="sub <?=($this->gets[0] == 'projektek' && $this->gets[1] == 'lezart')?'on':''?>"><a href="/projektek/lezart" title="<?=__('Lezárt projektek')?>"><span class="ni">8</span><i class="fas fa-folder"></i> <?=__('Lezárt projektek')?> <? if($this->badges['projects']['closed']!=0): ?><span class="badge badge-primary"><?=$this->badges['projects']['closed']?></span><? endif; ?></a></li>
            <?php endif; ?>

            <?php if (in_array($this->_USERDATA['data']['user_group'], array('user', 'szolgaltato'))): ?>
            <li class="has-more <?=($this->gets[0] == 'uzenetek')?'on':''?>"><a href="/uzenetek" title="<?=__('Üzenetek')?>"><span class="ni">8</span><i class="fas fa-envelope"></i> <?=__('Üzenetek')?></a></li>
            <?php endif; ?>

            <li class="has-more <?=($this->gets[0] == 'dokumentumok')?'on':''?>"><a href="/dokumentumok" title="<?=__('Dokumentumok')?>"><span class="ni">8</span><i class="far fa-file-alt"></i> <?=__('Dokumentumok')?></a></li>
            <?php if ($this->gets[0] == 'dokumentumok'): ?>
              <li class="sub"><a href="/dokumentumok/dijbekero" title="<?=__('Díjbekérők')?>"><span class="ni">8</span><i class="fas fa-ellipsis-h"></i> <?=__('Díjbekérők')?></a></li>
              <li class="sub"><a href="/dokumentumok/szamla" title="<?=__('Számlák')?>"><span class="ni">8</span><i class="fas fa-ellipsis-h"></i> <?=__('Számlák')?></a></li>
            <?php endif; ?>
            <li class="div"></li>

            <?php if (in_array($this->_USERDATA['data']['user_group'], array('user', 'szolgaltato'))): ?>
              <li class="shortcut"><a href="/dokumentumok/dijbekero" title="<?=__('Díjbekérők')?>"><span class="ni">8</span><i class="fas fa-external-link-alt"></i> <?=__('Díjbekérők')?></a></li>
              <li class="shortcut"><a href="/dokumentumok/szamla" title="<?=__('Számlák')?>"><span class="ni">8</span><i class="fas fa-external-link-alt"></i> <?=__('Számlák')?></a></li>
            <?php endif; ?>


            <?php if ($this->_USERDATA['data']['user_group'] == 'szolgaltato'): ?>
              <li class="head"><?=__('Adminisztráció')?></li>
              <li class="<?=($this->gets[0] == 'cegem')?'on':''?>"><a href="/cegem" title="<?=__('Szolgáltatásaim')?>"><span class="ni">8</span><i class="far fa-building"></i> <?=__('Szolgáltatásaim')?></a></li>
            <?php endif; ?>

            <?php
            // Admin és Super admin menük
            if ($this->_USERDATA['data']['user_group'] == 'superadmin' || $this->_USERDATA['data']['user_group'] == 'admin'): ?>
              <li class="head"><?=__('Adminisztráció')?></li>
              <?php if (true): ?>
                <li class="<?=($this->gets[0] == 'emails')?'on':''?>"><a href="/emails" title="Email sablonok"><span class="ni">8</span><i class="fa fa-envelope"></i> Email sablonok</a></li>
              <?php endif; ?>
              <?php if (true): ?>
                <li class="<?=($this->gets[0] == 'adminconsole' && $this->gets[1] == 'felhasznalok')?'on':''?>"><a href="/adminconsole/felhasznalok" title="<?=__('Felhasználók')?>"><span class="ni">8</span><i class="fas fa-users"></i> <?=__('Felhasználók')?></a></li>
              <?php endif; ?>
              <?php if (true): ?>
                <li class="<?=($this->gets[0] == 'adminconsole' && $this->gets[1] == 'lists')?'on':''?>"><a href="/adminconsole/lists" title="<?=__('Listák')?>"><span class="ni">8</span><i class="fas fa-stream"></i> <?=__('Listák')?></a></li>
              <?php endif; ?>
              <?php if ($this->_USERDATA['data']['user_group'] == 'superadmin'): ?>
                <li class="<?=($this->gets[0] == 'beallitasok')?'on':''?>"><a href="/beallitasok" title="<?=__('Beállítások')?>"><span class="ni">8</span><i class="fas fa-cogs"></i> <?=__('Beállítások')?></a></li>
              <?php endif; ?>
            <?php endif; ?>
      	</ul>
      </div>
  </div>
  <? endif; ?>
  <div class="ct">
  	<div class="innerContent">

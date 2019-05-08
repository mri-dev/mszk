<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/html4"
      xmlns:og="http://ogp.me/ns#"
      xmlns:fb="http://www.facebook.com/2008/fbml" lang="hu-HU">
<head>
	<title><?=$this->title?></title>
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
  				$('.ct').css({
  					'paddingLeft' : '280px'
  				});
          $('#top').css({
  					'paddingLeft' : '280px'
  				});
  			}else{
  				slideMenu.css({
  					'left' : '-'+closeNum+'px'
  				});
  				$('.ct').css({
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
  					$('.ct').animate({
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
  					$('.ct').animate({
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
<body class="<?=$this->bodyclass?> <? if($this->_USER): ?>logged-in user-group-<?=$this->_USERDATA['data']['user_group']?><? endif; ?>">

<? if($this->_USER): ?>
<div id="top">
	<div class="control-bar">
    <div class="d-flex justify-content-between align-items-center">
      <div class="message-alerts">
        <div class="d-flex align-items-center">
          <div class="ico">
            <div class="has-msg"><i class="far fa-dot-circle"></i></div>
            <a href="/uzenetek"><i class="far fa-envelope"></i></a>
          </div>
          <div class="alert-message"><?=end(explode(" ", $this->_USERDATA['data']['nev']))?>, <?=sprintf(__('%d db olvasatlan üzenete van!'), 0)?> <a href="/uzenetek"><?=__('Megnézem')?></a>  </div>
        </div>
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
      <img src="<?=IMG?>logo-white.svg" alt="<?=$this->settings['page_title']?>">
    </div>
  	<div class="slideMenuToggle" title="Kinyit/Becsuk"><i class="fa fa-arrows-h"></i></div>
    <div class="clr"></div>
 		<div class="menu">
      	<ul>
          	<li class="<?=($this->gets[0] == 'home')?'on':''?>"><a href="/" title="Dashboard"><span class="ni">1</span><i class="fas fa-tachometer-alt"></i> <?=__('Gépház')?></a></li>
            <li class="<?=($this->gets[0] == 'ajanlatkeresek')?'on':''?>"><a href="/ajanlatkeresek" title="Ajánlatkérések"><span class="ni">2</span><i class="fa fa-edit"></i> Ajánlatkérések</a></li>
            <?php if (false): ?>
              <li class="<?=($this->gets[0] == 'emails')?'on':''?>"><a href="/emails" title="Email sablonok"><span class="ni">8</span><i class="fa fa-envelope"></i> Email sablonok</a></li>
            <?php endif; ?>
            <li class="<?=($this->gets[0] == 'beallitasok')?'on':''?>"><a href="/beallitasok" title="Beállítások"><span class="ni">8</span><i class="fa fa-gear"></i> Beállítások</a></li>
            <li class="div"></li>
            <!-- MODULS-->
            <?php if ( !empty($this->modules) ): ?>
            <li class="div"></li>
            <?php foreach ($this->modules as $module): ?>
            <li class="<?=($this->gets[0] == $module['menu_slug'])?'on':''?>"><a href="/<?=$module['menu_slug']?>" title="<?=$module['menu_title']?>"><span class="ni"><?=$module['ID']?></span><i class="fa fa-<?=$module['faico']?>"></i> <?=$module['menu_title']?></a></li>
            <?php endforeach; ?>
            <?php endif; ?>
            <!-- End of MODULS-->
            <li class="<?=($this->gets[0] == 'kategoriak')?'on':''?>"><a href="/kategoriak" title="Kategóriák"><span class="ni">6</span><i class="fa fa-bars"></i> Kategóriák</a></li>
            <li class="<?=($this->gets[0] == 'szinek')?'on':''?>"><a href="/szinek" title="Színek"><span class="ni">8</span><i class="fa fa-th"></i> Színek</a></li>
            <li class="<?=($this->gets[0] == 'motivumjaim')?'on':''?>"><a href="/motivumjaim" title="Motívumok"><span class="ni">8</span><i class="fa fa-stop"></i> Motívumok</a></li>
            <li class="<?=($this->gets[0] == "motivumconfig")?'on':''?>"><a href="/motivumconfig" title="Saját motívum beállítások"><span class="ni">8</span><i class="fa fa-stop"></i> Saját minták</a></li>
      	</ul>
      </div>
  </div>
  <? endif; ?>
  <div class="ct">
  	<div class="innerContent">

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/html4"
      xmlns:og="http://ogp.me/ns#"
      xmlns:fb="http://www.facebook.com/2008/fbml" lang="hu-HU">
<head>
    <title><?=$this->title?></title>
    <?=$this->addMeta('robots','index,folow')?>
    <?=$this->SEOSERVICE?>
    <?php if ( $this->settings['FB_APP_ID'] != '' ): ?>
    <meta property="fb:app_id" content="<?=$this->settings['FB_APP_ID']?>" />
    <?php endif; ?>
    <? $this->render('meta'); ?>
</head>
<body class="<?=$this->bodyclass?>" ng-app="Software" ng-controller="App" ng-init="init()">
<? if(!empty($this->settings[google_analitics])): ?>
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', '<?=$this->settings[google_analitics]?>', 'auto');
  ga('send', 'pageview');
</script>
<? endif; ?>
<header>
  <div class="top">
    <div class="wrapper">
      <div class="top-left">
        <div class="navigator">
          <ul>
            <li><a href="/" class="active"><?=__('Vállalkozásoknak')?></a></li>
            <li><a href="#"><?=__('Cégeknek')?></a></li>
          </ul>
        </div>
      </div>
      <div class="top-center">
        <div class="logo">
          <a href="/"><img src="<?=IMG?>logo.svg" alt="<?=$this->settings['page_title']?>"></a>
        </div>
      </div>
      <div class="top-right">
        <div class="navigator">
          <ul>
            <li><a href="#"><?=__('Információ')?></a></li>
            <li><a href="#"><?=__('Segítség')?></a></li>
            <li class="login"><a href="#"><?=__('Belépés')?></a></li>
            <li class="reg"><a href="#"><?=__('Regisztráció')?></a></li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</header>

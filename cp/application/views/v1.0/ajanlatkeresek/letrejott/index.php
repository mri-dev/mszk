<div class="page-desc">
  <?=__('Jelen listában azokat a kéréseket látja, melynél létrejött a megállapodás és a projektek.')?>
</div>

<div class="request-controller" ng-controller="RequestControl" ng-init="init({loadpossibleservices:0, offerout: 1, letrejott: 1, preselected: '<?=$this->gets[2]?>'})">
  <?=$this->render('ajanlatkeresek/requestcontrol')?>
</div>

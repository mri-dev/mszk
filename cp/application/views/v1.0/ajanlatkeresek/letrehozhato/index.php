<div class="page-desc">
  <?=__('Jelen listában azokat a kéréseket látja, melynél a szolgáltató és az ajánlatkérő részéről is megvan a pozitív ajánlat elbírálás, létrehozhazó a megállapodás.')?>
</div>

<div class="request-controller" ng-controller="RequestControl" ng-init="init({loadpossibleservices:0, allpositive: 1, offerout: 1})">
  <?=$this->render('ajanlatkeresek/requestcontrol')?>
</div>

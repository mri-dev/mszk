<div class="page-desc">
  <?=__('Itt megtekintheti azokat az ajánlat kéréseket, melyet már feldolgozott, ki lett ajánlva a szolgáltatók felé.')?>
</div>

<div class="request-controller" ng-controller="RequestControl" ng-init="init({loadpossibleservices:0, offerout: 1})">
  <?=$this->render('ajanlatkeresek/requestcontrol')?>
</div>

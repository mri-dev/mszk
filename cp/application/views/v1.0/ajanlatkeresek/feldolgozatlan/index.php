<div class="page-desc">
  <?=__('Ezen az oldalon elvégezheti a kiajánlást a szolgáltatók felé. Ellenőrizze, hogy a beérkezett igények megfelelőek-e és hogy kinek szeretné kiajánlani a kérést.')?>
</div>

<div class="request-controller" ng-controller="RequestControl" ng-init="init({loadpossibleservices:0})">
  <?=$this->render('ajanlatkeresek/requestcontrol')?>
</div>

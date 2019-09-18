<md-dialog aria-label="<?=__('Ajánlat küldése - Áttekintés és szerkesztés')?>" class="ajaxdialog offertouser">
  <form ng-cloak>
    <md-toolbar>
      <div class="md-toolbar-tools">
        <h2><?=__('Ajánlat küldése - Áttekintés és szerkesztés')?></h2>
        <span flex></span>
        <md-button class="md-icon-button" ng-click="closeDialog()">
          <md-icon md-svg-src="/src/images/ic_close_white_24px.svg" aria-label="Close dialog"></md-icon>
        </md-button>
      </div>
    </md-toolbar>

    <md-dialog-content>
      <div class="md-dialog-content">
        <md-input-container class="md-block">
          <label><?=__('Szolgáltatás díja (nettó)')?></label>
          <input type="number" ng-model="newoffer.price">
        </md-input-container>
        <md-input-container class="md-block">
          <label><?=__('Várható kezdés')?></label>
          <input type="date" ng-model="newoffer.project_start_at">
        </md-input-container>
        <md-input-container class="md-block">
          <label><?=__('Várható időtartam')?></label>
          <input type="text" ng-model="newoffer.offer_project_idotartam">
        </md-input-container>
        <md-input-container class="md-block">
          <label><?=__('Ajánlat szövege')?></label>
          <textarea ng-model="newoffer.message"></textarea>
        </md-input-container>
        <div class="" ng-show="saving">
          <div class="alert alert-warning">
            <?=__('Ajánlat létrehozása és küldése folyamatban...')?>
          </div>
        </div>
      </div>
    </md-dialog-content>

    <md-dialog-actions layout="row" ng-show="!saving">
      <md-button ng-click="closeDialog()">
       <?=__('Mégse')?>
      </md-button>
      <span flex></span>
      <md-button class="md-primary md-raised" ng-click="sendOfferToUserByServiceOffer()">
        <?=__('Ajánlat továbbküldése')?> <i class="fas fa-paper-plane"></i></i>
      </md-button>
    </md-dialog-actions>
  </form>
</md-dialog>

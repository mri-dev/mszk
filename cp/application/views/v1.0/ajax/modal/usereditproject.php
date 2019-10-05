<md-dialog aria-label="<?=__('Projekt adatainak módosítása')?>" class="ajaxdialog saveproject">
  <form ng-cloak>
    <md-toolbar>
      <div class="md-toolbar-tools">
        <h2><?=__('Projekt adatainak módosítása')?></h2>
        <span flex></span>
        <md-button class="md-icon-button" ng-click="closeDialog()">
          <md-icon md-svg-src="/src/images/ic_close_white_24px.svg" aria-label="Close dialog"></md-icon>
        </md-button>
      </div>
    </md-toolbar>

    <md-dialog-content>
      <div class="md-dialog-content">
        <md-input-container class="md-block" ng-if="project.my_relation!='admin'">
          <label><?=__('Projekt elnevezése')?></label>
          <input ng-model="project.title">
        </md-input-container>
        <md-input-container class="md-block" ng-if="project.my_relation=='admin'">
          <label><?=__('Igénylő - projekt elnevezése')?></label>
          <input ng-model="project.requester_title">
        </md-input-container>
        <md-input-container class="md-block" ng-if="project.my_relation=='admin'">
          <label><?=__('Szolgáltató - projekt elnevezése')?></label>
          <input ng-model="project.servicer_title">
        </md-input-container>
        <md-input-container class="md-block" ng-if="project.my_relation=='admin'">
          <label><?=__('Projekt készültségi állapota (%)')?></label>
          <md-slider-container>
            <md-slider md-discrete min="0" max="100" ng-model="project.status_percent" aria-label="red" id="status_percent" class="md-warn">
            </md-slider>
            <md-input-container>
              <input type="number" ng-model="project.status_percent" aria-label="red" aria-controls="status_percent">
            </md-input-container>
          </md-slider-container>
        </md-input-container>
        <md-input-container class="md-block" ng-if="project.my_relation=='admin'">
          <md-switch ng-true-value="0" ng-false-value="1" md-invert="true" ng-model="project.closed" aria-label="<?=__('Projekt státusz')?>">
            <?=__('Aktív projekt')?>:
          </md-switch>
        </md-input-container>
        <div layout-gt-xs="row" ng-if="project.my_relation=='admin'">
          <div flex-gt-xs>
            <md-input-container class="md-block">
              <h4><?=__('Projekt indulásának ideje')?></h4>
              <md-datepicker ng-model="project.project_start_Date" md-placeholder="<?=__('Időpont választása')?>"></md-datepicker>
            </md-input-container>
          </div>
          <div flex-gt-xs>
            <md-input-container class="md-block">
              <h4><?=__('Projekt befejezésének ideje')?></h4>
              <md-datepicker ng-model="project.project_end_Date" md-placeholder="<?=__('Időpont választása')?>"></md-datepicker>
            </md-input-container>
          </div>
        </div>
        <div class="" ng-show="saving">
          <div class="alert alert-warning">
            <?=__('Adatok mentése folyamatban...')?>
          </div>
        </div>
      </div>
    </md-dialog-content>

    <md-dialog-actions layout="row" ng-show="!saving">
      <md-button ng-click="closeDialog()">
       <?=__('Mégse')?>
      </md-button>
      <span flex></span>
      <md-button class="md-primary md-raised" ng-click="saveProject()">
        <?=__('Projekt adatok mentése')?> <i class="far fa-save"></i>
      </md-button>
    </md-dialog-actions>
  </form>
</md-dialog>

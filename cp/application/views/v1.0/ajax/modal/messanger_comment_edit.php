<md-dialog aria-label="<?=__('Saját privát megjegyzés')?>" class="ajaxdialog messagecommenteditor">
  <form ng-cloak>
    <md-toolbar>
      <div class="md-toolbar-tools">
        <h2><?=__('Saját privát megjegyzés')?></h2>
        <span flex></span>
        <md-button class="md-icon-button" ng-click="closeDialog()">
          <md-icon md-svg-src="/src/images/ic_close_white_24px.svg" aria-label="Close dialog"></md-icon>
        </md-button>
      </div>
    </md-toolbar>

    <md-dialog-content>
      <div class="md-dialog-content">
        <md-input-container class="md-block">
          <label><?=__('Megjegyzésem')?></label>
          <textarea ng-model="message.notice"></textarea>
        </md-input-container>
        <div class="" ng-show="saving">
          <div class="alert alert-warning">
            <?=__('Megjegyzés mentése folyamatban...')?>
          </div>
        </div>
      </div>
    </md-dialog-content>

    <md-dialog-actions layout="row" ng-show="!saving">
      <md-button ng-click="closeDialog()">
       <?=__('Mégse')?>
      </md-button>
      <span flex></span>
      <md-button class="md-primary md-raised" ng-click="editComment()">
        <?=__('Megjegyzés mentése')?> <i class="far fa-save"></i>
      </md-button>
    </md-dialog-actions>
  </form>
</md-dialog>

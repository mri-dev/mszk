<md-dialog aria-label="<?=__('Üzenet munkamenet archiválása')?>" class="ajaxdialog messagearchiver">
  <form ng-cloak>
    <md-toolbar>
      <div class="md-toolbar-tools">
        <h2><?=__('Üzenet munkamenet archiválása')?></h2>
        <span flex></span>
        <md-button class="md-icon-button" ng-click="closeDialog()">
          <md-icon md-svg-src="/src/images/ic_close_white_24px.svg" aria-label="Close dialog"></md-icon>
        </md-button>
      </div>
    </md-toolbar>

    <md-dialog-content>
      <div class="md-dialog-content">
        <small><?=__('Archivált üzenetek esetében a rendszer <strong>nem küld e-mail értesítést az olvasatlan üzenetekről</strong>!')?></small><br>
        <small><?=__('Továbbá az üzenetváltást hátra sorolja a listázásban.')?></small>
        <md-input-container class="md-block">
          <label><?=__('Üzenetváltás archiválása')?></label>
          <md-switch ng-true-value="1" ng-false-value="0" md-invert="true" ng-model="message.archived" aria-label="<?=__('Archivált')?>">
            <?=__('Archivált')?>:
          </md-switch>
        </md-input-container>
        <div class="" ng-show="saving">
          <div class="alert alert-warning">
            <?=__('Mentése folyamatban...')?>
          </div>
        </div>
      </div>
    </md-dialog-content>

    <md-dialog-actions layout="row" ng-show="!saving">
      <md-button ng-click="closeDialog()">
       <?=__('Mégse')?>
      </md-button>
      <span flex></span>
      <md-button class="md-primary md-raised" ng-click="changeArchive()">
        <?=__('Változás mentése')?> <i class="far fa-save"></i>
      </md-button>
    </md-dialog-actions>
  </form>
</md-dialog>

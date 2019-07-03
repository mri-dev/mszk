<md-dialog aria-label="<?=__('Dokumentum hozzáadása a projekthez')?>" class="ajaxdialog projectdocsadder">
  <form ng-cloak>
    <md-toolbar>
      <div class="md-toolbar-tools">
        <h2><?=__('Dokumentum hozzáadása a projekthez')?></h2>
        <span flex></span>
        <md-button class="md-icon-button" ng-click="closeDialog()">
          <md-icon md-svg-src="/src/images/ic_close_white_24px.svg" aria-label="Close dialog"></md-icon>
        </md-button>
      </div>
    </md-toolbar>

    <md-dialog-content>
      <div class="md-dialog-content">
        <div class="" ng-if="docs">
          <?=__('A kiválasztott dokumentumot hozzáadja a projekthez, melyről partnere értesítést kap:')?>
        </div>
        <md-input-container ng-if="docs">
          <label><?=__('Dokumentum kiválasztása')?></label>
          {{selected_doc_to_add}}
          <md-select ng-model="project.selected_doc_to_add">
            <md-option ng-value="doc.ID" ng-repeat="doc in docs">
              {{doc.name}}
              <div class="dialog-option-infos">
                <span class="docs-folder">{{doc.folders[0].folder_name}}</span>
                <span class="ertek" ng-if="doc.ertek!=0">{{doc.ertek}} <?=__('Ft + ÁFA')?></span>
                <span class="created">{{doc.created_at}}</span>
              </div>
            </md-option>
          </md-select>
        </md-input-container>

        <div ng-if="!docs" style="color:#e25e5e;">
          <?=__('Jelenleg nincs olyan dokumentum, amit hozzá tudna adni a projekthez.')?>
        </div>

        <div class="" ng-show="saving">
          <div class="alert alert-warning">
            <?=__('Dokumentum hozzáadása folyamatban...')?>
          </div>
        </div>
      </div>
    </md-dialog-content>

    <md-dialog-actions layout="row" ng-show="!saving">
      <md-button ng-click="closeDialog()">
       <?=__('Mégse')?>
      </md-button>
      <span flex></span>
      <md-button class="md-primary md-raised" ng-if="docs" ng-click="addDocToroject()">
        <?=__('Dokumentum hozzáadása')?> <i class="fas fa-file-medical"></i>
      </md-button>
    </md-dialog-actions>
  </form>
</md-dialog>

<md-dialog aria-label="<?=__('Projektek létrehozása')?>" class="ajaxdialog projectcreatoroffer">
  <form ng-cloak>
    <md-toolbar>
      <div class="md-toolbar-tools">
        <h2><?=__('Projektek létrehozása')?></h2>
        <span flex></span>
        <md-button class="md-icon-button" ng-click="closeDialog()">
          <md-icon md-svg-src="/src/images/ic_close_white_24px.svg" aria-label="Close dialog"></md-icon>
        </md-button>
      </div>
    </md-toolbar>

    <md-dialog-content>
      <div class="md-dialog-content">
        <strong><?=__('A projektek létrehozása során 2 db projekt jön létre: megrendelői, szolgáltatói.')?></strong>
        <br><br>
        <md-input-container class="md-block">
          <label><?=__('Saját projekt elnevezés / Egyedi azonosító / Szerződés sorszám')?></label>
          <input type="text" ng-model="newoffer.admin_title">
        </md-input-container>
        <div class="userdetails">
          <h3><?=__('Megrendelő adatai')?></h3>
          <table>
            <tr>
              <td><?=__('Név')?></td>
              <td><strong>{{request.name}} <span ng-if="request.company">({{request.company}})</span></strong></td>
            </tr>
            <tr>
              <td><?=__('E-mail')?></td>
              <td><strong>{{request.email}}</strong></td>
            </tr>
          </table>
          <h3><?=__('Szolgáltató adatai')?></h3>
          <table>
            <tr>
              <td><?=__('Név')?></td>
              <td><strong>{{newoffer.from_user.data.nev}} <span ng-if="newoffer.from_user.data.company_name">({{newoffer.from_user.data.company_name}})</span></strong></td>
            </tr>
            <tr>
              <td><?=__('E-mail')?></td>
              <td><strong>{{newoffer.from_user.data.email}}</strong></td>
            </tr>
          </table>
        </div>
      </div>
    </md-dialog-content>

    <md-dialog-actions layout="row" ng-show="!saving">
      <md-button ng-click="closeDialog()">
       <?=__('Mégse')?>
      </md-button>
      <span flex></span>
      <md-button class="md-primary md-raised" ng-click="adminCreatProjectByOffer()">
        <?=__('Projektek létrehozása')?> <i class="fas fa-paper-plane"></i></i>
      </md-button>
    </md-dialog-actions>
  </form>
</md-dialog>

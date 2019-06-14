<div class="overview">
  <div class="d-flex flex-row">
    <div class="project">
      <div class="head">
        <?=__('Projekt')?>
      </div>
      <div class="cont">
        <table class="table table-bordered">
          <tbody>
            <tr>
              <td><?=__('Elnevezés')?></td>
              <td><strong>{{project.title}}</strong></td>
            </tr>
            <tr>
              <td><?=__('Státusz')?></td>
              <td><strong>{{project.title}}</strong></td>
            </tr>
            <tr>
              <td><?=__('Hashkey')?></td>
              <td><strong><?=$this->gets[2]?></strong></td>
            </tr>
            <tr>
              <td><?=__('Állapot')?></td>
              <td>
                <div class="progress">
                  <div class="progress-bar <?=\Helper::progressBarColor($p['status_percent'])?>" role="progressbar" style="width: <?=$p['status_percent']?>%;" aria-valuenow="<?=$p['status_percent']?>" aria-valuemin="0" aria-valuemax="100"><?=$p['status_percent']?>%</div>
                </div>
              </td>
            </tr>
            <tr>
              <td><?=__('Projekt költsége')?></td>
              <td><strong></strong></td>
            </tr>
            <tr>
              <td><?=__('Díjfizetés')?></td>
              <td>
                <div class="progress">
                  <div class="progress-bar <?=\Helper::progressBarColor($p['status_percent'])?>" role="progressbar" style="width: <?=$p['status_percent']?>%;" aria-valuenow="<?=$p['status_percent']?>" aria-valuemin="0" aria-valuemax="100"><?=$p['status_percent']?>%</div>
                </div>
              </td>
            </tr>
            <tr>
              <td><?=__('Létrejött')?></td>
              <td><strong></strong></td>
            </tr>
            <tr>
              <td><?=__('Indulás')?></td>
              <td><strong></strong></td>
            </tr>
            <tr>
              <td><?=__('Befejezés')?></td>
              <td><strong></strong></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <div class="requester">
      <div class="head">
        <?=__('Igénylő adatai')?>
        <div class="own-data" ng-if="project.my_relation=='requester'"><?=__('Az Ön adatai')?></div>
      </div>
      <div class="cont">
        <table class="table table-bordered">
          <tbody>
            <tr>
              <td><?=__('Név / Kapcsolattartó')?></td>
              <td><strong>{{project.user_requester.data.nev}}</strong></td>
            </tr>
            <tr>
              <td><?=__('Cég neve')?></td>
              <td><strong>{{project.user_requester.data.company_name}}</strong></td>
            </tr>
            <tr>
              <td><?=__('Adószám')?></td>
              <td><strong>{{project.user_requester.data.company_adoszam}}</strong></td>
            </tr>
            <tr>
              <td><?=__('E-mail cím')?></td>
              <td><strong>{{project.user_requester.data.email}}</strong></td>
            </tr>
            <tr>
              <td><?=__('Telefonszám')?></td>
              <td><strong>{{project.user_requester.data.szallitas_phone}}</strong></td>
            </tr>
            <tr>
              <td><?=__('Számlázási név')?></td>
              <td><strong>{{project.user_requester.szamlazasi_adat.nev}}</strong></td>
            </tr>
            <tr>
              <td><?=__('Számlázási cím')?></td>
              <td><strong>{{project.user_requester.szamlazasi_adat.irsz}} {{project.user_requester.szamlazasi_adat.city}}, {{project.user_requester.szamlazasi_adat.kozterulet_nev}} {{project.user_requester.szamlazasi_adat.kozterulet_jelleg}} {{project.user_requester.szamlazasi_adat.hazszam}}.</strong><br>
                <span ng-if="project.user_requester.szamlazasi_adat.kerulet"><?=__('Kerület')?>: {{project.user_requester.szamlazasi_adat.kerulet}}</span>
                <span ng-if="project.user_requester.szamlazasi_adat.epulet"><?=__('Épület')?>: {{project.user_requester.szamlazasi_adat.epulet}}</span>
                <span ng-if="project.user_requester.szamlazasi_adat.lepcsohaz"><?=__('Lépcsőház')?>: {{project.user_requester.szamlazasi_adat.lepcsohaz}}</span>
                <span ng-if="project.user_requester.szamlazasi_adat.szint"><?=__('Szint')?>: {{project.user_requester.szamlazasi_adat.szint}}</span>
                <span ng-if="project.user_requester.szamlazasi_adat.ajto"><?=__('Ajtó')?>: {{project.user_requester.szamlazasi_adat.ajto}}</span>
              </td>
            </tr>
            <tr>
              <td><?=__('Szállítási cím')?></td>
              <td><strong>{{project.user_requester.szallitasi_adat.irsz}} {{project.user_requester.szallitasi_adat.city}}, {{project.user_requester.szallitasi_adat.kozterulet_nev}} {{project.user_requester.szallitasi_adat.kozterulet_jelleg}} {{project.user_requester.szallitasi_adat.hazszam}}.</strong><br>
                <span ng-if="project.user_requester.szallitasi_adat.kerulet"><?=__('Kerület')?>: {{project.user_requester.szallitasi_adat.kerulet}}</span>
                <span ng-if="project.user_requester.szallitasi_adat.epulet"><?=__('Épület')?>: {{project.user_requester.szallitasi_adat.epulet}}</span>
                <span ng-if="project.user_requester.szallitasi_adat.lepcsohaz"><?=__('Lépcsőház')?>: {{project.user_requester.szallitasi_adat.lepcsohaz}}</span>
                <span ng-if="project.user_requester.szallitasi_adat.szint"><?=__('Szint')?>: {{project.user_requester.szallitasi_adat.szint}}</span>
                <span ng-if="project.user_requester.szallitasi_adat.ajto"><?=__('Ajtó')?>: {{project.user_requester.szallitasi_adat.ajto}}</span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <div class="servicer">
      <div class="head">
        <?=__('Szolgáltató adatai')?>
        <div class="own-data" ng-if="project.my_relation=='servicer'"><?=__('Az Ön adatai')?></div>
      </div>
      <div class="cont">
        <table class="table table-bordered">
          <tbody>
            <tr>
              <td><?=__('Név / Kapcsolattartó')?></td>
              <td><strong>{{project.user_servicer.data.nev}}</strong></td>
            </tr>
            <tr>
              <td><?=__('Cég neve')?></td>
              <td><strong>{{project.user_servicer.data.company_name}}</strong></td>
            </tr>
            <tr>
              <td><?=__('Adószám')?></td>
              <td><strong>{{project.user_servicer.data.company_adoszam}}</strong></td>
            </tr>
            <tr>
              <td><?=__('E-mail cím')?></td>
              <td><strong>{{project.user_servicer.data.email}}</strong></td>
            </tr>
            <tr>
              <td><?=__('Telefonszám')?></td>
              <td><strong>{{project.user_servicer.data.szallitas_phone}}</strong></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<br>
<div class="row">
  <div class="col-md-12">
    <h2><?=__('Üzenetküldés')?></h2>
    <div class="messenger-quick-msg">
      <div class="wrapper">
        <label for="messanger_text"><i class="far fa-envelope"></i> <?=__('Gyors üzenet küldése <strong>{{partner.data.nev}}</strong> részére:')?></label>
        <textarea ng-model="messanger.text" id="messanger_text" class="form-control no-editor"></textarea>
        <br>
        <div class="d-flex flex-row justify-content-between align-items-center">
          <div class="">
            <a href="/uzenetek/session/{{project.hashkey}}"><i class="fas fa-envelope-open-text"></i> <?=__('Tovább a projekt üzeneteire')?></a>
            <span class="unreaded-txt" ng-if="project.messages.unreaded && project.messages.unreaded!=0">{{project.messages.unreaded}} olvasatlan üzenet</span>
          </div>
          <div class="">
            <button type="button" class="btn btn-primary btn-sm" ng-click="sendQuickMessage(project.hashkey)"><?=__('Üzenet küldése')?> <i class="fas fa-paper-plane"></i></button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<br>
<div class="row">
  <div class="col-md-5">
    <div class="wblock color-red">
      <div class="data-container">
        <div class="no-data-view">
          <div class="ico"><i class="far fa-check-circle"></i></div>
          <div class="text"><?=__('Nincsenek díjbekérők!')?></div>
        </div>
      </div>
      <div class="data-footer">
        <div class="d-flex align-items-center">
          <div class="title">
            <h3><?=__('Lejárt díjbekérők')?></h3>
            <a href="/dokumentumok/dijbekero"><?=__('Tovább az összes díjbekérőhöz')?></a>
          </div>
          <div class="count">
            <div class="count-wrapper"><div class="num"><?=$this->badges['docs']['dijbekero']['aktualis']?></div></div>
          </div>
        </div>
      </div>
    </div>
    <div class="wblock color-green">
      <div class="data-container">
        <div class="no-data-view">
          <div class="ico"><i class="far fa-check-circle"></i></div>
          <div class="text"><?=__('Nincsenek számlák!')?></div>
        </div>
      </div>
      <div class="data-footer">
        <div class="d-flex align-items-center">
          <div class="title">
            <h3><?=__('Számlák')?></h3>
            <a href="/dokumentumok/szamla"><?=__('Tovább az összes számlához')?></a>
          </div>
          <div class="count">
            <div class="count-wrapper"><div class="num"><?=$this->badges['docs']['dijbekero']['aktualis']?></div></div>
          </div>
        </div>
      </div>
    </div>
    <div class="wblock color-blue">
      <div class="data-container">
        <div class="no-data-view">
          <div class="ico"><i class="far fa-check-circle"></i></div>
          <div class="text"><?=__('Nincsenek dokumentumok!')?></div>
        </div>
      </div>
      <div class="data-footer">
        <div class="d-flex align-items-center">
          <div class="title">
            <h3><?=__('Legfrissebb dokumentumok')?></h3>
            <a href="/dokumentumok/"><?=__('Tovább az összes dokumentumhoz')?></a>
          </div>
          <div class="count">
            <div class="count-wrapper"><div class="num"><?=$this->badges['docs']['dijbekero']['aktualis']?></div></div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-7">
    <h2><?=__('Szolgáltatás leírása')?></h2>
    <h2><?=__('Ajánlat referencia')?></h2>
  </div>
</div>

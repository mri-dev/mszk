<div class="overview admin-view">
  <div class="d-flex">
    <div class="project">
      <div class="head"><?=__('Projekt adatok')?><div class="own-data edit" ng-click="projectEditor()"><?=__('Szerkesztés')?> <i class="far fa-edit"></i></div></div>
      <div class="cont">
        <div class="d-flex project-overall-data">
          <div class="infos">
            <div class="chead"><?=__('Általános információk')?></div>
            <div class="dpad">
              <table class="table table-bordered">
                <tbody>
                  <tr>
                    <td><?=__('Projekt')?></td>
                    <td><strong>{{project.admin_title}}</strong></td>
                  </tr>
                  <tr>
                    <td><?=__('Közvetítői projekt azonosító')?></td>
                    <td><strong>{{project.order_hashkey}}</strong></td>
                  </tr>
                  <tr>
                    <td><?=__('Létrejött')?></td>
                    <td><strong>{{project.created_dist}} ({{project.created_at}})</strong></td>
                  </tr>
                  <tr>
                    <td><?=__('Státusz')?></td>
                    <td>
                      <div class="progress">
                        <div class="progress-bar" ng-class="project.status_percent_class" role="progressbar" style="width: {{project.status_percent}}%;" aria-valuenow="{{project.status_percent}}" aria-valuemin="0" aria-valuemax="100"><span ng-if="project.status_percent!=0">{{project.status_percent}}%</span></div>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td><?=__('Indulás')?></td>
                    <td><strong>{{project.project_start}}</strong><span class="nosetdata" ng-if="!project.project_start"><?=__('Még nincs meghatározva.')?></span></td>
                  </tr>
                  <tr>
                    <td><?=__('Befejezés')?></td>
                    <td><strong>{{project.project_end}}</strong><span class="nosetdata" ng-if="!project.project_end"><?=__('Még nincs meghatározva.')?></span></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          <div class="timeline">
            <div class="chead"><?=__('Idővonal')?></div>
            <div class="dpad">
              <div class="timeline">
                <div class="record" ng-repeat="tl in project.timeline">
                  <span class="time"><strong>{{tl.time}}</strong></span> &mdash; <span class="ev">{{tl.title}}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="requester">
      <div class="head"><?=__('Ajánlatkérő')?></div>
      <div class="cont">
        <div class="chead"><?=__('Személyes adatok')?></div>
        <div value="dpad">
          <table class="table table-bordered">
            <tbody>
              <tr>
                <td><?=__('Projekt azonosító')?></td>
                <td><strong>{{project.requester_project_data.hashkey}}</strong></td>
              </tr>
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
        <div class="chead"><?=__('Ajánlat részletei')?></div>
        <div class="dpad wpadding infoline">
          <label for=""><?=__('Közvetítői ajánlat kiküldve')?>:</label>
          <div class="pval">{{project.requester_project_data.offer_data.sended_at_dist}} ({{project.requester_project_data.offer_data.sended_at}})</div>

          <label for=""><?=__('Ajánlat elfogadva - Ajánlatkérő által')?>:</label>
          <div class="pval">{{project.requester_project_data.offer_data.accepted_at_dist}} ({{project.requester_project_data.offer_data.accepted_at}})</div>

          <label for=""><?=__('Szolgáltatás díja - Ajánlatkérő fizet')?>:</label>
          <div class="pval">
            {{project.requester_project_data.offer_data.price|cash}} <span ng-if="project.requester_paidamount!=0" class="paidamount">/ <strong><i class="fas fa-check"></i> {{project.requester_paidamount|cash}} <?=__('teljesítve!')?></strong></span>
            <div class="progress">
              <div class="progress-bar" title="<?=__('Díjfizetési állapot')?>" ng-class="project.requester_paying_percent_class" role="progressbar" style="width: {{project.requester_paying_percent}}%;" aria-valuenow="{{project.requester_paying_percent}}" aria-valuemin="0" aria-valuemax="100"><span ng-if="project.requester_paying_percent!=0">{{project.requester_paying_percent}}%</span></div>
            </div>
          </div>

          <label for=""><?=__('Ajánlat részletei')?>:</label>
          <div class="pval" ng-bind-html="project.requester_project_data.offer_data.message|unsafe"></div>
        </div>
        <div class="chead"><?=__('Dokumentumok')?><div class="own-data edit" ng-click="projectDocsAdder(project.requester_project_data.hashkey)"><?=__('új hozzáadása')?> <i class="fas fa-plus"></i></div></div>
        <div class="dpad">
          <div class="wblock color-red">
            <div class="data-container">
              <?php $doc = $this->doc['requester']['dijbekero']; ?>
              <?php if ($doc['return_num'] != 0): ?>
              <div class="data-list">
                <div class="wrapper">
                  <div class="header">
                    <div class="holder">
                      <div class="data"><?=__('Dokumentum adatok')?></div>
                      <div class="relation"><?=__('Hozzáadta')?></div>
                      <div class="add-at"><?=__('Határidő')?></div>
                    </div>
                  </div>
                  <?php foreach ((array)$doc['data'] as $d): ?>
                  <div class="list-item">
                    <div class="holder">
                      <div class="data">
                        <div class="title">
                          <a href="/doc/<?=$d['hashkey']?>" target="_blank"><strong><?=$d['name']?></strong></a>
                        </div>
                        <div class="subtitle">
                          <?php if ($d['ertek'] != 0): ?>
                          <span class="doc-ertek"><strong><?=\Helper::cashFormat($d['ertek'])?></strong> <?=__('Ft + ÁFA')?></span>
                          <?php endif; ?>
                          <?php if (!empty($d['teljesites_at'])): ?>
                          <span class="teljesitve" title="<?=$d['teljesites_at']?>"><?=__('Teljesítve')?> <i class="fas fa-check"></i></span>
                          <?php endif; ?>
                        </div>
                      </div>
                      <div class="relation">
                        <?=($d['xrefproject'] && $d['xrefproject']['adder_user_id'] == $this->_USERDATA['data']['ID'])?__('Én'):__('Partner')?>
                      </div>
                      <div class="add-at">
                        <?=date('Y/m/d', strtotime($d['expire_at']))?>
                      </div>
                    </div>
                  </div>
                  <?php endforeach; ?>
                </div>
              </div>
              <?php else: ?>
              <div class="no-data-view">
                <div class="ico"><i class="far fa-check-circle"></i></div>
                <div class="text"><?=__('Nincsenek lejárt díjbekérők!')?></div>
              </div>
              <?php endif; ?>
            </div>
            <div class="data-footer">
              <div class="d-flex align-items-center">
                <div class="title">
                  <h3><?=__('Lejárt díjbekérők')?></h3>
                  <a href="/dokumentumok/dijbekero?setproject=<?=$this->gets[2]?>"><?=__('Tovább az összes díjbekérőhöz')?></a>
                </div>
                <div class="count">
                  <div class="count-wrapper"><div class="num"><?=(int)$doc['total_num']?></div></div>
                </div>
              </div>
            </div>
          </div>
          <div class="wblock color-green">
            <div class="data-container">
              <?php $doc = $this->doc['requester']['szamla']; ?>
              <?php if ($doc['return_num'] != 0): ?>
              <div class="data-list">
                <div class="wrapper">
                  <div class="header">
                    <div class="holder">
                      <div class="data"><?=__('Dokumentum adatok')?></div>
                      <div class="relation"><?=__('Hozzáadta')?></div>
                      <div class="add-at"><?=__('Időpont')?></div>
                    </div>
                  </div>
                  <?php foreach ((array)$doc['data'] as $d): ?>
                  <div class="list-item">
                    <div class="holder">
                      <div class="data">
                        <div class="title">
                          <a href="/doc/<?=$d['hashkey']?>" target="_blank"><strong><?=$d['name']?></strong></a>
                        </div>
                        <div class="subtitle">
                          <?php if ($d['folders'][0]): ?>
                          <span class="infolder"><?=$d['folders'][0]['folder_name']?></span>
                          <?php endif; ?>
                          <?php if ($d['ertek'] != 0): ?>
                          <span class="doc-ertek"><strong><?=\Helper::cashFormat($d['ertek'])?></strong> <?=__('Ft + ÁFA')?></span>
                          <?php endif; ?>
                          <?php if (!empty($d['expire_at'])): ?>
                          <span class="expire"><?=__('Határidő')?>: <strong><?=$d['expire_at']?></strong></span>
                          <?php endif; ?>
                          <?php if (!empty($d['teljesites_at'])): ?>
                          <span class="teljesitve" title="<?=$d['teljesites_at']?>"><?=__('Teljesítve')?> <i class="fas fa-check"></i></span>
                          <?php endif; ?>
                        </div>
                      </div>
                      <div class="relation">
                        <?=($d['xrefproject'] && $d['xrefproject']['adder_user_id'] == $this->_USERDATA['data']['ID'])?__('Én'):__('Partner')?>
                      </div>
                      <div class="add-at">
                        <?=date('Y/m/d H:i', strtotime($d['xproject_added_at']))?>
                      </div>
                    </div>
                  </div>
                  <?php endforeach; ?>
                </div>
              </div>
              <?php else: ?>
              <div class="no-data-view">
                <div class="ico"><i class="far fa-check-circle"></i></div>
                <div class="text"><?=__('Nincsenek számlák!')?></div>
              </div>
              <?php endif; ?>
            </div>
            <div class="data-footer">
              <div class="d-flex align-items-center">
                <div class="title">
                  <h3><?=__('Számlák')?></h3>
                  <a href="/dokumentumok/szamla?setproject=<?=$this->gets[2]?>"><?=__('Tovább az összes számlához')?></a>
                </div>
                <div class="count">
                  <div class="count-wrapper"><div class="num"><?=(int)$doc['total_num']?></div></div>
                </div>
              </div>
            </div>
          </div>
          <div class="wblock color-blue">
            <div class="data-container">
              <?php $doc = $this->doc['requester']['all']; ?>
              <?php if ($doc['return_num'] != 0): ?>
              <div class="data-list">
                <div class="wrapper">
                  <div class="header">
                    <div class="holder">
                      <div class="data"><?=__('Dokumentum adatok')?></div>
                      <div class="relation"><?=__('Hozzáadta')?></div>
                      <div class="add-at"><?=__('Időpont')?></div>
                    </div>
                  </div>
                  <?php foreach ((array)$doc['data'] as $d): ?>
                  <div class="list-item">
                    <div class="holder">
                      <div class="data">
                        <div class="title">
                          <a href="/doc/<?=$d['hashkey']?>" target="_blank"><strong><?=$d['name']?></strong></a>
                        </div>
                        <div class="subtitle">
                          <?php if ($d['folders'][0]): ?>
                          <span class="infolder"><?=$d['folders'][0]['folder_name']?></span>
                          <?php endif; ?>
                          <?php if ($d['ertek'] != 0): ?>
                          <span class="doc-ertek"><strong><?=\Helper::cashFormat($d['ertek'])?></strong> <?=__('Ft + ÁFA')?></span>
                          <?php endif; ?>
                          <?php if (!empty($d['expire_at'])): ?>
                          <span class="expire"><?=__('Határidő')?>: <strong><?=$d['expire_at']?></strong></span>
                          <?php endif; ?>
                          <?php if (!empty($d['teljesites_at'])): ?>
                          <span class="teljesitve" title="<?=$d['teljesites_at']?>"><?=__('Teljesítve')?> <i class="fas fa-check"></i></span>
                          <?php endif; ?>
                        </div>
                      </div>
                      <div class="relation">
                        <?=($d['xrefproject'] && $d['xrefproject']['adder_user_id'] == $this->_USERDATA['data']['ID'])?__('Én'):__('Partner')?>
                      </div>
                      <div class="add-at">
                        <?=date('Y/m/d H:i', strtotime($d['xproject_added_at']))?>
                      </div>
                    </div>
                  </div>
                  <?php endforeach; ?>
                </div>
              </div>
              <?php else: ?>
              <div class="no-data-view">
                <div class="ico"><i class="far fa-check-circle"></i></div>
                <div class="text"><?=__('Nincsenek dokumentumok!')?></div>
              </div>
              <?php endif; ?>
            </div>
            <div class="data-footer">
              <div class="d-flex align-items-center">
                <div class="title">
                  <h3><?=__('Legfrissebb dokumentumok')?></h3>
                  <a href="/dokumentumok/?setproject=<?=$this->gets[2]?>"><?=__('Tovább az összes dokumentumhoz')?>: <strong><?=$doc['total_num']?> <?=__('darab')?></strong> </a>
                </div>
                <div class="count">
                  <div class="count-wrapper"><div class="num"><?=(int)$doc['return_num']?></div></div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="chead"><?=__('Üzenetek')?></div>
        <div class="dpad">
          <div class="messenger-quick-msg">
            <div class="wrapper">
              <label for="messanger_text"><i class="far fa-envelope"></i> <?=__('Gyors üzenet küldése <strong>{{project.requester_project_data.user_requester.data.nev}}</strong> részére:')?></label>
              <textarea ng-model="messanger.text" id="messanger_text" class="form-control no-editor"></textarea>
              <br>
              <div class="d-flex flex-row justify-content-between align-items-center">
                <div class="">
                  <a href="/uzenetek/session/{{project.requester_project_data.hashkey}}"><i class="fas fa-envelope-open-text"></i> <?=__('Tovább a projekt üzeneteire')?></a>
                  <span class="unreaded-txt" ng-if="project.messages.unreaded && project.messages.unreaded!=0">{{project.messages.unreaded}} olvasatlan üzenet</span>
                </div>
                <div class="">
                  <button type="button" class="btn btn-primary btn-sm" ng-click="sendQuickMessage(project.requester_project_data.hashkey)"><?=__('Üzenet küldése')?> <i class="fas fa-paper-plane"></i></button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="servicer">
      <div class="head"><?=__('Szolgáltató')?></div>
      <div class="cont">
        <div class="chead"><?=__('Személyes adatok')?></div>
        <div value="dpad">
          <table class="table table-bordered">
            <tbody>
              <tr>
                <td><?=__('Projekt azonosító')?></td>
                <td><strong>{{project.servicer_project_data.hashkey}}</strong></td>
              </tr>
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
              <tr>
                <td><?=__('Számlázási név')?></td>
                <td><strong>{{project.user_servicer.szamlazasi_adat.nev}}</strong></td>
              </tr>
              <tr>
                <td><?=__('Számlázási cím')?></td>
                <td><strong>{{project.user_servicer.szamlazasi_adat.irsz}} {{project.user_servicer.szamlazasi_adat.city}}, {{project.user_servicer.szamlazasi_adat.kozterulet_nev}} {{project.user_servicer.szamlazasi_adat.kozterulet_jelleg}} {{project.user_servicer.szamlazasi_adat.hazszam}}.</strong><br>
                  <span ng-if="project.user_servicer.szamlazasi_adat.kerulet"><?=__('Kerület')?>: {{project.user_servicer.szamlazasi_adat.kerulet}}</span>
                  <span ng-if="project.user_servicer.szamlazasi_adat.epulet"><?=__('Épület')?>: {{project.user_servicer.szamlazasi_adat.epulet}}</span>
                  <span ng-if="project.user_servicer.szamlazasi_adat.lepcsohaz"><?=__('Lépcsőház')?>: {{project.user_servicer.szamlazasi_adat.lepcsohaz}}</span>
                  <span ng-if="project.user_servicer.szamlazasi_adat.szint"><?=__('Szint')?>: {{project.user_servicer.szamlazasi_adat.szint}}</span>
                  <span ng-if="project.user_servicer.szamlazasi_adat.ajto"><?=__('Ajtó')?>: {{project.user_servicer.szamlazasi_adat.ajto}}</span>
                </td>
              </tr>
              <tr>
                <td><?=__('Szállítási cím')?></td>
                <td><strong>{{project.user_servicer.szallitasi_adat.irsz}} {{project.user_servicer.szallitasi_adat.city}}, {{project.user_servicer.szallitasi_adat.kozterulet_nev}} {{project.user_servicer.szallitasi_adat.kozterulet_jelleg}} {{project.user_servicer.szallitasi_adat.hazszam}}.</strong><br>
                  <span ng-if="project.user_servicer.szallitasi_adat.kerulet"><?=__('Kerület')?>: {{project.user_servicer.szallitasi_adat.kerulet}}</span>
                  <span ng-if="project.user_servicer.szallitasi_adat.epulet"><?=__('Épület')?>: {{project.user_servicer.szallitasi_adat.epulet}}</span>
                  <span ng-if="project.user_servicer.szallitasi_adat.lepcsohaz"><?=__('Lépcsőház')?>: {{project.user_servicer.szallitasi_adat.lepcsohaz}}</span>
                  <span ng-if="project.user_servicer.szallitasi_adat.szint"><?=__('Szint')?>: {{project.user_servicer.szallitasi_adat.szint}}</span>
                  <span ng-if="project.user_servicer.szallitasi_adat.ajto"><?=__('Ajtó')?>: {{project.user_servicer.szallitasi_adat.ajto}}</span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="chead"><?=__('Ajánlat részletei')?></div>
        <div class="dpad wpadding infoline">
          <label for=""><?=__('Ajánlat beérkezett')?>:</label>
          <div class="pval">{{project.servicer_project_data.offer_data.sended_at_dist}} ({{project.servicer_project_data.offer_data.sended_at}})</div>

          <label for=""><?=__('Ajánlat elfogadva - Közvetítő által')?>:</label>
          <div class="pval">{{project.servicer_project_data.offer_data.accepted_at_dist}} ({{project.servicer_project_data.offer_data.accepted_at}})</div>

          <label for=""><?=__('Szolgáltatás díja - Közvetítő fizet')?>:</label>
          <div class="pval">
            {{project.servicer_project_data.offer_data.price|cash}}  <span ng-if="project.servicer_paidamount!=0" class="paidamount">/ <strong><i class="fas fa-check"></i> {{project.servicer_paidamount|cash}} <?=__('teljesítve!')?></strong></span>
            <div class="progress">
              <div class="progress-bar" title="<?=__('Díjfizetési állapot')?>" ng-class="project.servicer_paying_percent_class" role="progressbar" style="width: {{project.servicer_paying_percent}}%;" aria-valuenow="{{project.servicer_paying_percent}}" aria-valuemin="0" aria-valuemax="100"><span ng-if="project.servicer_paying_percent!=0">{{project.servicer_paying_percent}}%</span></div>
            </div>
          </div>

          <label for=""><?=__('Ajánlat részletei')?>:</label>
          <div class="pval" ng-bind-html="project.servicer_project_data.offer_data.message|unsafe"></div>
        </div>
        <div class="chead"><?=__('Dokumentumok')?><div class="own-data edit" ng-click="projectDocsAdder(project.servicer_project_data.hashkey)"><?=__('új hozzáadása')?> <i class="fas fa-plus"></i></div></div>
        <div class="dpad">
          <div class="wblock color-red">
            <div class="data-container">
              <?php $doc = $this->doc['servicer']['dijbekero']; ?>
              <?php if ($doc['return_num'] != 0): ?>
              <div class="data-list">
                <div class="wrapper">
                  <div class="header">
                    <div class="holder">
                      <div class="data"><?=__('Dokumentum adatok')?></div>
                      <div class="relation"><?=__('Hozzáadta')?></div>
                      <div class="add-at"><?=__('Határidő')?></div>
                    </div>
                  </div>
                  <?php foreach ((array)$doc['data'] as $d): ?>
                  <div class="list-item">
                    <div class="holder">
                      <div class="data">
                        <div class="title">
                          <a href="/doc/<?=$d['hashkey']?>" target="_blank"><strong><?=$d['name']?></strong></a>
                        </div>
                        <div class="subtitle">
                          <?php if ($d['ertek'] != 0): ?>
                          <span class="doc-ertek"><strong><?=\Helper::cashFormat($d['ertek'])?></strong> <?=__('Ft + ÁFA')?></span>
                          <?php endif; ?>
                          <?php if (!empty($d['teljesites_at'])): ?>
                          <span class="teljesitve" title="<?=$d['teljesites_at']?>"><?=__('Teljesítve')?> <i class="fas fa-check"></i></span>
                          <?php endif; ?>
                        </div>
                      </div>
                      <div class="relation">
                        <?=($d['xrefproject'] && $d['xrefproject']['adder_user_id'] == $this->_USERDATA['data']['ID'])?__('Én'):__('Partner')?>
                      </div>
                      <div class="add-at">
                        <?=date('Y/m/d', strtotime($d['expire_at']))?>
                      </div>
                    </div>
                  </div>
                  <?php endforeach; ?>
                </div>
              </div>
              <?php else: ?>
              <div class="no-data-view">
                <div class="ico"><i class="far fa-check-circle"></i></div>
                <div class="text"><?=__('Nincsenek lejárt díjbekérők!')?></div>
              </div>
              <?php endif; ?>
            </div>
            <div class="data-footer">
              <div class="d-flex align-items-center">
                <div class="title">
                  <h3><?=__('Lejárt díjbekérők')?></h3>
                  <a href="/dokumentumok/dijbekero?setproject=<?=$this->gets[2]?>"><?=__('Tovább az összes díjbekérőhöz')?></a>
                </div>
                <div class="count">
                  <div class="count-wrapper"><div class="num"><?=(int)$doc['total_num']?></div></div>
                </div>
              </div>
            </div>
          </div>
          <div class="wblock color-green">
            <div class="data-container">
              <?php $doc = $this->doc['servicer']['szamla']; ?>
              <?php if ($doc['return_num'] != 0): ?>
              <div class="data-list">
                <div class="wrapper">
                  <div class="header">
                    <div class="holder">
                      <div class="data"><?=__('Dokumentum adatok')?></div>
                      <div class="relation"><?=__('Hozzáadta')?></div>
                      <div class="add-at"><?=__('Időpont')?></div>
                    </div>
                  </div>
                  <?php foreach ((array)$doc['data'] as $d): ?>
                  <div class="list-item">
                    <div class="holder">
                      <div class="data">
                        <div class="title">
                          <a href="/doc/<?=$d['hashkey']?>" target="_blank"><strong><?=$d['name']?></strong></a>
                        </div>
                        <div class="subtitle">
                          <?php if ($d['folders'][0]): ?>
                          <span class="infolder"><?=$d['folders'][0]['folder_name']?></span>
                          <?php endif; ?>
                          <?php if ($d['ertek'] != 0): ?>
                          <span class="doc-ertek"><strong><?=\Helper::cashFormat($d['ertek'])?></strong> <?=__('Ft + ÁFA')?></span>
                          <?php endif; ?>
                          <?php if (!empty($d['expire_at'])): ?>
                          <span class="expire"><?=__('Határidő')?>: <strong><?=$d['expire_at']?></strong></span>
                          <?php endif; ?>
                          <?php if (!empty($d['teljesites_at'])): ?>
                          <span class="teljesitve" title="<?=$d['teljesites_at']?>"><?=__('Teljesítve')?> <i class="fas fa-check"></i></span>
                          <?php endif; ?>
                        </div>
                      </div>
                      <div class="relation">
                        <?=($d['xrefproject'] && $d['xrefproject']['adder_user_id'] == $this->_USERDATA['data']['ID'])?__('Én'):__('Partner')?>
                      </div>
                      <div class="add-at">
                        <?=date('Y/m/d H:i', strtotime($d['xproject_added_at']))?>
                      </div>
                    </div>
                  </div>
                  <?php endforeach; ?>
                </div>
              </div>
              <?php else: ?>
              <div class="no-data-view">
                <div class="ico"><i class="far fa-check-circle"></i></div>
                <div class="text"><?=__('Nincsenek számlák!')?></div>
              </div>
              <?php endif; ?>
            </div>
            <div class="data-footer">
              <div class="d-flex align-items-center">
                <div class="title">
                  <h3><?=__('Számlák')?></h3>
                  <a href="/dokumentumok/szamla?setproject=<?=$this->gets[2]?>"><?=__('Tovább az összes számlához')?></a>
                </div>
                <div class="count">
                  <div class="count-wrapper"><div class="num"><?=(int)$doc['total_num']?></div></div>
                </div>
              </div>
            </div>
          </div>
          <div class="wblock color-blue">
            <div class="data-container">
              <?php $doc = $this->doc['servicer']['all']; ?>
              <?php if ($doc['return_num'] != 0): ?>
              <div class="data-list">
                <div class="wrapper">
                  <div class="header">
                    <div class="holder">
                      <div class="data"><?=__('Dokumentum adatok')?></div>
                      <div class="relation"><?=__('Hozzáadta')?></div>
                      <div class="add-at"><?=__('Időpont')?></div>
                    </div>
                  </div>
                  <?php foreach ((array)$doc['data'] as $d): ?>
                  <div class="list-item">
                    <div class="holder">
                      <div class="data">
                        <div class="title">
                          <a href="/doc/<?=$d['hashkey']?>" target="_blank"><strong><?=$d['name']?></strong></a>
                        </div>
                        <div class="subtitle">
                          <?php if ($d['folders'][0]): ?>
                          <span class="infolder"><?=$d['folders'][0]['folder_name']?></span>
                          <?php endif; ?>
                          <?php if ($d['ertek'] != 0): ?>
                          <span class="doc-ertek"><strong><?=\Helper::cashFormat($d['ertek'])?></strong> <?=__('Ft + ÁFA')?></span>
                          <?php endif; ?>
                          <?php if (!empty($d['expire_at'])): ?>
                          <span class="expire"><?=__('Határidő')?>: <strong><?=$d['expire_at']?></strong></span>
                          <?php endif; ?>
                          <?php if (!empty($d['teljesites_at'])): ?>
                          <span class="teljesitve" title="<?=$d['teljesites_at']?>"><?=__('Teljesítve')?> <i class="fas fa-check"></i></span>
                          <?php endif; ?>
                        </div>
                      </div>
                      <div class="relation">
                        <?=($d['xrefproject'] && $d['xrefproject']['adder_user_id'] == $this->_USERDATA['data']['ID'])?__('Én'):__('Partner')?>
                      </div>
                      <div class="add-at">
                        <?=date('Y/m/d H:i', strtotime($d['xproject_added_at']))?>
                      </div>
                    </div>
                  </div>
                  <?php endforeach; ?>
                </div>
              </div>
              <?php else: ?>
              <div class="no-data-view">
                <div class="ico"><i class="far fa-check-circle"></i></div>
                <div class="text"><?=__('Nincsenek dokumentumok!')?></div>
              </div>
              <?php endif; ?>
            </div>
            <div class="data-footer">
              <div class="d-flex align-items-center">
                <div class="title">
                  <h3><?=__('Legfrissebb dokumentumok')?></h3>
                  <a href="/dokumentumok/?setproject=<?=$this->gets[2]?>"><?=__('Tovább az összes dokumentumhoz')?>: <strong><?=$doc['total_num']?> <?=__('darab')?></strong> </a>
                </div>
                <div class="count">
                  <div class="count-wrapper"><div class="num"><?=(int)$doc['return_num']?></div></div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="chead"><?=__('Üzenetek')?></div>
        <div class="dpad">
          <div class="messenger-quick-msg">
            <div class="wrapper">
              <label for="messanger_text"><i class="far fa-envelope"></i> <?=__('Gyors üzenet küldése <strong>{{project.servicer_project_data.user_servicer.data.nev}}</strong> részére:')?></label>
              <textarea ng-model="messanger.text" id="messanger_text" class="form-control no-editor"></textarea>
              <br>
              <div class="d-flex flex-row justify-content-between align-items-center">
                <div class="">
                  <a href="/uzenetek/session/{{project.servicer_project_data.hashkey}}"><i class="fas fa-envelope-open-text"></i> <?=__('Tovább a projekt üzeneteire')?></a>
                  <span class="unreaded-txt" ng-if="project.messages.unreaded && project.messages.unreaded!=0">{{project.messages.unreaded}} olvasatlan üzenet</span>
                </div>
                <div class="">
                  <button type="button" class="btn btn-primary btn-sm" ng-click="sendQuickMessage(project.servicer_project_data.hashkey)"><?=__('Üzenet küldése')?> <i class="fas fa-paper-plane"></i></button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

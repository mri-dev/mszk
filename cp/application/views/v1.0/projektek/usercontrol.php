<div class="overview">
  <div class="d-flex flex-row">
    <div class="project">
      <div class="head">
        <?=__('Projekt')?>
        <div class="own-data edit" ng-click="projectEditor()"><?=__('Szerkesztés')?> <i class="far fa-edit"></i></div>
      </div>
      <div class="cont">
        <table class="table table-bordered">
          <tbody>
            <tr ng-if="project.my_relation!='admin'">
              <td><?=__('Elnevezés')?></td>
              <td><strong>{{project.title}}</strong></td>
            </tr>
            <tr>
              <td><?=__('Státusz')?></td>
              <td><span class="badge" ng-class="{'badge-success': (project.closed==0), 'badge-danger': (project.closed==1) }">{{(project.closed==0)?'<?=__('Aktív projekt')?>':'<?=__('Lezárt projekt')?>'}}</span></td>
            </tr>
            <tr>
              <td><?=__('Hashkey')?></td>
              <td><strong><?=$this->gets[2]?></strong></td>
            </tr>
            <tr>
              <td><?=__('Állapot')?></td>
              <td>
                <div class="progress">
                  <div class="progress-bar" ng-class="project.status_percent_class" role="progressbar" style="width: {{project.status_percent}}%;" aria-valuenow="{{project.status_percent}}" aria-valuemin="0" aria-valuemax="100"><span ng-if="project.status_percent!=0">{{project.status_percent}}%</span></div>
                </div>
              </td>
            </tr>
            <tr>
              <td><?=__('Projekt költsége')?></td>
              <td><strong>{{project.offer.price}} <?=__('Ft + ÁFA')?></strong></td>
            </tr>
            <tr>
              <td><?=__('Díjfizetés')?></td>
              <td>
                <div class="progress">
                  <div class="progress-bar" ng-class="project[project.my_relation+'_paying_percent_class']" role="progressbar" style="width: {{project[project.my_relation+'_paying_percent']}}%;" aria-valuenow="{{project[project.my_relation+'_paying_percent']}}" aria-valuemin="0" aria-valuemax="100"><span ng-if="project[project.my_relation+'_paying_percent']!=0">{{project[project.my_relation+'_paying_percent']}}%</span></div>
                </div>
                <div class="paid-info">
                  <span class="allprice">{{project.offer.price}}</span> / <span class="paidprice">{{project[project.my_relation+'_paidamount']}}</span>
                  <span class="allpaid" ng-if="project.offer.price!=0&&project[project.my_relation+'_paidamount']>=project.offer.price"><?=__('Pénzügyileg teljesítve')?> <i class="fas fa-check"></i></span>
                </div>
              </td>
            </tr>
            <tr>
              <td><?=__('Létrejött')?></td>
              <td><strong>{{project.created_dist}}</strong> ({{project.created_at}})</td>
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
    <div class="requester" ng-if="project.my_relation=='requester'">
      <div class="head">
        <?=__('Az Ön adatai')?>
        <div class="own-data"><?=__('Ajánlatkérő')?></div>
      </div>
      <div class="cont">
        <table class="table table-bordered">
          <tbody>
            <tr ng-if="project.my_relation=='admin'">
              <td><?=__('Projekt elnevezése')?></td>
              <td><strong>{{project.requester_title}}</strong></td>
            </tr>
            <tr>
              <td><?=__('Név / Kapcsolattartó')?></td>
              <td><strong>{{project.user_requester.data.nev}}</strong></td>
            </tr>
            <tr ng-if="project.user_requester.data.company_name">
              <td><?=__('Cég neve')?></td>
              <td><strong>{{project.user_requester.data.company_name}}</strong></td>
            </tr>
            <tr ng-if="project.user_requester.data.company_adoszam">
              <td><?=__('Adószám')?></td>
              <td><strong>{{project.user_requester.data.company_adoszam}}</strong></td>
            </tr>
            <tr>
              <td><?=__('E-mail cím')?></td>
              <td><strong>{{project.user_requester.data.email}}</strong></td>
            </tr>
            <tr ng-if="project.user_requester.data.szallitas_phone">
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
    <div class="servicer" ng-if="project.my_relation=='servicer'">
      <div class="head">
        <?=__('Az Ön adatai')?>
        <div class="own-data"><?=__('Szoltáltató')?></div>
      </div>
      <div class="cont">
        <table class="table table-bordered">
          <tbody>
            <tr ng-if="project.my_relation=='admin'">
              <td><?=__('Projekt elnevezése')?></td>
              <td><strong>{{project.servicer_title}}</strong></td>
            </tr>
            <tr>
              <td><?=__('Név / Kapcsolattartó')?></td>
              <td><strong>{{project.user_servicer.data.nev}}</strong></td>
            </tr>
            <tr ng-if="project.user_servicer.data.company_name">
              <td><?=__('Cég neve')?></td>
              <td><strong>{{project.user_servicer.data.company_name}}</strong></td>
            </tr>
            <tr ng-if="project.user_servicer.data.company_adoszam">
              <td><?=__('Adószám')?></td>
              <td><strong>{{project.user_servicer.data.company_adoszam}}</strong></td>
            </tr>
            <tr>
              <td><?=__('E-mail cím')?></td>
              <td><strong>{{project.user_servicer.data.email}}</strong></td>
            </tr>
            <tr ng-if="project.user_servicer.data.szallitas_phone">
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
<div class="row" ng-if="project.my_relation!='admin' && !project.messages.closed">
  <div class="col-md-12">
    <h2><?=__('Üzenetküldés')?></h2>
    <div class="messenger-quick-msg">
      <div class="wrapper">
        <label for="messanger_text"><i class="far fa-envelope"></i> <?=__('Gyors üzenet küldése <strong>Szolgáltatás Közvetítő</strong> részére:')?></label>
        <textarea ng-model="messanger.text" id="messanger_text" class="form-control no-editor"></textarea>
        <br>
        <div class="d-flex flex-row justify-content-between align-items-center">
          <div class="">
            <a href="/uzenetek/session/{{project.hashkey}}"><i class="fas fa-envelope-open-text"></i> <?=__('Tovább a projekt üzeneteire')?></a>
            <span class="unreaded-txt" ng-if="project.messages[project.my_relation].unreaded && project.messages[project.my_relation].unreaded!=0">{{project.messages[project.my_relation].unreaded}} <?=__('olvasatlan üzenet')?></span>
          </div>
          <div class="">
            <button type="button" class="btn btn-primary btn-sm" ng-click="sendQuickMessage(project.hashkey)"><?=__('Üzenet küldése')?> <i class="fas fa-paper-plane"></i></button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="row" ng-if="project.my_relation!='admin'&&project.messages[project.my_relation].closed">
  <div class="col-md-12">
    <h2><?=__('Üzenetküldés')?></h2>
    <div class="alert alert-warning">
      <?=__('A gyors üzenetküldés nem elérhető a lezárt üzenetváltás esetében. Az üzenetváltást {{project.messages[project.my_relation].closed}} időponttal lezárták!')?>
    </div>
  </div>
</div>
<br>
<div class="row">
  <div class="col-md-5">
    <div class="action-buttons">
      <div class="d-flex">
        <div class="adddocs">
          <button type="button" class="btn btn-sm btn-primary" ng-click="projectDocsAdder(project.hashkey)"><i class="fas fa-file-medical"></i> <?=__('Dokumentum hozzáadása')?></button>
        </div>
      </div>
    </div>
    <div class="wblock color-red">
      <div class="data-container">
        <?php $doc = $this->doc['dijbekero']; ?>
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
        <?php $doc = $this->doc['szamla']; ?>
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
        <?php $doc = $this->doc['all']; ?>
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
  <div class="col-md-7">
    <h2><?=__('Ajánlat referencia')?></h2>
    <div class="offer-info">
      <h4><?=__('Ajánlat adatok')?></h4>
      <table class="table">
        <tbody>
          <tr>
            <td><?=__('Ajánlat beérkezése')?></td>
            <td><strong>{{project.offer.sended_at_dist}}</strong> ({{project.offer.sended_at}})</td>
          </tr>
          <tr>
            <td><?=__('Ajánlat elfogadva')?></td>
            <td><strong>{{project.offer.accepted_at_dist}}</strong> ({{project.offer.accepted_at}})</td>
          </tr>
          <tr>
            <td><?=__('Tervezett indulás')?></td>
            <td><strong>{{project.offer.project_start_at}}</strong></td>
          </tr>
          <tr>
            <td><?=__('Tervezett projekt időtartam')?></td>
            <td><strong>{{project.offer.offer_project_idotartam}}</strong></td>
          </tr>
        </tbody>
      </table>

      <h4><?=__('Ajánlat tartalma')?></h4>
      <div class="message" ng-bind-html="project.offer.message|unsafe"></div>
    </div>
    <div ng-if="project.my_relation=='requester'" class="offer-info">
     <h2><?=__('Ajánlatkérés referencia adatok')?></h2>
     <h4><?=__('Szolgáltatások')?></h4>
     <div class="selected-services-overview">
       <div class="service" ng-repeat="serv in project.request_data.services">
         <div class="header">
           {{serv.neve}}
         </div>
         <div class="subservices">
           <div class="subservice" ng-if="(subserv.szulo_id == serv.ID)" ng-repeat="subserv in project.request_data.subservices">
             <div class="header">
                {{subserv.neve}}
                <span class="sub-cash" title="<?=__('Szolgáltatás összesített költségkeret')?>"  ng-if="project.request_data.cash[subserv.ID]">{{project.request_data.cash[subserv.ID] | cash}}</span>
             </div>
             <div class="subservicesitems">
               <div class="subservice-item" ng-if="(subserv.szulo_id == serv.ID && subservitem.szulo_id == subserv.ID)" ng-repeat="subservitem in project.request_data.subservices_items">
                 <div class="header">
                    {{subservitem.neve}} <span class="cash" title="<?=__('Költségkeret')?>" ng-if="project.request_data.cash_config[subserv.ID][subservitem.ID]">{{project.request_data.cash_config[subserv.ID][subservitem.ID] | cash}}</span>
                 </div>
               </div>
             </div>
             <div class="subservice-comment" ng-if="project.request_data.service_description[subserv.ID]">
               <div class="head" ng-if="relation=='to'"><?=__('Ajánlatkérő igénye:')?></div>
               <div class="head" ng-if="relation=='from'"><?=__('Igényeim:')?></div>
               <div class="comment" ng-bind-html="project.request_data.service_description[subserv.ID]|unsafe" style="white-space: pre-line;"></div>
             </div>
           </div>
         </div>
       </div>
     </div>
     <table class="table">
       <tbody>
         <tr>
           <td><?=__('Elnevezés')?></td>
           <td><strong>{{project.request_data.user_requester_title}}</strong></td>
         </tr>
           <tr>
             <td><?=__('Hashkey')?></td>
             <td><strong>{{project.request_data.hashkey}}</strong></td>
           </tr>
         <tr>
           <td><?=__('Ajánlatkérés elküldve')?></td>
           <td><strong>{{project.request_data.requested}}</strong></td>
         </tr>
         <tr>
           <td><?=__('Összesített költségkeret')?></td>
           <td><strong>{{project.request_data.cash_total|cash}}</strong></td>
         </tr>
       </tbody>
     </table>
     <h4><?=__('Ajánlatkérés szövege')?></h4>
     <div class="message" ng-bind-html="project.request_data.message|unsafe"></div>
    </div>
  </div>
</div>

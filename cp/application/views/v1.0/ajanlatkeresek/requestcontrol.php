<div class="wblock fullheight">
  <div class="data-container">
    <div class="d-flex">
      <div class="request-list">
        <div class="head">
          <input type="text" ng-model="quicksearch" class="form-control" placeholder="<?=__('Gyors keresés...')?>">
        </div>
        <div class="req-list">
          <div class="request" ng-class="{'active': (readrequest == request.ID)}" ng-repeat="request in requests|filter:quickFilterSearch" ng-click="pickRequest(request)">
            <div class="wrapper">
              <div class="head">
                <div class="name">{{request.name}}</div>
                <div class="badges" ng-if="!request.project_id">
                  <span ng-if="request.unwatched_offers!=0" class="badge badge-danger badge-sm"><i class="far fa-eye-slash"></i> <?=__('{{request.unwatched_offers}} olvasatlan ajánlat')?></span>
                  <span ng-if="request.visited==1" class="badge badge-success badge-sm"><i class="far fa-eye"></i> <?=__('láttam')?></span>
                  <span ng-if="request.offerout==1 && request.elutasitva==0" class="badge badge-success badge-sm"><i class="fas fa-check"></i> <?=__('szolgáltatónak kiajánlva')?></span>
                  <span ng-if="request.offerout==1 && request.elutasitva==0 && request.admin_offer" class="badge badge-primary badge-sm"><i class="fas fa-check"></i> <?=__('ajánlatkérőnek kiajánlva')?></span>
                  <span ng-if="request.elutasitva==1" class="badge badge-danger badge-sm"><i class="fas fa-ban"></i> <?=__('elutasítva')?></span>
                </div>
                <div class="badges" ng-if="request.project_id">
                  <span class="badge badge-success badge-sm"><i class="fas fa-check-double"></i> Projeketek létrejöttek</span>
                </div>
                <div class="company" ng-if="request.company">{{request.company}}</div>
                <div class="contacts"><span class="email" title="<?=__('E-mail cím')?>">{{request.email}}</span><span class="phone" title="<?=__('Telefonszám')?>">{{request.phone}}</span></div>
                <div class="time" title="{{request.requested}}">{{request.requested_at}}</div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="request-data">
        <div class="" ng-if="!request">
          <div class="wrapper">
            <div class="iteractive-infos">
              <div class="loading-msg" ng-if="loading">
                <div class="spinner">
                  <i class="fas fa-spinner fa-spin"></i>
                </div>
                <h3><?=__('Adatok betöltése folyamatban...')?> </h3>
              </div>
              <div class="" ng-if="!loading && requests.length==0">
                <h3><?=__('Az adatok betöltésre kerültek')?></h3>
                <?=__('Jelenleg nincs megjelenítetendő adat.')?>
              </div>
              <div class="" ng-if="!loading && requests.length!=0 && !request">
                <div class="icon">
                  <i class="fas fa-long-arrow-alt-left"></i>
                </div>
                <h3><?=__('Az adatok betöltésre kerültek')?></h3>
                <?=__('A bal oldali kérsek közül válassza ki a kezelendő ajánlatatot.')?>
              </div>
            </div>
          </div>
        </div>
        <div class="wrapper" ng-if="request">
          <div class="requester">

            <div class="actual-project-data" ng-if="request.project_data">
              <div class="name"><?=__('Projekt')?>: <strong>{{request.project_data.admin_title}}</strong></div>
              <div class="datas">
                <div class="">
                  <?=__('Létrejött')?>: <strong>{{request.project_data.created_at}}</strong>
                </div>
                <br>
                <a href="/projektek/projekt/{{request.project_data.order_hashkey}}" class="btn btn-sm btn-default"><?=__('Tovább a projekt adatlapjára >>')?></a>
              </div>
            </div>

            <div class="" ng-if="request.offers">
              <div class="row-header">
                <h3><?=__('Bérkezett ajánlatok')?></h3>
                <div class="desc"><?=__('A szolgáltatóknak kiküldött ajánlatkérésre megküldött ajánlatok jelennek meg itt.')?></div>
              </div>
              <div class="dpad">
                <div class="incoming-offers">
                  <div class="offer" ng-repeat="offer in request.offers" ng-class="{visited:(offer.admin_visited), adminofferouted:(offer.admin_offered_out!=0), projected:(offer.project_id)}">
                    <div class="wrapper">
                      <div class="name">
                        <strong>{{offer.from_user.data.nev}}</strong> <span class="company" title="<?=__('Cég elnevezése')?>" ng-if="offer.from_user.data.company_name"> // {{offer.from_user.data.company_name}}</span>
                        <div class="email">{{offer.from_user.email}}</div>
                        <div class="labs">
                          <div class="adminofferouted" ng-if="offer.admin_offered_out!=0">> <?=__('Kiajánlva az ajánlatkérőnek!')?></div>
                          <div class="requesteraccept not-response" ng-if="offer.admin_offered_out!=0 && request.admin_offer && request.admin_offer.accepted==0">> <?=__('Ajánlatkérő döntés: még nincs elfogadva!')?></div>
                          <div class="requesteraccept resp-success" ng-if="offer.admin_offered_out!=0 && request.admin_offer && request.admin_offer.accepted==1">> <?=__('Ajánlatkérő döntés: ELFOGADVA')?> ({{request.admin_offer.accepted_at}})</div>
                        </div>
                      </div>
                      <div class="incoming-date">
                        <div class="lab"><?=__('Beérkezett')?></div>
                        <div class="val"><strong>{{offer.sended_at}}</strong></div>
                        <div class="val adminval" title="<?=__('Kiajánlás ideje az ajánlatkérőnek')?>" ng-if="offer.admin_offered_out!=0"><strong>{{request.admin_offer.sended_at}}</strong></div>
                      </div>
                      <div class="price">
                        <div class="lab"><?=__('Ajánlott ár')?></div>
                        <div class="val"><strong>{{offer.price | cash}}</strong></div>
                        <div class="val adminval" title="<?=__('Kiajánlott szolgáltatási díj')?>" ng-if="offer.admin_offered_out!=0"><strong>{{request.admin_offer.price|cash}}</strong></div>
                      </div>
                      <div class="dates">
                        <div class="lab"><?=__('Vállalt idők')?></div>
                        <div class="val">
                          <?=__('Kezdés')?>: <strong>{{offer.project_start_at}}</strong> <span class="adminval" title="<?=__('Kiajánlott érték')?>" ng-if="offer.admin_offered_out!=0">/ {{request.admin_offer.project_start_at}}</span> <br>
                          <?=__('Időtartam')?>: <strong>{{offer.offer_project_idotartam}}</strong> <span class="adminval" title="<?=__('Kiajánlott érték')?>"  ng-if="offer.admin_offered_out!=0">/ {{request.admin_offer.offer_project_idotartam}}</span><br>
                        </div>
                      </div>
                      <div class="abtns">
                        <i class="fa fa-bars" ng-click="toggleDetails('offerdet', offer.ID, $event)" title="<?=__('Ajánlat részletei')?>"></i>
                      </div>
                      <div class="details" id="offerdet{{offer.ID}}">
                        <div class="message">
                          <h4><?=__('Beérkezett ajánlat tartalma')?></h4>
                          <div ng-bind-html="offer.message|unsafe" style="white-space: pre-line;"></div>
                        </div>
                        <div class="message adminval" ng-if="offer.admin_offered_out!=0">
                          <br>
                          <h4><?=__('Kiajánlott ajánlat tartalma')?></h4>
                          <div ng-bind-html="request.admin_offer.message|unsafe" style="white-space: pre-line;"></div>
                        </div>
                        <div class="accept-service-offer">
                          <button type="button" ng-if="!request.admin_offer_id" ng-click="previewOfferToUser(request, offer)" class="btn btn-primary btn-sm">Ajánlat tovább ajánlása az ajánlatkérőnek <i class="fas fa-external-link-alt"></i></button>
                          <button type="button" ng-if="offer.admin_offered_out!=0 && request.admin_offer && request.admin_offer.accepted==1 && !request.project_id" ng-click="acceptAdminServiceOffer(request, offer)" class="btn btn-success btn-sm">Szolgáltatói ajánlat elfogadása <i class="fa fa-check"></i></button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="row-header">
              <h3><?=__('Ajánlatkérő')?></h3>
            </div>
            <div class="dpad">
              <div class="row">
                <div class="col">
                  <label for=""><?=__('Név')?></label>
                </div>
                <div class="col">
                  {{request.name}}
                </div>
              </div>
              <div class="row" ng-if="request.company">
                <div class="col">
                  <label for=""><?=__('Cégnév')?></label>
                </div>
                <div class="col">
                  {{request.company}}
                </div>
              </div>
              <div class="row">
                <div class="col">
                  <label for=""><?=__('E-mail')?></label>
                </div>
                <div class="col">
                  {{request.email}}
                </div>
              </div>
              <div class="row">
                <div class="col">
                  <label for=""><?=__('Telefon')?></label>
                </div>
                <div class="col">
                  {{request.phone}}
                </div>
              </div>
              <div class="row">
                <div class="col">
                  <label for=""><?=__('Ajánlatkérés ideje')?></label>
                </div>
                <div class="col">
                  {{request.requested_at}} ({{request.requested}})
                </div>
              </div>
              <div class="row">
                <div class="col">
                  <label for=""><?=__('Hashkey')?></label>
                </div>
                <div class="col">
                  {{request.hashkey}}
                </div>
              </div>
              <div class="row" ng-if="(request && request.visited == '1')" >
                <div class="col">
                  <label for=""><?=__('Admin látta')?></label>
                </div>
                <div class="col">
                  <span class="badge badge-success"><?=__('IGEN')?>: {{request.visited_at}}</span>
                </div>
              </div>
              <div class="row" ng-if="(request && request.elutasitva == '1')" >
                <div class="col">
                  <label for=""><?=__('Admin elutasította')?></label>
                </div>
                <div class="col">
                  <span class="badge badge-danger"><?=__('IGEN')?></span>
                </div>
              </div>
              <div class="row" ng-if="!request.project_id">
                <div class="col">
                  <label for=""><?=__('Műveletek')?></label>
                </div>
                <div class="col">
                  <button ng-if="(request && request.visited == '1')" type="button" class="btn btn-sm btn-primary" ng-click="runRequestAction(request.ID, 'unvisit')"><?=__('Láttam eltávolítása')?> <i class="far fa-eye-slash"></i></button>
                  <button ng-if="(request && request.visited == '0')" type="button" class="btn btn-sm btn-primary" ng-click="runRequestAction(request.ID, 'visit')"><?=__('Láttam / Megtekintettem')?> <i class="far fa-eye"></i></button>
                  <button ng-if="(request && request.elutasitva == '0')" type="button" class="btn btn-sm btn-danger" ng-click="runRequestAction(request.ID, 'elutasit')"><?=__('Elutasítás')?> <i class="fas fa-ban"></i></button>
                </div>
              </div>
            </div>

            <div class="message" ng-if="request.message">
              <div class="row-header">
                  <h3><?=__('Megjegyzés')?></h3>
              </div>
              <div class="dpad">
                <div class="row">
                  <div class="col-md-12" ng-bind-html="request.message|unsafe" style="white-space: pre-line;"></div>
                </div>
              </div>
            </div>
            <div class="row-header">
                <h3><?=__('Szolgáltatás igények')?></h3>
            </div>
            <div class="dpad">
              <div class="selected-services-overview">
                <div class="service" ng-repeat="serv in request.services">
                  <div class="header">
                    {{serv.neve}}
                  </div>
                  <div class="subservices">
                    <div class="subservice" ng-if="(subserv.szulo_id == serv.ID)" ng-repeat="subserv in request.subservices">
                      <div class="header">
                         {{subserv.neve}}
                         <span class="sub-cash" title="<?=__('Szolgáltatás összesített költségkeret')?>"  ng-if="request.cash[subserv.ID]">{{request.cash[subserv.ID] | cash}}</span>
                      </div>
                      <div class="subservicesitems">
                        <div class="subservice-item" ng-if="(subserv.szulo_id == serv.ID && subservitem.szulo_id == subserv.ID)" ng-repeat="subservitem in request.subservices_items">
                          <div class="header">
                             {{subservitem.neve}} <span class="cash" title="<?=__('Költségkeret')?>" ng-if="request.cash_config[subserv.ID][subservitem.ID]">{{request.cash_config[subserv.ID][subservitem.ID] | cash}}</span>
                          </div>
                        </div>
                      </div>
                      <div class="subservice-comment" ng-if="request.service_description[subserv.ID]">
                        <div class="head"><?=__('Ajánlatkérő igénye:')?></div>
                        <div class="comment" ng-bind-html="request.service_description[subserv.ID]|unsafe" style="white-space: pre-line;"></div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="row-header">
                <h3><?=__('Teljes költségkeret')?></h3>
            </div>
            <div class="dpad">
              <div class="total-cash">
                <div class="row">
                  <div class="col-md-12">
                    <div class="value">
                      {{request.cash_total | cash}}
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="row-header" ng-if="request.offerout == 0">
              <h3><?=__('Igénylés elküldése a szolgáltatók felé')?></h3>
              <div class="desc">
                <?=__('Itt adhatja hozzá a lehetséges szolgáltatókat, akiknek ki szeretné küldeni az ajánlást!')?>
              </div>
            </div>
            <div class="row-header" ng-if="request.offerout == 1">
              <h3><?=__('Szolgáltatók listája')?></h3>
              <div class="desc">
                <?=__('Az alábbi listában láthatja, hogy mely szolgáltató(k)nak lett kiajánlva a kérés!')?>
              </div>
            </div>
            <div class="dpad">
              <div class="services-hints">
                <div class="no-hints" ng-if="servicerAccounts.length==0">
                  <div class="alert alert-danger">
                    <?=__('Jelenleg nincs szolgáltató rögzítve a rendszerben!')?>
                  </div>
                </div>
                <div class="service-user-list" ng-if="servicerAccounts.length!=0">
                  <div class="service-group">
                    <div class="listfilters" ng-if="request.elutasitva==0 && request.offerout == 0">
                      <md-autocomplete
                        id="custom-template"
                        ng-disabled="saac.isDisabled"
                        md-no-cache="saac.noCache"
                        md-selected-item="saac.selectedItem"
                        md-search-text-change="saac.searchTextChange(saac.searchText)"
                        md-search-text="saac.searchText"
                        md-selected-item-change="saac.selectedItemChange(request, user.ID)"
                        md-items="user in saac.querySearch(saac.searchText)"
                        md-item-text="user.nev"
                        md-min-length="0"
                        input-aria-label="<?=__('Szolgáltató')?>"
                        placeholder="<?=__('Szolgáltatók keresése')?>"
                        md-menu-class="autocomplete-custom-template"
                        md-menu-container-class="custom-container">
                        <md-item-template>
                          <div class="item-wrapper">
                            <div class="name">
                              <strong>{{user.nev}}</strong>
                              <span class="company" ng-if="user.total_data.data.company_name">// {{user.total_data.data.company_name}}</span>
                              <span class="email">({{user.email}})</span>
                            </div>
                          </div>
                        </md-item-template>
                    </md-autocomplete>
                    </div>
                    <div class="servicers">
                      <div class="user" ng-repeat="user in servicerAccounts" ng-if="request.passed_user_offer_id && request.passed_user_offer_id.indexOf(user.ID) !== -1">
                        <div class="wrapper">
                          <label for="servu{{service.item.ID}}_us{{user.ID}}"> <strong><span class="company" ng-if="user.total_data.data.company_name">{{user.total_data.data.company_name}}</span>{{user.nev}} (#{{user.ID}})</strong> {{user.email}}
                            <div class="infos" ng-hide="request.offerout == 1">
                              <span title="{{user.utoljara_belepett}}"><?=__('Belépett:')?> <strong>{{user.utoljara_belepett_dist}}</strong></span>
                              <span title="{{user.regisztralt}}"><?=__('Regisztrált:')?> <strong>{{user.regisztralt_dist}}</strong></span>
                            </div>
                            <div class="infos" ng-show="request.offerout == 1">
                              <span title="{{request.offerouts.users[user.ID].offerout_at}}"><?=__('Ajánlás kiküldve:')?> <strong>{{request.offerouts.users[user.ID].offerout_at_dist}}</strong></span>
                            </div>
                          </label>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="text-right" ng-if="request.elutasitva==0 && request.offerout == 0">
                    <button type="button" ng-if="!servicesrequestprogress && !servicesrequestsendedsuccess" class="btn btn-danger" ng-click="sendServicesRequest()"><?=__('Kiajánlás elindítása')?> <i class="far fa-arrow-alt-circle-right"></i></button>
                    <div class="" ng-if="servicesrequestprogress">
                      <div class="alert alert-primary text-left">
                        <?=__('Ajánlatkérés kiajánlása folyamatban van...')?> <i class="fas fa-spinner fa-spin"></i>
                      </div>
                    </div>
                    <div class="" ng-if="servicesrequestsendedsuccess">
                      <div class="alert alert-success text-left" ng-bind-html="servicesrequestsendedsuccess|unsafe">

                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

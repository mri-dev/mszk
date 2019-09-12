<div class="wblock">
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
                <div class="badges">
                  <span ng-if="request.visited==1" class="badge badge-success badge-sm"><i class="far fa-eye"></i> <?=__('láttam')?></span>
                  <span ng-if="request.offerout==1 && request.elutasitva==0" class="badge badge-success badge-sm"><i class="fas fa-check"></i> <?=__('kiajánlva')?></span>
                  <span ng-if="request.elutasitva==1" class="badge badge-danger badge-sm"><i class="fas fa-ban"></i> <?=__('elutasítva')?></span>
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
              <div class="" ng-if="requests.length==0">
                <?=__('Nincs megjeleníthető ajánlat kérés.')?>
              </div>
              <div class="" ng-if="requests.length!=0 && !request">
                <?=__('A bal oldali kérsek közül válassza ki a kezelendő ajánlat kérést.')?>
              </div>
            </div>
          </div>
        </div>
        <div class="wrapper" ng-if="request">
          <div class="requester">
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
              <div class="row">
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
                  <div class="col-md-12" ng-bind-html="request.message|unsafe"></div>
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
                         <span class="sub-cash" title="<?=__('Szolgáltatás összesített költségkeret')?>"  ng-if="request.cash[subserv.ID]">{{request.cash[subserv.ID]}} <?=__('Ft + ÁFA')?></span>
                      </div>
                      <div class="subservicesitems">
                        <div class="subservice-item" ng-if="(subserv.szulo_id == serv.ID && subservitem.szulo_id == subserv.ID)" ng-repeat="subservitem in request.subservices_items">
                          <div class="header">
                             {{subservitem.neve}} <span class="cash" title="<?=__('Költségkeret')?>" ng-if="request.cash_config[subserv.ID][subservitem.ID]">{{request.cash_config[subserv.ID][subservitem.ID]}}  <?=__('Ft + ÁFA')?></span>
                          </div>
                        </div>
                      </div>
                      <div class="subservice-comment" ng-if="request.service_description[subserv.ID]">
                        <div class="head"><?=__('Ajánlatkérő igénye:')?></div>
                        <div class="comment" ng-bind-html="request.service_description[subserv.ID]|unsafe"></div>
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
                      {{request.cash_total}} <?=__('Ft + ÁFA')?>
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
                    <button type="button" ng-if="!servicesrequestprogress" class="btn btn-danger" ng-click="sendServicesRequest()"><?=__('Kiajánlás elindítása')?> <i class="far fa-arrow-alt-circle-right"></i></button>
                    <div class="" ng-if="servicesrequestprogress">
                      <div class="alert alert-primary text-left">
                        <?=__('Ajánlatkérés kiajánlása folyamatban van...')?> <i class="fas fa-spinner fa-spin"></i>
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

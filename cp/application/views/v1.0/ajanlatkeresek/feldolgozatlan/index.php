<div class="page-desc">
  <?=__('Ezen az oldalon elvégezheti a kiajánlást a szolgáltatók felé. Ellenőrizze, hogy a beérkezett igények megfelelőek-e és hogy kinek szeretné kiajánlani a kérést.')?>
</div>

<div class="request-controller" ng-controller="RequestControl" ng-init="init({loadpossibleservices:1})">
  <div class="wblock">
    <div class="data-container">
      <div class="d-flex">
        <div class="request-list">
          <div class="head">
            <input type="text" class="form-control" placeholder="<?=__('Gyors keresés...')?>">
          </div>
          <div class="req-list">
            <div class="request" ng-class="{'active': (readrequest == request.ID)}" ng-repeat="request in requests" ng-click="pickRequest(request)">
              <div class="wrapper">
                <div class="head">
                  <div class="name">{{request.name}}</div>
                  <div class="company" ng-if="request.company">{{request.company}}</div>
                  <div class="contacts"><span class="email" title="<?=__('E-mail cím')?>">{{request.email}}</span><span class="phone" title="<?=__('Telefonszám')?>">{{request.phone}}</span></div>
                  <div class="time" title="{{request.requested}}">{{request.requested_at}}</div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="request-data">
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

              <div class="row-header">
                <h3><?=__('Igénylés elküldése a szolgáltatók felé')?></h3>
                <div class="desc">
                  <?=__('Jelölje ki a szolgáltató felhasználókat ahova a rendszer küldje ki az ajánlatkérést!')?>
                </div>
              </div>
              <div class="dpad">
                <div class="services-hints">
                  <div class="no-hints" ng-if="request.services_hints.length==0">
                    <div class="alert alert-danger">
                      <?=__('Erre az ajánlatkérésre jelenleg nincs szolgáltató partner rögzítve a rendszerben!')?>
                    </div>
                  </div>
                  <div class="service-user-list" ng-if="request.services_hints.length!=0">
                    <div class="text-right">
                      <button type="button" ng-if="!servicesrequestprogress" class="btn btn-danger" ng-click="sendServicesRequest()"><?=__('Kiajánlás elindítása')?> <i class="far fa-arrow-alt-circle-right"></i></button>
                      <div class="" ng-if="servicesrequestprogress">
                        <div class="alert alert-primary text-left">
                          <?=__('Ajánlatkérés kiajánlása folyamatban van...')?> <i class="fas fa-spinner fa-spin"></i>
                        </div>
                      </div>
                    </div>
                    <br>
                    <div class="service-group" ng-repeat="service in request.services_hints">
                      <div class="header">
                        <div class="wrapper">
                          <div class="serv"><div class="txt">{{service.service.nev}}</div></div>
                          <div class="subserv"><div class="txt">{{service.subservice.nev}}</div></div>
                          <div class="item"><div class="txt">{{service.item.nev}}</div></div>
                        </div>
                        <div class="clr"></div>
                      </div>
                      <div class="servicers">
                        <div class="user" ng-repeat="user in service.users">
                          <div class="wrapper">
                            <input type="checkbox" checked="checked" ng-model="servuser['item_'+service.item.ID][user.ID]" class="ccb" id="servu{{service.item.ID}}_us{{user.ID}}"> <label for="servu{{service.item.ID}}_us{{user.ID}}"> <strong><span class="company" ng-if="user.company">{{user.company}}</span>{{user.nev}} (#{{user.ID}})</strong> {{user.email}}
                              <div class="infos">
                                <span title="{{user.utoljara_belepett}}"><?=__('Belépett:')?> {{user.utoljara_belepett_dist}}</span>  <span title="{{user.regisztralt}}"><?=__('Regisztrált:')?> {{user.regisztralt_dist}}</span>
                              </div>
                            </label>
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
  </div>
</div>
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
              <div class="dpad">
                <div class="row">
                  <div class="col-md-12">
                    <h3><?=__('Ajánlatkérő')?></h3>
                  </div>
                </div>
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
                <div class="divider"></div>
                <div class="dpad">
                  <div class="row">
                    <div class="col-md-12">
                      <h3><?=__('Megjegyzés')?></h3>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-12" ng-bind-html="request.message|unsafe"></div>
                  </div>
                </div>
              </div>

              <div class="divider"></div>

              <div class="dpad">
                <div class="row">
                  <div class="col-md-12">
                    <h3><?=__('Szolgáltatás igények')?></h3>
                  </div>
                </div>
                <div class="selected-services-overview">
                  <div class="service" ng-repeat="serv in request.services">
                    <div class="header">
                      {{serv.neve}}
                    </div>
                    <div class="subservices">
                      <div class="subservice" ng-if="(subserv.szulo_id == serv.ID)" ng-repeat="subserv in request.subservices">
                        <div class="header">
                           - {{subserv.neve}}
                        </div>
                        <div class="subservicesitems">
                          <div class="subservice-item" ng-if="(subserv.szulo_id == serv.ID && subservitem.szulo_id == subserv.ID)" ng-repeat="subservitem in request.subservices_items">
                            <div class="header">
                               -- {{subservitem.neve}} <span class="cash" ng-if="request.cash_config[subserv.ID][subservitem.ID]">{{request.cash_config[subserv.ID][subservitem.ID]}}  <?=__('Ft + ÁFA')?></span>
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

                <div class="row">
                  <div class="col-md-12">
                    <h3><?=__('Teljes költségkeret')?></h3>
                    {{request.cash_total}} <?=__('Ft + ÁFA')?>
                  </div>
                </div>
              </div>

              <div class="divider"></div>

              <div class="dpad">
                <div class="row">
                  <div class="col-md-12">
                    <h3><?=__('Igénylés elküldése a szolgáltatók felé')?></h3>
                  </div>
                </div>
                asd
              </div>

            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

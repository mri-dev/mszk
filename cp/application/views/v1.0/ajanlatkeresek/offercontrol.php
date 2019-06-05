<div class="wblock">
  <div class="data-container">
    <div class="d-flex">
      <div class="request-list">
        <div class="head">
          <div class="switcher">
            <div class="" ng-click="changeRelation('to')" ng-class="{'active': (relation=='to')}">
              <button type="button"><i class="far fa-arrow-alt-circle-right"></i> <?=__('Beérkező')?></button>
            </div>
            <div class="" ng-click="changeRelation('from')" ng-class="{'active': (relation=='from')}">
              <button type="button"><?=__('Kimenő')?> <i class="far fa-arrow-alt-circle-right"></i></button>
            </div>
          </div>
        </div>
        <div class="req-list">
          <div class="request-service"  ng-repeat="request in requests[relation]">
            <div class="wrapper">
              <div class="head">
                <div class="name">{{request.name}}</div>
              </div>
              <div class="request-subservice" ng-repeat="r in request.items">
                <div class="head">{{r.name}}</div>
                <div class="request" ng-class="{'active': (readrequest == u.ID), 'closed': (u.request_closed==1), 'declined': (u.recepient_declined==1), 'accepted': (u.recepient_accepted==1)}" ng-repeat="u in r.users" ng-click="pickRequest(u)">
                  <div class="head">
                    <span class="name"><i class="fas" ng-class="(!u.recepient_visited_at)?'fa-eye-slash':'fa-eye'"></i> {{u.requester_form_name}}</span> <span ng-if="u.requester_form_company && u.requester_form_company!=''" class="company">// <strong>{{u.requester_form_company}}</strong></span>
                    <div class="badges">
                      <span ng-if="u.request_closed==1" class="badge badge-danger badge-sm"><i class="fas fa-lock"></i> <?=__('lezárva')?></span>
                      <span ng-if="u.recepient_declined==1" class="badge badge-warning badge-sm"><i class="fas fa-times"></i> <?=__('tárgytalan')?></span>
                      <span ng-if="u.recepient_accepted==1" class="badge badge-success badge-sm"><i class="fas fa-check"></i> <?=__('Ajánlat küldve')?></span>
                    </div>
                  </div>
                  <div class="sub">
                    <span class="date" title="<?=__('Az igénylés rögzítési ideje')?>: {{u.requested}}"><i class="far fa-clock"></i> {{u.requested_dist}}</span>
                    <span class="cashinfo" ng-if="u.cash_config[u.subservice.ID][u.item_id]"><span class="cash">{{u.cash_config[u.subservice.ID][u.item_id]}} Ft + ÁFA</span></span>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="no-requests" ng-if="!requests[relation]">
            <div class="wrapper" ng-if="relation=='to'">
              <i class="far fa-folder-open"></i><?=__('Nincs beérkező ajánlat kérése.')?>
            </div>
            <div class="wrapper" ng-if="relation=='from'">
                <i class="far fa-folder-open"></i><?=__('Nincs kimenő ajánlat kérése.')?>
              </div>
          </div>
        </div>
      </div>
      <div class="request-data">
        <div class="" ng-if="!request">
          <div class="wrapper">
            <div class="iteractive-infos">
              <div class="" ng-if="requests[relation].length==0">
                <?=__('Nincs megjeleníthető ajánlat kérés.')?>
              </div>
              <div class="" ng-if="requests[relation].length!=0 && !request">
                <?=__('A bal oldali kérsek közül válassza ki a kezelendő ajánlat kérést.')?>
              </div>
            </div>
          </div>
        </div>
        <div class="wrapper" ng-if="request">
          <div class="requester">
            <div class="row-header">
              <h3><?=__('Ajánlat adatai')?></h3>
            </div>
            <div class="dpad">
              <div class="row">
                <div class="col">
                  <label for=""><?=__('Szolgáltatás')?></label>
                </div>
                <div class="col">
                  {{request.service.neve}} / {{request.subservice.neve}} / {{request.item.neve}}
                </div>
              </div>
              <div class="row">
                <div class="col">
                  <label for=""><?=__('Név')?></label>
                </div>
                <div class="col">
                  {{request.requester_form_name}}
                </div>
              </div>
              <div class="row" ng-if="request.requester_form_company">
                <div class="col">
                  <label for=""><?=__('Cégnév')?></label>
                </div>
                <div class="col">
                  {{request.requester_form_company}}
                </div>
              </div>
              <div class="row">
                <div class="col">
                  <label for=""><?=__('Ajánlatkérés hashkey')?></label>
                </div>
                <div class="col">
                  {{request.request_hashkey}}
                </div>
              </div>
              <div class="row">
                <div class="col">
                  <label for=""><?=__('Ajánlatkérés ideje')?></label>
                </div>
                <div class="col">
                  {{request.requested_dist}} ({{request.requested}})
                </div>
              </div>
              <div class="row" ng-if="(relation=='to' && request && request.recepient_visited_at)" >
                <div class="col">
                  <label for=""><?=__('Láttam')?></label>
                </div>
                <div class="col">
                  <span class="badge badge-success"><?=__('IGEN')?>: {{request.recepient_visited_at}}</span>
                </div>
              </div>
              <div class="row" ng-if="(relation=='to' && request && request.recepient_declined == '1')" >
                <div class="col">
                  <label for=""><?=__('Tárgytalan részemről')?></label>
                </div>
                <div class="col">
                  <span class="badge badge-danger"><?=__('IGEN')?></span>
                </div>
              </div>
            </div>

            <div class="message" ng-if="request.requester_form_message">
              <div class="row-header">
                  <h3><?=__('Megjegyzés')?></h3>
              </div>
              <div class="dpad">
                <div class="row">
                  <div class="col-md-12" ng-bind-html="request.requester_form_message|unsafe"></div>
                </div>
              </div>
            </div>
            <div class="row-header">
                <h3><?=__('Szolgáltatás igények leírása')?></h3>
            </div>
            <div class="dpad">
              <div ng-bind-html="request.service_description[request.subservice.ID]|unsafe"></div>
            </div>

            <div class="row-header">
                <h3><?=__('Műveletek')?></h3>
            </div>
            <div class="dpad">
              <div class="row">
                <div class="col-md-6">
                  <button ng-if="(request && request.recepient_visited_at)" type="button" class="btn btn-sm btn-primary" ng-click="runRequestAction(request.ID, 'unvisit')"><?=__('Láttam eltávolítása')?> <i class="far fa-eye-slash"></i></button>
                  <button ng-if="(request && !request.recepient_visited_at)" type="button" class="btn btn-sm btn-primary" ng-click="runRequestAction(request.ID, 'visit')"><?=__('Láttam / Megtekintettem')?> <i class="far fa-eye"></i></button>
                </div>
                <div class="col-md-6 text-right">
                  <div class="" ng-if="request.request_closed==0">
                    <button type="button" ng-if="request.recepient_declined==0 && request.recepient_accepted==0" class="btn btn-sm btn-success" ng-click="prepareOfferSend(request.ID)"><?=__('ÉRDEKEL - Ajánlatot küldök')?> <i class="fas fa-check"></i></button>
                    <button type="button" ng-if="request.recepient_declined==0" class="btn btn-sm btn-danger" ng-click="runRequestAction(request.ID, 'decline')"><?=__('NEM ÉRDEKEL - Tárgytalan')?> <i class="fas fa-times"></i></button>
                  </div>
                  <div class="red-txt" ng-if="request.request_closed==1">
                    <strong><?=__('Ezt az ajánlatkérőt lezárta az igénylő! Nem lehet rá ajánlatot küldeni!')?></strong>
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

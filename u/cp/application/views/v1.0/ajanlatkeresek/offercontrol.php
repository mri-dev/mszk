<div class="wblock">
  <div class="data-container">
    <div class="d-flex">
      <div class="request-list">
        <div class="head">
          <div class="switcher">
            <div class="" ng-click="changeRelation('to')" ng-class="{'active': (relation=='to')}">
              <span class="badge" ng-if="badges.in!=0">{{badges.in}}</span>
              <button type="button"><i class="far fa-arrow-alt-circle-right"></i> <?=__('Beérkező')?></button>
            </div>
            <div class="" ng-click="changeRelation('from')" ng-class="{'active': (relation=='from')}">
              <span class="badge" ng-if="badges.out!=0">{{badges.out}}</span>
              <button type="button"><?=__('Kimenő')?> <i class="far fa-arrow-alt-circle-right"></i></button>
            </div>
          </div>
        </div>
        <div class="req-list">
          <div class="requestgroup" ng-repeat="request in requests[relation]">
            <div class="head">
              <div class="name" title="<?=__('Ajánlatkérő')?>"><i class="far fa-user-circle"></i> <strong>{{request.user_name}}</strong></div>
              <div class="reqdate" title="<?=__('Ajánlatkérés ideje')?>"><i class="far fa-clock"></i> {{request.idopont}}</div>
              <div class="hashkey" title="<?=__('Ajánlatkérés hashkey')?>"><i class="fas fa-database"></i> {{request.hashkey}}</div>
            </div>
            <div class="request-service" ng-repeat="services in request.services">
              <div class="wrapper">
                <div class="head">
                  <div class="name">{{services.name}}</div>
                </div>
                <div class="request-subservice" ng-repeat="r in services.items">
                  <div class="head">{{r.name}}</div>
                  <div class="request" ng-class="{'active': (readrequest == u.ID), 'closed': (u.request_closed==1), 'declined': (u.recepient_declined==1), 'accepted': (u.recepient_accepted==1)}" ng-repeat="u in r.users" ng-click="pickRequest(u)">
                    <div class="head">
                      <div class="names" ng-if="relation=='to'">
                        <span class="name"><i class="fas" ng-class="(!u.recepient_visited_at)?'fa-eye-slash':'fa-eye'"></i> {{u.requester_form_name}}</span> <span ng-if="u.requester_form_company && u.requester_form_company!=''" class="company">// <strong>{{u.requester_form_company}}</strong></span>
                      </div>
                      <div class="names" ng-if="relation=='from'">
                        <span class="name"><i class="fas" ng-class="(!u.recepient_visited_at)?'fa-eye-slash':'fa-eye'"></i> {{u.user_to.data.nev}}</span> <span ng-if="u.user_to.data.company_name && u.user_to.data.company_name!=''" class="company">// <strong>{{u.user_to.data.company_name}}</strong></span>
                      </div>
                      <div class="badges">
                        <span ng-if="u.request_closed==1" class="badge badge-danger badge-sm"><i class="fas fa-lock"></i> <?=__('lezárva')?></span>
                        <span ng-if="u.recepient_declined==1" class="badge badge-warning badge-sm"><i class="fas fa-times"></i> <?=__('tárgytalan')?></span>
                        <span ng-if="u.recepient_accepted==1" class="badge badge-success badge-sm"><i class="fas fa-check"></i> <?=__('Ajánlat küldve')?></span>
                        <span ng-if="u.requester_accepted==1" class="badge badge-success badge-sm"><i class="fas fa-check-double"></i> <?=__('Ajánlat elfogadva')?></span>
                      </div>
                    </div>
                    <div class="sub">
                      <span class="date" title="<?=__('Az igénylés rögzítési ideje')?>: {{u.requested}}"><i class="far fa-clock"></i> {{u.requested_dist}}</span>
                      <span class="cashinfo" ng-if="u.cash_config[u.subservice.ID][u.item_id]">
                        <span class="cash" title="<?=__('Költséghatár')?>">{{u.cash_config[u.subservice.ID][u.item_id]}} Ft + ÁFA</span>
                        <span class="cash" ng-if="u.offers" style="background: #ff6a21;" title="<?=__('Ajánlott szolgáltatás díja')?>">{{u.offers[0].price}} Ft + ÁFA</span>
                      </span>
                    </div>
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
              <div class="row" ng-if="(request && request.recepient_visited_at)" >
                <div class="col">
                  <label for="" ng-if="relation=='to'"><?=__('Láttam')?></label>
                  <label for="" ng-if="relation=='from'"><?=__('Látta')?></label>
                </div>
                <div class="col">
                  <span class="badge badge-success"><?=__('IGEN')?>: {{request.recepient_visited_at}}</span>
                </div>
              </div>
              <div class="row" ng-if="(request && request.recepient_declined == '1')" >
                <div class="col">
                  <label for="" ng-if="relation=='to'"><?=__('Tárgytalan részemről')?></label>
                  <label for="" ng-if="relation=='from'"><?=__('Tárgytalan')?></label>
                </div>
                <div class="col">
                  <span class="badge badge-warning"><?=__('IGEN')?></span>
                </div>
              </div>
              <div class="row" ng-if="(request && request.requester_accepted == '1')" >
                <div class="col">
                  <label for="" ng-if="relation=='from'"><?=__('Ajánlatot elfogadtam')?></label>
                  <label for="" ng-if="relation=='to'"><?=__('Elfogadták az ajánlatot')?></label>
                </div>
                <div class="col">
                  <span class="badge badge-success"><?=__('IGEN')?></span>
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
            <div class="" ng-if="!request.user_offer_id && relation=='to'">
              <div class="row-header">
                  <h3><?=__('Műveletek')?></h3>
              </div>
              <div class="dpad">
                <div class="row">
                  <div class="col-md-4">
                    <button ng-if="(request && request.recepient_visited_at && !request.user_offer_id)" type="button" class="btn btn-sm btn-primary" ng-click="runRequestAction(request.ID, 'unvisit')"><?=__('Láttam eltávolítása')?> <i class="far fa-eye-slash"></i></button>
                    <button ng-if="(request && !request.recepient_visited_at && !request.user_offer_id)" type="button" class="btn btn-sm btn-primary" ng-click="runRequestAction(request.ID, 'visit')"><?=__('Láttam / Megtekintettem')?> <i class="far fa-eye"></i></button>
                  </div>
                  <div class="col-md-8 text-right">
                    <div class="" ng-if="request.request_closed==0">
                      <button type="button" ng-if="request.recepient_declined==0 && request.recepient_accepted==0" class="btn btn-sm btn-success" ng-click="showOfferSending(true)"><?=__('ÉRDEKEL - Ajánlatot küldök')?> <i class="fas fa-check"></i></button>
                      <button type="button" ng-if="request.recepient_declined==0 && !request.user_offer_id" class="btn btn-sm btn-danger" ng-click="runRequestAction(request.ID, 'decline')"><?=__('NEM ÉRDEKEL - Tárgytalan')?> <i class="fas fa-times"></i></button>
                    </div>
                    <div class="red-txt" ng-if="request.request_closed==1">
                      <strong><?=__('Ezt az ajánlatkérőt lezárta az igénylő! Nem lehet rá ajánlatot küldeni!')?></strong>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="send-offer-holder" ng-if="showoffersend && (!request.user_offer_id && relation=='to')">
              <div class="row-header">
                <h3><?=__('Ajánlat küldése erre az ajánlat kérésre')?></h3>
              </div>
              <div class="dpad">
                <div ng-if="sendingoffer">
                  <div class="alert alert-warning">
                    <?=__('Ajánlat elküldése folyamatban...')?> <i class="fas fa-sync fa-spin"></i>
                  </div>
                </div>
                <div class="" ng-if="!sendingoffer">
                  <div class="row">
                    <div class="col-md-4">
                      <label for="offer_price"><?=__('Kínált szolgáltatás díja')?></label>
                      <div class="input-group">
                        <input type="number" id="offer_price" ng-model="offer.price" class="form-control">
                        <div class="input-group-append">
                          <span class="input-group-text"><?=__('Ft + ÁFA')?></span>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-4">
                      <label for="offer_project_start_at"><?=__('Legkorábbi indulás')?>?</label>
                      <input type="date" id="offer_project_start_at" ng-model="offer.project_start_at" class="form-control">
                    </div>
                    <div class="col-md-4">
                      <label for="offer_project_idotartam"><?=__('Várható projekt időtartama')?>?</label>
                      <input type="text" id="offer_project_idotartam" ng-model="offer.project_idotartam" class="form-control">
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-12">
                      <label for="offer_message"><?=__('Ajánlat tartalma')?></label>
                      <textarea class="form-control" id="offer_message" ng-model="offer.message" rows="12"></textarea>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-12 text-right">
                      <button type="button" class="btn btn-md btn-success" ng-click="sendOffer()"><?=__('Ajánlat elküldése')?></button>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="send-offer-holder" ng-if="request.user_offer_id">
              <div class="row-header">
                <h3 ng-if="relation=='to'"><?=__('Elküldött ajánlatom részletei')?></h3>
                <h3 ng-if="relation=='from'"><?=__('Beérkezett ajánlat részletei')?></h3>
              </div>
              <div class="dpad">
                <div class="row">
                  <div class="col-md-3">
                    <?=__('Rögzítve')?>
                  </div>
                  <div class="col-md-9">
                    <strong>{{request.offer.sended_at_dist}} ({{request.offer.sended_at}})</strong>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-3">
                    <?=__('Ajánlott szolgáltatás díj')?>
                  </div>
                  <div class="col-md-9">
                    <strong>{{request.offer.price}} <?=__('Ft + ÁFA')?></strong>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-3">
                    <?=__('Legkorábbi indulás')?>
                  </div>
                  <div class="col-md-9">
                    <strong>{{request.offer.project_start_at}}</strong>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-3">
                    <?=__('Projekt időtartam')?>
                  </div>
                  <div class="col-md-9">
                    <strong>{{request.offer.offer_project_idotartam}}</strong>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12">
                    <?=__('Ajánlat tartalma')?>:<br>
                    <div style="color:black; line-height: 1.4;" ng-bind-html="request.offer.message|unsafe"></div>
                  </div>
                </div>

                <div class="row" ng-if="request.offer.accepted==1">
                  <div class="col-md-3">
                    <i class="fas fa-check-double"></i> <?=__('Projekt ajánlat elfogadva')?>
                  </div>
                  <div class="col-md-9">
                    <strong>{{request.offer.accepted_at}}</strong>
                  </div>
                </div>
              </div>
              <div class="offer-accepter" ng-if="relation=='from' && !request.requester_accepted">
                <div class="head">
                  <?=__('Ajánlat elfogadása / Projekt létrehozása')?>
                </div>
                <div class="dpad">
                  <?=__('Az elfogadásával Ön elfogadja jelen ajánlatot és lezárul az ajánlat kérése. További ajánlatot nem tudnak küldeni erre az ajánlat kérésére!')?>
                  <br><br>
                  <?=__('Az elfogadás követően egy új projektet nyit, melyen keresztül lebonyolítható az ajánlat tárgya, hozzáférhet a partner adataihoz.')?>
                  <br><br>
                  <div class="" ng-if="!sendingofferaccept">
                    <div class="row align-items-end">
                      <div class="col-md-5">
                        <label for=""><?=__('Új projekt elnevezése / Azonosító')?></label>
                        <input type="text" ng-model="acceptofferdata.project" autocomplete="new-password" class="form-control">
                      </div>
                      <div class="col-md-3">
                        <label for=""><?=__('Fiók jelszó')?></label>
                        <input type="password" ng-model="acceptofferdata.password" autocomplete="new-password" class="form-control">
                      </div>
                      <div class="col-md-4">
                        <button type="button" class="btn btn-block btn-warning" ng-click="acceptOffer()"><?=__('Új projekt létrehozása')?></button>
                      </div>
                    </div>
                  </div>
                  <div class="" ng-if="acceptoffererror">
                    <br>
                    <div class="alert alert-danger">
                      {{acceptoffererror}}
                    </div>
                  </div>
                  <div ng-if="sendingofferaccept">
                    <div class="alert alert-warning">
                      <?=__('Ajánlat elfogadása és projekt létrehozása folyamatban...')?>
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

<div class="wblock fullheight">
  <div class="data-container">
    <div class="d-flex">
      <div class="request-list">
        <div class="head">
          <?php if (false): ?>
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
          <?php endif; ?>
        </div>
        <div class="req-list">
          <div class="requestgroup" ng-click="pickRequest(r)" ng-class="{offered: (r.user_offer_id), opened: (r.ID == readrequest), visited: (r.recepient_visited_at), 'declined': (r.recepient_declined==1 || (r.request_closed==1 && !r.user_offer_id)), incomingoffer:(relation=='from' && r.admin_offer && r.admin_offer.accepted=='0'), acceptedoffer:(relation=='from' && r.admin_offer && r.admin_offer.accepted=='1')}" ng-repeat="r in requests">
            <div class="head" ng-if="relation=='to'">
              <div class="" ng-if="r.recepient_declined==0">
                <div class="name" ng-if="!r.user_offer_id && r.request_closed==0"><strong><?=__('Új feldolgozatlan ajánlatkérés {{r.services.length}} db szolgáltatásra!')?></strong></div>
                <div class="name" ng-if="r.user_offer_id"><strong><?=__('Ajánlat elküldve: {{r.services.length}} db szolgáltatásra!')?></strong></div>
                <div class="name" ng-if="r.request_closed==1 && !r.user_offer_id"><strong><?=__('Ajánlat lezárva: {{r.services.length}} db szolgáltatásra!')?></strong></div>

              </div>
              <div class="name" ng-if="r.recepient_declined==1"><strong><?=__('Ajánlatkérés elutasítva: {{r.services.length}} db szolgáltatásra!')?></strong></div>
              <div class="reqdate" title="<?=__('Ajánlatkérés ideje')?>"><i class="far fa-clock"></i> {{r.offerout_at}}</div>
              <div class="hashkey" title="<?=__('Ajánlatkérés hashkey')?>"><i class="fas fa-database"></i> {{r.hashkey}}</div>
            </div>
            <div class="head" ng-if="relation=='from'">
              <div class="name"><strong>{{r.user_requester_title}}</strong></div>
              <div class="labels">
                <span class="badge badge-primary" ng-if="r.admin_offer && r.admin_offer.accepted=='0'"><?=__('Bejövő ajánlat')?></span>
                <span class="badge badge-success" ng-if="r.admin_offer && r.admin_offer.accepted=='1'"><?=__('Általam elfogadott ajánlat')?></span>
              </div>
              <div class="reqdate" title="<?=__('Ajánlatkérés ideje')?>"><i class="far fa-clock"></i> {{r.offerout_at}}</div>
              <div class="hashkey" title="<?=__('Ajánlatkérés hashkey')?>"><i class="fas fa-database"></i> {{r.hashkey}}</div>
            </div>
          </div>
          <div class="no-requests" ng-if="isObjectEmpty(requests) && !loading">
            <div class="wrapper">
              <i class="far fa-folder-open"></i>
              <i class="fas fa-ellipsis-v"></i>
              <?=__('Nincs elérhető ajánlatkérés')?>
            </div>
          </div>
        </div>
      </div>
      <div class="request-data">
      {{Object.keys(requests).length}}
        <div class="iteractive-infos">
          <div class="loading-msg" ng-if="loading">
            <div class="spinner">
              <i class="fas fa-spinner fa-spin"></i>
            </div>
            <h3><?=__('Adatok betöltése folyamatban...')?> </h3>
          </div>
          <div class="" ng-if="!loading && isObjectEmpty(requests)">
            <h3><?=__('Az adatok betöltésre kerültek')?></h3>
            <?=__('Jelenleg nincs megjelenítetendő adat.')?>
          </div>
          <div class="" ng-if="!loading && !isObjectEmpty(requests) && !request">
            <div class="icon">
              <i class="fas fa-long-arrow-alt-left"></i>
            </div>
            <h3><?=__('Az adatok betöltésre kerültek')?></h3>
            <?=__('A bal oldali kérsek közül válassza ki a kezelendő ajánlatatot.')?>
          </div>
        </div>
        <div class="wrapper" ng-if="request">
          <div class="requester">
            <div class="row-header">
              <h3><?=__('Ajánlat adatai')?></h3>
            </div>
            <div class="dpad">
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
                  {{request.hashkey}}
                </div>
              </div>
              <div class="row">
                <div class="col">
                  <label for=""><?=__('Ajánlatkérés ideje')?></label>
                </div>
                <div class="col">
                  {{request.offerout_dist}} ({{request.offerout_at}})
                </div>
              </div>
              <div class="row" ng-if="(request && request.recepient_visited_at)" >
                <div class="col">
                  <label for="" ng-if="relation=='to'"><?=__('Láttam')?></label>
                  <label for="" ng-if="relation=='from'"><?=__('Admin látta')?></label>
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
                  <span class="badge badge-danger"><?=__('IGEN')?></span>
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
            <div class="row-header">
              <h3><?=__('Ajánlatkérés szövege')?></h3>
            </div>
            <div class="dpad">
              <div class="" ng-bind-html="request.message|unsafe"></div>
            </div>

            <div ng-if="request.attachments.length">
              <div class="attachments">
                <div class="row-header" ng-if="relation=='from'">
                    <h3><i class="fas fa-paperclip"></i> <?=__('Csatolmányaim')?></h3>
                </div>
                <div class="row-header" ng-if="relation=='to'">
                    <h3><i class="fas fa-paperclip"></i> <?=__('Csatolmányok')?></h3>
                </div>
                <div class="dpad">
                  <div class="attachment-list">
                    <div class="file" ng-repeat="a in request.attachments">
                      <a href="{{a.filepath}}" target="_blank"><i class="fas fa-external-link-alt"></i> <strong>{{a.filename}}</strong> ({{a.extension}}) - {{a.sizetext}}</a>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="row-header">
              <h3><?=__('Igényelt szolgáltatások')?></h3>
            </div>
            <div class="dpad">
              <div class="selected-services-overview">
                <div class="service" ng-repeat="serv in request.services">
                  <div class="header">{{serv.neve}}</div>
                  <div class="service-describe">
                    <div class="data">
                      <div class="line">
                        <div class="d-flex">
                          <div class="h"><?=__('Kezdő időpont')?>:</div>
                          <div class="v"><strong>{{request.overall_service_details[serv.ID].date_start|date:'yyyy. MM. dd.'}}</strong><em ng-if="!request.overall_service_details[serv.ID].date_start"><?=__('nem lett meghatározva')?></em></div>
                        </div>
                      </div>
                      <div class="line">
                        <div class="d-flex">
                          <div class="h"><?=__('Időtartam')?>:</div>
                          <div class="v"><strong>{{request.overall_service_details[serv.ID].date_duration}}</strong><em ng-if="!request.overall_service_details[serv.ID].date_duration"><?=__('nem lett meghatározva')?></em></div>
                        </div>
                      </div>
                      <div class="line">
                        <div class="d-flex">
                          <div class="h"><?=__('Teljes költségkeret')?>:</div>
                          <div class="v"><strong>{{request.overall_service_details[serv.ID].cash_total|cash}}</strong><em ng-if="!request.overall_service_details[serv.ID].cash_total"><?=__('nem lett meghatározva')?></em></div>
                        </div>
                      </div>
                      <div class="line mdesc">
                        <div class="h"><?=__('Megjegyzés / Részletek')?>:</div>
                        <div class="v"><strong>{{request.overall_service_details[serv.ID].description}}</strong><em ng-if="!request.overall_service_details[serv.ID].description"><?=__('nem lett meghatározva')?></em></div>
                      </div>
                    </div>
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

            <div class="row-header" ng-if="request.cash_total">
                <h3><?=__('Költségvetés keret')?></h3>
            </div>
            <div class="dpad" ng-if="request.cash_total">
              <div class="total-cash">
                {{request.cash_total|cash}}
              </div>
            </div>

            <div ng-if="relation=='to' && request.kozvetito_comment">
              <div class="row-header"><h3><?=__('Közvetítői megjegyzés')?></h3></div>
              <div class="dpad">
                <div class="comment" ng-bind-html="request.kozvetito_comment|unsafe" style="white-space: pre-line;"></div>
              </div>
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
                      <strong><?=__('Ezt az ajánlatot lezárta az igénylő. Ajánlat beküldése nem lehetséges!')?></strong>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="send-offer-holder" ng-if="showoffersend && (!request.user_offer_id)">
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

            <div class="send-offer-holder" ng-class="{unaccepted:(request.admin_offer.accepted=='0')}" ng-if="relation=='from' && request.admin_offer">
              <div class="row-header">
                <h3 ng-if="request.admin_offer.accepted=='0'"><?=__('Beérkezett ajánlat')?></h3>
                <h3 ng-if="request.admin_offer.accepted=='1'"><?=__('Elfogadott ajánlat')?></h3>
              </div>
              <div class="dpad">
                <div class="row">
                  <div class="col-md-3">
                    <?=__('Beérkezett')?>
                  </div>
                  <div class="col-md-9">
                    <strong>{{request.admin_offer.sended_at_dist}} ({{request.admin_offer.sended_at}})</strong>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-3">
                    <?=__('Ajánlott szolgáltatás díj')?>
                  </div>
                  <div class="col-md-9">
                    <strong>{{request.admin_offer.price|cash}}</strong>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-3">
                    <?=__('Legkorábbi indulás')?>
                  </div>
                  <div class="col-md-9">
                    <strong>{{request.admin_offer.project_start_at}}</strong>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-3">
                    <?=__('Projekt időtartam')?>
                  </div>
                  <div class="col-md-9">
                    <strong>{{request.admin_offer.offer_project_idotartam}}</strong>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12">
                    <?=__('Ajánlat tartalma')?>:<br>
                    <div style="white-space: pre-line; font-weight: bold; color:black; line-height: 1.4; margin: 10px 0 10px 0;" ng-bind-html="request.admin_offer.message|unsafe"></div>
                  </div>
                </div>
                <div class="row" ng-if="request.admin_offer.accepted==1">
                  <div class="col-md-3">
                    <i class="fas fa-check-double"></i> <?=__('Projekt ajánlat elfogadva')?>
                  </div>
                  <div class="col-md-9">
                    <strong>{{request.admin_offer.accepted_at}}</strong>
                  </div>
                </div>
              </div>
              <div class="offer-accepter" ng-if="relation=='from' && request.admin_offer.accepted == '0'">
                <div class="head">
                  <?=__('Ajánlat elfogadása')?>
                </div>
                <div class="dpad">
                  <?=__('Az ajánlat elfogadásával lezárul az Ön ajánlatkérése. Az elfogadás követően adminisztrátorunk jóváhagyása után létrejön a projekt.')?>
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
                        <button type="button" class="btn btn-block btn-warning" ng-click="acceptOffer()"><?=__('Elfogadom az ajánlatot')?></button>
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
                    <strong>{{request.offer.price|cash}}</strong>
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
                    <div style="white-space: pre-line; color:black; line-height: 1.4; margin: 10px 0 10px 0;" ng-bind-html="request.offer.message|unsafe"></div>
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
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="home inside-content">
  <?php echo $this->render('templates/home'); ?>
  <div class="ajanlat-requester" ng-init="prepareAjanlatkeres()">
    <div class="pw">
      <div class="header">
        <div class="d-flex align-items-center">
          <div class="titles">
            <div class="main">
              {{title[step-1].main}}
            </div>
            <div class="sub">
              {{title[step-1].sub}}
            </div>
          </div>
          <div class="steps">
            <div class="step-progress"><div class="progress" style="width:{{getProgressPercent()}}%"></div></div>
            <div class="step-holder">
              <div class="step" ng-click="goToStep($index+1)" ng-class="{'active': (walkedstep > $index+1), 'now': (walkedstep == $index+1), 'current': ($index+1 == step)  }" ng-repeat="s in getNumber(max_step) track by $index">
                <div class="index"><span ng-hide="(step > $index+1)">{{$index+1}}</span><span ng-show="(step > $index+1)"><i class="fas fa-check"></i></span></div>
                <div class="text">{{steps[$index]}}</div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="step-containers">
        <!-- Step 1 - Kategória -->
        <div class="step-layout step1" ng-show="(step == 1)">
          <div class="services">
            <div class="service" ng-click="pickService(service.ID)" ng-class="{'picked': isPickedService(service.ID)}" ng-repeat="service in resources.szolgaltatasok">
              <div class="wrapper">
                <div class="title">
                  {{service.neve}}
                </div>
                <div class="image autocorrect-height-by-width" data-image-ratio="1:1">
                  <img class="" ng-src="<?=str_replace('/src/','', SOURCE)?>{{service.kep}}" alt="">
                </div>
                <div class="desc" ng-bind-html="service.leiras|unsafe"></div>
              </div>
            </div>
          </div>
          <div class="next-btn">
            <div class="" ng-show="selected_services.length > 0" >
              <div class="row justify-content-end align-items-center">
                <div class="col text-right">{{selected_services.length}} <?=__('kiválasztott szolgáltatás.')?> <button type="button" ng-click="nextStep()" class="btn btn-primary btn-lg"><?=__('Tovább a testreszabáshoz')?> <i class="fas fa-chevron-right"></i></button></div>
              </div>
            </div>
            <div class="info-next text-right">
              <div ng-hide="selected_services.length > 0">
                <div class="text-error">
                  <?=__('A továbbhaladáshoz válasszon szolgáltatásaink közül.')?>
                </div>

              </div>
            </div>
          </div>
        </div>
        <!-- Step 2 - Testreszabás -->
        <div class="step-layout step2" ng-show="(step == 2)">
          <div class="services-configurator">
            <div class="row">
              <div class="col-md-7">
                <div class="services-group">
                  <div class="service" ng-repeat="service in resources.szolgaltatasok" ng-hide="selected_services.indexOf(service.ID)===-1">
                    <div class="head">{{service.neve}}</div>
                    <div class="service-describe">
                      <div class="head"><h3><?=__('Részletek megadása')?></h3></div>
                      <div class="line">
                        <label for=""><?=__('Megjegyzés')?></label>
                        <textarea class="form-control editor" ng-model="overall_service_details[service.ID].description" placeholder="<?=__('Itt adhatja meg a(z) {{service.neve}} szolgáltatással kapcsolatos információit.')?>"></textarea>
                      </div>
                      <div class="line more-detail">
                        <div class="d-flex">
                          <div class="startdate">
                            <div class="w">
                              <label for=""><?=__('Kezdő időpont meghatározása')?></label>
                              <input type="date" class="form-control" ng-model="overall_service_details[service.ID].date_start">
                            </div>
                          </div>
                          <div class="rundate">
                            <div class="w">
                              <label for=""><?=__('Időtartam meghatározása')?></label>
                              <input type="text" class="form-control" ng-model="overall_service_details[service.ID].date_duration">
                            </div>
                          </div>
                          <div class="cashoverall">
                            <div class="w">
                              <label for=""><?=__('Teljes nettó költségkeret')?></label>
                              <div class="input-group">
                                <input type="number" step="1" class="form-control" ng-model="overall_service_details[service.ID].cash_total">
                                <div class="input-group-append"><span class="input-group-text"><?=__('+ ÁFA')?></span></div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="cashdifference-msg" ng-if="overallCashNotSame(service.ID) && cashdifference[service.ID].cc  != 0">
                      <div class="title"><?php echo __('A költségvetés kalkuláció nem egyezik a teljes költségkerettel:'); ?></div>
                      <div class="calculation">
                        <div class="ctext">
                          <?=__('A számolt költségvetés <strong>{{cashdifference[service.ID].cc|cash}}</strong>.')?>
                          <div class="cdiff diff_positiv" ng-if="(cashdifference[service.ID].diff*-1) >= 0"><?=__('Eltérés:')?> <strong>+{{(cashdifference[service.ID].diff*-1)|cash}}</strong>.</div>
                          <div class="cdiff diff_negativ" ng-if="(cashdifference[service.ID].diff*-1) < 0"><?=__('Eltérés:')?> <strong>{{(cashdifference[service.ID].diff*-1)|cash}}</strong>.</div>
                        </div>
                        <button type="button" class="btn btn-default btn-sm" ng-click="refreshOverallCash(service.ID,cashdifference[service.ID].cc)"><?=__('Teljes költségkeret frissítése:')?> {{cashdifference[service.ID].cc|cash}}<?=__('-ra')?> ></button>
                      </div>
                    </div>
                    <div class="sub-services">
                      <div class="head center"><h3><?=__('További konfigurációs lehetőségek')?></h3></div>
                      <div class="sub-service" ng-class="{'selected': isPickedSubService(subserv.ID)}" ng-repeat="subserv in service.child">
                        <div class="wrapper">
                          <div class="title" ng-click="pickServiceSub(subserv.ID)">
                            <div class="adder" ></div>
                            {{subserv.neve}}
                            <div class="subserv-item-text" ng-show="(subserv.child.length)">
                              {{subserv.child.length}} <?=__('db alszolgáltatás konfigurálható.')?>
                            </div>
                          </div>
                          <div class="subserv-items" ng-show="isPickedSubService(subserv.ID)">
                            <div class="subserv-item" ng-class="{'selected': isPickedSubServiceItem(subservitem.ID)}" ng-repeat="subservitem in subserv.child">
                              <div class="wrapper" ng-click="pickServiceSubItem(subservitem.ID)">
                                {{subservitem.neve}}
                              </div>
                            </div>
                          </div>
                          <div class="service-comment" ng-show="isPickedSubService(subserv.ID)">
                            <div class="head"><strong>{{subserv.neve}}</strong> <?=__('szolgáltatással kapcsolatos igényei')?>:</div>
                            <textarea class="form-control" ng-model="service_desc[subserv.ID]" placeholder="<?=__('Részletezze személyes igényeit a szolgáltatással kapcsolatban...')?>"></textarea>
                          </div>
                          <div class="service-cash" ng-show="isPickedSubService(subserv.ID)">
                            <div class="head"><?=__('Tételes keretösszegek')?>:</div>
                            <div class="cashrow" ng-repeat="subservitem in subserv.child" ng-show="isPickedSubServiceItem(subservitem.ID)">
                              <div class="input-group">
                                <div class="input-group-prepend"><span class="input-group-text">+ {{subservitem.neve}}</span></div>
                                <input type="number" step="1" ng-change="recalcCashAll(subserv.ID, subservitem.ID)" ng-model="service_cashrow[subserv.ID][subservitem.ID]" class="form-control">
                                <div class="input-group-append"><span class="input-group-text"><?=__('+ ÁFA')?></span></div>
                              </div>
                            </div>
                            <div class="head"><?=__('Teljes keretösszeg a(z) <strong>{{subserv.neve}}</strong> projektekre')?>:</div>
                            <div class="input-group">
                              <div class="input-group-prepend"><span class="input-group-text"><?=__('Nettó')?></span></div>
                              <input type="number" step="1" ng-change="checkServiceCashAll()" ng-model="service_cashall[subserv.ID]" class="form-control">
                              <div class="input-group-append"><span class="input-group-text"><?=__('+ ÁFA')?></span></div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-5">
                <div class="block-list">
                  <div class="header">
                    <?=__('Kiválasztott szolgáltatások')?>
                  </div>
                  <div class="wrapper">
                    <div class="item" ng-repeat="service in resources.szolgaltatasok" ng-hide="selected_services.indexOf(service.ID)===-1">
                      <div class="head">
                        {{service.neve}}
                      </div>
                      <div class="subitem" ng-repeat="subserv in service.child" ng-show="isPickedSubService(subserv.ID)">
                        <div class="head">
                          <i class="fas fa-check"></i> {{subserv.neve}}
                        </div>
                        <div class="paramitem" ng-repeat="subservitem in subserv.child" ng-show="isPickedSubServiceItem(subservitem.ID)">
                          <i class="fas fa-check-double"></i> {{subservitem.neve}}
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="next-btn">
              <div class="" >
                <div class="row justify-content-between align-items-center">
                  <div class="col text-left">
                      <button type="button" ng-click="prevStep()" class="btn btn-default btn-sm"><i class="fas fa-chevron-left"></i> <?=__('vissza: Szolgáltatások')?> </button>
                  </div>
                  <div class="col text-right">
                    <button type="button" ng-click="nextStep()" class="btn btn-primary btn-lg"><?=__('Tovább az összegzéshez')?> <i class="fas fa-chevron-right"></i></button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- Step 3 - Összegzés -->
        <div class="step-layout step3" ng-show="(step == 3)">
          <div class="services-overview">
            <div class="row">
              <div class="col-md-8">
                <div class="block-list">
                  <div class="wrapper">
                    <div class="item" ng-repeat="service in resources.szolgaltatasok" ng-hide="selected_services.indexOf(service.ID)===-1">
                      <div class="head">
                        {{service.neve}}
                      </div>
                      <div class="service-describe">
                        <div class="head"><?=__('A szolgáltatással kapcsolatos igények')?></div>
                        <div class="data">
                          <div class="line">
                            <div class="d-flex">
                              <div class="h"><?=__('Kezdő időpont')?>:</div>
                              <div class="v"><strong>{{overall_service_details[service.ID].date_start|date:'yyyy. MM. dd.'}}</strong><em ng-if="!overall_service_details[service.ID].date_start"><?=__('nem lett meghatározva')?></em></div>
                            </div>
                          </div>
                          <div class="line">
                            <div class="d-flex">
                              <div class="h"><?=__('Időtartam')?>:</div>
                              <div class="v"><strong>{{overall_service_details[service.ID].date_duration}}</strong><em ng-if="!overall_service_details[service.ID].date_duration"><?=__('nem lett meghatározva')?></em></div>
                            </div>
                          </div>
                          <div class="line">
                            <div class="d-flex">
                              <div class="h"><?=__('Teljes költségkeret')?>:</div>
                              <div class="v"><strong>{{overall_service_details[service.ID].cash_total|cash}}</strong><em ng-if="!overall_service_details[service.ID].cash_total"><?=__('nem lett meghatározva')?></em></div>
                            </div>
                          </div>
                          <div class="line mdesc">
                            <div class="h"><?=__('Igények részletezése')?>:</div>
                            <div class="v"><strong>{{overall_service_details[service.ID].description}}</strong><em ng-if="!overall_service_details[service.ID].description"><?=__('nem lett meghatározva')?></em></div>
                          </div>
                        </div>
                      </div>
                      <div class="subitem" ng-repeat="subserv in service.child" ng-show="isPickedSubService(subserv.ID)">
                        <div class="head">
                          <i class="fas fa-check"></i> {{subserv.neve}}
                        </div>
                        <div class="paramitem" ng-repeat="subservitem in subserv.child" ng-show="isPickedSubServiceItem(subservitem.ID)">
                          <i class="fas fa-check-double"></i> {{subservitem.neve}} <span class="cashrow-conf" ng-show="service_cashrow[subserv.ID][subservitem.ID]">{{service_cashrow[subserv.ID][subservitem.ID]|cash}}</span>
                        </div>
                        <div class="service-comment" ng-if="ervice_desc[subserv.ID]">
                          <strong><i class="far fa-comment-dots"></i> <?=__('Megjegyzés / Igények')?>:</strong>
                          <div class="" ng-bind-html="service_desc[subserv.ID]|unsafe"></div>
                        </div>
                        <div class="service-cash" ng-show="service_cashall[subserv.ID]">
                          <strong><i class="fas fa-money-check-alt"></i> <?=__('Tétel keretösszege')?>:</strong> <strong>{{service_cashall[subserv.ID]|cash}}</strong>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="block-list">
                  <div class="header">
                    <?=__('Ezek is érdekelhetik')?>
                  </div>
                  <div class="wrapper">
                    <div class="item" ng-repeat="service in resources.szolgaltatasok" ng-hide="selected_services.indexOf(service.ID)===-1">
                      <div class="head">
                        <i class="fas fa-plus"></i> {{service.neve}}
                      </div>
                      <div class="subitem" ng-class="{'unselected': !isPickedSubService(subserv.ID)}" ng-repeat="subserv in service.child">
                        <div class="head" ng-click="pickServiceSub(subserv.ID)">
                          <i class="fas fa-check" ng-show="isPickedSubService(subserv.ID)"></i>
                          <i class="far fa-square" ng-show="!isPickedSubService(subserv.ID)"></i>
                          {{subserv.neve}}
                        </div>
                        <div class="paramitem" ng-click="pickServiceSubItem(subservitem.ID)" ng-class="{'unselected': !isPickedSubServiceItem(subservitem.ID)}" ng-repeat="subservitem in subserv.child">
                          <i class="fas fa-check-double" ng-show="isPickedSubServiceItem(subservitem.ID)"></i>
                          <i class="far fa-square" ng-show="!isPickedSubServiceItem(subservitem.ID)"></i>
                          {{subservitem.neve}}
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="footer text-center">
                    <button type="button" ng-click="goToStep(2)" class="btn btn-secondary btn-sm"><i class="fas fa-chevron-left"></i> <?=__('vissza a tételek szerkesztéséhez')?> <i class="far fa-edit"></i></button>
                  </div>
                </div>
              </div>
            </div>
            <div class="next-btn">
              <div class="" ng-show="selected_services.length > 0" >
                <div class="row justify-content-between align-items-center">
                  <div class="col text-left">
                      <button type="button" ng-click="prevStep()" class="btn btn-default btn-sm"><i class="fas fa-chevron-left"></i> <?=__('vissza: Testreszabás')?> </button>
                  </div>
                  <div class="col text-right">
                    <button type="button" ng-click="nextStep()" class="btn btn-primary btn-lg"><?=__('Tovább a küldéshez')?> <i class="fas fa-chevron-right"></i></button>
                  </div>
                </div>
              </div>
              <div class="info-next">
                <div ng-hide="selected_services.length > 0">
                  <?=__('A továbbhaladáshoz válasszon szolgáltatásaink közül.')?>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- Step 4 - Küldés -->
        <div class="step-layout step4" ng-show="(step == 4)">
          <div class="wrapper">
            <div class="row">
              <div class="col-md-7">
                <div class="block-list">
                  <div class="wrapper">
                    <div class="item" ng-repeat="service in resources.szolgaltatasok" ng-hide="selected_services.indexOf(service.ID)===-1">
                      <div class="head">
                        {{service.neve}}
                      </div>
                      <div class="service-describe">
                        <div class="head"><?=__('A szolgáltatással kapcsolatos igények')?></div>
                        <div class="data">
                          <div class="line">
                            <div class="d-flex">
                              <div class="h"><?=__('Kezdő időpont')?>:</div>
                              <div class="v"><strong>{{overall_service_details[service.ID].date_start|date:'yyyy. MM. dd.'}}</strong><em ng-if="!overall_service_details[service.ID].date_start"><?=__('nem lett meghatározva')?></em></div>
                            </div>
                          </div>
                          <div class="line">
                            <div class="d-flex">
                              <div class="h"><?=__('Időtartam')?>:</div>
                              <div class="v"><strong>{{overall_service_details[service.ID].date_duration}}</strong><em ng-if="!overall_service_details[service.ID].date_duration"><?=__('nem lett meghatározva')?></em></div>
                            </div>
                          </div>
                          <div class="line">
                            <div class="d-flex">
                              <div class="h"><?=__('Teljes költségkeret')?>:</div>
                              <div class="v"><strong>{{overall_service_details[service.ID].cash_total|cash}}</strong><em ng-if="!overall_service_details[service.ID].cash_total"><?=__('nem lett meghatározva')?></em></div>
                            </div>
                          </div>
                          <div class="line mdesc">
                            <div class="h"><?=__('Igények részletezése')?>:</div>
                            <div class="v"><strong>{{overall_service_details[service.ID].description}}</strong><em ng-if="!overall_service_details[service.ID].description"><?=__('nem lett meghatározva')?></em></div>
                          </div>
                        </div>
                      </div>
                      <div class="subitem" ng-repeat="subserv in service.child" ng-show="isPickedSubService(subserv.ID)">
                        <div class="head">
                          <i class="fas fa-check"></i> {{subserv.neve}}
                        </div>
                        <div class="paramitem" ng-repeat="subservitem in subserv.child" ng-show="isPickedSubServiceItem(subservitem.ID)">
                          <i class="fas fa-check-double"></i> {{subservitem.neve}} <span class="cashrow-conf" ng-show="service_cashrow[subserv.ID][subservitem.ID]">{{service_cashrow[subserv.ID][subservitem.ID]|cash}}</span>
                        </div>
                        <div class="service-comment" ng-if="ervice_desc[subserv.ID]">
                          <strong><i class="far fa-comment-dots"></i> <?=__('Megjegyzés / Igények')?>:</strong>
                          <div class="" ng-bind-html="service_desc[subserv.ID]|unsafe"></div>
                        </div>
                        <div class="service-cash" ng-show="service_cashall[subserv.ID]">
                          <strong><i class="fas fa-money-check-alt"></i> <?=__('Tétel keretösszege')?>:</strong> <strong>{{service_cashall[subserv.ID]|cash}}</strong>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="next-btn">
                  <div class="" ng-show="selected_services.length > 0" >
                    <div class="row justify-content-between align-items-start">
                      <div class="col text-left">
                          <button type="button" ng-click="prevStep()" class="btn btn-default btn-sm"><i class="fas fa-chevron-left"></i> <?=__('vissza: Összegzés')?> </button>
                          <button type="button" ng-click="goToStep(2)" class="btn btn-cian btn-sm"><i class="far fa-edit"></i> <?=__('Módosítás')?> </button>
                      </div>
                      <div class="col text-right">
                        <button type="button" ng-click="saveSession()" class="btn btn-warning btn-sm"><?=__('Konfiguráció mentése')?> <i class="fas fa-save"></i></button>
                        <div class="" ng-show="savingsession">
                          <?=__('Konfiguráció mentése folyamatban')?> <i class="fas fa-spinner fa-spin"></i>
                        </div>
                        <div class="saved-config-date" ng-show="savedconfigtime">
                          <strong><?=__('Utoljára mentve')?>:</strong>
                          <div class="datetime">{{savedconfigtime|date:'yyyy. MM. dd. HH:mm'}}</div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="info-next">
                    <div ng-hide="selected_services.length > 0">
                      <?=__('A továbbhaladáshoz válasszon szolgáltatásaink közül.')?>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-5">
                <div class="requester-form">
                  <div class="wrapper">
                    <div class="row">
                      <div class="col-md-12">
                        <input type="text" ng-model="requester.name" value="" class="form-control" placeholder="* <?=__('Az Ön neve')?>" required="required">
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-12">
                        <input type="text" ng-model="requester.company" value="" class="form-control"  placeholder="<?=__('Cégnév')?>">
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-12">
                        <input type="text" ng-model="requester.phone" value="" class="form-control" placeholder="* <?=__('Telefonszám')?>" required="required">
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-12">
                        <input type="email" ng-model="requester.email" value="" class="form-control" email="true"  placeholder="* <?=__('E-mail cím')?>" required="required">
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-12">
                        <input type="text" ng-model="requester.requester_title" maxlength="250" value="" class="form-control" placeholder="<?=__('Projekt / ajánlatkérés rövid elnevezése')?>">
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-12">
                        <textarea style="min-height: 400px;" ng-model="requester.message" placeholder="<?=__('Az ajánlatkérésének üzenete')?>"></textarea>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-12">
                        <label for="file" class="file-label"><i class="fas fa-paperclip"></i> <?=__('Csatolmányok hozzáadása')?></label>
                        <input type='file' name='file[]' id='file' multiple="multiple" ng-file='uploadfiles'>
                      </div>
                    </div>

                    <div class="row">
                      <div class="col-md-12">
                        <div class="">
                          <input type="checkbox" id="check1" ng-model="requester.aszf" required class="ccb"><label for="check1"><?=sprintf(__('Elolvastam és elfogadom az <a target="_blank" href="%s">Általános Szerződési Feltételeket</a>.'),'/aszf')?></label>
                        </div>
                        <div class="">
                          <input type="checkbox" id="check2" ng-model="requester.adatvedelem" required class="ccb"><label for="check2"><?=sprintf(__('Elolvastam és elfogadom az <a target="_blank" href="%s">Adatvédelmi Tájékoztatót</a> és hozzájárulok az adataim kezeléséhez.'),'/adatvedelem')?></label>
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-12 text-right">
                        <div class="alert text-left" ng-class="requestmessageclass" ng-bind-html="requestmessage|unsafe" ng-show="sendingofferrequest"></div>
                        <div class="" ng-show="requestreturn">
                          <div class="" ng-show="!requestreturn.created_user_id">
                            <div class="alert alert-warning text-left">
                              <?=__('Az ajánlatkérés részleteit megtekintheti a profiljában!')?><br><br>
                              <a class="btn btn-sm btn-warning"  href="<?=ADMROOT?>belepes/?email={{requestreturn.email}}&request={{requestreturn.request_hashkey}}"><?=__('Bejelentkezés')?></a>
                            </div>
                          </div>
                          <div class="" ng-show="requestreturn.created_user_id">
                            <div class="alert alert-warning text-left">
                              <?=__('Új profilja elkészült! Az ajánlatkérés részleteit megtekintheti a fiókjában!')?><br><br>
                              <a class="btn btn-sm btn-warning" href="<?=ADMROOT?>belepes/?email={{requestreturn.email}}&request={{requestreturn.request_hashkey}}"><?=__('Bejelentkezés')?></a>
                            </div>
                          </div>
                        </div>
                        <div class="redalert" ng-show="!requester.name || !requester.phone || !requester.email">
                          <?=__('Az ajánlatkérés küldéséhez kérjük a a kötelező (*) adatok megadását!')?>
                        </div>
                        <!--<div ng-hide="!requester.name || !requester.phone || !requester.email">-->
                        <div>
                          <button ng-show="!sendingofferrequest" type="submit" class="btn btn-danger btn-lg" ng-click="sendAjanlatkeres()"><?=__('Ajánlatkérés elküldése')?> <i class="far fa-arrow-alt-circle-right"></i></button>
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

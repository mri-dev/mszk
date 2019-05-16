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
        <div class="step-layout step1" ng-show="(step == 1)">
          <div class="services">
            <div class="service" ng-class="{'picked': isPickedService(service.ID)}" ng-repeat="service in resources.szolgaltatasok">
              <div class="wrapper">
                <div class="title">
                  {{service.neve}}
                </div>
                <div class="image">

                </div>
                <div class="desc">
                  {{service.leiras}}
                </div>
                <div class="more-info"><i class="fas fa-info-circle"></i> <?=__('Bővebb információ')?></div>
                <div class="sel-item"><input id="service_s{{service.ID}}" type="checkbox" name="" class="ccb" value="" ng-click="pickService(service.ID)"> <label for="service_s{{service.ID}}"><?=__('Kiválasztom')?></label></div>
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
                <?=__('A továbbhaladáshoz válasszon szolgáltatásaink közül.')?>
              </div>
            </div>
          </div>
        </div>
        <div class="step-layout step2" ng-show="(step == 2)">
          <div class="services-configurator">
            <div class="row">
              <div class="col-md-7">
                <div class="services-group">
                  <div class="service" ng-repeat="service in resources.szolgaltatasok" ng-hide="selected_services.indexOf(service.ID)===-1">
                    <div class="head">
                      {{service.neve}}
                    </div>
                    <div class="sub-services">
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
                            <div class="head"><?=__('Az Ön igényei')?>:</div>
                            <textarea class="form-control" ng-model="service_desc[subserv.ID]" placeholder="<?=__('Milyen igényei vannak a(z) {{subserv.neve}} szolgáltatással kapcsolatban?')?>"></textarea>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-5">
                jobb
              </div>
            </div>
            <div class="next-btn">
              <div class="" ng-show="selected_services.length > 0" >
                <div class="row justify-content-between align-items-center">
                  <div class="col text-left">
                      <button type="button" ng-click="prevStep()" class="btn btn-default btn-sm"><i class="fas fa-chevron-left"></i> <?=__('vissza: Szolgáltatások')?> </button>
                  </div>
                  <div class="col text-right">
                    <button type="button" ng-click="nextStep()" class="btn btn-primary btn-lg"><?=__('Tovább az összegzéshez')?> <i class="fas fa-chevron-right"></i></button>
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
        <div class="step-layout step3" ng-show="(step == 3)">
          <div class="services-overview">
            <div class="row">
              <div class="col-md-7">
                bal
              </div>
              <div class="col-md-5">
                jobb
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
        <div class="step-layout step4" ng-show="(step == 4)">
          <div class="wrapper">
            <div class="row">
              <div class="col-md-7">

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
                        <input type="text" ng-model="requester.email" value="" class="form-control"  placeholder="* <?=__('E-mail cím')?>" required="required">
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-12">
                        <textarea ng-model="requester.message" placeholder="<?=__('Üzenet')?>"></textarea>
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
                        <input type="submit"class="btn btn-danger btn-lg" value="<?=__('Ajánlatkérés elküldése')?>">
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

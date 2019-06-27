var app = angular.module('Software', ['ngMaterial', 'ngMessages', 'ngCookies']);

app.controller('App', ['$scope', '$sce', '$http', '$mdToast', '$mdDialog', '$location','$cookies', '$cookieStore', '$timeout', function($scope, $sce, $http, $mdToast, $mdDialog, $location, $cookies, $cookieStore, $timeout)
{
  $scope.formready = false;
  $scope.selected_services = [];
  $scope.selected_subservices = [];
  $scope.selected_subservices_items = [];
  $scope.service_desc = [];
  $scope.service_cashall = [];
  $scope.service_cashrow = [];
  $scope.step = 1;
  $scope.walkedstep = 1;
  $scope.max_step = 4;
  $scope.resources = {};
  $scope.savingsession = false;
  $scope.savedconfig = false;
  $scope.savedconfigtime = false;
  $scope.sendingofferrequest = false;
  $scope.requester = {};
  $scope.title = [
    {
      main: 'Szolgáltatásaink',
      sub: 'Válasszon szolgáltatásaink közül'
    },
    {
      main: 'Szolgáltatások testreszabása',
      sub: 'Konfigurálja a kiválasztott szolgáltatásokat'
    },
    {
      main: 'Összegzés',
      sub: 'Ellenőrizze le a konfigurációt'
    },
    {
      main: 'Küldés',
      sub: 'Küldje el egyedileg öszzeállított ajánlatkérését'
    }
  ];
  $scope.steps = [
    'Kategória',
    'Testreszabás',
    'Összegzés',
    'Küldés'
  ];

  $scope.init = function()
  {

  }

  $scope.pickServiceSub = function( id ) {
    if ($scope.selected_subservices.indexOf(id) === -1) {
      $scope.selected_subservices.push(id);
    } else {
      $scope.selected_subservices.splice($scope.selected_subservices.indexOf(id), 1);
    }
  }

  $scope.pickServiceSubItem = function( id ) {
    if ($scope.selected_subservices_items.indexOf(id) === -1) {
      $scope.selected_subservices_items.push(id);
    } else {
      $scope.selected_subservices_items.splice($scope.selected_subservices_items.indexOf(id), 1);
    }
  }

  $scope.pickService = function( id ) {
    if ($scope.selected_services.indexOf(id) === -1) {
      $scope.selected_services.push(id);
    } else {
      $scope.selected_services.splice($scope.selected_services.indexOf(id), 1);
    }
  }

  $scope.isPickedService = function( id ) {
    if ($scope.selected_services.indexOf(id) !== -1) {
      return true
    } else {
      return false;
    }
  }

  $scope.isPickedSubServiceItem = function( id ) {
    if ($scope.selected_subservices_items.indexOf(id) !== -1) {
      return true
    } else {
      return false;
    }
  }

  $scope.isPickedSubService = function( id ) {
    if ($scope.selected_subservices.indexOf(id) !== -1) {
      return true
    } else {
      return false;
    }
  }

  $scope.saveSession = function()
  {
    if ( !$scope.savingsession )
    {
      var session = {};
      $scope.savingsession = true;
      session.selected_services = $scope.selected_services;
      session.selected_subservices = $scope.selected_subservices;
      session.selected_subservices_items  = $scope.selected_subservices_items;
      session.service_desc = $scope.service_desc;
      session.service_cashall = $scope.service_cashall;
      session.service_cashrow = $scope.service_cashrow;
      session.walkedstep = $scope.walkedstep;
      session.step = 4;

      var date = new Date();
      var expires = new Date(date.setDate(date.getDate() + 30));
      $cookies.putObject( 'config', session, {'expires': expires} );
      $cookies.put( 'config_time', new Date(), {'expires': expires});
      $scope.loadSavedConfig();
      $scope.savingsession = false;
    }
  }

  $scope.clearSession = function() {
    $cookies.remove('config');
    $cookies.remove('config_time');
    $scope.loadSavedConfig();
  }

  $scope.loadSavedConfig = function( callback ) {
    var config  = $cookies.getObject( 'config' );
    var time  = $cookies.get( 'config_time' );
    if (typeof config === 'undefined') {
      $scope.savedconfig = false;
      $scope.savedconfigtime = false;
    } else {
      $scope.savedconfig = config;
      $scope.savedconfigtime = new Date(time);
    }

    if (typeof callback !== 'undefined') {
      callback();
    }
  }

  $scope.recalcCashAll = function(subservid, subservitemid ) {
    var totalcash = 0;
    angular.forEach($scope.service_cashrow[subservid], function(cash,sid){
      totalcash += cash;
    });

    $scope.service_cashall[subservid] = totalcash;
  }

  $scope.prepareAjanlatkeres = function()
  {
    $scope.loadAjanlatkeresResources(function(){
      $scope.loadSavedConfig(function(){
        if ($scope.savedconfig) {
          $scope.selected_services = $scope.savedconfig.selected_services;
          $scope.selected_subservices = $scope.savedconfig.selected_subservices;
          $scope.selected_subservices_items  = $scope.savedconfig.selected_subservices_items;
          $scope.service_desc = $scope.savedconfig.service_desc;
          $scope.walkedstep = $scope.savedconfig.walkedstep;
          $scope.service_cashall = $scope.savedconfig.service_cashall;
          $scope.service_cashrow = $scope.savedconfig.service_cashrow;
          $scope.step = $scope.savedconfig.step;
        }
      });
    });
  }

  $scope.sendAjanlatkeres = function( callback ) {
    $scope.requestreturn = false;
    if (!$scope.sendingofferrequest)
    {
      $scope.saveSession();
      $scope.sendingofferrequest = true;
      $scope.requestmessageclass = 'requestmessage alert-warning'
      $scope.requestmessage = 'Ajánlatkérés küldése folyamatban <i class="fas fa-spinner fa-spin"></i>';
      $http({
        method: 'POST',
        url: '/ajax/post',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        data: $.param({
          type: "Ajanlatkeres",
          mode: 'send',
          requester: $scope.requester,
          config: {
            selected_services: $scope.savedconfig.selected_services,
            selected_subservices: $scope.savedconfig.selected_subservices,
            selected_subservices_items : $scope.savedconfig.selected_subservices_items,
            selected_cashall : $scope.savedconfig.service_cashall,
            selected_cashrow : $scope.savedconfig.service_cashrow,
            service_desc: $scope.savedconfig.service_desc
          }
        })
      }).success(function(r){
        //console.log(r);
        $scope.requestreturn = r;
        if (r.success == 1) {
          $scope.requestmessageclass = 'requestmessage alert-success'
          $scope.requestmessage = r.msg;
          $scope.clearSession();
        } else {
          $scope.requestmessageclass = 'requestmessage alert-danger'
          $scope.requestmessage = r.msg;
        }
        if (typeof callback !== 'undefined') {
          callback();
        }
      });
    }
  }

  $scope.loadAjanlatkeresResources = function( callback )
  {
    $http({
      method: 'POST',
      url: '/ajax/post',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      data: $.param({
        type: "Ajanlatkeres",
        mode: 'getResources'
      })
    }).success(function(r){
      console.log(r);
      if (r.success == 1) {
        $scope.resources = r.data;
      }
      if (typeof callback !== 'undefined') {
        callback();
      }
    });
  }

  $scope.getProgressPercent = function() {
    var p = 0;

    p = 100 / 3 * ($scope.walkedstep-1);

    return p;
  }

  $scope.prevStep = function() {
    step = $scope.step;
    if (step <= 1) {
      step = 1;
    } else {
      step = step -  1;
    }

    $scope.step = step;
  }

  $scope.nextStep = function() {
    switch ( $scope.step ) {
      case 1:
        console.log('step 1');
      break;
      case 2:

      break;
      case 3:

      break;
      case 4:

      break;
    }

    $scope.step = $scope.step + 1;
    if ($scope.walkedstep < $scope.step) {
      $scope.walkedstep = $scope.walkedstep + 1;
    }
  }

  $scope.goToStep = function( step ) {
    if (step >= $scope.max_step) {
      step = $scope.max_step;
    }

    if (step > $scope.walkedstep) {
      step = $scope.walkedstep;
    }

    $scope.step  = step;
  }

  $scope.getNumber = function(num) {
      return new Array(num);
  }

  $scope.toast = function( text, mode, delay ){
    mode = (typeof mode === 'undefined') ? 'simple' : mode;
    delay = (typeof delay === 'undefined') ? 5000 : delay;

    if (typeof text !== 'undefined') {
      $mdToast.show(
        $mdToast.simple()
        .textContent(text)
        .position('top')
        .toastClass('alert-toast mode-'+mode)
        .hideDelay(delay)
      );
    }
  }

}]);


app.filter('unsafe', function($sce){ return $sce.trustAsHtml; });

$(function(){
  $.each($('.autocorrect-height-by-width'), function(i,e){
    var ew = $(e).width();
    var ap = $(e).data('image-ratio');
    var respunder = $(e).data('image-under');
  	var pw = $(window).width();
    ap = (typeof ap !== 'undefined') ? ap : '4:3';
  	console.log(ap);
    var aps = ap.split(":");
    var th = ew / parseInt(aps[0])  * parseInt(aps[1]);

  	if (respunder) {
  		if (pw < respunder) {
  			$(e).css({
          height: th
        });
  		}
  	} else{
  		$(e).css({
        height: th
      });
  	}

  });
});

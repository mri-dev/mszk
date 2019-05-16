var app = angular.module('Software', ['ngMaterial', 'ngMessages', 'ngCookies']);

app.controller('App', ['$scope', '$sce', '$http', '$mdToast', '$mdDialog', '$location','$cookies', '$cookieStore', '$timeout', function($scope, $sce, $http, $mdToast, $mdDialog, $location, $cookies, $cookieStore, $timeout)
{
  $scope.formready = false;
  $scope.selected_services = [];
  $scope.selected_subservices = [];
  $scope.selected_subservices_items = [];
  $scope.service_desc = [];
  $scope.step = 1;
  $scope.walkedstep = 1;
  $scope.max_step = 4;
  $scope.resources = {};
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

  $scope.prepareAjanlatkeres = function()
  {
    $scope.loadAjanlatkeresResources(function(){
      console.log('Prepared');
    });
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

});

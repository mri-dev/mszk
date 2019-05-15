var app = angular.module('Software', ['ngMaterial', 'ngMessages', 'ngCookies']);

app.controller('App', ['$scope', '$sce', '$http', '$mdToast', '$mdDialog', '$location','$cookies', '$cookieStore', '$timeout', function($scope, $sce, $http, $mdToast, $mdDialog, $location, $cookies, $cookieStore, $timeout)
{
  $scope.step = 4;
  $scope.max_step = 4;
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
      sub: 'Küldje el egyedi ajánlatkérését'
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

  $scope.getProgressPercent = function() {
    var p = 0;

    p = 100 / 3 * ($scope.step-1);

    return p;
  }

  $scope.goToStep = function( step ) {
    if (step >= $scope.max_step) {
      step = $scope.max_step;
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

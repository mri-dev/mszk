var a = angular.module('App', ['ngMaterial', 'ngSanitize']);

a.controller("ctrl", ['$scope', '$http', '$mdToast', function($scope, $http, $mdToast)
{
	$scope.init = function( )
	{
	}

	$scope.saveOwnStyle = function() {
		$http({
			method: 'POST',
			url: '/ajax/post',
			headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
			data: $.param({
				type: "Moza",
				mode: 'saveStyleConfig',
				id: $scope.loadid,
				motivum: $scope.motivum,
				name: $scope.ownmotifname
			})
		}).success(function(r){

		});
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

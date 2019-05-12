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

$(function(){
	$('*[data-list-searcher]').keyup(function(ev){
		ev.preventDefault();
		var src = $(this).val();
		var list = $(this).data('list-searcher');
		var target_table = $('table#'+list);
		var lines = target_table.find('tbody > tr[data-itemsrc]');

		jQuery.each(lines, function(i,e){
			var t = $(e);
			var ts = t.data('itemsrc');
			var finded = -1;

			if (src != '') {
				finded = ts.indexOf(src);
				if (finded == -1) {
					t.hide();
				} else {
					t.show();
				}
			} else {
				t.show();
			}
		});
	})
});

var a = angular.module('App', ['ngMaterial', 'ngCookies', 'ngSanitize', 'nl2br']);

a.controller("AlertsWatcher", ['$scope', '$http', '$timeout', function($scope, $http, $timeout)
{
	$scope.syncdelay = 2000;
	$scope.unreaded = 0;

	$scope.init = function()
	{
		$scope.watchAlerts();
	}

	$scope.watchAlerts = function(){
		$http({
			method: 'POST',
			url: '/ajax/post',
			headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
			data: $.param({
				type: 'Alerts',
				mode: 'watch'
			})
		}).success(function(r){
			$scope.unreaded = r.data.unreaded;
			$timeout.cancel($scope.syncMsgTimeout);
			$scope.syncMsgTimeout = $timeout(function() {
				$scope.watchAlerts();
			}, $scope.syncdelay);
		});
	}
}]);

a.controller("ProjectControl", ['$scope', '$http', '$mdToast', '$mdDialog', '$sce', '$filter', function($scope, $http, $mdToast, $mdDialog, $sce, $filter)
{
	$scope.partner = {
		neve: 'Molnár István'
	};
	$scope.messanger = {
		text: ''
	};
	$scope.project = {};
	$scope.newmsg_send_progress = false;

	$scope.init = function( conf )
	{
		if (typeof conf !== 'undefined') {
			$scope.loadconfig = conf;
		} else {
			$scope.loadconfig = {};
		}

		$scope.loadEverything();
	}

	$scope.loadEverything = function()
	{
		$scope.loadLists(function( data )
		{
			// Watches
			$scope.$watch('project.project_start_Date', function(newValues, oldValues, scope){
				if (typeof newValues !== 'undefined') {
					$scope.project.project_start = $filter('date')(newValues, "yyyy-MM-dd");
				}
			});
			$scope.$watch('project.project_start', function(newValues, oldValues, scope){
				$scope.project.project_start_Date = new Date(newValues);
			});
			$scope.$watch('project.project_end_Date', function(newValues, oldValues, scope){
				if (typeof newValues !== 'undefined') {
					$scope.project.project_end = $filter('date')(newValues, "yyyy-MM-dd");
				}
			});
			$scope.$watch('project.project_end', function(newValues, oldValues, scope){
				$scope.project.project_end_Date = new Date(newValues);
			});
		});
	}

	$scope.sendQuickMessage = function( project_hashkey ) {
		if ( !$scope.newmsg_send_progress ) {
			$scope.newmsg_send_progress = true;

			$http({
				method: 'POST',
				url: '/ajax/post',
				headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
				data: $.param({
					type: 'Messanger',
					mode: 'sendMessage',
					session: project_hashkey,
					text: $scope.messanger.text
				})
			}).success(function(r){
				$scope.newmsg_send_progress = false;
				if (r.success == 1) {
					$('#messanger_text').focus();
					$scope.messanger.text='';
					$scope.toast(r.msg, 'success', 5000);
				} else {
					$scope.toast(r.msg, 'alert', 5000);
				}
				console.log(r);
			});
		}
	}

	$scope.loadLists = function( callback )
	{
		var filters = {};

		if (typeof $scope.loadconfig.inprogress !== 'undefined') {
			//filters.inprogress = parseInt($scope.loadconfig.inprogress);
		}

		$http({
			method: 'POST',
			url: '/ajax/post',
			headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
			data: $.param({
				type: "Projects",
				mode: 'get',
				hashkey: $scope.loadconfig.hashkey,
				filter: filters
			})
		}).success(function(r){
			console.log(r);
			if (r.success == 1) {
				$scope.project = r.data;
				$scope.partner = r.data.partner;

				console.log($scope.project);
			}
			if (typeof callback !== 'undefined') {
				callback(r.data);
			}
		});
	}

	$scope.loadMyDocs = function( hashkey, callback ) {
		$http({
			method: 'POST',
			url: '/ajax/post',
			headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
			data: $.param({
				type: "Documents",
				mode: 'getList',
				params: {
					not_in_project: hashkey
				}
			})
		}).success(function(r){
			console.log(r);
			if (typeof callback !== 'undefined') {
				callback(r.data);
			}
		});
	}

	$scope.projectDocsAdder = function( hashkey )
	{
		$scope.loadMyDocs(hashkey, function(docs){
			var confirm = $mdDialog.confirm({
				controller: ProjectDOCSAdderController,
				templateUrl: '/ajax/modal/projectdocsadder',
				parent: angular.element(document.body),
				scope: $scope,
				preserveScope:true,
				locals: {
	        config: $scope.loadconfig,
					project: $scope.project,
					docs: docs,
					hashkey: hashkey
				}
			});

			$mdDialog.show(confirm)
			.then(function() {
	      $scope.status = 'You decided to get rid of your debt.';
	    }, function() {
	      $scope.status = 'You decided to keep your debt.';
	    });
		});


		function ProjectDOCSAdderController( $scope, $mdDialog, config, project, docs, hashkey) {
      $scope.saving = false;
      $scope.loadconfig = config;
      $scope.project = project;
      $scope.docs = docs;
      $scope.hashkey = hashkey;
			$scope.closeDialog = function(){
				$mdDialog.hide();
			}

      $scope.addDocToroject = function( type ){
        if (!$scope.saving) {
          $scope.saving = true;
					console.log($scope);

          $http({
      			method: 'POST',
      			url: '/ajax/post',
      			headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      			data: $.param({
      				type: "Projects",
              mode: 'addDocument',
							hashkey: $scope.hashkey,
							doc: $scope.project.selected_doc_to_add
      			})
      		}).success(function(r){
            console.log(r);
      			$scope.saving = false;
						if (r.success == 1) {
							$scope.toast(r.msg, 'success', 10000);
							$scope.closeDialog();
						} else {
							$scope.toast(r.msg, 'alert', 10000);
						}
	        });
      	}
			}
		}
	}

	$scope.projectEditor = function()
	{
		var confirm = $mdDialog.confirm({
			controller: ProjectEditorDialogController,
			templateUrl: '/ajax/modal/usereditproject',
			parent: angular.element(document.body),
			scope: $scope,
			preserveScope:true,
			locals: {
        config: $scope.loadconfig,
				project: $scope.project
			}
		});

		$mdDialog.show(confirm)
		.then(function() {
      $scope.status = 'You decided to get rid of your debt.';
    }, function() {
      $scope.status = 'You decided to keep your debt.';
    });

		function ProjectEditorDialogController( $scope, $mdDialog, config, project) {
      $scope.saving = false;
      $scope.loadconfig = config;
      $scope.project = project;

			$scope.closeDialog = function(){
				$mdDialog.hide();
			}

      $scope.saveProject = function( type ){
        if (!$scope.saving) {
          $scope.saving = true;

          $http({
      			method: 'POST',
      			url: '/ajax/post',
      			headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      			data: $.param({
      				type: "Projects",
              mode: 'saveProject',
							project: $scope.project
      			})
      		}).success(function(r){
            console.log(r);
      			$scope.saving = false;
						if (r.success == 1) {
							$scope.toast(r.msg, 'success', 10000);
							$scope.closeDialog();
						} else {
							$scope.toast(r.msg, 'alert', 10000);
						}
	        });
      	}
			}
		}
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

a.controller("OfferControl", ['$scope', '$http', '$mdToast', '$sce', '$filter', '$cookies', function($scope, $http, $mdToast, $sce, $filter, $cookies)
{
	$scope.quicksearch = '';
	$scope.relation = 'to';
	$scope.loadconfig = {};
	$scope.offer = {};
	$scope.requests = {};
	$scope.request = false;
	$scope.readrequest = 0;
	$scope.showoffersend = false;
	$scope.sendingoffer = false;
	$scope.sendingofferaccept = false;
	$scope.acceptoffererror= false;
	$scope.acceptofferdata = {
		project: '',
		password: ''
	};
	$scope.badges = {
		'in': 0,
		'out': 0
	}
	$scope.init = function( conf )
	{
		if (typeof conf !== 'undefined') {
			$scope.loadconfig = conf;
		} else {
			$scope.loadconfig = {};
		}

		if ( typeof $scope.loadconfig.relation !== 'undefined' ) {
			$scope.relation = $scope.loadconfig.relation;
		}

		$scope.loadEverything();
	}

	$scope.loadEverything = function() {
		$scope.loadLists(function( data )
		{
			/*
			var storerelation = $cookies.get('relation');
			if (typeof storerelation !== 'undefined') {
				$scope.relation = storerelation;
			}
			*/
		});
	}

	$scope.showOfferSending = function( f ) {
		$scope.showoffersend = f;
	}

	$scope.changeRelation = function( what )
	{
		$scope.request = false;
		$scope.readrequest = 0;
		if (typeof what === 'undefined') {
			$scope.relation = ($scope.relation == 'to') ? 'from' : 'to';
		} else {
			$scope.relation = what;
		}

		$cookies.put('relation', $scope.relation);
	}

	$scope.pickRequest = function( request )
	{
		$scope.readrequest = request.ID;
		$scope.request = request;
		$scope.showOfferSending(false);

		if ($scope.relation == 'from') {
			$scope.acceptofferdata.project = angular.copy(request.user_requester_title);
		}

		console.log(request);
	}

	$scope.acceptOffer = function()
	{
		if ( !$scope.sendingofferaccept )
		{
			$scope.sendingofferaccept = true;
			$scope.acceptoffererror= false;

			$http({
				method: 'POST',
				url: '/ajax/post',
				headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
				data: $.param({
					type: "RequestOffers",
					mode: 'acceptOffer',
					user_id: $scope.request.user_id,
					request: $scope.request.ID,
					offer: $scope.request.admin_offer.ID,
					project: $scope.acceptofferdata
				})
			}).success(function(r){
				$scope.sendingofferaccept = false;
				console.log(r);
				if (r.success == 1) {
					$scope.acceptofferdata = {};
					$scope.toast( r.msg, 'success', 5000);
					$scope.loadLists(function( data ){
						$scope.request.request_accepted = 1;
						$scope.reloadRequestObject(data, $scope.request.ID );
					});
				} else {
					$scope.acceptoffererror= r.msg;
				}
			});

		}
	}

	$scope.sendOffer = function()
	{
		if ( !$scope.sendingoffer )
		{
			$scope.sendingoffer = true;
			var offer = {};
			angular.copy($scope.offer, offer);
			var project_date = $filter('date')($scope.offer.project_start_at, 'yyyy-MM-dd');
			offer.project_start_at = project_date;

			$http({
				method: 'POST',
				url: '/ajax/post',
				headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
				data: $.param({
					type: "RequestOffers",
					mode: 'sendOffer',
					offer: offer,
					request: $scope.request
				})
			}).success(function(r){
				$scope.sendingoffer = false;
				console.log(r);
				if (r.success == 1) {
					$scope.showoffersend = false;
					$scope.loadLists(function( data ){
						$scope.request.recepient_accepted = 1;
						$scope.reloadRequestObject(data[$scope.relation], $scope.request.ID );
					});
				} else {

				}
			});
		}
	}

	$scope.loadLists = function( callback ) {
		var filters = {};
		if (typeof $scope.loadconfig.inprogress !== 'undefined') {
			filters.inprogress = parseInt($scope.loadconfig.inprogress);
		}
		if (typeof $scope.loadconfig.accepted !== 'undefined') {
			filters.accepted = parseInt($scope.loadconfig.accepted);
		}
		if (typeof $scope.loadconfig.offeraccepted !== 'undefined') {
			filters.offeraccepted = parseInt($scope.loadconfig.offeraccepted);
		}
		if (typeof $scope.loadconfig.progressed !== 'undefined') {
			filters.progressed = parseInt($scope.loadconfig.progressed);
		}
		$http({
			method: 'POST',
			url: '/ajax/post',
			headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
			data: $.param({
				type: "RequestOffers",
				relation: $scope.relation,
				mode: 'List',
				filter: filters
			})
		}).success(function(r){
			console.log(r);

			if (r.data && r.data.length != 0) {
				$scope.requests = r.data;
			}
			if (typeof callback !== 'undefined') {
				callback(r.data);
			}
		});
	}

	$scope.runRequestAction = function(request_id, mode )
	{
		$http({
			method: 'POST',
			url: '/ajax/post',
			headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
			data: $.param({
				type: "RequestOffers",
				mode: 'requestActions',
				what: mode,
				request: request_id,
			})
		}).success(function(r){
			if (r.success == 0) {
				$scope.toast( r.msg, 'alert', 5000);
			} else if(r.success == 1){
				$scope.toast( r.msg, 'success', 5000);
				$scope.loadLists(function( data ){
					$scope.reloadRequestObject(data, request_id );
				});
			}
		});
	}

	$scope.reloadRequestObject = function( data, id ) {
		if (data) {
			angular.forEach(data, function(e,i){
				if(e.ID == id) {
					$scope.pickRequest( e );
				}
			});
		}
	}

	$scope.quickFilterSearch = function( row )
	{
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

a.controller("RequestControl", ['$scope', '$http', '$mdToast', '$sce', '$window', '$mdDialog', function($scope, $http, $mdToast, $sce, $window, $mdDialog)
{
	$scope.quicksearch = '';
	$scope.requests = [];
	$scope.servicerAccounts = [];
	$scope.request = false;
	$scope.request_offerouts = {};
	$scope.readrequest = 0;
	$scope.loadconfig = {};
	$scope.init = function( conf )
	{
		$scope.loadconfig = conf;
		$scope.loadEverything();
	}
	$scope.servicesrequestprogress = false;

	$scope.loadEverything = function() {
		$scope.loadLists(function( data ){

		});
	}

	$scope.saac = {
		searchText: '',
		isDisabled: false,
		noCache: true,
		querySearch: function(query){

			var results = query ? $scope.servicerAccounts.filter(createFilterFor(query)) : $scope.servicerAccounts.filter(checkPickedUsers());
			return results;
		},
		selectedItem: null,
		selectedItemChange: function(request, user) {
			if (typeof request.passed_user_offer_id === 'undefined') {
				request.passed_user_offer_id = [];
			}

			if (user && request.passed_user_offer_id.indexOf(user) === -1) {
				request.passed_user_offer_id.push(user);
			}

			this.selectedItem = null;
		}
	};

	function checkPickedUsers() {
		var current_picked = [];
		if ($scope.request && typeof $scope.request.passed_user_offer_id !== 'undefined') {
			current_picked = $scope.request.passed_user_offer_id;
		}
		return function filterFn(item) {
      return (current_picked.indexOf(item.ID) === -1);
    };
	}

	function createFilterFor( query ) {
    var lowercaseQuery = query.toLowerCase();

    return function filterFn(item) {
      return (
				(item.nev_value.indexOf(lowercaseQuery) !== -1) ||
				(item.company_value && item.company_value.indexOf(lowercaseQuery) !== -1) ||
				(item.email_value && item.email_value.indexOf(lowercaseQuery) !== -1)
			);
    };
  }

	$scope.acceptAdminServiceOffer = function(request, offer)
	{
		console.log(request);
		console.log(offer);

		var confirm = $mdDialog.confirm({
			controller: ProjectCreatorOfferDialogController,
			templateUrl: '/ajax/modal/offers_to_project',
			parent: angular.element(document.body),
			scope: $scope,
			preserveScope:true,
			locals: {
				request: request,
				offer: offer
			}
		});

		$mdDialog.show(confirm)
		.then(function() {
			$scope.status = 'You decided to get rid of your debt.';
		}, function() {
			$scope.status = 'You decided to keep your debt.';
		});

		function ProjectCreatorOfferDialogController( $scope, $mdDialog, request, offer)
		{
			$scope.saving = false;
			$scope.request = request;
			$scope.newoffer = angular.copy(offer);
			//$scope.newoffer.price = parseFloat($scope.newoffer.price);
			//$scope.newoffer.project_start_at = new Date($scope.newoffer.project_start_at);

			$scope.closeDialog = function(){
				$mdDialog.hide();
			}

			$scope.adminCreatProjectByOffer = function() {
				if (!$scope.saving) {
					console.log($scope.request);
					console.log($scope.newoffer);

					/* */
					$scope.saving = true;
					$http({
						method: 'POST',
						url: '/ajax/post',
						headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
						data: $.param({
							type: "Projects",
							mode: 'adminOfferCreator',
							request_id: $scope.request.ID,
							offer: $scope.newoffer
						})
					}).success(function(r){
						console.log(r);
						$scope.saving = false;
						if (r.success == 1) {
							$scope.toast(r.msg, 'success', 10000);
							$scope.closeDialog();
							//$window.location.href = '/projektek/projekt/';
						} else {
							$scope.toast(r.msg, 'alert', 10000);
						}
					});
					/* */
				}
			}
		}
	}

	$scope.previewOfferToUser = function( request, offer ) {
		var confirm = $mdDialog.confirm({
			controller: OfferToUseDialogController,
			templateUrl: '/ajax/modal/offer_to_user',
			parent: angular.element(document.body),
			scope: $scope,
			preserveScope:true,
			locals: {
				request: request,
				offer: offer
			}
		});

		$mdDialog.show(confirm)
		.then(function() {
      $scope.status = 'You decided to get rid of your debt.';
    }, function() {
      $scope.status = 'You decided to keep your debt.';
    });

		function OfferToUseDialogController( $scope, $mdDialog, request, offer)
		{
      $scope.saving = false;
      $scope.request = request;
      $scope.newoffer = angular.copy(offer);
			$scope.newoffer.price = parseFloat($scope.newoffer.price);
			$scope.newoffer.project_start_at = new Date($scope.newoffer.project_start_at);

			$scope.closeDialog = function(){
				$mdDialog.hide();
			}

      $scope.sendOfferToUserByServiceOffer = function() {
        if (!$scope.saving) {
					console.log($scope.request);
					console.log($scope.newoffer);

					/* */
          $scope.saving = true;
          $http({
      			method: 'POST',
      			url: '/ajax/post',
      			headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      			data: $.param({
      				type: "RequestOffers",
              mode: 'sendAdminOffer',
							request_id: $scope.request.ID,
							offer: $scope.newoffer
      			})
      		}).success(function(r){
						console.log(r);
      			$scope.saving = false;
						if (r.success == 1) {
							$scope.toast(r.msg, 'success', 10000);
							$scope.closeDialog();
						} else {
							$scope.toast(r.msg, 'alert', 10000);
						}
	        });
					/* */
      	}
			}
		}
	}

	$scope.toggleDetails = function( prefix, id, ev) {
		ev.preventDefault();
		$('.offer .details').slideUp(100);
		$('#'+prefix+id).slideToggle(200);

		$http({
			method: 'POST',
			url: '/ajax/post',
			headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
			data: $.param({
				type: "RequestOffers",
				mode: 'setWatched',
				offerid: id
			})
		}).success(function(r){ });
	}

	$scope.pickRequest = function( request ) {
		$scope.readrequest = request.ID;
		$scope.request = request;

		if (request.offerout == 1) {
			if (request.offerouts.user_ids && request.offerouts.user_ids.length != 0) {
				$scope.request.passed_user_offer_id = request.offerouts.user_ids+"";
			}
		}
		console.log($scope.request);
	}

	$scope.checkOfferOuts = function( request_id, user, request ) {
		var configval = false;

		if ( request && request.item && request.service && request.subservice ) {
			configval = request.service.ID+'_'+request.subservice.ID+'_'+request.item.ID
		}

		if ( !configval ) {
			return false;
		}

		var check = -1;

		if ( $scope.request.offerouts && $scope.request.offerouts.configval_users && typeof $scope.request.offerouts.configval_users[ configval ] !== 'undefined') {
			check = $scope.request.offerouts.configval_users[ configval ].indexOf( user );
		}

		return (check === -1) ? false : true;
	}

	$scope.loadLists = function( callback ) {
		$scope.loadServicerAccounts(function( servicers )
		{
			$scope.servicerAccounts = servicers;

			$scope.servicerAccounts.map(function (user) {
        user.nev_value = user.nev.toLowerCase();
				user.email_value = user.email.toLowerCase();

				if (user.total_data.data.company_name) {
					user.company_value = user.total_data.data.company_name.toLowerCase();
				}

				return user;
      });

			$http({
				method: 'POST',
				url: '/ajax/post',
				headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
				data: $.param({
					type: "Requests",
					mode: 'List',
					filter: {
						offerout: ($scope.loadconfig && $scope.loadconfig.offerout) ? $scope.loadconfig.offerout : 0,
						allpositive: ($scope.loadconfig && $scope.loadconfig.allpositive) ? $scope.loadconfig.allpositive : 0,
						letrejott: ($scope.loadconfig && $scope.loadconfig.letrejott) ? $scope.loadconfig.letrejott : 0,
						loadpossibleservices: ($scope.loadconfig && $scope.loadconfig.loadpossibleservices) ? 1: 0,
						bindIDToList: 0
					}
				})
			}).success(function(r){
				console.log(r);
				if (r.data && r.data.length != 0) {
					$scope.requests = r.data;

					console.log($scope.loadconfig);
					if ($scope.loadconfig && typeof $scope.loadconfig.preselected !== 'undefined' && $scope.loadconfig.preselected != '') {
						angular.forEach($scope.requests, function(e,i){
							if ( e.hashkey == $scope.loadconfig.preselected ) {
								$scope.pickRequest( e );
							}
						});
					}
				}
				if (typeof callback !== 'undefined') {
					callback(r.data);
				}
			});
		});
	}

	$scope.loadServicerAccounts = function( callback ) {
		$http({
			method: 'POST',
			url: '/ajax/post',
			headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
			data: $.param({
				type: "Requests",
				mode: 'ServicerAccounts',
				filter: {}
			})
		}).success(function(r){
			console.log(r);
			callback(r.data);
		});
	}

	$scope.runRequestAction = function(request_id, mode )
	{
		$http({
			method: 'POST',
			url: '/ajax/post',
			headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
			data: $.param({
				type: "Requests",
				mode: 'requestActions',
				what: mode,
				request: request_id,
			})
		}).success(function(r){
			if (r.success == 0) {
				$scope.toast( r.msg, 'alert', 5000);
			} else if(r.success == 1){
				$scope.toast( r.msg, 'success', 5000);
				$scope.loadLists(function( data ){
					$scope.reloadRequestObject(data, request_id );
				});
			}
		});
	}

	$scope.reloadRequestObject = function( data, id ) {
		if (data) {
			angular.forEach(data, function(e,i){
				if(e.ID == id) {
					$scope.pickRequest( e );
				}
			});
		}
	}

	$scope.sendServicesRequest = function()
	{
		$scope.servicesrequestprogress = true;

		$http({
			method: 'POST',
			url: '/ajax/post',
			headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
			data: $.param({
				type: "Requests",
				mode: 'sendServiceRequest',
				servicesus: $scope.request.passed_user_offer_id,
				request: $scope.request.hashkey,
			})
		}).success(function(r){
			console.log(r);
			if (r.success == 1) {
				//$window.location.reload();
			}
			$scope.servicesrequestprogress = false;
		});
	}

	$scope.quickFilterSearch = function( row )
	{
		return !!(
			(
				row.name.indexOf($scope.quicksearch || '') !== -1 ||
				row.phone.indexOf($scope.quicksearch || '') !== -1 ||
				row.hashkey.indexOf($scope.quicksearch || '') !== -1 ||
				(row.company && row.company.indexOf($scope.quicksearch || '') !== -1) ||
				row.email.indexOf($scope.quicksearch || '') !== -1)
		);
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

a.controller( "MessagesList", ['$scope', '$http', '$timeout', '$mdToast', '$mdDialog', function($scope, $http, $timeout, $mdToast, $mdDialog)
{
  $scope.is_msg = false;
  $scope.unreaded_messages = {
    inbox: 0,
    outbox: 0
  };
	$scope.unreaded = 0;
  $scope.data_loaded = false;
  $scope.messages = {};
	$scope.sessions = {};
	$scope.current_session = '';
  $scope.result = {};
  $scope.newnoticemsg = {};
  $scope.msgtgl = {};
  $scope.messanger = {text:''};
  $scope.newmsg_left_length = 2000;
  $scope.newmsg = '';
  $scope.newmsg_focused = false;
  $scope.newmsg_send_progress = false;
  $scope.newmsgerrmsg=false;
  $scope.syncMsgTimeout = null;
  $scope.syncCount = 0;
	$scope.syncdelay = 1000;

  // Init messanger
  $scope.init = function(group, is_msg, uid, session){
    $scope.is_msg = is_msg;

		if (typeof session !== 'undefined' && session != '') {
			$scope.current_session = session;
		}

    $scope.loadMessages(group);
  }

	$scope.changeSession = function( sessionid )
	{
		$scope.current_session = sessionid;
		$scope.syncMessages();
	}

  $scope.archiveMessageSession = function(session, admin){
    if (admin) {
      $scope.saveMsgSessionData(session, 'archived_by_admin', 1);
    } else {
      $scope.saveMsgSessionData(session, 'archived_by_user', 1);
    }
  }

  $scope.saveMsgSessionData = function(session, record, value){
    $scope.newnoticemsg[session] = false;

    $http({
      method: 'POST',
      url: '/ajax/data',
      params: {
        type: 'messanger_messagesession_edit',
        session: session,
        what: record,
        value: value
      }
    }).then(function successCallback(response) {
      var d = response.data;

      if (d.success) {
        $scope.msgtgl[session] = false;
      } else {
        $scope.newnoticemsg[d.session] = d.msg;
      }
    }, function errorCallback(response) {});
	}

  $scope.syncMessages = function(){
    $scope.loadMessages();
  }

	$scope.sendMessage = function()
	{
		if ( !$scope.newmsg_send_progress ) {
			$scope.newmsg_send_progress = true;

			$http({
				method: 'POST',
				url: '/ajax/post',
				headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
				data: $.param({
					type: 'Messanger',
					mode: 'sendMessage',
					session: $scope.current_session,
					text: $scope.messanger.text
				})
			}).success(function(r){
				$scope.newmsg_send_progress = false;
				if (r.success == 1) {
					$('#messanger_text').focus();
					$scope.messanger.text='';
					$scope.syncMessages();
				} else {
					$scope.toast(r.msg, 'alert', 5000);
				}
			});
		}
	}

  $scope.loadMessages = function(type){
    // Üzenetek betöltése
		$http({
			method: 'POST',
			url: '/ajax/post',
			headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
			data: $.param({
				type: 'Messanger',
        mode: 'messanger_messages',
        by: type,
				session: $scope.current_session
			})
		}).success(function(r){
			var d = r.data;
			console.log(r);
			$scope.result = d;
			$scope.unreaded_messages = d.unreaded;

			if (typeof d.sessions !== 'undefined' ) {
				$scope.sessions = d.sessions;
			}

			if (typeof d.messages !== 'undefined' ) {
				$scope.messages = d.messages;
			} else {
				$scope.messages = {};
			}

			$scope.unreaded = d.unreaded;
			$scope.data_loaded = true;

			if ($scope.syncCount <= 1000) {
				$timeout.cancel($scope.syncMsgTimeout);
				$scope.syncMsgTimeout = $timeout(function() {
					$scope.syncCount++;
					$scope.syncMessages();
				}, $scope.syncdelay);
			}
		});
  }

	// Comment editor
	$scope.openCommentEditor = function()
	{
		var confirm = $mdDialog.confirm({
			controller: MessangerCommentDialogController,
			templateUrl: '/ajax/modal/messanger_comment_edit',
			parent: angular.element(document.body),
			scope: $scope,
			preserveScope:true,
			locals: {
				message: $scope.sessions[$scope.current_session]
			}
		});

		$mdDialog.show(confirm)
		.then(function() {
      $scope.status = 'You decided to get rid of your debt.';
    }, function() {
      $scope.status = 'You decided to keep your debt.';
    });

		function MessangerCommentDialogController( $scope, $mdDialog, message)
		{
      $scope.saving = false;
      $scope.message = message;

			$scope.closeDialog = function(){
				$mdDialog.hide();
			}

      $scope.editComment = function() {
        if (!$scope.saving) {
          $scope.saving = true;
          $http({
      			method: 'POST',
      			url: '/ajax/post',
      			headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      			data: $.param({
      				type: "Messanger",
              mode: 'editComment',
							message: $scope.message
      			})
      		}).success(function(r){
						console.log(r);
      			$scope.saving = false;
						if (r.success == 1) {
							$scope.toast(r.msg, 'success', 10000);
							$scope.closeDialog();
						} else {
							$scope.toast(r.msg, 'alert', 10000);
						}
	        });
      	}
			}
		}
	}

	// Archive editor
	$scope.openArchiver = function()
	{
		var confirm = $mdDialog.confirm({
			controller: MessangerArchiveDialogController,
			templateUrl: '/ajax/modal/messanger_archiver',
			parent: angular.element(document.body),
			scope: $scope,
			preserveScope:true,
			locals: {
				message: $scope.sessions[$scope.current_session]
			}
		});

		$mdDialog.show(confirm)
		.then(function() {
      $scope.status = 'You decided to get rid of your debt.';
    }, function() {
      $scope.status = 'You decided to keep your debt.';
    });

		function MessangerArchiveDialogController( $scope, $mdDialog, message)
		{
      $scope.saving = false;
      $scope.message = message;

			$scope.closeDialog = function(){
				$mdDialog.hide();
			}

      $scope.changeArchive = function() {
        if (!$scope.saving) {
          $scope.saving = true;

					console.log($scope.message);

          $http({
      			method: 'POST',
      			url: '/ajax/post',
      			headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      			data: $.param({
      				type: "Messanger",
              mode: 'archiver',
							message: $scope.message
      			})
      		}).success(function(r){
            console.log(r);
      			$scope.saving = false;
						if (r.success == 1) {
							$scope.toast(r.msg, 'success', 10000);
							$scope.closeDialog();
						} else {
							$scope.toast(r.msg, 'alert', 10000);
						}
	        });
      	}
			}
		}
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

a.directive('focusMe', function($timeout) {
  return {
    scope: { trigger: '=focusMe' },
    link: function(scope, element) {
      scope.$watch('trigger', function(value) {
        if(value === true) {
          //console.log('trigger',value);
          //$timeout(function() {
            element[0].focus();
            scope.trigger = false;
          //});
        }
      });
    }
  };
});

a.filter('unsafe', function($sce){ return $sce.trustAsHtml; });
a.filter('cash', function(){
	return function(cash, text, aftertext){
		if (cash) {
			cash = cash.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
			if (typeof text === 'undefined' || text == 1) {
				if (typeof aftertext === 'undefined') {
					cash += " Ft + ÁFA";
				} else {
					cash += " "+aftertext;
				}
			}
			return cash;
		} else {
			return '';
		}
	};
});

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
	$('.con i.hbtn').click(function(){
		var key = $(this).attr('key');

		$('.'+key).slideToggle(200);
	});

	getNotifications();
	startReceiveNotification( 10000 );

	tinymce.init({
	    selector: "textarea:not(.no-editor)",
	    editor_deselector : 'no-editor',
	    theme: "modern",
	    language: "hu_HU",
	    content_css : "/public/v1.0/styles/DinFonts.css",
	    allow_styles: 'family-font',
	    font_formats :
	   			"Din Composit=Din Comp, sans-serif;"+
	   			"Din Condensed=Din Cond, sans-serif;"+
	    		"Andale Mono=andale mono,times;"+
                "Arial=arial,helvetica,sans-serif;"+
                "Arial Black=arial black,avant garde;"+
                "Book Antiqua=book antiqua,palatino;"+
                "Comic Sans MS=comic sans ms,sans-serif;"+
                "Courier New=courier new,courier;"+
                "Georgia=georgia,palatino;"+
                "Helvetica=helvetica;"+
                "Impact=impact,chicago;"+
                "Symbol=symbol;"+
                "Tahoma=tahoma,arial,helvetica,sans-serif;"+
                "Terminal=terminal,monaco;"+
                "Times New Roman=times new roman,times;"+
                "Trebuchet MS=trebuchet ms,geneva;"+
                "Verdana=verdana,geneva;"+
                "Webdings=webdings;"+
                "Wingdings=wingdings,zapf dingbats",
	    plugins: [
	         "advlist autolink link image lists charmap print preview hr anchor pagebreak autoresize",
	         "searchreplace wordcount visualblocks visualchars insertdatetime media nonbreaking",
	         "table contextmenu directionality emoticons paste textcolor responsivefilemanager fullscreen code"
	   ],
	   toolbar1: "undo redo | bold italic underline | fontselect fontsizeselect forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | styleselect",
	   toolbar2: "| responsivefilemanager | link unlink anchor | image media |  print preview code ",
	   image_advtab: true ,
	   theme_advanced_resizing : true,
	   external_filemanager_path:"/filemanager/",
	   filemanager_title:"Responsive Filemanager" ,
	   external_plugins: { "filemanager" : "/filemanager/plugin.min.js"}
	 });

	$('.zoom').fancybox({
		openEffect	: 'none',
		closeEffect	: 'none'
	});

	$('.iframe-btn').fancybox({
		maxWidth	: 800,
		maxHeight	: 600,
		fitToView	: false,
		width		: '70%',
		height		: '70%',
		autoSize	: false,
		closeClick	: false,
		openEffect	: 'none',
		closeEffect	: 'none',
		closeBtn 	: false,
		padding		: 0
    });
});


var t = null;
function startReceiveNotification( timer ){
	t = setInterval( getNotifications, timer );
}

function loadTemplate ( key, arg, callback) {
	$.post('/ajax/post', {
		type : 'template',
		key : key,
		arg : $.param(arg)
	}, function(d){
		callback(d);
	},"html");
}

// Admin live értesítő
function getNotifications(){
	$.post("/ajax/get", {
		type : 'getNotification'
	}, function(d){
		if (d) {
			var a = $.parseJSON(d);
			// Üzenetek
			var msg_nf 		= $('.slideMenu .menu li a[title=Üzenetek]');
			var msg_nf_e 	= msg_nf.find('.ni');

			if( a.data.new_msg == 0 ){
				msg_nf_e
					.text( 0 )
					.attr( 'title', '' );
				msg_nf_e.css({
					visibility : 'hidden'
				});
			}else{
				msg_nf_e
					.text( a.data.new_msg )
					.attr( 'title', a.data.new_msg+ ' db új üzenet' );
				msg_nf_e.css({
					visibility : 'visible'
				});
			}

			// Megrendelés
			var order_nf 		= $('.slideMenu .menu li a[title=Megrendelések]');
			var order_nf_e 		= order_nf.find('.ni');

			if( a.data.new_order == 0 ){
				order_nf_e
					.text( 0 )
					.attr( 'title', '' );
				order_nf_e.css({
					visibility : 'hidden'
				});
			}else{
				order_nf_e
					.text( a.data.new_order )
					.attr( 'title', a.data.new_order+ ' db új megrendelés' );
				order_nf_e.css({
					visibility : 'visible'
				});
			}
		}
	}, "html");
}

var a = angular.module('App', ['ngMaterial']);

a.controller("ProjectControl", ['$scope', '$http', '$mdToast', '$mdDialog', '$sce', '$filter', function($scope, $http, $mdToast, $mdDialog, $sce, $filter)
{
	$scope.partner = {
		neve: 'Molnár István'
	};
	$scope.messanger = {
		text: ''
	};
	$scope.project = {};

	$scope.init = function( conf )
	{
		if (typeof conf !== 'undefined') {
			$scope.loadconfig = conf;
		} else {
			$scope.loadconfig = {};
		}

		$scope.loadEverything();
	}

	$scope.loadEverything = function() {
		$scope.loadLists(function( data ){

		});
	}

	$scope.sendQuickMessage = function( project_hashkey ) {

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

	$scope.projectEditor = function()
	{
		var confirm = $mdDialog.confirm({
			controller: ProjectEditorDialigController,
			templateUrl: '/ajax/modal/usereditproject',
			parent: angular.element(document.body),
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

		function ProjectEditorDialigController( $scope, $mdDialog, config, project) {
      $scope.saving = false;
      $scope.loadconfig = config;
      $scope.project = project;

			$scope.closeDialog = function(){
				$mdDialog.hide();
			}

      $scope.saveProject = function( type ){
        if (!$scope.saving) {
          $scope.saving = true;

					/*
          $http({
      			method: 'POST',
      			url: '/ajax/post',
      			headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      			data: $.param({
      				type: "modalMessage",
              modalby: type,
              datas: $scope[type]
      			})
      		}).success(function(r){
            console.log(r);
      			$scope.sending = false;
      			$scope.termekkerdes = {};

            if (r.error == 1) {
              $scope.toast(r.msg, 'alert', 10000);
            } else {
              $mdToast.hide();
              $scope.closeDialog();
              $scope.toast(r.msg, 'success', 10000);
            }
      		});
					*/
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

a.controller("OfferControl", ['$scope', '$http', '$mdToast', '$sce', '$filter', function($scope, $http, $mdToast, $sce, $filter)
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

		$scope.loadEverything();
	}

	$scope.loadEverything = function() {
		$scope.loadLists(function( data ){

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
	}

	$scope.pickRequest = function( request )
	{
		$scope.acceptofferdata = {};
		$scope.readrequest = request.ID;
		$scope.request = request;
		var price = request.cash_config[request.subservice.ID][request.item_id];
		if (price) {
			$scope.offer.price = parseFloat(price);
		}
		$scope.showOfferSending(false);
		console.log($scope.request);
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
					request: $scope.request.ID,
					offer: $scope.request.offer.ID,
					fromuserid: $scope.request.user_from_id,
					touserid: $scope.request.user_to_id,
					relation: $scope.request.my_relation,
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
						$scope.reloadRequestObject(data[$scope.relation], $scope.request.ID );
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
				mode: 'List',
				filter: filters
			})
		}).success(function(r){
			console.log(r);

			if (r.data && r.data.length != 0) {
				$scope.requests = r.data;
				if (r.data.from_num) {
					$scope.badges.out = r.data.from_num;
				}
				if (r.data.to_num) {
					$scope.badges.in = r.data.to_num;
				}
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
					$scope.reloadRequestObject(data[$scope.relation], request_id );
				});
			}
		});
	}

	$scope.reloadRequestObject = function( data, id ) {
		if (data) {
			angular.forEach(data, function(e,i){
				angular.forEach(e.services, function(a,i){
					angular.forEach(a.items, function(b,i){
						angular.forEach(b.users, function(c,i){
							if(c.ID == id) {
								$scope.pickRequest( c );
							}
						});
					});
				});
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

a.controller("RequestControl", ['$scope', '$http', '$mdToast', '$sce', function($scope, $http, $mdToast, $sce)
{
	$scope.quicksearch = '';
	$scope.requests = [];
	$scope.request = false;
	$scope.request_offerouts = {};
	$scope.readrequest = 0;
	$scope.loadconfig = {};
	$scope.init = function( conf )
	{
		$scope.loadconfig = conf;
		$scope.loadEverything();
	}
	$scope.servuser = {};
	$scope.servicesrequestprogress = false;

	$scope.loadEverything = function() {
		$scope.loadLists(function( data ){

		});
	}

	$scope.pickRequest = function( request ) {
		$scope.readrequest = request.ID;
		$scope.request = request;
		$scope.servuser = {};
		$scope.request_offerouts = {};

		if (request.services_hints) {
			angular.forEach(request.services_hints, function(requester,itemid){
				$scope.servuser['item_'+itemid] = {};
				angular.forEach(requester.users, function(user,i){
					if (typeof $scope.servuser['item_'+itemid][user.ID] === 'undefined') {
						var already_offered = $scope.checkOfferOuts( request.ID, user.ID, requester);

						if (typeof $scope.request_offerouts[requester.service.ID+'_'+requester.subservice.ID+'_'+requester.item.ID] === 'undefined') {
							$scope.request_offerouts[requester.service.ID+'_'+requester.subservice.ID+'_'+requester.item.ID] = {};
						}

						$scope.request_offerouts[requester.service.ID+'_'+requester.subservice.ID+'_'+requester.item.ID][user.ID] = already_offered;

						if ( !already_offered ) {
							$scope.servuser['item_'+itemid][user.ID] = true;
						}
					}
				});
			});
		}
		console.log($scope.request_offerouts);
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
		$http({
			method: 'POST',
			url: '/ajax/post',
			headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
			data: $.param({
				type: "Requests",
				mode: 'List',
				filter: {
					offerout: ($scope.loadconfig && $scope.loadconfig.offerout) ? $scope.loadconfig.offerout : 0,
					loadpossibleservices: ($scope.loadconfig && $scope.loadconfig.loadpossibleservices) ? 1: 0,
					bindIDToList: 0
				}
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
				servicesus: $scope.servuser,
				request: $scope.request.hashkey,
			})
		}).success(function(r){
			console.log(r);
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


a.filter('unsafe', function($sce){ return $sce.trustAsHtml; });

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

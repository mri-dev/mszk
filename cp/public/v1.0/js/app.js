var a = angular.module('App', ['ngMaterial']);

a.controller("RequestControl", ['$scope', '$http', '$mdToast', '$sce', function($scope, $http, $mdToast, $sce)
{
	$scope.requests = [];
	$scope.request = false;
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

		if (request.services_hints) {
			angular.forEach(request.services_hints, function(requester,itemid){
				$scope.servuser['item_'+itemid] = {};
				angular.forEach(requester.users, function(user,i){
					if (typeof $scope.servuser['item_'+itemid][user.ID] === 'undefined') {
						$scope.servuser['item_'+itemid][user.ID] = true;
					}
				});
			});
		}
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
					loadpossibleservices: ($scope.loadconfig && $scope.loadconfig.loadpossibleservices) ? 1: 0
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
		console.log($scope.servuser);
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

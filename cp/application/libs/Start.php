<? class Start{

		function __construct(){

			$url = Helper::GET();

			$openControl = null;

			if(count($url) == 0){

				$openControl = 'home';

			}else if(count($url) != 0){

				$openControl = $url[0];

			}

			if(!file_exists(CONTROL . $openControl . '.php')){
				$openControl = "PageNotFound";
			}

			require CONTROL . $openControl . '.php';

			$control = new $openControl();
			if(count($url) > 1){
				if(method_exists($control,$url[1])){
					$ctrl = $url[1];
					$control->fnTemp = $url[1];
					$control->{$ctrl}();
				}
			}

		}

	}

?>

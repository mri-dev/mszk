<?php
class ajax extends Controller{
		function __construct()
		{
			header("Access-Control-Allow-Origin: *");
			parent::__construct();
		}

		function post()
		{
			extract($_POST);
			$ret = array(
				'success' => 0,
				'msg' => false
			);
			switch($type)
			{
				default: break;
			}
			echo json_encode($ret);
		}

		function get()
		{
			extract($_POST);

			switch($type){
				case 'settings':
					$_POST['key'] = ($_POST['key'] != '') ? (array)$_POST['key'] : array();

					if ( empty($_POST['key']) ) {
						$ret['data'] = $this->view->settings;
					} else {
						$settings = array();

						foreach ( $_POST['key'] as $key ) {
							$settings[$key] = $this->view->settings[$key];
						}

						$ret['data'] = $settings;
					}

					$ret['pass'] = $_POST;
					echo json_encode($ret);
				break;
			}

			$this->view->render(__CLASS__.'/'.__FUNCTION__.'/'.$type, true);
		}

		function template(){
			$this->view->render(__CLASS__.'/'.__FUNCTION__.'/'.$this->gets[2], true);
		}

		private function setSuccess($msg, &$ret){
			$ret[msg] = $msg;
			$ret[success]	= 1;
			return true;
		}

		private function escape($msg, &$ret){
			$ret[msg] 		= $msg;
			$ret[success] 	= 0;
			return true;
		}
		function __destruct(){ }
	}

?>

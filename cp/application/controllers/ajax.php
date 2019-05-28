<?
use PortalManager\Template;
use PortalManager\OfferRequests;

class ajax extends Controller
{
		function __construct(){
			parent::__construct();
		}

		function post(){
			extract($_POST);

			$ret = array(
				'success' => 0,
				'msg' => false
			);

			switch($type)
			{
				case 'Requests':
					$ret['data'] = array();
					$ret['pass'] = $_POST;

					switch ( $mode )
					{
						case 'List':
							$requests = new OfferRequests( array('db' => $this->db) );
							$arg = array();
							if (isset($_POST['filter'])) {
								$arg = (array)$_POST['filter'];
							}

							$ret['data'] = $requests->getList( $arg );
						break;
					}
					echo json_encode($ret);
					return;
				break;
			}
		}

		private function setSuccess($msg, &$ret){
			$ret[msg] 		= $msg;
			$ret[success] 	= 1;
			return true;
		}
		private function escape($msg, &$ret){
			$ret[msg] 		= $msg;
			$ret[success] 	= 0;
			return true;
		}

		function get(){
			extract($_POST);

			$sub_page = '';

			switch($type){
				/**
				* ANGULAR ACTIONS
				**/
				case 'Sample':
					$key = $_POST['key'];

					$re = array(
						'error' => 0,
						'msg' => null,
						'data' 	=> array()
					);
					$re['pass'] = $_POST;


					echo json_encode( $re );
				break;
				/* END: ANGULAR ACTIONS */
			}

			$sub_page = ( $sub_page != '' ) ? '_'.$sub_page : '';
			$this->view->render(__CLASS__.'/'.__FUNCTION__.'/'.$type.$sub_page, true);
		}

		function __destruct(){
		}
	}

?>

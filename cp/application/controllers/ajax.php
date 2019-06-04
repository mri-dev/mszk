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
						case 'sendServiceRequest':
							$requests = new OfferRequests( array('db' => $this->db) );
							try {
								$ret['t'] = $requests->sendServiceRequest( $request, $servicesus );
							} catch (\Exception $e) {
								$this->escape($e->getMessage(), $ret);
							}
						break;
						case 'requestActions':
							$requests = new OfferRequests( array('db' => $this->db) );
							try {
								switch ($what) {
									case 'visit':
										$requests->setRequestData( $request, 'visited', 1);
										$requests->setRequestData( $request, 'visited_at', NOW);
										$this->setSuccess(sprintf(__('Megtekintett állapot módosítva lett: Látta (%s)'), NOW), $ret);
									break;
									case 'unvisit':
										$requests->setRequestData( $request, 'visited', 0);
										$requests->setRequestData( $request, 'visited_at', NULL);
										$this->setSuccess(__('Megtekintett állapot módosítva lett: Láttam levétele'), $ret);
									break;
									case 'elutasit':
										$requests->setRequestData( $request, 'elutasitva', 1);
										$requests->setRequestData( $request, 'offerout', 1);
										$requests->setRequestData( $request, 'visited', 1);
										$requests->setRequestData( $request, 'visited_at', NOW);
									$this->setSuccess(__('Az ajánlatkérést sikeresen elutasítottnak jelölte.'), $ret);
									break;
									default:
										$this->escape(sprintf(__('Nincs ilyen végrehajtható művelet: %s'), $what), $ret);
									break;
								}
							} catch (\Exception $e) {
								$this->escape($e->getMessage(), $ret);
							}
						break;
					}
					echo json_encode($ret);
					return;

				break;

				case 'RequestOffers':
					$ret['data'] = array();
					$ret['pass'] = $_POST;

					if ( !$this->view->_USERDATA ) {
						$this->escape(__('Kérjük, hogy jelentkezzen be újra!'), $ret);
					}

					$uid = (int)$this->view->_USERDATA['data']['ID'];
					$user_group = $this->view->_USERDATA['data']['user_group'];

					switch ( $mode )
					{
						case 'List':
							$requests = new OfferRequests( array('db' => $this->db) );
							try {
								$requestoffers = $requests->getUserOfferRequests( $uid, $user_group );
								$ret['data'] = $requestoffers;
							} catch (\Exception $e) {
								$this->escape($e->getMessage(), $ret);
							}

						break;
					}

					echo json_encode($ret);
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
